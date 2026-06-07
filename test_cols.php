<?php
require 'd:/xampp/htdocs/pharmalink/api/db_connect.php';
$stmt = $his_pdo->query("SHOW COLUMNS FROM opd.opd_pi");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "opd.opd_pi:\n";
foreach($cols as $c) echo $c['Field']."\n";

$stmt = $his_pdo->query("SELECT * FROM opd.opd_pi LIMIT 1");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
