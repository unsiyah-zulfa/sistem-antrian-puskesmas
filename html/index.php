<?php
require_once '../config.php';
 
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
 
$tanggal_hari_ini = date('Y-m-d');
 
// Statistik umum
$query_stat = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'menunggu') as menunggu,
        SUM(status = 'dipanggil') as dipanggil,
        SUM(status = 'selesai') as selesai,
        SUM(status = 'Tidak Hadir') as absen
    FROM antrian 
    WHERE tanggal = '$tanggal_hari_ini'
");
$stat = $query_stat->fetch_assoc();
if (!$stat || $stat['total'] === null) {
    $stat = ['total' => 0, 'menunggu' => 0, 'dipanggil' => 0, 'selesai' => 0, 'absen' => 0];
}
 
// Total pasien terdaftar
$total_pasien = $conn->query("SELECT COUNT(*) as total FROM pasien")->fetch_assoc()['total'];
 
// Grafik: antrian per poli
$query_per_poli = $conn->query("
    SELECT 
        po.nama_poli,
        SUM(a.status IN ('selesai','dipanggil')) as dilayani,
        SUM(a.status = 'menunggu') as menunggu
    FROM antrian a
    JOIN poli po ON a.poli_id = po.id
    WHERE a.tanggal = '$tanggal_hari_ini'
    GROUP BY po.id, po.nama_poli
    ORDER BY (SUM(a.status IN ('selesai','dipanggil')) + SUM(a.status = 'menunggu')) DESC
");
 
$labels_poli   = [];
$data_dilayani = [];
$data_menunggu = [];
 
while ($row = $query_per_poli->fetch_assoc()) {
    $labels_poli[]   = $row['nama_poli'];
    $data_dilayani[] = (int) $row['dilayani'];
    $data_menunggu[] = (int) $row['menunggu'];
}
 
// Grafik: tren antrian per jam 
$query_per_jam = $conn->query("
    SELECT 
        HOUR(jam_daftar) as jam,
        COUNT(*) as masuk,
        SUM(status IN ('selesai','dipanggil')) as dilayani
    FROM antrian
    WHERE tanggal = '$tanggal_hari_ini'
      AND HOUR(jam_daftar) BETWEEN 7 AND 14
    GROUP BY HOUR(jam_daftar)
    ORDER BY jam ASC
");
 
$jam_range         = range(7, 14);
$data_jam_masuk    = array_fill_keys($jam_range, 0);
$data_jam_dilayani = array_fill_keys($jam_range, 0);
 
while ($row = $query_per_jam->fetch_assoc()) {
    $data_jam_masuk[(int)$row['jam']]    = (int) $row['masuk'];
    $data_jam_dilayani[(int)$row['jam']] = (int) $row['dilayani'];
}
 
$labels_jam           = array_map(fn($h) => sprintf('%02d:00', $h), $jam_range);
$values_masuk         = array_values($data_jam_masuk);
$values_jam_dilayani  = array_values($data_jam_dilayani);
 

$json_labels_poli         = json_encode($labels_poli);
$json_dilayani            = json_encode($data_dilayani);
$json_menunggu            = json_encode($data_menunggu);
$json_labels_jam          = json_encode($labels_jam);
$json_values_masuk        = json_encode($values_masuk);
$json_values_jam_dilayani = json_encode($values_jam_dilayani);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sistem Antri Tiket Puskesmas - Dashboard Admin">
  <title>Dashboard</title>
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
            <!-- <span class="brand-subtitle">Admin Panel</span> -->
          </span>
        </a>
      </div>
 
      <nav class="sidebar-nav">
        <a class="nav-link active" href="index.php" aria-current="page">
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
        <a class="nav-link" href="settings.php">
          <span class="nav-icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
          <span class="nav-text">Pengaturan</span>
        </a>
      </nav>
 
    </aside>
 
    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="true" aria-label="Toggle sidebar">
            <span></span><span></span><span></span>
          </button>
          
          
          
          <div class="navbar-actions ms-auto">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema">
              <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
            </button>
            
            <div class="dropdown">
              <button class="profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <img class="avatar-img avatar-sm" src="../assets/images/avatar/my.jpg" alt="Admin PKM">
                <span class="profile-name d-none d-sm-inline"><?= htmlspecialchars($_SESSION['nama']) ?></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="settings.php">Pengaturan Akun</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Keluar</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
 
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
 
          <div class="page-heading">
            <div class="page-heading-copy">
              <div>
                
                <h1 class="h3 mb-0">Dashboard Antrian</h1>
                
              </div>
            </div>
            <div class="heading-actions">
             
              <a class="btn btn-primary btn-sm" href="tambah-antrian.php"><i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Antrian</a>
            </div>
          </div>
 
          
          <section class="row g-3 mt-1" aria-label="Metrik antrian hari ini">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">Total Antrian</span>
                  
                </div>
                <div class="metric-value"><?= $stat['total'] ?></div>
                <div class="metric-meta">
                  <span class="text-success">Hari ini</span>
                  <span>antrian aktif</span>
                </div>
              </article>
            </div>
 
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">Sudah Dilayani</span>
                  
                </div>
                <div class="metric-value"><?= $stat['selesai'] ?></div>
                <div class="metric-meta">
                  <span class="text-success">
                    <?= $stat['total'] > 0 ? round(($stat['selesai'] / $stat['total']) * 100) : 0 ?>%
                  </span>
                  <span>selesai</span>
                </div>
              </article>
            </div>
 
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">Menunggu</span>
                  
                </div>
                <div class="metric-value"><?= $stat['menunggu'] ?></div>
                <div class="metric-meta">
                  <span class="text-warning">belum dipanggil</span>
                </div>
              </article>
            </div>
 
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-info">
                <div class="metric-top">
                  <span class="metric-label">Dipanggil</span>
                  
                </div>
                <div class="metric-value"><?= $stat['dipanggil'] ?></div>
                <div class="metric-meta">
                  <span class="text-info">sedang dilayani</span>
                </div>
              </article>
            </div>
          </section>
 
        
          <section class="row g-3 mt-1">
            <div class="col-12">
              <div class="panel">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title">
                      
                      <span>Antrian per Poli Hari Ini</span>
                    </h2>
                    <p class="text-muted mb-0">Distribusi antrian berdasarkan poli layanan.</p>
                  </div>
                  <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Pilih tampilan grafik">
                      <button type="button" class="btn btn-primary active" id="btnPerPoli">Per Poli</button>
                      <button type="button" class="btn btn-outline-secondary" id="btnPerJam">Tren Per Jam</button>
                    </div>
                    
                  </div>
                </div>
 
                
                <div class="d-flex gap-3 mb-3 px-1" id="legendPerPoli" style="font-size:12px;color:#6c757d;">
                  <span>
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#1D9E75;margin-right:4px;"></span>
                    Sudah Dilayani
                  </span>
                  <span>
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#EF9F27;margin-right:4px;"></span>
                    Menunggu
                  </span>
                </div>
 
                >
                <div class="d-flex gap-3 mb-3 px-1 d-none" id="legendPerJam" style="font-size:12px;color:#6c757d;">
                  <span>
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#1D9E75;margin-right:4px;"></span>
                    Pasien Masuk
                  </span>
                  <span>
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#3266ad;margin-right:4px;vertical-align:middle;"></span>
                    Dilayani
                  </span>
                </div>
 
                <?php if (empty($labels_poli)): ?>
                  <p class="text-muted text-center py-4">Belum ada data antrian hari ini.</p>
                <?php else: ?>
                  <div style="position:relative;width:100%;height:300px;">
                    <canvas id="chartDashboard"
                      role="img"
                      aria-label="Grafik antrian per poli dan tren per jam hari ini">
                    </canvas>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </section>
 
          
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-0 section-title">
                  
                  <span>Antrian Menunggu</span>
                </h2>
               
              </div>
              <a class="btn btn-outline-secondary btn-sm" href="antrian.php">Kelola Antrian</a>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>No. Antri</th>
                    <th>Nama Pasien</th>
                    <th>Poli / Layanan</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Jam Daftar</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                 
                  $antrian_terbaru = $conn->query("
                      SELECT a.*, p.nama as nama_pasien, po.nama_poli
                      FROM antrian a
                      JOIN pasien p ON a.pasien_id = p.id
                      JOIN poli po ON a.poli_id = po.id
                      WHERE a.tanggal = '$tanggal_hari_ini'
                        AND a.status = 'menunggu'
                      ORDER BY a.jam_daftar ASC
                  ");
 
                  if ($antrian_terbaru->num_rows == 0) {
                      echo '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada antrian menunggu.</td></tr>';
                  } else {
                      while ($row = $antrian_terbaru->fetch_assoc()) {
                          $badge_status = 'secondary';
                          $badge_jenis = strtolower($row['jenis_pasien']) === 'bpjs' ? 'primary' : 'secondary';
                          echo '<tr>';
                          echo '<td><strong>' . htmlspecialchars($row['no_antri']) . '</strong></td>';
                          echo '<td>' . htmlspecialchars($row['nama_pasien']) . '</td>';
                          echo '<td>' . htmlspecialchars($row['nama_poli']) . '</td>';
                          echo '<td><span class="badge text-bg-' . $badge_jenis . '">' . strtoupper($row['jenis_pasien']) . '</span></td>';
                          echo '<td><span class="badge text-bg-' . $badge_status . '">' . ucfirst($row['status']) . '</span></td>';
                          echo '<td>' . substr($row['jam_daftar'], 0, 5) . '</td>';
                          echo '<td class="text-end"><a class="btn btn-light btn-sm" href="antrian.php">Detail</a></td>';
                          echo '</tr>';
                      }
                  }
                  ?>
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
 
  <?php if (!empty($labels_poli)): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
  (function () {
    const labelsPoli   = <?= $json_labels_poli ?>;
    const dataDilayani = <?= $json_dilayani ?>;
    const dataMenunggu = <?= $json_menunggu ?>;
    const labelsJam    = <?= $json_labels_jam ?>;
    const dataMasuk    = <?= $json_values_masuk ?>;
    const dataLayani   = <?= $json_values_jam_dilayani ?>;
 
    const ctx = document.getElementById('chartDashboard').getContext('2d');
    let chart = null;
 
    function buildBarChart() {
      return new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labelsPoli,
          datasets: [
            {
              label: 'Sudah Dilayani',
              data: dataDilayani,
              backgroundColor: '#1D9E75',
              borderRadius: 4,
              borderSkipped: false
            },
            {
              label: 'Menunggu',
              data: dataMenunggu,
              backgroundColor: '#EF9F27',
              borderRadius: 4,
              borderSkipped: false
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: { mode: 'index' }
          },
          scales: {
            x: {
              stacked: true,
              ticks: { font: { size: 11 } }
            },
            y: {
              stacked: true,
              beginAtZero: true,
              ticks: { stepSize: 5, font: { size: 11 } },
              grid: { color: 'rgba(0,0,0,0.06)' }
            }
          }
        }
      });
    }
 
    function buildLineChart() {
      return new Chart(ctx, {
        type: 'line',
        data: {
          labels: labelsJam,
          datasets: [
            {
              label: 'Pasien Masuk',
              data: dataMasuk,
              borderColor: '#1D9E75',
              backgroundColor: 'rgba(29,158,117,0.1)',
              tension: 0.4,
              fill: true,
              pointRadius: 4,
              pointHoverRadius: 6,
              borderDash: []
            },
            {
              label: 'Dilayani',
              data: dataLayani,
              borderColor: '#3266ad',
              backgroundColor: 'rgba(50,102,173,0.07)',
              tension: 0.4,
              fill: false,
              pointRadius: 4,
              pointHoverRadius: 6,
              borderDash: [5, 3]
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: { mode: 'index' }
          },
          scales: {
            x: {
              ticks: { autoSkip: false, font: { size: 11 } }
            },
            y: {
              beginAtZero: true,
              ticks: { stepSize: 5, font: { size: 11 } },
              grid: { color: 'rgba(0,0,0,0.06)' }
            }
          }
        }
      });
    }
 
   
    chart = buildBarChart();
 
    document.getElementById('btnPerPoli').addEventListener('click', function () {
      if (this.classList.contains('active')) return;
      this.classList.add('active', 'btn-primary');
      this.classList.remove('btn-outline-secondary');
      const btnJam = document.getElementById('btnPerJam');
      btnJam.classList.remove('active', 'btn-primary');
      btnJam.classList.add('btn-outline-secondary');
      document.getElementById('legendPerPoli').classList.remove('d-none');
      document.getElementById('legendPerJam').classList.add('d-none');
      chart.destroy();
      chart = buildBarChart();
    });
 
    document.getElementById('btnPerJam').addEventListener('click', function () {
      if (this.classList.contains('active')) return;
      this.classList.add('active', 'btn-primary');
      this.classList.remove('btn-outline-secondary');
      const btnPoli = document.getElementById('btnPerPoli');
      btnPoli.classList.remove('active', 'btn-primary');
      btnPoli.classList.add('btn-outline-secondary');
      document.getElementById('legendPerPoli').classList.add('d-none');
      document.getElementById('legendPerJam').classList.remove('d-none');
      chart.destroy();
      chart = buildLineChart();
    });
  })();
  </script>
  <?php endif; ?>
 
</body>
</html>