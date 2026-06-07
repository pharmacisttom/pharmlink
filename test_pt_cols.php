<?php
require 'd:/xampp/htdocs/pharmalink/api/db_connect.php';
$stmt = $his_pdo->query('SHOW COLUMNS FROM pt.pt');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c) {
    echo $c['Field'] . "\n";
}
