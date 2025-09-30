<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}
require_once __DIR__ . '/../config/database.php';

// 📊 Statistik total
$total_siswa = $conn->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$total_guru = $conn->query("SELECT COUNT(*) FROM guru")->fetchColumn();

// 📊 Total presensi hari ini
$total_presensi = $conn->query("
    SELECT (
        (SELECT COUNT(*) FROM presensi_masuk_guru WHERE tanggal = CURDATE()) +
        (SELECT COUNT(*) FROM presensi_masuk_siswa WHERE tanggal = CURDATE()) +
        (SELECT COUNT(*) FROM presensi_pulang_guru WHERE tanggal = CURDATE()) +
        (SELECT COUNT(*) FROM presensi_pulang_siswa WHERE tanggal = CURDATE())
    ) AS total
")->fetchColumn();

// 📈 Ambil jumlah presensi 7 hari terakhir
$chartData = $conn->query("
    SELECT DATE(tanggal) AS tgl, COUNT(*) AS jumlah FROM (
        SELECT tanggal FROM presensi_masuk_guru
        UNION ALL
        SELECT tanggal FROM presensi_masuk_siswa
        UNION ALL
        SELECT tanggal FROM presensi_pulang_guru
        UNION ALL
        SELECT tanggal FROM presensi_pulang_siswa
    ) AS all_presensi
    WHERE tanggal >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(tanggal)
    ORDER BY tgl
")->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];
$period = new DatePeriod(new DateTime('-6 days'), new DateInterval('P1D'), 7);
foreach ($period as $day) {
    $date = $day->format('Y-m-d');
    $labels[] = $day->format('d M');
    $values[] = 0;
    foreach ($chartData as $row) {
        if ($row['tgl'] == $date) {
            $values[count($values) - 1] = $row['jumlah'];
        }
    }
}

// ✅ Ambil 5 presensi guru terbaru
$presensi_guru = $conn->query("
    SELECT nama_guru, tanggal, waktu, status, 'Masuk' AS tipe
    FROM presensi_masuk_guru pm
    JOIN guru g ON pm.id_guru = g.id_guru
    UNION ALL
    SELECT nama_guru, tanggal, waktu, status, 'Pulang' AS tipe
    FROM presensi_pulang_guru pp
    JOIN guru g ON pp.id_guru = g.id_guru
    ORDER BY tanggal DESC, waktu DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Ambil 5 presensi siswa terbaru
$presensi_siswa = $conn->query("
    SELECT nama_siswa, tanggal, waktu, status, 'Masuk' AS tipe
    FROM presensi_masuk_siswa pm
    JOIN siswa s ON pm.id_siswa = s.id_siswa
    UNION ALL
    SELECT nama_siswa, tanggal, waktu, status, 'Pulang' AS tipe
    FROM presensi_pulang_siswa pp
    JOIN siswa s ON pp.id_siswa = s.id_siswa
    ORDER BY tanggal DESC, waktu DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="p-4" style="margin-left: 200px;">
  <h2 class="mb-4">Dashboard</h2>

  <!-- 📊 Statistik Ringkas -->
  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
        <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
             style="width: 50px; height: 50px; background-color: #e6f4ff; color: #0d6efd; font-size: 24px;">
          <i class="bi bi-people"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-semibold"><?= $total_siswa ?></h3>
          <p class="text-muted mb-0">Siswa</p>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
        <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
             style="width: 50px; height: 50px; background-color: #e6f4ff; color: #0d6efd; font-size: 24px;">
          <i class="bi bi-person"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-semibold"><?= $total_guru ?></h3>
          <p class="text-muted mb-0">Guru</p>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
        <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
             style="width: 50px; height: 50px; background-color: #e6f4ff; color: #0d6efd; font-size: 24px;">
          <i class="bi bi-clipboard2-check"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-semibold"><?= $total_presensi ?></h3>
          <p class="text-muted mb-0">Presensi Hari Ini</p>
        </div>
      </div>
    </div>
  </div>

  <!-- 📈 Grafik Presensi 7 Hari Terakhir -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">📊 Ringkasan Presensi 7 Hari Terakhir</h5>
    </div>
    <div class="card-body">
      <canvas id="presensiChart" height="70"></canvas>
    </div>
  </div>

  <!-- ✅ TABEL PRESENSI TERBARU -->
  <div class="row">
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><h5>Presensi Guru Terbaru</h5></div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0 text-center">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Tipe</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($presensi_guru) > 0): ?>
                <?php foreach ($presensi_guru as $pg): ?>
                <tr>
                  <td><?= htmlspecialchars($pg['nama_guru']) ?></td>
                  <td><?= htmlspecialchars($pg['tanggal']) ?></td>
                  <td><?= htmlspecialchars($pg['waktu']) ?></td>
                  <td><span class="badge <?= $pg['status'] === 'Valid' ? 'bg-success' : 'bg-danger' ?>"><?= htmlspecialchars($pg['status']) ?></span></td>
                  <td><?= htmlspecialchars($pg['tipe']) ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">Belum ada data presensi guru.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6 mb-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><h5>Presensi Siswa Terbaru</h5></div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0 text-center">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Ket</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($presensi_siswa) > 0): ?>
                <?php foreach ($presensi_siswa as $ps): ?>
                <tr>
                  <td><?= htmlspecialchars($ps['nama_siswa']) ?></td>
                  <td><?= htmlspecialchars($ps['tanggal']) ?></td>
                  <td><?= htmlspecialchars($ps['waktu']) ?></td>
                  <td><span class="badge <?= $ps['status'] === 'Valid' ? 'bg-success' : 'bg-danger' ?>"><?= htmlspecialchars($ps['status']) ?></span></td>
                  <td><?= htmlspecialchars($ps['tipe']) ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">Belum ada data presensi siswa.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('presensiChart');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Jumlah Presensi',
      data: <?= json_encode($values) ?>,
      borderColor: '#0d6efd',
      backgroundColor: 'rgba(13,110,253,0.2)',
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, precision: 0 } }
  }
});
</script>

</body>
</html>
