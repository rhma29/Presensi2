<?php
// admin/log_aktivitas.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Ambil semua log aktivitas terbaru
$stmt = $conn->query("
    SELECT la.id_log, a.username AS nama_admin, la.aksi, la.waktu 
    FROM log_aktivitas la
    JOIN admin a ON la.id_admin = a.id_admin
    ORDER BY la.waktu DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="p-4" style="margin-left: 200px;">
  <h2 class="mb-4">📜 Log Aktivitas Admin</h2>

  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <span><i class="bi bi-activity"></i> Riwayat Aktivitas Admin</span>
      <a href="log_aktivitas.php" class="btn btn-sm btn-light">🔄 Refresh</a>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
          <thead class="table-primary text-left">
            <tr>
              <th class="text-center width:60px">No</th>
              <th>Nama Admin</th>
              <th>Aksi</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($logs) > 0): ?>
              <?php foreach ($logs as $i => $log): ?>
                <tr>
                  <td class="text-center"><?= $i + 1 ?></td>
                  <td><?= htmlspecialchars($log['nama_admin']) ?></td>
                  <td><?= htmlspecialchars($log['aksi']) ?></td>
                  <td><?= date('d M Y, H:i:s', strtotime($log['waktu'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted">📭 Belum ada aktivitas yang tercatat.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
