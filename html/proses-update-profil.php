<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

$nama = trim($_POST['nama'] ?? '');
$id = (int) $_SESSION['user_id'];

if (empty($nama)) {
    $_SESSION['flash_error'] = 'Nama tidak boleh kosong.';
    header('Location: settings.php');
    exit;
}

$stmt = $conn->prepare("UPDATE users SET nama = ? WHERE id = ?");
$stmt->bind_param('si', $nama, $id);

if ($stmt->execute()) {
    $_SESSION['nama'] = $nama;
    $_SESSION['flash_success'] = 'Profil berhasil diupdate.';
} else {
    $_SESSION['flash_error'] = 'Gagal update: ' . $stmt->error;
}

$stmt->close();
$conn->close();

header('Location: settings.php');
exit;
?>