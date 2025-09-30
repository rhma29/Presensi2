<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// ✅ Reset session setiap kali halaman login dibuka
session_unset();
session_destroy();
session_start();

$error = "";

// ✅ Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1️⃣ Cek login sebagai ADMIN
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $admin['password'] === $password) {
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['nama_admin'] = $admin['nama_admin'];

        header("Location: ../admin/dashboard.php");
        exit;
    }

    // 2️⃣ Cek login sebagai SISWA
    $stmt = $conn->prepare("SELECT * FROM siswa WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($siswa && $siswa['password'] === $password) {
        $_SESSION['siswa_id'] = $siswa['id_siswa'];
        $_SESSION['siswa_username'] = $siswa['username'];
        $_SESSION['nama'] = $siswa['nama_siswa'];
        $_SESSION['email'] = $siswa['email'];

        header("Location: ../siswa/dashboard.php");
        exit;
    }

    // 3️⃣ Cek login sebagai GURU
    $stmt = $conn->prepare("SELECT * FROM guru WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $guru = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($guru && $guru['password'] === $password) {
        $_SESSION['guru_id'] = $guru['id_guru'];
        $_SESSION['guru_username'] = $guru['username'];
        $_SESSION['nama'] = $guru['nama_guru'];
        $_SESSION['email'] = $guru['email'];

        header("Location: ../guru/dashboard.php");
        exit;
    }

    // ❌ Jika semua gagal
    $error = "⚠️ Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - PresenX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #007bff, #0056b3);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: Arial, sans-serif;
    }
    .login-box {
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.25);
      width: 100%;
      max-width: 420px;
      animation: fadeIn 0.4s ease;
    }
    .login-box h2 {
      margin-bottom: 1.5rem;
      text-align: center;
      color: #333;
      font-weight: bold;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0069d9;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>Login</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Login</button>
  </form>
</div>

</body>
</html>
