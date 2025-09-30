<?php
// guru/dashboard.php
session_start();
if (!isset($_SESSION['guru_id'])) {
    header("Location: ../public/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PresenX - Dashboard Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ✅ Badge custom */
    .custom-badge {
      font-size: 1.2rem;
      font-weight: 600;
      letter-spacing: 0.4px;
      color: #ffffff;
      background: linear-gradient(90deg, #0d6efd, #0b5ed7);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      border: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: inline-block;
    }
    .custom-badge:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    /* ✅ Hero section */
    section.hero {
      min-height: 85vh;
      display: flex;
      align-items: center;
    }
  </style>
</head>
<body>

<!-- 🔝 Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark px-4" style="background-color: #0d4a7c;">
  <a class="navbar-brand text-white d-flex align-items-center" href="#">
    <img src="../assets/img/logo.svg" alt="logo" width="28" class="me-2">
    PresenX
  </a>
  <div class="ms-auto d-flex align-items-center text-white">
    <div class="me-3 text-end">
      <strong><?= $_SESSION['nama'] ?? 'Guru' ?></strong><br>
      <small><?= $_SESSION['email'] ?? '' ?></small>
    </div>
    <a href="../public/logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
  </div>
</nav>

<!-- 🏠 Hero Section -->
<section class="container hero py-5">
  <div class="row align-items-center">
    <!-- ✨ Konten Teks -->
    <div class="col-md-6">
      <div class="custom-badge mb-3 px-4 py-2 rounded-pill">
        📚 Presensi Digital Guru
      </div>
      <h1 class="fw-bold mb-3">Disiplin Mengajar,<br>Profesionalitas Terjaga</h1>
      <p class="text-muted mb-4">
        Selamat datang di <strong>PresenX</strong>, sistem presensi online yang dirancang khusus untuk para pendidik.
        Dengan kemudahan pencatatan kehadiran digital, Anda dapat lebih fokus pada pengajaran tanpa khawatir masalah administrasi.
        Mulailah hari mengajar Anda dengan presensi yang cepat, tepat, dan efisien.
      </p>
      <div>
        <a href="presensi.php" class="btn btn-primary px-4 py-2 me-2">
          <i class="bi bi-calendar-check me-2"></i>Presensi Hari Ini
        </a>
        <a href="riwayat.php" class="btn btn-outline-primary px-4 py-2">
          <i class="bi bi-clock-history me-2"></i>Riwayat Presensi
        </a>
      </div>
    </div>

    <!-- ✨ Gambar Ilustrasi -->
    <div class="col-md-6 text-center mt-4 mt-md-0">
      <img src="../assets/img/teacher_illustration.png" alt="Presensi Guru" class="img-fluid" style="max-width: 420px;">
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
