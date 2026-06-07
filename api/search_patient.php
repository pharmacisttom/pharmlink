<?php
// api/search_patient.php
require_once 'db_connect.php';

header('Content-Type: application/json');

// เช็คสิทธิ์ 
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$query = $_GET['q'] ?? '';

if (empty(trim($query))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'กรุณาระบุคำค้นหา']);
    exit();
}

// ลบช่องว่างส่วนเกิน
$search_term = trim($query);

try {
    // โครงสร้างตาราง HIS (Himpro)
    // ค้นหาจากฐานข้อมูล pt ตาราง pt
    if (is_numeric($search_term) && strlen($search_term) === 13) {
        // ค้นหาด้วย CID 13 หลัก (ลบขีดออกเวลาค้นหา)
        $sql = "SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptdob AS birthday, ptsex AS sex FROM pt.pt WHERE REPLACE(cardid, '-', '') = :search LIMIT 10";
        $stmt = $his_pdo->prepare($sql);
        $stmt->execute([':search' => $search_term]);
    } elseif (is_numeric($search_term) || preg_match('/^[A-Z0-9]+$/i', $search_term)) {
        // ค้นหาด้วย HN
        $sql = "SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptdob AS birthday, ptsex AS sex FROM pt.pt WHERE hn = :search LIMIT 10";
        $stmt = $his_pdo->prepare($sql);
        $stmt->execute([':search' => $search_term]);
    } else {
        // ค้นหาด้วย ชื่อ-สกุล
        $search_tis = @iconv("UTF-8", "TIS-620//IGNORE", $search_term);
        $search_like = "%{$search_tis}%";
        $sql = "SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptdob AS birthday, ptsex AS sex FROM pt.pt WHERE ptfname LIKE :search1 OR ptlname LIKE :search2 LIMIT 20";
        $stmt = $his_pdo->prepare($sql);
        $stmt->execute([':search1' => $search_like, ':search2' => $search_like]);
    }

    $results = [];
    while ($row = $stmt->fetch()) {
        // แปลง TIS-620 กลับเป็น UTF-8 เพื่อส่งออก API
        $results[] = [
            'hn' => $row['hn'],
            'cid' => $row['cid'],
            'fname' => decodeThai($row['fname'] ?? ''),
            'lname' => decodeThai($row['lname'] ?? ''),
            'birthday' => $row['birthday'],
            'sex' => $row['sex']
        ];
    }

    // บันทึก Log
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $log_stmt = $app_pdo->prepare("INSERT INTO pharmalink_logs (user_id, action, details, ip_address) VALUES (?, 'SEARCH_PATIENT', ?, ?)");
    $log_stmt->execute([$_SESSION['user_id'], "Searched for: {$search_term}", $ip_address]);

    echo json_encode(['success' => true, 'data' => $results]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'HIS Database error: ' . $e->getMessage()]);
}
?>
