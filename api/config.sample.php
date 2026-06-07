<?php
// เปลี่ยนฐานข้อมูลเป็น pharmcare ตามที่ระบุ
$app_db = 'pharmcare';

// --- APP DB ---
// Server uses local MySQL account.
// Developer/XAMPP machines connect to the server MySQL by IP.
$server_ips = ['127.0.0.1']; // เปลี่ยนเป็น IP ของ Server 
$current_host = $_SERVER['HTTP_HOST'] ?? '';
$current_server_addr = $_SERVER['SERVER_ADDR'] ?? ($_SERVER['LOCAL_ADDR'] ?? '');
$is_server = in_array($current_server_addr, $server_ips, true)
    || strpos($current_host, '127.0.0.1') === 0;

if ($is_server) {
    $app_host = 'localhost';
    $app_user = 'ใส่_User_ที่นี่';
    $app_pass = 'ใส่_Password_ที่นี่';
} else {
    $app_host = 'APP_DB_IP_ADDRESS'; // ใส่ IP ฐานข้อมูลหลัก
    $app_user = 'ใส่_User_ที่นี่';
    $app_pass = 'ใส่_Password_ที่นี่';
}

// --- HIS DB (ฐานข้อมูลโรงพยาบาล) ---
$his_host = 'HIS_DB_IP_ADDRESS'; // ใส่ IP ฐานข้อมูล HIS
$his_db   = 'hos'; 
$his_user = 'ใส่_User_ที่นี่';
$his_pass = 'ใส่_Password_ที่นี่';
