<?php
require 'd:/xampp/htdocs/pharmalink/api/db_connect.php';
$sql = file_get_contents('d:/xampp/htdocs/pharmalink/database/schema.sql');
$app_pdo->exec($sql);
echo "Tables created successfully\n";
