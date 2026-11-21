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

$result = $conn->query("SELECT * FROM poli WHERE id = $id");
if ($result->num_rows == 0) {
    header('Location: layanan.php');
    exit;
}
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_poli = $_POST['nama_poli'];
    $dokter = $_POST['dokter'];
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];
    $kuota = (int)$_POST['kuota'];
    $status = $_POST['status'] ?? 'Buka';

    $stmt = $conn->prepare("UPDATE poli SET nama_poli=?, dokter=?, jam_buka=?, jam_tutup=?, kuota=?, status=? WHERE id=?");
    $stmt->bind_param('ssssisi', $nama_poli, $dokter, $jam_buka, $jam_tutup, $kuota, $status, $id);

    if ($stmt->execute()) {
        $_SESSION['flash_success'] = 'Data poli berhasil diupdate.';
        header('Location: layanan.php');
        exit;
    } else {
        $error = 'Gagal update: ' . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Poli</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendors/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>
<aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi utama">
      <div class="sidebar-header">
        <a class="brand-mark" href="index.php" aria-label="SiAntrePKM Dashboard">
          <span class="brand-icon">
             <i>
               <img src="../assets/images/logos/hospital.png" alt="" class="hospital-logos">
             </i>
          </span>
          <span class="brand-copy">
            <span class="brand-title">Puskesmas Sehat Semua</span>
          </span>
        </a>
      </div>
 
      <nav class="sidebar-nav">
        <a class="nav-link" href="index.php" >
          <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>
        <a class="nav-link" href="antrian.php">
          <span class="nav-icon"><i class="bi bi-ticket-detailed" aria-hidden="true"></i></span>
          <span class="nav-text">Daftar Antrian</span>
        </a>
        <a class="nav-link" href="tambah-antrian.php">
          <span class="nav-icon"><i class="bi bi-plus-circle" aria-hidden="true"></i></span>
          <span class="nav-text">Tambah Antrian</span>
        </a>
        <a class="nav-link" href="pasien.php">
          <span class="nav-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span>
          <span class="nav-text">Data Pasien</span>
        </a>
        <a class="nav-link" href="tambah-pasien.php">
          <span class="nav-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
          <span class="nav-text">Tambah Pasien</span>
        </a>
        <a class="nav-link active" href="layanan.php" aria-current="page">
          <span class="nav-icon"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
          <span class="nav-text">Layanan / Poli</span>
        </a>
        <a class="nav-link" href="settings.php">
          <span class="nav-icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
          <span class="nav-text">Pengaturan</span>
        </a>
      </nav>
 
    </aside>

    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" data-sidebar-toggle><span></span><span></span><span></span></button>
          <div class="navbar-actions ms-auto">
            <button class="icon-button theme-toggle" data-theme-toggle><i class="bi bi-moon-stars" data-theme-icon></i></button>
            <div class="dropdown">
              <button class="profile-button dropdown-toggle" data-bs-toggle="dropdown">
                <img class="avatar-img avatar-sm" src="../assets/images/avatar/my.jpg" alt="">
                <span class="profile-name d-none d-sm-inline"><?= htmlspecialchars($_SESSION['nama']) ?></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="settings.php">Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">

          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <!-- JUDUL -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <div>
                <h1 class="h3 mb-0">Edit Poli</h1>
              </div>
            </div>
          </div>

          <div class="panel mt-3">
            <form method="POST">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold">Nama Poli <span class="text-danger">*</span></label>
                  <input type="text" name="nama_poli" class="form-control" value="<?= htmlspecialchars($row['nama_poli']) ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Dokter <span class="text-danger">*</span></label>
                  <input type="text" name="dokter" class="form-control" value="<?= htmlspecialchars($row['dokter'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Jam Buka</label>
                  <input type="time" name="jam_buka" class="form-control" value="<?= $row['jam_buka'] ?? '08:00' ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Jam Tutup</label>
                  <input type="time" name="jam_tutup" class="form-control" value="<?= $row['jam_tutup'] ?? '12:00' ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Kuota</label>
                  <input type="number" name="kuota" class="form-control" value="<?= $row['kuota'] ?? 50 ?>" min="1">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Status</label>
                  <select name="status" class="form-select">
                    <option value="Buka" <?= ($row['status'] ?? 'Buka') == 'Buka' ? 'selected' : '' ?>>Buka</option>
                    <option value="Tutup" <?= ($row['status'] ?? 'Buka') == 'Tutup' ? 'selected' : '' ?>>Tutup</option>
                  </select>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                  <a href="layanan.php" class="btn btn-outline-secondary">Batal</a>
                </div>
              </div>
            </form>
          </div>

        </div>
      </main>

      <footer class="admin-footer">
        <div class="container-fluid px-3 px-lg-4">
          <span>© 2026 Puskesmas Sehat Semua</span>
          <span>Kelompok 8</span>
          <span>Pemograman Web E</span>
        </div>
      </footer>
    </div>
  </div>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <?php include 'init-user.php'; ?>
<script src="../assets/js/main.js"></script>
</body>
</html>