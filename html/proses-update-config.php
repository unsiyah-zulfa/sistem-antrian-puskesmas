<?php
require_once '../config.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$nama_puskesmas = $_POST['nama_puskesmas'] ?? '';
$kode_puskesmas = $_POST['kode_puskesmas'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$no_telp = $_POST['no_telp'] ?? '';
$jam_buka = $_POST['jam_buka'] ?? '07:30:00';
$jam_tutup = $_POST['jam_tutup'] ?? '12:00:00';
$kepala = $_POST['kepala'] ?? '';

$cek = $conn->query("SELECT id FROM config LIMIT 1");
if ($cek->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE config SET nama_puskesmas=?, kode_puskesmas=?, alamat=?, no_telp=?, jam_buka=?, jam_tutup=?, kepala=?");
    $stmt->bind_param('sssssss', $nama_puskesmas, $kode_puskesmas, $alamat, $no_telp, $jam_buka, $jam_tutup, $kepala);
} else {
    $stmt = $conn->prepare("INSERT INTO config (nama_puskesmas, kode_puskesmas, alamat, no_telp, jam_buka, jam_tutup, kepala) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssss', $nama_puskesmas, $kode_puskesmas, $alamat, $no_telp, $jam_buka, $jam_tutup, $kepala);
}

if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'Konfigurasi puskesmas berhasil disimpan.';
} else {
    $_SESSION['flash_error'] = 'Gagal menyimpan konfigurasi.';
}
header('Location: settings.php');
exit;
?>