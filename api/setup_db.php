<?php
// api/setup_db.php
require_once 'db_connect.php';

header('Content-Type: text/html; charset=utf-8');

try {
    // อ่านไฟล์ SQL ที่เราสร้างไว้
    $sqlFile = __DIR__ . '/../database/schema.sql';
    
    if (!file_exists($sqlFile)) {
        die("Error: ไม่พบไฟล์ schema.sql ที่ path: $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    if (empty(trim($sql))) {
        die("Error: ไฟล์ schema.sql ว่างเปล่า");
    }

    // รันคำสั่ง SQL ผ่าน $app_pdo (ถ้าคุณรันบนเครื่อง XAMPP มันจะใช้ tomwebdbnavicat วิ่งไปหา 192.168.111.240 อัตโนมัติ)
    // การใช้ exec() สามารถรันคำสั่ง SQL หลายๆ คำสั่งติดกันได้
    $app_pdo->exec($sql);

    echo "<div style='color: green; font-family: sans-serif; padding: 20px;'>";
    echo "<h2>✅ สร้างตารางฐานข้อมูลสำเร็จ!</h2>";
    echo "<p>เชื่อมต่อผ่าน: <strong>$app_host</strong></p>";
    echo "<p>ตาราง <code>drugs</code>, <code>transactions</code>, และ <code>transaction_logs</code> ถูกสร้างลงในฐานข้อมูล <code>$app_db</code> เรียบร้อยแล้วครับ</p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='color: red; font-family: sans-serif; padding: 20px;'>";
    echo "<h2>❌ เกิดข้อผิดพลาดในการสร้างตาราง</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
