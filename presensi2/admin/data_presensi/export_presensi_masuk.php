<?php
// admin/data_presensi/export_presensi_masuk.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$jenis = $_GET['jenis'] ?? '';

if ($jenis === 'guru') {
    $stmt = $conn->prepare("
        SELECT g.nama_guru, p.tanggal, p.waktu, p.status
        FROM presensi_masuk_guru p
        JOIN guru g ON p.id_guru = g.id_guru
        WHERE DATE(p.tanggal) = CURDATE()
        ORDER BY p.waktu DESC
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $filename = "presensi_guru_" . date('Y-m-d') . ".csv";

    $header = ['No', 'Nama Guru', 'Tanggal', 'Waktu', 'Status'];

} elseif ($jenis === 'siswa') {
    $stmt = $conn->prepare("
        SELECT s.nama_siswa, s.kelas, p.tanggal, p.waktu, p.status
        FROM presensi_masuk_siswa p
        JOIN siswa s ON p.id_siswa = s.id_siswa
        WHERE DATE(p.tanggal) = CURDATE()
        ORDER BY p.waktu DESC
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $filename = "presensi_siswa_" . date('Ymd') . ".csv";

    $header = ['No', 'Nama Siswa', 'Kelas', 'Tanggal', 'Waktu', 'Status'];

} else {
    die("Jenis presensi tidak valid.");
}

// Set header agar browser men-download file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom
fputcsv($output, $header);

// Tulis data
foreach ($data as $i => $row) {
    $no = $i + 1;
    if ($jenis === 'guru') {
        fputcsv($output, [$no, $row['nama_guru'], $row['tanggal'], $row['waktu'], $row['status']]);
    } else {
        fputcsv($output, [$no, $row['nama_siswa'], $row['kelas'], $row['tanggal'], $row['waktu'], $row['status']]);
    }
}

// Tutup output stream
fclose($output);
exit;
?>
