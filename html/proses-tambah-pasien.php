<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah-pasien.php');
    exit;
}

// Ambil data dari form
$nik = $_POST['nik'] ?? '';
$nama = $_POST['nama'] ?? '';
$tempat_lahir = $_POST['tempat_lahir'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$gol_darah = $_POST['gol_darah'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$no_hp = $_POST['no_hp'] ?? '';
$no_bpjs = $_POST['no_bpjs'] ?? '';
$nama_wali = $_POST['nama_wali'] ?? '';
$alergi = $_POST['alergi'] ?? '';

// Validasi
if (empty($nik) || empty($nama) || empty($tempat_lahir) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($alamat)) {
    $_SESSION['flash_error'] = 'Semua field wajib diisi.';
    header('Location: tambah-pasien.php');
    exit;
}

// Cek NIK duplikat
$cek = $conn->query("SELECT id FROM pasien WHERE nik = '$nik'");
if ($cek->num_rows > 0) {
    $_SESSION['flash_error'] = 'NIK sudah terdaftar. Gunakan NIK lain.';
    header('Location: tambah-pasien.php');
    exit;
}

// Insert ke database
$stmt = $conn->prepare("
    INSERT INTO pasien (nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, gol_darah, alamat, no_hp, no_bpjs, nama_wali, alergi) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'sssssssssss',
    $nik, $nama, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $gol_darah, $alamat, $no_hp, $no_bpjs, $nama_wali, $alergi
);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'Data pasien berhasil ditambahkan.';
} else {
    $_SESSION['flash_error'] = 'Gagal menambahkan data: ' . $stmt->error;
}

header('Location: pasien.php');
exit;
?>