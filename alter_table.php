<?php
require 'd:/xampp/htdocs/pharmalink/api/db_connect.php';
try {
    $sql = "ALTER TABLE pharmalink_inquiries ADD COLUMN duration_seconds INT DEFAULT 0 AFTER recorded_by";
    $app_pdo->exec($sql);
    echo "Column added successfully.";
} catch(Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "ERROR: " . $e->getMessage();
    }
}
