<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    echo json_encode(['sukses' => false, 'pesan' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$id   = (int)($_POST['id'] ?? 0);
$aksi = $_POST['aksi'] ?? '';

if (!$id || !in_array($aksi, ['panggil', 'selesai', 'absen'])) {
    echo json_encode(['sukses' => false, 'pesan' => 'Parameter tidak valid.']);
    exit;
}

$map = [
    'panggil' => ['status' => 'Dipanggil', 'kolom' => 'jam_panggil'],
    'selesai' => ['status' => 'Selesai',   'kolom' => 'jam_selesai'],
    'absen'   => ['status' => 'Tidak Hadir', 'kolom' => null],  
];

$status = $map[$aksi]['status'];
$kolom  = $map[$aksi]['kolom'];
$waktu  = date('H:i:s');

if ($kolom) {
    $stmt = $conn->prepare("UPDATE antrian SET status = ?, $kolom = ? WHERE id = ?");
    $stmt->bind_param('ssi', $status, $waktu, $id);
} else {
    $stmt = $conn->prepare("UPDATE antrian SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
}

$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['sukses' => true]);
} else {
    echo json_encode(['sukses' => false, 'pesan' => 'Data tidak ditemukan.']);
}
exit;
?>