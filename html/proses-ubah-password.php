<?php
require_once '../config.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$password_konfirmasi = $_POST['password_konfirmasi'] ?? '';
$id = $_SESSION['user_id'];

if (empty($password_lama) || empty($password_baru) || empty($password_konfirmasi)) {
    $_SESSION['flash_error'] = 'Semua field harus diisi.';
    header('Location: settings.php');
    exit;
}

if ($password_baru !== $password_konfirmasi) {
    $_SESSION['flash_error'] = 'Password baru dan konfirmasi tidak sama.';
    header('Location: settings.php');
    exit;
}

if (strlen($password_baru) < 8) {
    $_SESSION['flash_error'] = 'Password minimal 8 karakter.';
    header('Location: settings.php');
    exit;
}

$user = $conn->query("SELECT password FROM users WHERE id = $id")->fetch_assoc();
if (!password_verify($password_lama, $user['password'])) {
    $_SESSION['flash_error'] = 'Password lama salah.';
    header('Location: settings.php');
    exit;
}

$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param('si', $password_hash, $id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'Password berhasil diubah.';
} else {
    $_SESSION['flash_error'] = 'Gagal mengubah password.';
}
header('Location: settings.php');
exit;
?>