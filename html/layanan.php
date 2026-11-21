<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$tanggal = date('Y-m-d');


$poli = $conn->query("SELECT * FROM poli ORDER BY nama_poli ASC");


$antrian_hari_ini = $conn->query("
    SELECT 
        poli_id,
        COUNT(*) as total,
        SUM(status = 'Menunggu') as menunggu,
        SUM(status = 'Dipanggil') as dipanggil,
        SUM(status = 'Selesai') as selesai,
        SUM(status = 'Tidak Hadir') as tidak_hadir
    FROM antrian 
    WHERE tanggal = '$tanggal'
    GROUP BY poli_id
");


$stat_antrian = [];
while ($row = $antrian_hari_ini->fetch_assoc()) {
    $stat_antrian[$row['poli_id']] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Layanan</title>
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
                <h1 class="h3 mb-0">Layanan / Poli</h1>
              </div>
            </div>
            <div class="heading-actions">
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPoli">
                <i class="bi bi-plus-circle"></i> Tambah Poli
              </button>
            </div>
          </div>

          <!-- Kartu Poli -->
          <section class="row g-3 mt-1">
            <?php if ($poli->num_rows == 0): ?>
              <div class="col-12">
                <div class="alert alert-info">Belum ada poli. Silakan tambah poli baru.</div>
              </div>
            <?php else: ?>
              <?php while ($row = $poli->fetch_assoc()): 
                $id = $row['id'];
                $menunggu = $stat_antrian[$id]['menunggu'] ?? 0;
                $dipanggil = $stat_antrian[$id]['dipanggil'] ?? 0;
                $selesai = $stat_antrian[$id]['selesai'] ?? 0;
                
                $status = $row['status'] ?? 'Buka';
                $badge_color = $status == 'Buka' ? 'success' : 'secondary';
                $btn_action = $status == 'Buka' ? 'Tutup' : 'Buka';
                $btn_color = $status == 'Buka' ? 'danger' : 'success';
                
                $kuota = $row['kuota'] ?? 50;
              ?>
                <div class="col-12 col-sm-6 col-xl-4">
                  <div class="panel">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h3 class="h6 fw-bold mb-1"><?= htmlspecialchars($row['nama_poli']) ?></h3>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($row['dokter'] ?? 'Dokter') ?></p>
                      </div>
                      <span class="badge text-bg-<?= $badge_color ?>"><?= $status ?></span>
                    </div>
                    <div class="d-flex gap-3 text-center mb-3">
                      <div>
                        <div class="fw-bold text-primary"><?= $menunggu + $dipanggil ?></div>
                        <div class="text-muted small">Menunggu</div>
                      </div>
                      <div>
                        <div class="fw-bold text-success"><?= $selesai ?></div>
                        <div class="text-muted small">Selesai</div>
                      </div>
                      <div>
                        <div class="fw-bold text-secondary"><?= $kuota ?></div>
                        <div class="text-muted small">Kuota</div>
                      </div>
                    </div>
                    <div class="d-flex gap-2">
                      <a href="edit-poli.php?id=<?= $id ?>" class="btn btn-light btn-sm flex-grow-1">Edit</a>
                      <a href="toggle-poli.php?id=<?= $id ?>" class="btn btn-<?= $btn_color ?> btn-sm" onclick="return confirm('Ubah status poli ini?')"><?= $btn_action ?></a>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </section>

        </div>
      </main>

      <!-- Modal Tambah Poli -->
      <div class="modal fade" id="modalTambahPoli" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Poli Baru</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses-tambah-poli.php" method="POST">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Nama Poli <span class="text-danger">*</span></label>
                  <input type="text" name="nama_poli" class="form-control" placeholder="Contoh: Poli TB, Laboratorium..." required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Dokter / Penanggung Jawab <span class="text-danger">*</span></label>
                  <input type="text" name="dokter" class="form-control" placeholder="Nama dokter atau tenaga kesehatan" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Kuota Antrian / Hari</label>
                  <input type="number" name="kuota" class="form-control" placeholder="Jumlah maksimum pasien per hari" min="1" value="50">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Poli</button>
              </div>
            </form>
          </div>
        </div>
      </div>

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