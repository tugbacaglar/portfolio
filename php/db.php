<?php
$host = 'sqlXXX.infinityfree.com'; // 
$dbname = 'if0_41912995_tugbacaglar';
$username = 'if0_41912995';
$password = 'MDY0f9L9JYhic';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}
?>