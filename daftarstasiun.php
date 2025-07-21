<?php
session_start();
require 'config.php'; // koneksi database

// Cek apakah pengguna sudah login (role apa pun)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari session untuk ditampilkan di navbar
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_nav = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Daftar Stasiun Kereta Api - SIMKAPROV</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <style>
    :root {
        --primary-color: #00529B;
        --secondary-color: #FFC107;
        --light-color: #FFFFFF;
        --dark-color: #2c3e50;
        --page-bg: #4A89DC; 
        --font-family: 'Poppins', sans-serif;
    }

    body {
        background-color: var(--page-bg);
        font-family: var(--font-family);
        color: var(--dark-color);
    }

    .navbar {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.75rem 2rem;
        position: sticky;
        top: 0;
        z-index: 1020;
    }
    .logo-img { height: 45px; width: auto; }
    .navbar-brand span { font-weight: 600; color: var(--light-color); }
    .navbar .dropdown-toggle { color: var(--light-color); }

    .page-header {
        padding: 2rem 0;
        text-align: center;
        color: var(--light-color);
    }
    .page-header h1 {
        font-weight: 700;
        font-size: 2.5rem;
        text-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .content-card {
        background: var(--light-color);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    /* Custom Accordion Style */
    .accordion-item {
        border: 1px solid #dee2e6;
        border-radius: 10px !important;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .accordion-button {
        font-weight: 600;
        color: var(--dark-color);
    }
    .accordion-button:not(.collapsed) {
        color: var(--light-color);
        background-color: var(--primary-color);
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(0, 82, 155, 0.25);
    }
    .accordion-body {
        padding: 1.5rem;
    }
    .table {
        margin-bottom: 0;
    }
    
    .btn-floating {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background-color: var(--secondary-color);
        color: var(--dark-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        text-decoration: none;
        transition: all 0.3s ease;
        z-index: 1000;
    }
    .btn-floating:hover {
        transform: scale(1.1);
        color: var(--dark-color);
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/img/logo_dishub2.png" class="logo-img me-2" alt="Logo" />
            <span>SIMKAPROV</span>
        </a>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fw-bold d-none d-md-inline">Halo, <?php echo htmlspecialchars($user_nav['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <header class="page-header" data-aos="fade-down">
      <h1>Daftar Stasiun Kereta Api</h1>
      <p>Berikut adalah daftar stasiun kereta api yang ada di Sumatera Selatan, diklasifikasikan berdasarkan kota.</p>
    </header>

    <div class="content-card" data-aos="fade-up" data-aos-delay="200">
        <div class="accordion" id="stationAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Kota Palembang & Sekitarnya
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#stationAccordion">
                    <div class="accordion-body">
                        <table class="table table-hover">
                            <thead><tr><th>Nama Stasiun</th><th>Kode</th><th>Status</th><th>Divisi Regional</th></tr></thead>
                            <tbody>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Kertapati" target="_blank">Stasiun Kertapati</a></td><td>KPT</td><td>Beroperasi</td><td>Divre III Palembang</td></tr>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Prabumulih" target="_blank">Stasiun Prabumulih</a></td><td>PBM</td><td>Beroperasi</td><td>Divre III Palembang</td></tr>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Tanjungenim Baru" target="_blank">Stasiun Tanjungenim Baru</a></td><td>TMB</td><td>Beroperasi</td><td>Divre III Palembang</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Kota Lubuklinggau & Sekitarnya
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#stationAccordion">
                    <div class="accordion-body">
                        <table class="table table-hover">
                             <thead><tr><th>Nama Stasiun</th><th>Kode</th><th>Status</th><th>Divisi Regional</th></tr></thead>
                            <tbody>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Lubuklinggau" target="_blank">Stasiun Lubuklinggau</a></td><td>LLG</td><td>Beroperasi</td><td>Divre III Palembang</td></tr>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Lahat" target="_blank">Stasiun Lahat</a></td><td>LT</td><td>Beroperasi</td><td>Divre III Palembang</td></tr>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Banjarsari" target="_blank">Stasiun Banjarsari</a></td><td>BJS</td><td>Beroperasi</td><td>Divre III Palembang</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Kabupaten Ogan Komering Ulu (Baturaja) & Sekitarnya
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#stationAccordion">
                    <div class="accordion-body">
                         <table class="table table-hover">
                             <thead><tr><th>Nama Stasiun</th><th>Kode</th><th>Status</th><th>Divisi Regional</th></tr></thead>
                            <tbody>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Baturaja" target="_blank">Stasiun Baturaja</a></td><td>BTA</td><td>Beroperasi</td><td>Divre IV Tanjungkarang</td></tr>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Martapura" target="_blank">Stasiun Martapura</a></td><td>MP</td><td>Beroperasi</td><td>Divre IV Tanjungkarang</td></tr>
                                <tr><td><a href="https://maps.google.com/?q=Stasiun Tigagajah" target="_blank">Stasiun Tigagajah</a></td><td>TJH</td><td>Beroperasi</td><td>Divre IV Tanjungkarang</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="index.php" class="btn-floating" title="Kembali ke Halaman Utama">
    <i class="bi bi-house-door-fill"></i>
</a>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800
    });
</script>

</body>
</html>