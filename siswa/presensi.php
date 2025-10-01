<?php
session_start();
if (!isset($_SESSION['siswa_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$id_siswa = $_SESSION['siswa_id'];
$nama     = $_SESSION['nama'] ?? 'Siswa';
$email    = $_SESSION['email'] ?? '';

$alert = "";

// ✅ Cek presensi hari ini
$stmtMasuk = $conn->prepare("SELECT * FROM presensi_masuk_siswa WHERE id_siswa = :id AND tanggal = CURDATE()");
$stmtMasuk->execute([':id' => $id_siswa]);
$dataMasuk = $stmtMasuk->fetch(PDO::FETCH_ASSOC);

$stmtPulang = $conn->prepare("SELECT * FROM presensi_pulang_siswa WHERE id_siswa = :id AND tanggal = CURDATE()");
$stmtPulang->execute([':id' => $id_siswa]);
$dataPulang = $stmtPulang->fetch(PDO::FETCH_ASSOC);

// ✅ Proses submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil tipe presensi dari POST jika ada
    $tipe = $_POST['tipe'] ?? null;
    // Ambil koordinat lokasi dari POST jika ada
    $lat = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $lng = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    // ✅ Presensi Masuk
    if ($tipe === 'Masuk' && !$dataMasuk) {
        $stmt = $conn->prepare("INSERT INTO presensi_masuk_siswa 
            (id_siswa, tanggal, waktu, latitude, longitude, status) 
            VALUES (:id, CURDATE(), NOW(), :lat, :lng, 'Hadir')");
        $stmt->execute([':id' => $id_siswa, ':lat' => $lat, ':lng' => $lng]);
        $alert = "✅ Presensi masuk berhasil.";
    }

    // ✅ Presensi Pulang
    elseif ($tipe === 'Pulang' && !$dataPulang) {
        $stmt = $conn->prepare("INSERT INTO presensi_pulang_siswa 
            (id_siswa, tanggal, waktu, latitude, longitude, status) 
            VALUES (:id, CURDATE(), NOW(), :lat, :lng, 'Hadir')");
        $stmt->execute([':id' => $id_siswa, ':lat' => $lat, ':lng' => $lng]);
        $alert = "✅ Presensi pulang berhasil.";
    }


// Izin / Sakit
if (isset($_POST['jenis_absen']) && ($_POST['jenis_absen'] === 'Izin' || $_POST['jenis_absen'] === 'Sakit')) {
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['bukti']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $targetFile)) {
            $jenis_absen = $_POST['jenis_absen']; // 'Izin' atau 'Sakit'

            $stmt = $conn->prepare("
                INSERT INTO presensi_masuk_siswa (id_siswa, tanggal, waktu, status, keterangan) 
                VALUES (:id, CURDATE(), NOW(), :status, :ket)
            ");
            $stmt->execute([
                ':id' => $id_siswa,
                ':status' => $jenis_absen,
                ':ket' => $filename // simpan nama file bukti sebagai keterangan
            ]);

            $alert = "✅ Absensi $jenis_absen berhasil dikirim.";
        } else {
            $alert = "⚠️ Gagal upload bukti foto.";
        }
    } else {
        $alert = "⚠️ Harap unggah bukti foto.";
    }
}

}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Presensi Siswa - Smartas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f5f7fb; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .card-box { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 1.5rem; margin-bottom: 1.5rem; }
    .map-box { border-radius: 12px; overflow: hidden; }
  </style>
</head>
<body>

<!-- ✅ Header -->
<nav class="navbar navbar-expand-lg navbar-dark px-4 bg-transparent">
  <a class="navbar-brand text-dark d-flex align-items-center" href="#">
    <img src="../assets/img/logo.png" alt="logo" width="28" class="me-2">
    Smartas
  </a>
  <div class="ms-auto d-flex align-items-center text-dark">
    <div class="me-3 text-end">
      <strong><?= htmlspecialchars($nama) ?></strong><br>
      <small><?= htmlspecialchars($email) ?></small>
    </div>
    <a href="../public/logout.php" class="btn btn-outline-secondary btn-sm ms-2">Logout</a>
  </div>
</nav>

<!-- ✅ Tombol Kembali -->
<div class="container mt-3">
  <a href="../siswa/dashboard.php" class="btn btn-outline-primary mb-3">&larr; Kembali</a>
</div>

<div class="container">
  <?php if ($alert): ?>
    <div class="alert <?= strpos($alert,'✅')!==false?'alert-success':'alert-warning' ?>">
      <?= htmlspecialchars($alert) ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-6">
      <!-- Presensi Masuk / Pulang -->
      <div class="card-box bg-white">
        <h5>Presensi - (otomatis hari, tanggal saat ini)</h5>
        <p class="text-muted mb-2">
          • Presensi masuk dibuka pukul 06.00 <br>
          • Presensi pulang dibuka pukul 14.10 <br>
          • Lokasi harus dalam radius 100m dari SMK Antartika Surabaya
        </p>
        <form method="post" id="formPresensi">
          <input type="hidden" name="latitude" id="latitude">
          <input type="hidden" name="longitude" id="longitude">
          <button type="submit" name="tipe" value="Masuk" id="btnMasuk" class="btn btn-primary w-100 mb-2" disabled>
            Presensi Masuk
          </button>
          <button type="submit" name="tipe" value="Pulang" id="btnPulang" class="btn btn-success w-100" disabled>
            Presensi Pulang
          </button>
        </form>
        <div id="lokasiMsg" class="text-danger mt-2"></div>
      </div>

      <!-- Absensi Izin / Sakit -->
      <div class="card-box bg-white">
        <h5>Absensi Izin/Sakit - (otomatis hari, tanggal saat ini)</h5>
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Jenis Absensi</label>
            <select class="form-select text-muted" name="jenis_absen" required>
              <option value="">Pilih jenis absensi</option>
              <option value="Izin">Izin</option>
              <option value="Sakit">Sakit</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Bukti Foto</label>
            <input type="file" class="form-control text-muted" name="bukti" accept=".jpg,.jpeg,.png" required>
            <small class="text-muted">*format file jpg/png maks. 10 mb</small>
          </div>
          <button type="submit" class="btn btn-warning w-100">Kirim Absensi</button>
        </form>
      </div>
    </div>

    <!-- Map -->
    <div class="col-md-6">
      <div class="map-box">
        <iframe src="https://www.google.com/maps?q=-7.311255,112.737098&z=17&output=embed" 
          width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
// Koordinat SMK Antartika Surabaya
const sekolahLat = -7.311255;
const sekolahLng = 112.737098;
const radius = 80000; // meter

function hitungJarak(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function cekLokasi() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            const jarak = hitungJarak(lat, lng, sekolahLat, sekolahLng);

            if (jarak <= radius) {
                document.getElementById('btnMasuk').disabled = <?= $dataMasuk ? 'true':'false' ?>;
                document.getElementById('btnPulang').disabled = <?= $dataPulang ? 'true':'false' ?>;
                document.getElementById('lokasiMsg').innerHTML = "<span class='text-success'>✅ Kamu berada di area sekolah (jarak " + jarak.toFixed(1) + " m)</span>";
            } else {
                document.getElementById('btnMasuk').disabled = true;
                document.getElementById('btnPulang').disabled = true;
                document.getElementById('lokasiMsg').innerHTML = "⚠️ Kamu berada di luar area sekolah (jarak " + jarak.toFixed(1) + " m)";
            }
        }, function() {
            document.getElementById('lokasiMsg').innerHTML = "⚠️ Tidak bisa mendeteksi lokasi.";
        });
    } else {
        document.getElementById('lokasiMsg').innerHTML = "⚠️ Browser tidak mendukung geolocation.";
    }
}

cekLokasi();
</script>

</body>
</html>
