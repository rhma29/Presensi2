<?php
// includes/sidebar.php
// Pastikan BASE_URL sudah didefinisikan di config/database.php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/presensi2/admin/');
}
?>

<div class="sidebar bg-primary text-white position-fixed d-flex flex-column" style="width: 200px; height: 100vh; top: 0; left: 0;">
  
  <!-- 🪪 Logo Aplikasi -->
  <div class="p-2 text-center">
    <a href="<?= BASE_URL ?>dashboard.php">
      <img src="<?= BASE_URL ?>../assets/img/logo.svg" alt="PresenX Logo" 
           style="width: 150px; height: auto; object-fit: contain; background: transparent;">
    </a>
  </div>

  <!-- 📁 Menu Navigasi -->
  <ul class="nav flex-column px-2 mt-2">

    <!-- 📊 Dashboard -->
    <li class="nav-item mb-2">
      <a href="<?= BASE_URL ?>dashboard.php" class="nav-link text-white d-flex align-items-center gap-3">
        <i class="bi bi-graph-up fs-5"></i> Dashboard
      </a>
    </li>

    <!-- 🎓 Kelola Siswa -->
    <li class="nav-item mb-2">
      <a href="<?= BASE_URL ?>kelola_siswa.php" class="nav-link text-white d-flex align-items-center gap-3">
        <i class="bi bi-mortarboard-fill fs-5"></i> Kelola Siswa
      </a>
    </li>

    <!-- 👨‍🏫 Kelola Guru -->
    <li class="nav-item mb-2">
      <a href="<?= BASE_URL ?>kelola_guru.php" class="nav-link text-white d-flex align-items-center gap-3">
        <i class="bi bi-person-fill fs-5"></i> Kelola Guru
      </a>
    </li>

    <!-- 🛠️ Kelola Admin -->
    <li class="nav-item mb-2">
      <a href="<?= BASE_URL ?>kelola_admin.php" class="nav-link text-white d-flex align-items-center gap-3">
        <i class="bi bi-person-fill-gear fs-5"></i> Kelola Admin
      </a>
    </li>

    <!-- 📋 Data Presensi -->
    <li class="nav-item">
      <a class="nav-link text-white d-flex align-items-center gap-3" 
         data-bs-toggle="collapse" 
         href="#presensiMenu" 
         role="button" 
         aria-expanded="false" 
         aria-controls="presensiMenu">
        <i class="bi bi-clipboard2-check-fill fs-5"></i> Data Presensi
      </a>
      <div class="collapse ms-2 mt-2" id="presensiMenu">
        <ul class="nav flex-column">
          <li class="nav-item mb-2">
            <a href="<?= BASE_URL ?>data_presensi/presensi_masuk.php" class="nav-link text-white d-flex align-items-center gap-2">
              <i class="bi bi-box-arrow-in-right fs-5"></i> Presensi Masuk
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= BASE_URL ?>data_presensi/presensi_pulang.php" class="nav-link text-white d-flex align-items-center gap-2">
              <i class="bi bi-box-arrow-left fs-5"></i> Presensi Pulang
            </a>
          </li>
        </ul>
      </div>
    </li>

    <!-- 📄 Log Aktivitas -->
    <li class="nav-item mt-2">
      <a href="<?= BASE_URL ?>log_aktivitas.php" class="nav-link text-white d-flex align-items-center gap-3">
        <i class="bi bi-activity fs-5"></i> Log Aktivitas
      </a>
    </li>
  </ul>
</div>

<!-- ✅ Tambahkan ini di akhir body (jika belum ada) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
