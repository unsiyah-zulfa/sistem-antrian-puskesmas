<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$nama_poli = $_POST['nama_poli'] ?? '';
$dokter = $_POST['dokter'] ?? '';
$jam_buka = $_POST['jam_buka'] ?? '08:00';
$jam_tutup = $_POST['jam_tutup'] ?? '12:00';
$kuota = (int)($_POST['kuota'] ?? 50);

if (empty($nama_poli) || empty($dokter)) {
    $_SESSION['flash_error'] = 'Nama poli dan dokter wajib diisi.';
    header('Location: layanan.php');
    exit;
}

$stmt = $conn->prepare("INSERT INTO poli (nama_poli, dokter, jam_buka, jam_tutup, kuota) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssssi', $nama_poli, $dokter, $jam_buka, $jam_tutup, $kuota);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'Poli berhasil ditambahkan.';
} else {
    $_SESSION['flash_error'] = 'Gagal menambahkan poli: ' . $stmt->error;
}

header('Location: layanan.php');
exit;
?>