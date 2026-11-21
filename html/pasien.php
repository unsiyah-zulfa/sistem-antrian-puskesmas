<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$pasien = $conn->query("SELECT * FROM pasien ORDER BY nama ASC");
$total = $pasien->num_rows;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pasien</title>
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

                    <?php if (!empty($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['flash_success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_success']); ?>
                    <?php endif; ?>

                    <!-- JUDUL -->
                    <div class="page-heading">
                        <div class="page-heading-copy">
                            <div>
                                <h1 class="h3 mb-0">Data Pasien</h1>
                                <p class="text-muted small mb-0">Total <?= $total ?> pasien terdaftar</p>
                            </div>
                        </div>
                        <div class="heading-actions">
                            <a class="btn btn-primary btn-sm" href="tambah-pasien.php">
                                <i class="bi bi-person-plus"></i> Tambah Pasien
                            </a>
                        </div>
                    </div>

                    <!-- TABEL -->
                    <div class="panel mt-3">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Pasien</th>
                                        <th>NIK</th>
                                        <th>Tgl. Lahir</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No. HP</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total == 0): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                Belum ada data pasien.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $no = 1;
                                        while ($row = $pasien->fetch_assoc()): 
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama']) ?></td>
                                            <td><?= htmlspecialchars($row['nik']) ?></td>
                                            <td><?= date('d-m-Y', strtotime($row['tanggal_lahir'])) ?></td>
                                            <td><?= $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                            <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                            <td class="text-end">
                                                <a href="tambah-antrian.php?pasien_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-plus-circle"></i> Antrikan
                                                </a>
                                                <a href="edit-pasien.php?id=<?= $row['id'] ?>" class="btn btn-light btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="hapus-pasien.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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