<?php
// admin/kelola_siswa.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}
require_once __DIR__ . "/../config/database.php";

$alert = "";

// ==================== TAMBAH DATA ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $nis = $_POST['nis'] ?? "";
    $nama_siswa = $_POST['nama_siswa'] ?? "";
    $kelas = $_POST['kelas'] ?? "";
    $email = $_POST['email'] ?? "";
    $no_hp = $_POST['no_hp'] ?? "";
    $alamat = $_POST['alamat'] ?? "";
    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    // Cek duplikat
    $cek = $conn->prepare("SELECT COUNT(*) FROM siswa WHERE nis = :nis");
    $cek->execute([':nis' => $nis]);
    if ($cek->fetchColumn() > 0) {
        $alert = "❌ Gagal: NIS sudah terdaftar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO siswa (nis, nama_siswa, kelas, email, no_hp, alamat, username, password) 
                                VALUES (:nis, :nama, :kelas, :email, :no_hp, :alamat, :username, :password)");
        $stmt->execute([
            ':nis' => $nis,
            ':nama' => $nama_siswa,
            ':kelas' => $kelas,
            ':email' => $email,
            ':no_hp' => $no_hp,
            ':alamat' => $alamat,
            ':username' => $username,
            ':password' => $password
        ]);

        // Ambil ID terakhir
        $id_siswa = $conn->lastInsertId();

        // ✅ Tambahkan log aktivitas
        $log = $conn->prepare("INSERT INTO log_aktivitas (id_admin, aksi, waktu) VALUES (:id_admin, :aksi, NOW())");
        $log->execute([
            ':id_admin' => $_SESSION['admin_id'],
            ':aksi' => "Menambahkan data siswa: $nama_siswa (ID: $id_siswa)"
        ]);

        $alert = "✅ Tambah data siswa berhasil.";
    }
}

// ==================== EDIT DATA ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id_siswa = intval($_POST['id_siswa']);
    $nis = $_POST['nis'] ?? "";
    $nama_siswa = $_POST['nama_siswa'] ?? "";
    $kelas = $_POST['kelas'] ?? "";
    $email = $_POST['email'] ?? "";
    $no_hp = $_POST['no_hp'] ?? "";
    $alamat = $_POST['alamat'] ?? "";
    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    $stmt = $conn->prepare("UPDATE siswa SET nis=:nis, nama_siswa=:nama, kelas=:kelas, email=:email, no_hp=:no_hp, alamat=:alamat, username=:username, password=:password 
                            WHERE id_siswa=:id");
    $stmt->execute([
        ':nis'=>$nis, ':nama'=>$nama_siswa, ':kelas'=>$kelas, ':email'=>$email,
        ':no_hp'=>$no_hp, ':alamat'=>$alamat, ':username'=>$username, ':password'=>$password, ':id'=>$id_siswa
    ]);

    // ✅ Tambahkan log aktivitas
    $log = $conn->prepare("INSERT INTO log_aktivitas (id_admin, aksi, waktu) VALUES (:id_admin, :aksi, NOW())");
    $log->execute([
        ':id_admin' => $_SESSION['admin_id'],
        ':aksi' => "Mengedit data siswa: $nama_siswa (ID: $id_siswa)"
    ]);

    $alert = "✅ Update data siswa berhasil.";
}

// ==================== DELETE DATA ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id_siswa = intval($_POST['id_siswa']);

    // Ambil nama sebelum dihapus
    $nama_siswa = $conn->query("SELECT nama_siswa FROM siswa WHERE id_siswa = $id_siswa")->fetchColumn();

    $stmt = $conn->prepare("DELETE FROM siswa WHERE id_siswa = :id");
    $stmt->execute([':id'=>$id_siswa]);

    // ✅ Tambahkan log aktivitas
    $log = $conn->prepare("INSERT INTO log_aktivitas (id_admin, aksi, waktu) VALUES (:id_admin, :aksi, NOW())");
    $log->execute([
        ':id_admin' => $_SESSION['admin_id'],
        ':aksi' => "Menghapus data siswa: $nama_siswa (ID: $id_siswa)"
    ]);

    $alert = "✅ Hapus data siswa berhasil.";
}

// ==================== PENCARIAN ====================
$q = $_GET['q'] ?? '';
$params = [];
$sql = "SELECT * FROM siswa";
if (!empty($q)) {
    $sql .= " WHERE nis LIKE :q OR nama_siswa LIKE :q OR kelas LIKE :q OR username LIKE :q";
    $params[':q'] = "%$q%";
}
$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . "/../includes/header.php"; ?>
<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main class="p-4" style="margin-left: 200px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Kelola Siswa</h3>
    <div class="d-flex">
      <form class="d-flex" method="get" action="">
        <input class="form-control form-control-sm" name="q" placeholder="Cari NIS/Nama/Kelas/Username" value="<?= htmlspecialchars($q) ?>" style="width:220px;">
        <button class="btn btn-sm btn-secondary ms-1" type="submit">Cari</button>
        <a href="kelola_siswa.php" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
      </form>
      <button class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#modalAddSiswa">+ Tambah Data</button>
    </div>
  </div>

  <?php if($alert): ?>
    <div class="alert alert-success"><?= htmlspecialchars($alert) ?></div>
  <?php endif; ?>

  <div class="card card-body mb-4 shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Email</th>
            <th>No HP</th>
            <th>Username</th>
            <th>Password</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($siswa_list as $i => $s): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($s['nis']) ?></td>
            <td><?= htmlspecialchars($s['nama_siswa']) ?></td>
            <td><?= htmlspecialchars($s['kelas']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['no_hp']) ?></td>
            <td><?= htmlspecialchars($s['username']) ?></td>
            <td><?= htmlspecialchars($s['password']) ?></td>
            <td>
              <button class="btn btn-sm btn-warning btn-edit" 
                data-id="<?= $s['id_siswa'] ?>"
                data-nis="<?= htmlspecialchars($s['nis'], ENT_QUOTES) ?>"
                data-nama="<?= htmlspecialchars($s['nama_siswa'], ENT_QUOTES) ?>"
                data-kelas="<?= htmlspecialchars($s['kelas'], ENT_QUOTES) ?>"
                data-email="<?= htmlspecialchars($s['email'], ENT_QUOTES) ?>"
                data-nohp="<?= htmlspecialchars($s['no_hp'], ENT_QUOTES) ?>"
                data-alamat="<?= htmlspecialchars($s['alamat'], ENT_QUOTES) ?>"
                data-username="<?= htmlspecialchars($s['username'], ENT_QUOTES) ?>"
                data-password="<?= htmlspecialchars($s['password'], ENT_QUOTES) ?>"
              >Edit</button>
              <form method="post" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id_siswa" value="<?= $s['id_siswa'] ?>">
                <button class="btn btn-sm btn-danger">Hapus</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(count($siswa_list)===0): ?>
            <tr><td colspan="9" class="text-center text-muted">Belum ada data siswa.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Modal Add -->
<div class="modal fade" id="modalAddSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <input type="hidden" name="action" value="add">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" name="nis" placeholder="NIS" required></div>
          <div class="col-md-6"><input class="form-control" name="nama_siswa" placeholder="Nama Siswa" required></div>
          <div class="col-md-4"><input class="form-control" name="kelas" placeholder="Kelas"></div>
          <div class="col-md-4"><input class="form-control" name="email" placeholder="Email"></div>
          <div class="col-md-4"><input class="form-control" name="no_hp" placeholder="No HP"></div>
          <div class="col-12"><textarea class="form-control" name="alamat" placeholder="Alamat"></textarea></div>
          <div class="col-md-6"><input class="form-control" name="username" placeholder="Username" required></div>
          <div class="col-md-6"><input class="form-control" name="password" placeholder="Password" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content" id="formEditSiswa">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id_siswa" id="edit_id_siswa">
      <div class="modal-header">
        <h5 class="modal-title">Edit Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6"><input class="form-control" id="edit_nis" name="nis" placeholder="NIS" required></div>
          <div class="col-md-6"><input class="form-control" id="edit_nama_siswa" name="nama_siswa" placeholder="Nama Siswa" required></div>
          <div class="col-md-4"><input class="form-control" id="edit_kelas" name="kelas" placeholder="Kelas"></div>
          <div class="col-md-4"><input class="form-control" id="edit_email" name="email" placeholder="Email"></div>
          <div class="col-md-4"><input class="form-control" id="edit_no_hp" name="no_hp" placeholder="No HP"></div>
          <div class="col-12"><textarea class="form-control" id="edit_alamat" name="alamat" placeholder="Alamat"></textarea></div>
          <div class="col-md-6"><input class="form-control" id="edit_username" name="username" placeholder="Username" required></div>
          <div class="col-md-6"><input class="form-control" id="edit_password" name="password" placeholder="Password" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const el = e.currentTarget;
    document.getElementById('edit_id_siswa').value = el.dataset.id;
    document.getElementById('edit_nis').value = el.dataset.nis;
    document.getElementById('edit_nama_siswa').value = el.dataset.nama;
    document.getElementById('edit_kelas').value = el.dataset.kelas;
    document.getElementById('edit_email').value = el.dataset.email;
    document.getElementById('edit_no_hp').value = el.dataset.nohp;
    document.getElementById('edit_alamat').value = el.dataset.alamat;
    document.getElementById('edit_username').value = el.dataset.username;
    document.getElementById('edit_password').value = el.dataset.password;
    var modal = new bootstrap.Modal(document.getElementById('modalEditSiswa'));
    modal.show();
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
