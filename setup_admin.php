<?php
require 'api/config.php';
try {
    $pdo = new PDO("mysql:host=$app_host;dbname=$app_db;charset=utf8", $app_user, $app_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $userlogin = 'admin';
    $username = 'Administrator';
    $password = 'admin1234';
    $password_hash = hash('sha256', $password); // ใช้ SHA256 ตามที่ API รองรับ
    $role = 'admin';

    // ตรวจสอบว่ามี admin หรือยัง
    $stmt = $pdo->prepare("SELECT id FROM datapharmacist WHERE userlogin = ?");
    $stmt->execute([$userlogin]);
    if ($stmt->fetch()) {
        echo "มีบัญชี admin อยู่ในระบบแล้ว\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO datapharmacist (userlogin, username, password_hash, role, active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$userlogin, $username, $password_hash, $role]);
        echo "สร้างบัญชี Admin สำเร็จ!\n";
        echo "Username: $userlogin\n";
        echo "Password: $password\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
