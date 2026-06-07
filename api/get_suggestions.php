<?php
// api/get_suggestions.php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $suggestions = [
        'hospitals' => [],
        'positions' => [],
        'reasons' => []
    ];

    // 1. ดึง 5 โรงพยาบาลที่ติดต่อมาบ่อยสุด (พร้อมจังหวัดล่าสุดที่คู่กัน)
    $stmt_hosp = $app_pdo->query("
        SELECT hospital, MAX(province) as province, COUNT(*) as cnt 
        FROM pharmalink_inquiries 
        GROUP BY hospital 
        ORDER BY cnt DESC 
        LIMIT 5
    ");
    $suggestions['hospitals'] = $stmt_hosp->fetchAll(PDO::FETCH_ASSOC);

    // 2. ดึง 5 ตำแหน่งที่โทรมาบ่อยสุด
    $stmt_pos = $app_pdo->query("
        SELECT position, COUNT(*) as cnt 
        FROM pharmalink_inquiries 
        GROUP BY position 
        ORDER BY cnt DESC 
        LIMIT 5
    ");
    $suggestions['positions'] = $stmt_pos->fetchAll(PDO::FETCH_ASSOC);

    // 3. ดึง 5 วัตถุประสงค์ที่เจอบ่อยสุด
    $stmt_reason = $app_pdo->query("
        SELECT inquiry_reason, COUNT(*) as cnt 
        FROM pharmalink_inquiries 
        GROUP BY inquiry_reason 
        ORDER BY cnt DESC 
        LIMIT 5
    ");
    $suggestions['reasons'] = $stmt_reason->fetchAll(PDO::FETCH_ASSOC);

    // ถ้าฐานข้อมูลว่าง ให้ส่ง Default Suggestions กลับไป
    if (empty($suggestions['hospitals'])) {
        $suggestions['hospitals'] = [
            ['hospital' => 'รพ.สต. ในเครือข่าย', 'province' => 'เชียงใหม่'],
            ['hospital' => 'โรงพยาบาลชุมชน', 'province' => 'เชียงใหม่']
        ];
    }
    if (empty($suggestions['positions'])) {
        $suggestions['positions'] = [
            ['position' => 'เภสัชกร'],
            ['position' => 'พยาบาลวิชาชีพ'],
            ['position' => 'แพทย์']
        ];
    }
    if (empty($suggestions['reasons'])) {
        $suggestions['reasons'] = [
            ['inquiry_reason' => 'เพื่อตรวจสอบประวัติแพ้ยา'],
            ['inquiry_reason' => 'เพื่อดูรายการยาเดิม (Medication Reconciliation)'],
            ['inquiry_reason' => 'เพื่อการรักษาต่อเนื่อง'],
            ['inquiry_reason' => 'ประเมินความร่วมมือในการใช้ยา']
        ];
    }

    echo json_encode(['success' => true, 'data' => $suggestions]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>
