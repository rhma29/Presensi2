<?php
// admin/data_presensi/presensi_pulang.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// =================== PRESENSI PULANG GURU ===================
$stmtGuru = $conn->prepare("
    SELECT g.nama_guru, p.tanggal, p.waktu, p.status
    FROM presensi_pulang_guru p
    JOIN guru g ON p.id_guru = g.id_guru
    WHERE DATE(p.tanggal) = CURDATE()
    ORDER BY p.waktu DESC
");
$stmtGuru->execute();
$presensiGuru = $stmtGuru->fetchAll(PDO::FETCH_ASSOC);

// =================== PRESENSI PULANG SISWA ===================
$stmtSiswa = $conn->prepare("
    SELECT s.nama_siswa, s.kelas, p.tanggal, p.waktu, p.status
    FROM presensi_pulang_siswa p
    JOIN siswa s ON p.id_siswa = s.id_siswa
    WHERE DATE(p.tanggal) = CURDATE()
    ORDER BY p.waktu DESC
");
$stmtSiswa->execute();
$presensiSiswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/sidebar.php'; ?>

<main class="p-4" style="margin-left: 200px;">
  <h2 class="mb-4">📤 Presensi Pulang — Hari Ini (<?= date('d M Y') ?>)</h2>

  <!-- 🔁 Tab Navigasi -->
  <ul class="nav nav-tabs" id="presensiTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="guru-tab" data-bs-toggle="tab" data-bs-target="#guru" type="button" role="tab">Presensi Guru</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="siswa-tab" data-bs-toggle="tab" data-bs-target="#siswa" type="button" role="tab">Presensi Siswa</button>
    </li>
  </ul>

  <div class="tab-content mt-3" id="presensiTabContent">

    <!-- 📊 Presensi Pulang Guru -->
    <div class="tab-pane fade show active" id="guru" role="tabpanel" aria-labelledby="guru-tab">
      <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
          <strong>Presensi Pulang Guru Hari Ini</strong>
          <!-- Tombol ekspor Guru -->
          <a href="export_presensi_pulang.php?jenis=guru" class="btn btn-outline-light btn-sm">
            📥 Ekspor ke Excel
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 text-center align-middle">
              <thead class="table-primary">
                <tr>
                  <th>No</th>
                  <th>Nama Guru</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($presensiGuru)): ?>
                  <?php foreach ($presensiGuru as $i => $row): ?>
                    <tr>
                      <td><?= $i + 1 ?></td>
                      <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                      <td><?= htmlspecialchars($row['tanggal']) ?></td>
                      <td><?= htmlspecialchars($row['waktu']) ?></td>
                      <td>
                        <span class="badge <?= $row['status'] === 'Valid' ? 'bg-success' : 'bg-danger' ?>">
                          <?= htmlspecialchars($row['status']) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-muted">Belum ada presensi guru hari ini.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- 📊 Presensi Pulang Siswa -->
    <div class="tab-pane fade" id="siswa" role="tabpanel" aria-labelledby="siswa-tab">
      <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
          <strong>Presensi Pulang Siswa Hari Ini</strong>
          <!-- Tombol ekspor Siswa -->
          <a href="export_presensi_pulang.php?jenis=siswa" class="btn btn-outline-light btn-sm">
            📥 Ekspor ke Excel
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 text-center align-middle">
              <thead class="table-success">
                <tr>
                  <th>No</th>
                  <th>Nama Siswa</th>
                  <th>Kelas</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($presensiSiswa)): ?>
                  <?php foreach ($presensiSiswa as $i => $row): ?>
                    <tr>
                      <td><?= $i + 1 ?></td>
                      <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                      <td><?= htmlspecialchars($row['kelas']) ?></td>
                      <td><?= htmlspecialchars($row['tanggal']) ?></td>
                      <td><?= htmlspecialchars($row['waktu']) ?></td>
                      <td>
                        <span class="badge <?= $row['status'] === 'Valid' ? 'bg-success' : 'bg-danger' ?>">
                          <?= htmlspecialchars($row['status']) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-muted">Belum ada presensi siswa hari ini.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
