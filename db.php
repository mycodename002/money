<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
try {
    $conn = new PDO("mysql:host=$servername;dbname=moneydb", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// ปิดการเชื่อมต่อ
// $conn = null;
?>
