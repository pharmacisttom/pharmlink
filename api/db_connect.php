<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Bangkok');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/config.php';


try {
    // เชื่อมต่อ APP (ใช้ UTF-8)
    $app_pdo = new PDO("mysql:host=$app_host;dbname=$app_db;charset=utf8mb4", $app_user, $app_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $app_pdo->exec("SET time_zone = '+07:00'");


    // เชื่อมต่อ HIS (ใช้ TIS620)
    $his_pdo = new PDO("mysql:host=$his_host;dbname=$his_db;charset=tis620", $his_user, $his_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $his_pdo->exec("SET NAMES tis620");
    $his_pdo->exec("SET time_zone = '+07:00'");


} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}


// ==========================================
// Function ช่วยแปลงตัวอักษร TIS-620 เป็น UTF-8 
// (ปรับปรุงสำหรับ PHP 8+ ป้องกัน ValueError)
// ==========================================
function decodeThai($str) {
    if (!$str) return "";
    
    // ใช้ iconv แทน และใส่ //IGNORE เพื่อข้ามตัวอักษรที่แปลงไม่ได้ จะได้ไม่เกิด Fatal Error
    $decoded = @iconv("TIS-620", "UTF-8//IGNORE", $str);
    
    // ถ้า TIS-620 ยังหาไม่เจอ ให้ใช้ Windows-874 (รหัสภาษาไทยของ Windows) แทน
    if (!$decoded) {
        $decoded = @iconv("Windows-874", "UTF-8//IGNORE", $str);
    }
    
    return $decoded ? $decoded : $str;
}


// ==========================================
// ฟังก์ชันตรวจสอบสิทธิ์การเข้าถึงหน้าเว็บ (RBAC System)
// ==========================================
function get_current_role_id(): ?int {
    global $app_pdo;


    if (!isset($_SESSION['user_id'])) {
        return null;
    }


    if (isset($_SESSION['role']) && $_SESSION['role'] !== '') {
        return $_SESSION['role'] === 'admin' ? 1 : 2;
    }

    // ดึงข้อมูลผู้ใช้จากตาราง datapharmacist
    $stmt = $app_pdo->prepare("SELECT role, userlogin, username AS fullname FROM datapharmacist WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] === null || $user['role'] === '') {
        return null;
    }

    $_SESSION['role'] = $user['role'];
    $_SESSION['userlogin'] = $_SESSION['userlogin'] ?? $user['userlogin'];
    $_SESSION['fullname'] = $_SESSION['fullname'] ?? $user['fullname'];

    return $_SESSION['role'] === 'admin' ? 1 : 2;
}


function is_admin(): bool {
    return get_current_role_id() === 1;
}


function render_access_denied(string $message = 'ขออภัย ท่านไม่มีสิทธิ์เข้าใช้งานหน้านี้ กรุณาติดต่อผู้ดูแลระบบ (Admin)'): void {
    die('
        <div style="font-family: sans-serif; text-align: center; margin-top: 50px; background-color: #fef2f2; padding: 40px; border-radius: 10px; max-width: 600px; margin: 0 auto;">
            <h1 style="color: #dc2626; font-size: 24px; font-weight: bold;">Access Denied</h1>
            <p style="color: #4b5563; margin-top: 10px; margin-bottom: 30px;">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>
            <a href="javascript:history.back()" style="padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">กลับหน้าก่อนหน้า</a>
        </div>
    ');
}


function check_permission($current_page) {
    global $app_pdo;


    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }


    $role_id = get_current_role_id();
    if ($role_id === null) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }


    if ($role_id === 1 || $current_page === 'dashboard.php') {
        return;
    }


    $stmt = $app_pdo->prepare("SELECT COUNT(*) FROM role_permissions WHERE role_id = ? AND page_name = ?");
    $stmt->execute([$role_id, $current_page]);
    $has_access = (int)$stmt->fetchColumn();


    if ($has_access === 0) {
        render_access_denied();
    }
}
?>
