<?php
require 'd:/xampp/htdocs/pharmalink/api/db_connect.php';

// Test 1: HN search
$search = '0000001';
$sql = "SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptdob AS birthday, ptsex AS sex FROM pt.pt WHERE hn = :search LIMIT 10";
$stmt = $his_pdo->prepare($sql);
$stmt->execute([':search' => $search]);
echo "HN Search:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Test 2: CID search without hyphens
$search = '1471090004821';
$sql = "SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptdob AS birthday, ptsex AS sex FROM pt.pt WHERE REPLACE(cardid, '-', '') = :search LIMIT 10";
$stmt = $his_pdo->prepare($sql);
$stmt->execute([':search' => $search]);
echo "\nCID Search:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Test 3: Name search (using TIS-620 converted string 'วันทา')
$search = 'วันทา';
$search_tis = iconv("UTF-8", "TIS-620//IGNORE", $search);
$search_like = "%{$search_tis}%";
$sql = "SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptdob AS birthday, ptsex AS sex FROM pt.pt WHERE ptfname LIKE :search OR ptlname LIKE :search LIMIT 20";
$stmt = $his_pdo->prepare($sql);
$stmt->execute([':search' => $search_like]);
echo "\nName Search ($search):\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
