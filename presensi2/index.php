<?php
session_start();

// Jika login sebagai ADMIN
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}

// Jika login sebagai SISWA
if (isset($_SESSION['siswa_id'])) {
    header("Location: siswa/dashboard.php");
    exit;
}

// Jika login sebagai GURU
if (isset($_SESSION['guru_id'])) {
    header("Location: guru/dashboard.php");
    exit;
}

// Jika belum login → arahkan ke halaman login
header("Location: public/login.php");
exit;
