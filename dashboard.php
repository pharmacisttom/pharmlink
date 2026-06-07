<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'api/db_connect.php';

try {
    // สรุปจำนวนการสอบถามทั้งหมด
    $stmt_total = $app_pdo->query("SELECT COUNT(*) FROM pharmalink_inquiries");
    $total_inquiries = $stmt_total->fetchColumn();

    // สรุป 5 โรงพยาบาลที่ติดต่อมาบ่อยสุด
    $stmt_hosp = $app_pdo->query("SELECT hospital, COUNT(*) as count FROM pharmalink_inquiries GROUP BY hospital ORDER BY count DESC LIMIT 5");
    $top_hospitals = $stmt_hosp->fetchAll();

    // คำนวณเวลาเฉลี่ย (AHT: Average Handling Time)
    $stmt_aht = $app_pdo->query("SELECT AVG(duration_seconds) FROM pharmalink_inquiries WHERE duration_seconds > 0");
    $aht_seconds = (int)$stmt_aht->fetchColumn();
    $aht_min = floor($aht_seconds / 60);
    $aht_sec = $aht_seconds % 60;
    $aht_display = sprintf("%02d:%02d", $aht_min, $aht_sec);

    // สรุปประวัติล่าสุด
    $stmt_recent = $app_pdo->query("
        SELECT i.hospital, i.inquirer_name, i.patient_hn, i.recorded_at, i.duration_seconds, d.username as fullname 
        FROM pharmalink_inquiries i 
        JOIN datapharmacist d ON i.recorded_by = d.id 
        ORDER BY i.recorded_at DESC LIMIT 10
    ");
    $recent_logs = $stmt_recent->fetchAll();

} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<?php require_once 'includes/header.php'; ?>

<!-- เริ่มเนื้อหาหลัก -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4 md:col-span-2 lg:col-span-1">
        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold"><i class="fa-solid fa-phone"></i></div>
        <div>
            <p class="text-sm text-slate-500 font-medium">จำนวนการขอข้อมูลทั้งหมด</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo number_format($total_inquiries); ?> <span class="text-base font-normal text-slate-500">ครั้ง</span></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4 md:col-span-2 lg:col-span-1">
        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-2xl font-bold"><i class="fa-solid fa-stopwatch"></i></div>
        <div>
            <p class="text-sm text-slate-500 font-medium">เวลาเฉลี่ยต่อเคส (AHT)</p>
            <p class="text-3xl font-bold text-slate-800"><?php echo $aht_display; ?> <span class="text-base font-normal text-slate-500">นาที</span></p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:col-span-4 lg:col-span-2">
        <h3 class="text-sm font-semibold text-slate-500 mb-4">5 อันดับโรงพยาบาลที่ติดต่อมาบ่อยที่สุด</h3>
        <div class="space-y-3">
            <?php foreach($top_hospitals as $idx => $h): ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-bold w-4 text-right"><?php echo $idx + 1; ?>.</span>
                    <span class="font-medium text-slate-700"><?php echo htmlspecialchars($h['hospital']); ?></span>
                </div>
                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold"><?php echo $h['count']; ?> ครั้ง</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-800">ประวัติการบันทึกล่าสุด</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider text-xs border-b border-slate-200">
                <tr>
                    <th class="p-4 pl-6">วัน/เวลา</th>
                    <th class="p-4">ผู้สอบถาม</th>
                    <th class="p-4">จากโรงพยาบาล</th>
                    <th class="p-4">เวลาทำเคส</th>
                    <th class="p-4">HN ผู้ป่วยที่ค้นหา</th>
                    <th class="p-4">ผู้บันทึกระบบ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach($recent_logs as $log): ?>
                <tr class="hover:bg-slate-50">
                    <td class="p-4 pl-6 text-slate-500"><?php echo date('d/m/Y H:i', strtotime($log['recorded_at'])); ?></td>
                    <td class="p-4 font-medium text-slate-800"><?php echo htmlspecialchars($log['inquirer_name']); ?></td>
                    <td class="p-4 text-slate-600"><?php echo htmlspecialchars($log['hospital']); ?></td>
                    <td class="p-4 text-slate-600 text-xs">
                        <?php 
                            if(isset($log['duration_seconds']) && $log['duration_seconds'] > 0) {
                                echo sprintf("%02d:%02d", floor($log['duration_seconds']/60), $log['duration_seconds']%60);
                            } else {
                                echo '-';
                            }
                        ?>
                    </td>
                    <td class="p-4 font-mono text-indigo-600"><?php echo htmlspecialchars($log['patient_hn'] ?: '-'); ?></td>
                    <td class="p-4 text-slate-500"><?php echo htmlspecialchars($log['fullname']); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recent_logs)): ?>
                <tr><td colspan="6" class="p-8 text-center text-slate-400">ยังไม่มีข้อมูล</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
