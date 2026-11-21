<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$selected_pasien = isset($_GET['pasien_id']) ? (int)$_GET['pasien_id'] : 0;

$pasien = $conn->query("SELECT id, nama, nik FROM pasien ORDER BY nama ASC");

// Ambil daftar poli
$poli = $conn->query("SELECT id, nama_poli FROM poli ORDER BY nama_poli ASC");

// Ambil jumlah antrian per poli hari ini
$tanggal = date('Y-m-d');
$antrian_per_poli = $conn->query("
    SELECT po.nama_poli, COUNT(*) as jumlah
    FROM antrian a
    JOIN poli po ON a.poli_id = po.id
    WHERE a.tanggal = '$tanggal' AND a.status IN ('Menunggu', 'Dipanggil')
    GROUP BY po.id, po.nama_poli
    ORDER BY po.nama_poli ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian</title>
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
        <a class="nav-link active" href="tambah-antrian.php" aria-current="page">
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

                    <?php if (!empty($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['flash_success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['flash_error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_error']); ?>
                    <?php endif; ?>

                    <!-- JUDUL -->
                    <div class="page-heading">
                        <div class="page-heading-copy">
                            <div>
                                <h1 class="h3 mb-0">Tambah Antrian</h1>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <!-- FORM -->
                        <div class="col-12 col-xl-8">
                            <div class="panel">
                                <div class="panel-header">
                                    <div>
                                        <h2 class="h5 mb-0 section-title">
                                            <span>Data Pendaftaran Antrian</span>
                                        </h2>
                                        <p class="text-muted small mb-0">Pilih pasien dan poli tujuan.</p>
                                    </div>
                                </div>

                                <form action="proses-tambah-antrian.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Pasien <span class="text-danger">*</span></label>
                                            <select name="pasien_id" class="form-select" required>
                                                <option value="">Pilih Pasien —</option>
                                                <?php while ($row = $pasien->fetch_assoc()): ?>
                                                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $selected_pasien ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($row['nama']) ?> (NIK: <?= htmlspecialchars($row['nik']) ?>)
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold">Poli / Layanan Tujuan <span class="text-danger">*</span></label>
                                            <select name="poli_id" class="form-select" required>
                                                <option value="">Pilih Poli —</option>
                                                <?php while ($row = $poli->fetch_assoc()): ?>
                                                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_poli']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold">Jenis Pasien <span class="text-danger">*</span></label>
                                            <select name="jenis_pasien" class="form-select" required>
                                                <option value="">Pilih Jenis —</option>
                                                <option value="umum">UMUM</option>
                                                <option value="bpjs">BPJS</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Keluhan Utama</label>
                                            <textarea name="keluhan" class="form-control" rows="3" placeholder="Contoh: demam, batuk, sakit gigi..."></textarea>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Tambah Antrian
                                            </button>
                                            <a href="antrian.php" class="btn btn-outline-secondary">Batal</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- INFO ANTRIAN -->
                        <div class="col-12 col-xl-4">
                            <div class="panel h-100">
                                <div class="panel-header">
                                    <div>
                                        <h2 class="h5 mb-0 section-title">
                                            <span>Info Antrian Saat Ini</span>
                                        </h2>
                                        <p class="text-muted small mb-0">Jumlah antrian menunggu per poli.</p>
                                    </div>
                                </div>

                                <?php if ($antrian_per_poli->num_rows == 0): ?>
                                    <p class="text-muted text-center py-3 mb-0">Belum ada antrian hari ini.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php while ($row = $antrian_per_poli->fetch_assoc()): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span><?= htmlspecialchars($row['nama_poli']) ?></span>
                                                <span class="badge bg-secondary rounded-pill"><?= $row['jumlah'] ?> antrian</span>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
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