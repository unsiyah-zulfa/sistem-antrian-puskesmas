<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah-antrian.php');
    exit;
}

$pasien_id    = (int)($_POST['pasien_id'] ?? 0);
$poli_id      = (int)($_POST['poli_id'] ?? 0);
$jenis_pasien = $_POST['jenis_pasien'] ?? '';
$prioritas    = $_POST['prioritas'] ?? 'normal';
$keluhan      = trim($_POST['keluhan'] ?? '');
$catatan      = trim($_POST['catatan'] ?? '');
$tanggal      = date('Y-m-d');
$jam_daftar   = date('H:i:s');


$cek = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE poli_id = ? AND tanggal = ?");
$cek->bind_param('is', $poli_id, $tanggal);
$cek->execute();
$urutan = $cek->get_result()->fetch_assoc()['total'] + 1;
$no_antri = 'P' . $poli_id . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

$stmt = $conn->prepare("INSERT INTO antrian (no_antri, pasien_id, poli_id, jenis_pasien, prioritas, keluhan, catatan, status, tanggal, jam_daftar) VALUES (?, ?, ?, ?, ?, ?, ?, 'menunggu', ?, ?)");
$stmt->bind_param('siissssss', $no_antri, $pasien_id, $poli_id, $jenis_pasien, $prioritas, $keluhan, $catatan, $tanggal, $jam_daftar);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Antrian $no_antri berhasil diterbitkan.";
    header('Location: antrian.php');
} else {
    $_SESSION['flash_error'] = 'Gagal menyimpan antrian: ' . $conn->error;
    header('Location: tambah-antrian.php');
}
exit;
?>