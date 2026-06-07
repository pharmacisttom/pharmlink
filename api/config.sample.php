<?php
// เปลี่ยนฐานข้อมูลเป็น pharmcare ตามที่ระบุ
$app_db = 'pharmcare';

// --- APP DB ---
// Server 192.168.111.240 uses local MySQL account.
// Developer/XAMPP machines connect to the server MySQL by IP.
$server_ips = ['192.168.111.240'];
$current_host = $_SERVER['HTTP_HOST'] ?? '';
$current_server_addr = $_SERVER['SERVER_ADDR'] ?? ($_SERVER['LOCAL_ADDR'] ?? '');
$is_server = in_array($current_server_addr, $server_ips, true)
    || strpos($current_host, '192.168.111.240') === 0;

if ($is_server) {
    $app_host = 'localhost';
    $app_user = 'ใส่_User_ที่นี่';
    $app_pass = 'ใส่_Password_ที่นี่';
} else {
    $app_host = '192.168.111.240';
    $app_user = 'ใส่_User_ที่นี่';
    $app_pass = 'ใส่_Password_ที่นี่';
}

// --- HIS DB (ฐานข้อมูลโรงพยาบาล @ 192.168.111.251) ---
$his_host = '192.168.111.251';
$his_db   = 'hos'; 
$his_user = 'ใส่_User_ที่นี่';
$his_pass = 'ใส่_Password_ที่นี่';
