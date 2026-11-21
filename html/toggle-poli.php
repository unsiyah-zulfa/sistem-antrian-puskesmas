<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header('Location: layanan.php');
    exit;
}

$result = $conn->query("SELECT status FROM poli WHERE id = $id");
if ($result->num_rows == 0) {
    header('Location: layanan.php');
    exit;
}

$row = $result->fetch_assoc();
$status_baru = $row['status'] == 'Buka' ? 'Tutup' : 'Buka';

$stmt = $conn->prepare("UPDATE poli SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status_baru, $id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'Status poli berhasil diubah menjadi ' . $status_baru;
} else {
    $_SESSION['flash_error'] = 'Gagal mengubah status: ' . $stmt->error;
}

header('Location: layanan.php');
exit;
?>