<?php
session_start();
require 'config.php'; // koneksi database

// Cek apakah pengguna sudah login dan memiliki role 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Arahkan ke login jika belum login
    exit();
}

// Ambil data admin dari session
$username = $_SESSION['username'];

// Query untuk mengambil data admin berdasarkan username
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Ambil jumlah kritik dan saran
$stmtKritik = $conn->prepare("SELECT COUNT(id) as total_kritik FROM kritik_saran");
$stmtKritik->execute();
$resultKritik = $stmtKritik->get_result();
$kritikData = $resultKritik->fetch_assoc();
$totalKritik = $kritikData['total_kritik'];

// Ambil jumlah jalur kereta
$stmtJalur = $conn->prepare("SELECT COUNT(id) as total_jalur FROM jalur_kereta_admin");
$stmtJalur->execute();
$resultJalur = $stmtJalur->get_result();
$jalurData = $resultJalur->fetch_assoc();
$totalJalur = $jalurData['total_jalur'];

// Ambil jumlah pengguna
$stmtUsers = $conn->prepare("SELECT COUNT(id) as total_users FROM users");
$stmtUsers->execute();
$resultUsers = $stmtUsers->get_result();
$userData = $resultUsers->fetch_assoc();
$totalUsers = $userData['total_users'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - SIMKAPROV</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #00529B;
            --secondary-color: #FFC107;
            --light-color: #FFFFFF;
            --dark-color: #2c3e50;
            /* [PERUBAHAN] Mengganti font family kembali ke Poppins */
            --font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(45deg, #003f7f, var(--primary-color), #5899e2, #002b55);
            background-size: 400% 400%;
            animation: animateGradient 20s ease infinite;
            color: var(--light-color);
            font-family: var(--font-family);
        }

        @keyframes animateGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* [PERUBAHAN] Style Navbar disamakan dengan index.html */
        .navbar {
            background-color: transparent; /* Awalnya transparan */
            transition: background-color 0.4s ease, box-shadow 0.4s ease;
            padding: 0.75rem 2rem;
        }
        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.9); /* Latar belakang solid putih saat di-scroll */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar .navbar-brand span, .navbar .dropdown-toggle {
            color: var(--light-color); /* Warna teks awal putih */
            transition: color 0.4s ease;
        }
        .navbar.scrolled .navbar-brand span, .navbar.scrolled .dropdown-toggle {
            color: var(--dark-color); /* Warna teks jadi gelap saat di-scroll */
        }
        
        .logo-img { height: 45px; width: auto; }
        .navbar-brand span { font-weight: 600; }

        .navbar .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }

        .dashboard-header {
            padding: 3rem 0;
            text-align: center;
        }
        .dashboard-header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .dashboard-header p {
            font-size: 1.1rem;
            opacity: 0.8;
            max-width: 600px;
            margin: 10px auto 0;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 25px;
            color: var(--light-color);
            text-decoration: none;
            display: block;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .stat-content {
            display: flex;
            align-items: center;
        }
        .stat-icon {
            font-size: 3rem;
            margin-right: 20px;
            background: -webkit-linear-gradient(45deg, var(--secondary-color), #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.8;
        }
        .stat-details .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .stat-details .stat-text {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .btn-user-dashboard {
            background: var(--secondary-color);
            color: #333;
            font-weight: 600;
            border-radius: 50px;
            padding: 12px 30px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-user-dashboard:hover {
            background: var(--light-color);
            transform: scale(1.05);
            color: #333;
        }

        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 4rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg" id="header">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="assets/img/logo_dishub2.png" class="logo-img me-2" alt="Logo" />
            <span>SIMKAPROV ADMIN</span>
        </a>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fw-bold d-none d-md-inline">Halo, <?php echo htmlspecialchars($user['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <header class="dashboard-header" data-aos="fade-down">
        <h1>Admin Dashboard</h1>
        <p>Selamat datang, Anda memiliki akses penuh untuk mengelola data sistem.</p>
    </header>

    <div class="row g-4">
        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <a href="kritik_saran.php" class="stats-card">
                <div class="stat-content">
                    <i class="bi bi-chat-left-text-fill stat-icon"></i>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo $totalKritik; ?></div>
                        <div class="stat-text">Kritik & Saran</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <a href="semua_jalur.php" class="stats-card">
                 <div class="stat-content">
                    <i class="bi bi-sign-turn-right-fill stat-icon"></i>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo $totalJalur; ?></div>
                        <div class="stat-text">Data Jalur Kereta</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <a href="user_list.php" class="stats-card">
                <div class="stat-content">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo $totalUsers; ?></div>
                        <div class="stat-text">Jumlah Pengguna</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <a href="railtrackerlocation.php" class="stats-card">
                <div class="stat-content">
                    <i class="bi bi-geo-alt-fill stat-icon"></i>
                    <div class="stat-details">
                        <div class="stat-value">Cek</div>
                        <div class="stat-text">Monitoring Lokasi</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center" data-aos="fade-up" data-aos-delay="500">
            <a href="index.php" class="btn btn-user-dashboard">Lihat Tampilan User <i class="bi bi-arrow-right-circle-fill ms-2"></i></a>
        </div>
    </div>
</div>

<div class="footer">
    &copy; 2025 SIMKAPROV. Dinas Perhubungan Provinsi Sumatera Selatan. All rights reserved.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800
    });

    // [PERUBAHAN] Menambahkan script untuk efek scroll pada navbar
    const header = document.getElementById('header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>

</body>
</html>