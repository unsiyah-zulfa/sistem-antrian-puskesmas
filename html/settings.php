<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

$config_result = $conn->query("SELECT * FROM config LIMIT 1");
if ($config_result && $config_result->num_rows > 0) {
    $config = $config_result->fetch_assoc();
} else {
    $config = [
        'nama_puskesmas' => 'Puskesmas Sehat Semua',
        'kode_puskesmas' => 'PKM-SSJ-001',
        'alamat' => 'Jl. Kesehatan No.17, Surabaya',
        'no_telp' => '(031) 5551234',
        'jam_buka' => '07:30:00',
        'jam_tutup' => '12:00:00',
        'kepala' => 'dr. Endang Susilawati, M.Kes'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengaturan</title>
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
        <a class="nav-link " href="index.php" >
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
        <a class="nav-link" href="layanan.php">
          <span class="nav-icon"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
          <span class="nav-text">Layanan / Poli</span>
        </a>
        <a class="nav-link active" href="settings.php" aria-current="page">
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
                <span class="profile-name d-none d-sm-inline"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></span>
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

          <div class="page-heading">
            <div class="page-heading-copy">
              <div>
                <h1 class="h3 mb-0">Pengaturan</h1>
              </div>
            </div>
          </div>

          <div class="row g-3 mt-1">

            <!-- Profil Akun -->
            <div class="col-12 col-xl-6">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title">
                      <span>Profil Akun</span>
                    </h2>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                  <img src="../assets/images/avatar/my.jpg" alt="Foto Profil" style="width:72px;height:72px;object-fit:cover;border-radius:100px;border:2px solid #e4e9f0;">
                  <div>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></p>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($_SESSION['role'] ?? 'Petugas') ?></p>
                  </div>
                </div>
                <form action="proses-update-profil.php" method="POST">
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label fw-semibold">Nama Lengkap</label>
                      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-semibold">Username</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" readonly>
                      <div class="form-text">Username tidak dapat diubah.</div>
                    </div>
                  </div>
                  <div class="d-flex justify-content-end mt-auto pt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Profil</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Keamanan Akun -->
            <div class="col-12 col-xl-6">
              <div class="panel h-100 d-flex flex-column">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title">
                      <span>Keamanan Akun</span>
                    </h2>
                  </div>
                </div>
                <form action="proses-ubah-password.php" method="POST">
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label fw-semibold">Password Sekarang</label>
                      <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-semibold">Password Baru</label>
                      <input type="password" name="password_baru" class="form-control" placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                      <input type="password" name="password_konfirmasi" class="form-control" placeholder="Ulangi password baru" required>
                    </div>
                  </div>
                  <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-warning btn-sm">Ubah Password</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Informasi Puskesmas -->
            <div class="col-12">
              <div class="panel">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title">
                      <span>Informasi Puskesmas</span>
                    </h2>
                  </div>
                </div>
                <form action="proses-update-config.php" method="POST">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Nama Puskesmas</label>
                      <input type="text" name="nama_puskesmas" class="form-control" value="<?= htmlspecialchars($config['nama_puskesmas'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Kode Puskesmas</label>
                      <input type="text" name="kode_puskesmas" class="form-control" value="<?= htmlspecialchars($config['kode_puskesmas'] ?? 'PKM-SSJ-001') ?>">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label fw-semibold">Alamat</label>
                      <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($config['alamat'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-semibold">No. Telp</label>
                      <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($config['no_telp'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Jam Buka</label>
                      <input type="time" name="jam_buka" class="form-control" value="<?= htmlspecialchars($config['jam_buka'] ?? '07:30') ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Jam Tutup</label>
                      <input type="time" name="jam_tutup" class="form-control" value="<?= htmlspecialchars($config['jam_tutup'] ?? '12:00') ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Kepala Puskesmas</label>
                      <input type="text" name="kepala" class="form-control" value="<?= htmlspecialchars($config['kepala'] ?? '') ?>">
                    </div>
                  </div>
                  <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Konfigurasi</button>
                  </div>
                </form>
              </div>
            </div>

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