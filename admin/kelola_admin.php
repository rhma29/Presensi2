<?php
// admin/kelola_admin.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}
require_once __DIR__ . '/../config/database.php';

$alert = "";

// ✅ Tambah Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $nama_admin = $_POST['nama_admin'] ?? '';
    $email      = $_POST['email'] ?? '';
    $username   = $_POST['username'] ?? '';
    $password   = $_POST['password'] ?? '';

    $stmt = $conn->prepare("INSERT INTO admin (nama_admin, email, username, password) VALUES (:nama, :email, :username, :password)");
    $stmt->execute([
        ':nama' => $nama_admin,
        ':email' => $email,
        ':username' => $username,
        ':password' => $password
    ]);

    // ✅ Ambil ID admin baru
    $id_admin_baru = $conn->lastInsertId();

    // ✅ Tambahkan log aktivitas
    $log = $conn->prepare("INSERT INTO log_aktivitas (id_admin, aksi, waktu) VALUES (:id_admin, :aksi, NOW())");
    $log->execute([
        ':id_admin' => $_SESSION['admin_id'],
        ':aksi' => "Menambahkan data admin: $nama_admin (ID: $id_admin_baru)"
    ]);

    $alert = "✅ Admin baru berhasil ditambahkan.";
}

// ✅ Edit Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id_admin   = intval($_POST['id_admin']);
    $nama_admin = $_POST['nama_admin'] ?? '';
    $email      = $_POST['email'] ?? '';
    $username   = $_POST['username'] ?? '';
    $password   = $_POST['password'] ?? '';

    $stmt = $conn->prepare("UPDATE admin SET nama_admin=:nama, email=:email, username=:username, password=:password WHERE id_admin=:id");
    $stmt->execute([
        ':nama' => $nama_admin,
        ':email' => $email,
        ':username' => $username,
        ':password' => $password,
        ':id' => $id_admin
    ]);

    // ✅ Tambahkan log aktivitas
    $log = $conn->prepare("INSERT INTO log_aktivitas (id_admin, aksi, waktu) VALUES (:id_admin, :aksi, NOW())");
    $log->execute([
        ':id_admin' => $_SESSION['admin_id'],
        ':aksi' => "Mengedit data admin: $nama_admin (ID: $id_admin)"
    ]);

    $alert = "✅ Data admin berhasil diperbarui.";
}

// ✅ Hapus Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id_admin = intval($_POST['id_admin']);

    // ✅ Ambil nama admin sebelum dihapus
    $nama_admin = $conn->prepare("SELECT nama_admin FROM admin WHERE id_admin = :id");
    $nama_admin->execute([':id' => $id_admin]);
    $nama_admin = $nama_admin->fetchColumn() ?: 'Tidak Diketahui';

    $stmt = $conn->prepare("DELETE FROM admin WHERE id_admin=:id");
    $stmt->execute([':id' => $id_admin]);

    // ✅ Tambahkan log aktivitas
    $log = $conn->prepare("INSERT INTO log_aktivitas (id_admin, aksi, waktu) VALUES (:id_admin, :aksi, NOW())");
    $log->execute([
        ':id_admin' => $_SESSION['admin_id'],
        ':aksi' => "Menghapus data admin: $nama_admin (ID: $id_admin)"
    ]);

    $alert = "🗑️ Admin berhasil dihapus.";
}

// ✅ Pencarian
$q = $_GET['q'] ?? '';
$params = [];
$sql = "SELECT * FROM admin";
if (!empty($q)) {
    $sql .= " WHERE nama_admin LIKE :q OR username LIKE :q OR email LIKE :q";
    $params = [':q' => "%$q%"];
}
$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$admin_list = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="p-4" style="margin-left: 200px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Kelola Admin</h3>
    <div class="d-flex">

      <!-- 🔎 Form Pencarian -->
      <form class="d-flex" method="get" action="kelola_admin.php">
        <input class="form-control form-control-sm" name="q" placeholder="Cari Nama/Email/Username"
          value="<?= htmlspecialchars($q) ?>" style="width:220px;">
        <button class="btn btn-sm btn-secondary ms-1" type="submit">Cari</button>
        <a href="kelola_admin.php" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
      </form>

      <!-- ➕ Tombol Tambah -->
      <button class="btn btn-primary btn-sm ms-2" type="button" data-bs-toggle="modal" data-bs-target="#modalAddAdmin">
        + Tambah Data
      </button>
    </div>
  </div>

  <?php if ($alert): ?>
    <div class="alert alert-success"><?= htmlspecialchars($alert) ?></div>
  <?php endif; ?>

  <!-- 📊 Tabel Admin -->
  <div class="card card-body mb-4 shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Username</th>
            <th>Password</th>
            <th>Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($admin_list as $i => $a): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($a['nama_admin']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= htmlspecialchars($a['username']) ?></td>
            <td><?= htmlspecialchars($a['password']) ?></td>
            <td><?= htmlspecialchars($a['created_at']) ?></td>
            <td>
              <button class="btn btn-sm btn-warning btn-edit"
                data-id="<?= $a['id_admin'] ?>"
                data-nama="<?= htmlspecialchars($a['nama_admin'], ENT_QUOTES) ?>"
                data-email="<?= htmlspecialchars($a['email'], ENT_QUOTES) ?>"
                data-username="<?= htmlspecialchars($a['username'], ENT_QUOTES) ?>"
                data-password="<?= htmlspecialchars($a['password'], ENT_QUOTES) ?>"
              >Edit</button>
              <form method="post" class="d-inline" onsubmit="return confirm('Hapus admin ini?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id_admin" value="<?= $a['id_admin'] ?>">
                <button class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (count($admin_list) === 0): ?>
            <tr><td colspan="7" class="text-center text-muted">Belum ada data admin.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- 🟢 Modal Tambah Admin -->
<div class="modal fade" id="modalAddAdmin" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <input type="hidden" name="action" value="add">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" name="nama_admin" placeholder="Nama Admin" required></div>
          <div class="col-md-6"><input class="form-control" name="email" placeholder="Email"></div>
          <div class="col-md-6"><input class="form-control" name="username" placeholder="Username" required></div>
          <div class="col-md-6"><input class="form-control" name="password" placeholder="Password" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- 🟡 Modal Edit Admin -->
<div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content" id="formEditAdmin">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id_admin" id="edit_id_admin">
      <div class="modal-header">
        <h5 class="modal-title">Edit Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" id="edit_nama_admin" name="nama_admin" required></div>
          <div class="col-md-6"><input class="form-control" id="edit_email" name="email"></div>
          <div class="col-md-6"><input class="form-control" id="edit_username" name="username" required></div>
          <div class="col-md-6"><input class="form-control" id="edit_password" name="password" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- 🧠 Script Modal Edit -->
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const el = e.currentTarget;
    document.getElementById('edit_id_admin').value = el.dataset.id;
    document.getElementById('edit_nama_admin').value = el.dataset.nama;
    document.getElementById('edit_email').value = el.dataset.email;
    document.getElementById('edit_username').value = el.dataset.username;
    document.getElementById('edit_password').value = el.dataset.password;

    var modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
    modal.show();
  });
});
</script>

<!-- ✅ Bootstrap JS agar modal berfungsi -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
