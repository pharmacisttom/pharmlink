<?php
session_start();

// ถ้ายอมรับ Session แล้ว ให้ไปหน้าหลัก (ฟอร์มค้นหา)
if (isset($_SESSION['user_id'])) {
    header("Location: inquiry_form.php");
    exit();
}

// ถ้ายังไม่เข้าสู่ระบบ ให้เด้งไปหน้า Login
header("Location: login.php");
exit();
?>
