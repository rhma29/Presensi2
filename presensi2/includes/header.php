<!-- includes/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PresenX - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


  <style>
    /* ✅ Navbar tetap di atas saat scroll */
    .navbar-fixed {
      position: fixed;
      top: 0;
      left: 200px; /* sama dengan lebar sidebar */
      width: calc(100% - 200px);
      z-index: 1030;
      background-color: #f8f9fa;
      border-bottom: 1px solid #dee2e6;
    }

    /* ✅ Tambahkan jarak di konten utama supaya tidak tertutup navbar */
    main {
      margin-top: 50px; /* tinggi navbar */
    }
  </style>
</head>
<body>

  <!-- ✅ Navbar sticky -->
  <nav class="navbar navbar-light shadow-sm px-4 navbar-fixed">
    <span class="navbar-brand mb-0 h1">Admin</span>
    <div class="d-flex align-items-center">
      <span class="me-3">Halo, <strong><?= $_SESSION['admin_username'] ?? 'Admin'; ?></strong></span>
      <a href="../public/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
    </div>
  </nav>
