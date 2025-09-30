<?php
session_start();
if (!isset($_SESSION['siswa_id'])) {
    header("Location: ../public/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Smartas - Halaman Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark px-4 bg-transparent";>
  <a class="navbar-brand text-dark d-flex align-items-center" href="#">
    <img src="../assets/img/logo.png" alt="logo" width="28" class="me-2">
    Smartas
  </a>
  <div class="ms-auto d-flex align-items-center text-dark">
    <div class="me-3 text-end">
      <strong><?= $_SESSION['nama'] ?? 'Siswa' ?></strong><br>
      <small><?= $_SESSION['email'] ?? '' ?></small>
    </div>
    <a href="../public/logout.php" class="btn btn-outline-secondary btn-sm ms-2">Logout</a>
  </div>
</nav>

<section class="container py-5 d-flex align-items-center" style="min-height: 85vh;">
  <div class="row align-items-center">
    <div class="col-md-6">
      <div class="badge bg-light text-primary mb-3 px-3 py-2 rounded-pill">Website Presensi Online</div>

      <h1 class="fw-bold mb-3">Presensi Tepat Waktu,<br>Prestasi Lebih Cemerlang</h1>
      <p class="text-muted mb-4">
        Kami hadir untuk mendukung kedisiplinan dan tanggung jawabmu sebagai pelajar melalui sistem presensi digital yang mudah, cepat, dan akurat.
        Mulailah langkah kecil menuju masa depan yang lebih teratur dengan mencatat kehadiranmu secara online, di mana pun dan kapan pun.
      </p>
      <div>
        <a href="presensi.php" class="btn btn-primary px-4 py-2 me-2"><i class="bi bi-calendar-check me-2"></i>Presensi</a>
        <a href="riwayat.php" class="btn btn-outline-primary px-4 py-2"><i class="bi bi-clock-history me-2"></i>Riwayat</a>
      </div>
    </div>
    <div class="col-md-6 text-center mt-4 mt-md-0">
      <img src="../assets/img/illustration.svg" alt="Presensi" class="img-fluid" style="max-width: 420px;">
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
