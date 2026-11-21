<?php
require_once '../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Statistik
$stat = $conn->query("
    SELECT
        COUNT(*) as total,
        SUM(status='menunggu') as menunggu,
        SUM(status='dipanggil') as dipanggil,
        SUM(status='selesai') as selesai,
        SUM(status='Tidak Hadir') as absen
    FROM antrian WHERE tanggal = '$tanggal'
")->fetch_assoc();

// Data antrian
$result = $conn->query("
    SELECT a.*, p.nama AS nama_pasien, po.nama_poli
    FROM antrian a
    JOIN pasien p  ON a.pasien_id = p.id
    JOIN poli po   ON a.poli_id   = po.id
    WHERE a.tanggal = '$tanggal'
    ORDER BY a.jam_daftar ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Antrian</title>
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
        <a class="nav-link " href="index.php" aria-current="page">
          <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>
        <a class="nav-link active" href="antrian.php"  aria-hidden="true">
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


          <div class="page-heading">
            <div class="page-heading-copy">
              <div>
               
                <h1 class="h3 mb-0">Daftar Antrian</h1>
                
              </div>
            </div>
            <div class="heading-actions">
              <a class="btn btn-primary btn-sm" href="tambah-antrian.php"><i class="bi bi-plus-circle"></i> Tambah Antrian</a>
            </div>
          </div>

          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                
                <h2 class="h5 mb-1 section-title">
                  
                  <span>Antrian Hari Ini · <?= date('d F Y', strtotime($tanggal)) ?></span>
                </h2>
                <p class="text-muted mb-0">
                  Total <?= $stat['total'] ?> antrian ·
                  <?= $stat['selesai'] ?> selesai ·
                  <?= $stat['menunggu'] ?> menunggu ·
                  <?= $stat['absen'] ?> tidak hadir
                </p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>No. Antri</th>
                    <th>Nama Pasien</th>
                    <th>Poli / Layanan</th>
                    <th>Jenis Pasien</th>
                    <th>Status</th>
                    <th>Jam Daftar</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($stat['total'] == 0): ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">Belum ada antrian hari ini.</td>
                    </tr>
                  <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><strong class="<?= $row['status'] === 'menunggu' ? '' : 'text-primary' ?>"><?= htmlspecialchars($row['no_antri']) ?></strong></td>
                      <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
                      <td><?= htmlspecialchars($row['nama_poli']) ?></td>
                      <td>
                        <span class="badge text-bg-<?= $row['jenis_pasien'] === 'bpjs' ? 'primary' : 'secondary' ?>">
                          <?= strtoupper($row['jenis_pasien']) ?>
                        </span>
                      </td>
                      <td>
                        <?php
                        $status_lower = strtolower($row['status']);
                        $badge = [
                          'menunggu'  => 'secondary',
                          'dipanggil' => 'warning',
                          'selesai'   => 'success',
                          'tidak hadir'     => 'danger',
                        ][$status_lower] ?? 'secondary';
                        ?>
                        <span class="badge text-bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span>
                      </td>
                      <td><?= substr($row['jam_daftar'], 0, 5) ?></td>
                      <td class="text-end">
                        <?php if (strtolower($row['status']) === 'menunggu'): ?>
                          <button onclick="ubahStatus(<?= $row['id'] ?>, 'panggil')" class="btn btn-warning btn-sm">Panggil</button>
                          <button onclick="ubahStatus(<?= $row['id'] ?>, 'absen')"   class="btn btn-danger btn-sm">Absen</button>
                        <?php elseif (strtolower($row['status']) === 'dipanggil'): ?>
                          <button onclick="ubahStatus(<?= $row['id'] ?>, 'selesai')" class="btn btn-success btn-sm">Selesai</button>
                        <?php else: ?>
                          <span class="text-muted small">—</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </section>

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
  <script>
  function ubahStatus(id, aksi) {
      console.log('ID yang dikirim:', id);
      console.log('Aksi yang dikirim:', aksi);
      
      if (!confirm('Yakin ' + aksi + ' antrian ini?')) return;
      
      
      if (!id || !aksi) {
          alert('Data tidak lengkap!');
          return;
      }
      
      fetch('api-antrian.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + id + '&aksi=' + aksi
      })
      .then(r => r.json())
      .then(data => {
          console.log('Response:', data);
          if (data.sukses) {
              location.reload();
          } else {
              alert('Gagal: ' + data.pesan);
          }
      })
      .catch(err => {
          console.error('Error:', err);
          alert('Terjadi kesalahan: ' + err.message);
      });
  }
  </script>
</body>
</html>