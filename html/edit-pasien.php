<?php
require_once '../config.php';
error_reporting(0);
ini_set('display_errors', 0);

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header('Location: pasien.php');
    exit;
}

$result = $conn->query("SELECT * FROM pasien WHERE id = $id");
if ($result->num_rows == 0) {
    header('Location: pasien.php');
    exit;
}
$row = $result->fetch_assoc();

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $gol_darah = $_POST['gol_darah'];
    $no_hp = $_POST['no_hp'];
    $no_bpjs = $_POST['no_bpjs'];
    $alamat = $_POST['alamat'];
    $nama_wali = $_POST['nama_wali'];
    $alergi = $_POST['alergi'];

    $stmt = $conn->prepare("
        UPDATE pasien SET 
            nik = ?, nama = ?, tempat_lahir = ?, tanggal_lahir = ?, 
            jenis_kelamin = ?, gol_darah = ?, no_hp = ?, no_bpjs = ?, 
            alamat = ?, nama_wali = ?, alergi = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        'sssssssssssi',
        $nik, $nama, $tempat_lahir, $tanggal_lahir,
        $jenis_kelamin, $gol_darah, $no_hp, $no_bpjs,
        $alamat, $nama_wali, $alergi, $id
    );

    if ($stmt->execute()) {
        $_SESSION['flash_success'] = 'Data pasien berhasil diupdate.';
        header('Location: pasien.php');
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
    <title>Pasien</title>
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
        <a class="nav-link active" href="pasien.php" aria-current="page">
          <span class="nav-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span>
          <span class="nav-text">Data Pasien</span>
        </a>
        <a class="nav-link" href="tambah-pasien.php">
          <span class="nav-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
          <span class="nav-text">Tambah Pasien</span>
        </a>
        <a class="nav-link" href="layanan.php">
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

                    <div class="page-heading">
                        <div class="page-heading-copy">
                            <div>
                                <h1 class="h3 mb-0">Edit Pasien</h1>
                                <p class="text-muted small mb-0">Ubah data pasien yang terdaftar.</p>
                            </div>
                        </div>
                    </div>

                    <div class="panel mt-3">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($row['nik']) ?>" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($row['tempat_lahir']) ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= $row['tanggal_lahir'] ?>">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select">
                                        <option value="L" <?= $row['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= $row['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold">Golongan Darah</label>
                                    <select name="gol_darah" class="form-select">
                                        <option value="">Pilih</option>
                                        <?php foreach (['A', 'B', 'AB', 'O'] as $gol): ?>
                                            <option value="<?= $gol ?>" <?= $row['gol_darah'] == $gol ? 'selected' : '' ?>><?= $gol ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold">No. HP</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($row['no_hp']) ?>">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold">No. BPJS</label>
                                    <input type="text" name="no_bpjs" class="form-control" value="<?= htmlspecialchars($row['no_bpjs']) ?>">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-semibold">Nama Wali</label>
                                    <input type="text" name="nama_wali" class="form-control" value="<?= htmlspecialchars(strip_tags($row['nama_wali'])) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($row['alamat']) ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alergi</label>
                                    <textarea name="alergi" class="form-control" rows="2"><?= htmlspecialchars($row['alergi']) ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    <a href="pasien.php" class="btn btn-outline-secondary">Batal</a>
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
                    <span>Pemograman WEB E</span>
                </div>
            </footer>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <?php include 'init-user.php'; ?>
<script src="../assets/js/main.js"></script>
</body>
</html>