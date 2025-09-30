<?php
function logAktivitas($conn, $admin_id, $admin_username, $aksi, $deskripsi) {
    $stmt = $conn->prepare("
        INSERT INTO log_aktivitas (admin_id, admin_username, aksi, deskripsi) 
        VALUES (:admin_id, :admin_username, :aksi, :deskripsi)
    ");
    $stmt->execute([
        ':admin_id' => $admin_id,
        ':admin_username' => $admin_username,
        ':aksi' => $aksi,
        ':deskripsi' => $deskripsi
    ]);
}
