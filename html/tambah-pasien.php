<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
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
            <!-- <i class="bi bi-hospital" aria-hidden="true"></i> -->
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
        <a class="nav-link active" href="tambah-pasien.php" aria-current="page">
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
                <h1 class="h3 mb-0">Tambah Pasien Baru</h1>
              </div>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-12 col-xl-12">
              <form class="panel" method="POST" action="proses-tambah-pasien.php">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title">
                      <span>Data Identitas Pasien</span>
                    </h2>
                    <p class="text-muted mb-0">Isi lengkap data diri pasien sesuai KTP.</p>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="nik">NIK <span class="text-danger">*</span></label>
                    <input class="form-control" id="nik" name="nik" type="text" placeholder="16 digit NIK sesuai KTP" maxlength="16" required>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold" for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                    <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama sesuai KTP" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                    <input class="form-control" id="tempat_lahir" name="tempat_lahir" type="text" placeholder="Kota/Kabupaten lahir" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input class="form-control" id="tanggal_lahir" name="tanggal_lahir" type="date" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                      <option value="">— Pilih —</option>
                      <option value="L">Laki-laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="gol_darah">Golongan Darah</label>
                    <select class="form-select" id="gol_darah" name="gol_darah">
                      <option value="">— Tidak Diketahui —</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                      <option value="AB">AB</option>
                      <option value="O">O</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold" for="alamat">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Nama jalan, RT/RW, Kelurahan, Kecamatan" required></textarea>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="no_hp">No. HP / Telepon</label>
                    <input class="form-control" id="no_hp" name="no_hp" type="tel" placeholder="08xxxxxxxxxx">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold" for="no_bpjs">No. BPJS</label>
                    <input class="form-control" id="no_bpjs" name="no_bpjs" type="text" placeholder="Kosongkan jika tidak ada">
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold" for="nama_wali">Nama Wali / Penanggung Jawab</label>
                    <input class="form-control" id="nama_wali" name="nama_wali" type="text" placeholder="Untuk pasien anak / lansia">
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold" for="alergi">Riwayat Alergi</label>
                    <input class="form-control" id="alergi" name="alergi" type="text" placeholder="Contoh: Penisilin, debu, makanan laut...">
                  </div>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                  <a class="btn btn-outline-secondary" href="pasien.php">Batal</a>
                  <button class="btn btn-outline-primary" type="reset">Reset Form</button>
                  <button class="btn btn-primary" type="submit"><i class="bi bi-person-check"></i> Simpan Data Pasien</button>
                </div>
              </form>
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