<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    $_SESSION['flash_error'] = 'ID pasien tidak valid.';
    header('Location: pasien.php');
    exit;
}


$conn->query("DELETE FROM antrian WHERE pasien_id = $id");


$stmt = $conn->prepare("DELETE FROM pasien WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'Data pasien dan antriannya berhasil dihapus.';
} else {
    $_SESSION['flash_error'] = 'Gagal menghapus data: ' . $stmt->error;
}

header('Location: pasien.php');
exit;
?>