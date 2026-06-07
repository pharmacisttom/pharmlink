<?php
// api/save_inquiry.php
require_once 'db_connect.php';

header('Content-Type: application/json');

// เช็คสิทธิ์ (ต้อง Login ก่อนบันทึก)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อนทำรายการ']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// ตรวจสอบข้อมูลที่จำเป็น
$required_fields = ['inquirer_name', 'hospital', 'province', 'position', 'phone_number', 'inquiry_reason'];
foreach ($required_fields as $field) {
    if (empty(trim($input[$field] ?? ''))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "กรุณากรอกข้อมูลให้ครบถ้วน ($field)"]);
        exit();
    }
}

$inquirer_name = htmlspecialchars(trim($input['inquirer_name']));
$hospital = htmlspecialchars(trim($input['hospital']));
$province = htmlspecialchars(trim($input['province']));
$position = htmlspecialchars(trim($input['position']));
$license_number = htmlspecialchars(trim($input['license_number'] ?? ''));
$phone_number = htmlspecialchars(trim($input['phone_number']));
$inquiry_reason = htmlspecialchars(trim($input['inquiry_reason']));
$patient_hn = htmlspecialchars(trim($input['patient_hn'] ?? ''));
$duration_seconds = intval($input['duration_seconds'] ?? 0);
$recorded_by = $_SESSION['user_id'];

try {
    $query = "INSERT INTO pharmalink_inquiries (inquirer_name, hospital, province, position, license_number, phone_number, inquiry_reason, patient_hn, duration_seconds, recorded_by) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $app_pdo->prepare($query);
    $stmt->execute([$inquirer_name, $hospital, $province, $position, $license_number, $phone_number, $inquiry_reason, $patient_hn, $duration_seconds, $recorded_by]);
    
    $record_id = $app_pdo->lastInsertId();

    // บันทึก Log การทำงาน
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $log_stmt = $app_pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, 'SAVE_INQUIRY', ?, ?)");
    $log_details = "Recorded inquiry from {$hospital} (Record ID: {$record_id})";
    $log_stmt->execute([$recorded_by, $log_details, $ip_address]);

    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'บันทึกข้อมูลการโทรสอบถามสำเร็จ',
        'record_id' => $record_id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
