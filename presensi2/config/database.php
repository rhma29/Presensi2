<?php
// config/database.php
// 🧭 Sesuaikan BASE_URL dengan path folder project kamu
define('BASE_URL', '/presensi2/admin/');

$host = "localhost";
$dbname = "presensi_2";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}


