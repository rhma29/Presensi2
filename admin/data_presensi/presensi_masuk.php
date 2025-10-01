<?php
// admin/data_presensi/presensi_masuk.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// helper untuk badge status (trim + case-insensitive)
function renderStatusBadge($status) {
    $s = trim((string)$status);
    $lower = strtolower($s);

    if ($lower === 'hadir') {
        return '<span class="badge" style="background-color:#E3F7FC; color:#005089; border:1px solid #005089;">Hadir</span>';
    } elseif ($lower === 'izin') {
        return '<span class="badge" style="background-color:#EDFDD6; color:#33A11A; border:1px solid #33A11A;">Izin</span>';
    } elseif ($lower === 'sakit') {
        return '<span class="badge" style="background-color:#FFF7CD; color:#FFAF1C; border:1px solid #FFAF1C;">Sakit</span>';
    } elseif ($lower === 'alpha' || $s === '') {
        return '<span class="badge bg-danger">Alpha</span>';
    } else {
        // fallback untuk nilai tak terduga (tampilkan apa adanya)
        return '<span class="badge bg-secondary">' . htmlspecialchars($s) . '</span>';
    }
}

// =================== PRESENSI MASUK GURU ===================
$stmtGuru = $conn->prepare("
    SELECT g.nama_guru, p.tanggal, p.waktu, p.status
    FROM presensi_masuk_guru p
    JOIN guru g ON p.id_guru = g.id_guru
    WHERE DATE(p.tanggal) = CURDATE()
    ORDER BY p.waktu DESC
");
$stmtGuru->execute();
$presensiGuru = $stmtGuru->fetchAll(PDO::FETCH_ASSOC);

// =================== PRESENSI MASUK SISWA ===================
$stmtSiswa = $conn->prepare("
    SELECT s.nama_siswa, s.kelas, p.tanggal, p.waktu, p.status, p.keterangan
    FROM presensi_masuk_siswa p
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
  <h2 class="mb-4">📥 Presensi Masuk — Hari Ini (<?= date('d M Y') ?>)</h2>

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="presensiTab" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" id="guru-tab" data-bs-toggle="tab" data-bs-target="#guru" type="button">Presensi Guru</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" id="siswa-tab" data-bs-toggle="tab" data-bs-target="#siswa" type="button">Presensi Siswa</button>
    </li>
  </ul>

  <div class="tab-content mt-3" id="presensiTabContent">

    <!-- Guru -->
    <div class="tab-pane fade show active" id="guru">
      <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
          <strong>Presensi Masuk Guru Hari Ini</strong>
          <a href="export_presensi_masuk.php?jenis=guru" class="btn btn-outline-light btn-sm">📥 Ekspor ke CSV</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover text-center mb-0 align-middle">
              <thead class="table-primary">
                <tr><th>No</th><th>Nama Guru</th><th>Tanggal</th><th>Waktu</th><th>Status</th></tr>
              </thead>
              <tbody>
                <?php if (!empty($presensiGuru)): ?>
                  <?php foreach ($presensiGuru as $i => $row): ?>
                    <tr>
                      <td><?= $i + 1 ?></td>
                      <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                      <td><?= htmlspecialchars($row['tanggal']) ?></td>
                      <td><?= htmlspecialchars($row['waktu']) ?></td>
                      <td><?= renderStatusBadge($row['status']) ?></td>
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

    <!-- Siswa -->
    <div class="tab-pane fade" id="siswa">
      <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
          <strong>Presensi Masuk Siswa Hari Ini</strong>
          <a href="export_presensi_masuk.php?jenis=siswa" class="btn btn-outline-light btn-sm">📥 Ekspor ke CSV</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover text-center mb-0 align-middle">
              <thead class="table-success">
                <tr>
                  <th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Tanggal</th><th>Waktu</th><th>Status</th><th>Keterangan</th>
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
                      <td><?= renderStatusBadge($row['status']) ?></td>
                      <td>
                        <?php if (!empty($row['keterangan']) && $row['keterangan'] !== '-'): ?>
                          <a href="../../uploads/<?= htmlspecialchars($row['keterangan']) ?>" target="_blank">Lihat Bukti</a>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="text-muted">Belum ada presensi siswa hari ini.</td></tr>
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
