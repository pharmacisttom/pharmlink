<?php
// api/auth.php
require_once 'db_connect.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- LOGIN ---
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'กรุณากรอก Username และ Password']);
        exit();
    }

    try {
        // ค้นหาผู้ใช้ในตาราง datapharmacist (ตามโครงสร้างจริง: userlogin, username, password_hash, role)
        $stmt = $app_pdo->prepare("SELECT id, userlogin, username AS fullname, password_hash AS password, role, active FROM datapharmacist WHERE userlogin = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // ตรวจสอบสถานะ (active)
        if ($user && $user['active'] == 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'บัญชีนี้ถูกระงับการใช้งาน']);
            exit();
        }

        // ตรวจสอบรหัสผ่าน (รองรับทั้ง MD5, SHA-256 และการตรวจสอบด้วย password_verify เผื่อไว้)
        $hashed_password_sha256 = hash('sha256', $password);
        $hashed_password_md5 = md5($password);
        
        $is_password_valid = false;
        if ($user) {
            if ($user['password'] === $password || 
                $user['password'] === $hashed_password_sha256 || 
                $user['password'] === $hashed_password_md5 ||
                password_verify($password, $user['password'])) {
                $is_password_valid = true;
            }
        }

        if ($user && $is_password_valid) {
            
            // ตั้งค่า Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['userlogin'] = $user['userlogin'];
            $_SESSION['fullname'] = $user['fullname'];

            // บันทึก Log
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $log_stmt = $app_pdo->prepare("INSERT INTO pharmalink_logs (user_id, action, details, ip_address) VALUES (?, 'LOGIN', 'User logged in successfully', ?)");
            $log_stmt->execute([$user['id'], $ip_address]);

            echo json_encode([
                'success' => true, 
                'message' => 'เข้าสู่ระบบสำเร็จ',
                'user' => [
                    'fullname' => $user['fullname'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Username หรือ Password ไม่ถูกต้อง']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// --- LOGOUT ---
if ($action === 'logout') {
    if (isset($_SESSION['user_id'])) {
        try {
            // บันทึก Log
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $log_stmt = $app_pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, 'LOGOUT', 'User logged out', ?)");
            $log_stmt->execute([$_SESSION['user_id'], $ip_address]);
        } catch (Exception $e) {
            // ละทิ้งข้อผิดพลาดตอนบันทึก log เพื่อให้ logout ทำงานได้
        }
    }

    session_unset();
    session_destroy();

    echo json_encode(['success' => true, 'message' => 'ออกจากระบบสำเร็จ']);
    exit();
}

// --- CHECK SESSION ---
if ($action === 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true, 
            'user' => [
                'fullname' => $_SESSION['fullname'],
                'role_id' => $_SESSION['role_id']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'ยังไม่ได้เข้าสู่ระบบ']);
    }
    exit();
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
