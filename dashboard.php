<?php
session_start();
require 'config.php'; // koneksi database

// Cek apakah pengguna sudah login dan memiliki role 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Ambil data user dari session
$username = $_SESSION['username'];

// Query untuk mengambil data user berdasarkan username
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard - SIMKAPROV</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <style>
    body {
      background: #003f5c url('assets/img/background.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding-bottom: 100px;
    }
    .navbar {
      background-color: rgba(0, 63, 92, 0.9);
    }
    .logo-img {
      width: 150px;
      height: 60px;
    }
    .contact-info {
      display: none;
    }
    .user-info {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: white;
      font-size: 1rem;
    }
    .user-info img {
      width: 30px;
      height: 30px;
    }
    .main-title-section {
      text-align: center;
      margin-top: 3rem;
      user-select: none;
    }
    h1 {
      font-size: 4rem;
      font-weight: 900;
      margin-bottom: 0.2rem;
      text-shadow: 2px 2px 4px #0008;
    }
    .simka { color: white; }
    .prov { color: gold; }
    h4 { font-weight: 400; text-shadow: 1px 1px 2px #0008; }
    .copyright-fixed {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: rgba(0, 63, 92, 0.85);
      color: white;
      text-align: center;
      padding: 10px 0;
      font-size: 0.85rem;
    }

    /* CSS untuk memberikan shape biru pada ikon */
    .service-card .service-icon {
      background-color: #007bff; /* Biru */
      border-radius: 50%; /* Membuat shape bulat */
      padding: 15px; /* Memberikan ruang di sekitar ikon */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Memberikan efek bayangan */
    }
  </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/img/logo_dishub2.png" class="logo-img me-2" alt="Logo Dishub" />
        <span class="fs-5 fw-bold">SIMKAPROV</span>
    </a>
    <div class="ms-auto">
        <!-- Dropdown untuk User -->
        <div class="user-info dropdown">
            <img src="assets/img/User.svg" alt="User Icon" class="text-white" />
            <span class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></span>
            <a href="#" class="d-flex align-items-center text-white" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user-circle fa-2x text-white"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="biodata.php">Biodata</a></li>
                <li><a class="dropdown-item" href="pengaturan.php">Pengaturan</a></li>
                <li><a class="dropdown-item" href="cek_jalur_kereta.php">Cek Jalur Kereta</a></li>
                <li><a class="dropdown-item" href="kritik_saran.php">Kritik & Saran</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Title Section -->
<section class="main-title-section" data-aos="fade-down">
    <h1><span class="simka">SIMKA</span><span class="prov">PROV</span></h1>
    <h4>Sistem Informasi Pemerintahan Daerah untuk Kemajuan Wilayah</h4>
    <p>SIMKAPROV merupakan platform yang membantu pengelolaan informasi pembangunan daerah, keuangan, dan pemerintahan untuk meningkatkan transparansi dan efisiensi dalam pengelolaan daerah.</p>
</section>

<!-- Fitur Utama -->
<div class="container text-center mt-5">
    <div class="row">
        <!-- Jadwal Kereta Api -->
        <div class="col-md-4">
            <div class="service-card">
                <img src="assets/img/train_schedule.svg" alt="Jadwal Kereta Api" class="service-icon" />
                <h5>Jadwal Kereta Api</h5>
                <p>Lihat jadwal keberangkatan dan kedatangan kereta api Sumatera Selatan.</p>
                <a href="https://www.kai.id/" class="btn btn-info" target="_blank">Lihat Jadwal</a>
            </div>
        </div>
        <!-- Daftar Stasiun -->
        <div class="col-md-4">
            <div class="service-card">
                <img src="assets/img/station_list.svg" alt="Daftar Stasiun" class="service-icon" />
                <h5>Daftar Stasiun Kereta Api</h5>
                <p>Temukan informasi lengkap tentang stasiun kereta api di Sumatera Selatan.</p>
                <a href="daftarstasiun.php" class="btn btn-info">Lihat Daftar Stasiun</a>
            </div>
        </div>
        <!-- Informasi Jalur Kereta -->
        <div class="col-md-4">
            <div class="service-card">
                <img src="assets/img/rail_track.svg" alt="Jalur Kereta Api" class="service-icon" />
                <h5>Informasi Jalur Kereta Api</h5>
                <p>Temukan jalur kereta api yang tersedia di Sumatera Selatan.</p>
                <a href="railtrackerlocation.php" class="btn btn-info">Lihat Jalur Kereta</a>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="copyright-fixed">
    &copy; 2025 SIMKAPROV. Dinas Perhubungan Provinsi Sumatera Selatan. All rights reserved.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    // Memastikan AOS bekerja
    AOS.init();
</script>

</body>
</html>
