<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

// เช็คสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied: คุณไม่มีสิทธิ์เข้าถึงส่วนนี้']);
    exit();
}

$action = $_GET['action'] ?? '';

// === 1. List Users ===
if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $app_pdo->query("SELECT id, userlogin, username AS fullname, role, active, created_at FROM datapharmacist ORDER BY id DESC");
        $users = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $users]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// === 2. Create User ===
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    $userlogin = trim($input['userlogin'] ?? '');
    $fullname = trim($input['fullname'] ?? '');
    $password = trim($input['password'] ?? '');
    $role = trim($input['role'] ?? 'pharmacist');
    
    if (empty($userlogin) || empty($fullname) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        exit();
    }

    try {
        // เช็คชื่อซ้ำ
        $check = $app_pdo->prepare("SELECT id FROM datapharmacist WHERE userlogin = ?");
        $check->execute([$userlogin]);
        if ($check->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username นี้มีอยู่ในระบบแล้ว']);
            exit();
        }

        $password_hash = hash('sha256', $password);
        $stmt = $app_pdo->prepare("INSERT INTO datapharmacist (userlogin, username, password_hash, role, active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$userlogin, $fullname, $password_hash, $role]);

        echo json_encode(['success' => true, 'message' => 'สร้างผู้ใช้งานสำเร็จ']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// === 3. Update User ===
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    $id = intval($input['id'] ?? 0);
    $fullname = trim($input['fullname'] ?? '');
    $role = trim($input['role'] ?? '');
    $active = isset($input['active']) ? intval($input['active']) : null;

    if (!$id || empty($fullname) || empty($role) || $active === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    try {
        $stmt = $app_pdo->prepare("UPDATE datapharmacist SET username = ?, role = ?, active = ? WHERE id = ?");
        $stmt->execute([$fullname, $role, $active, $id]);

        echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// === 4. Reset Password ===
if ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    $id = intval($input['id'] ?? 0);
    $new_password = trim($input['new_password'] ?? '');

    if (!$id || empty($new_password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'กรุณาระบุรหัสผ่านใหม่']);
        exit();
    }

    try {
        $password_hash = hash('sha256', $new_password);
        $stmt = $app_pdo->prepare("UPDATE datapharmacist SET password_hash = ? WHERE id = ?");
        $stmt->execute([$password_hash, $id]);

        echo json_encode(['success' => true, 'message' => 'เปลี่ยนรหัสผ่านสำเร็จ']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// === 5. Delete User ===
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    $id = intval($input['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ไม่พบ ID ที่ต้องการลบ']);
        exit();
    }
    
    // ป้องกันลบตัวเอง
    if ($id == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบบัญชีตัวเองได้']);
        exit();
    }

    try {
        $stmt = $app_pdo->prepare("DELETE FROM datapharmacist WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'ลบผู้ใช้งานสำเร็จ']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบได้เนื่องจากมีการอ้างอิงข้อมูล หรือเกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
    exit();
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
