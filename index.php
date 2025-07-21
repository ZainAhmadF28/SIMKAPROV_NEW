<?php
session_start();
// require 'config.php'; // Baris ini dibutuhkan untuk mengambil data user dari database jika sudah ada.

$user_data = null;
// Cek jika ada sesi username yang aktif, lalu ambil datanya untuk ditampilkan di navbar
if (isset($_SESSION['username'])) {
    // Di aplikasi nyata, Anda akan mengambil data dari database di sini.
    // Untuk demonstrasi, kita akan membuat data dummy.
    $user_data = [
        'username' => $_SESSION['username'] ?? 'User Demo',
        'role' => $_SESSION['role'] ?? 'user' 
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Kereta Api - Provinsi Sumatera Selatan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* --- Global & Reset CSS --- */
        :root {
            --primary-color: #00529B; /* Biru Tua Dishub */
            --secondary-color: #FFC107; /* Kuning Aksen */
            --light-color: #FFFFFF;
            --dark-color: #2c3e50;
            --gray-color: #f4f7f6;
            --text-color: #555;
            --font-family: 'Poppins', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 80px; }
        body { font-family: var(--font-family); color: var(--text-color); line-height: 1.6; overflow-x: hidden; }
        a { text-decoration: none; color: var(--primary-color); transition: color 0.3s ease; }
        a:hover { color: var(--secondary-color); }
        img { max-width: 100%; height: auto; }
        .content-section { padding: 80px 5%; }
        .section-title { text-align: center; font-size: 2.5rem; color: var(--dark-color); margin-bottom: 50px; font-weight: 700; }

        /* --- Tombol (Button) --- */
        .btn { display: inline-block; padding: 12px 28px; border-radius: 50px; font-weight: 600; transition: all 0.4s ease-in-out; border: 2px solid transparent; }
        .btn-primary { background-color: var(--secondary-color); color: var(--dark-color); }
        .btn-primary:hover { background-color: transparent; border-color: var(--secondary-color); color: var(--secondary-color); transform: translateY(-3px); }
        .btn-secondary { background-color: transparent; color: var(--primary-color); border: 2px solid var(--primary-color); margin-top: 20px; }
        .btn-secondary:hover { background-color: var(--primary-color); border-color: var(--primary-color); color: var(--light-color); transform: translateY(-3px); }

        /* --- Header & Navbar --- */
        #header { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; transition: background-color 0.4s ease, box-shadow 0.4s ease; }
        
        /* [DIMODIFIKASI] Mengembalikan style navbar seperti semula */
        .navbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            height: 80px; 
            padding: 0 5%; 
            background-color: rgb(0 82 155 / 40%); /* Warna semi-transparan yang dipertahankan */
            transition: box-shadow 0.4s ease;
        }
        
        .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Hanya menambahkan bayangan saat scroll, warna tidak berubah */
        }
        
        /* Mengubah warna teks dan logo saat di-scroll karena background tetap gelap */
        .navbar.scrolled .nav-logo { color: var(--light-color); }
        .navbar.scrolled .nav-link { color: var(--light-color); }
        .navbar.scrolled .user-dropdown .dropdown-toggle { color: var(--light-color); }
        .navbar.scrolled .bar { background-color: var(--light-color); }
        
        .nav-logo { font-size: 1.8rem; font-weight: 700; color: var(--light-color); display: flex; align-items: center; gap: 12px; }
        .logo-image { height: 45px; width: auto; }
        .nav-logo span { color: var(--secondary-color); }
        .nav-menu { display: flex; align-items: center; list-style: none; gap: 30px; margin-bottom: 0; }
        .nav-link { font-weight: 600; color: var(--light-color); padding: 5px 0; border-bottom: 2px solid transparent; }
        .nav-link:hover, .navbar .nav-link:hover { color: var(--secondary-color) !important; border-bottom-color: var(--secondary-color); }
        
        /* Style untuk tombol Login & Dropdown User */
        .login-link { display: flex; align-items: center; gap: 8px; padding: 8px 15px; background-color: rgba(255, 255, 255, 0.2); border-radius: 20px; transition: all 0.3s ease; color: var(--light-color) !important; }
        .navbar .login-link:hover { background-color: var(--secondary-color); color: var(--dark-color) !important; border-bottom-color: transparent; }
        
        .user-dropdown .dropdown-toggle { display: flex; align-items: center; gap: 8px; color: var(--light-color); }
        .user-dropdown .dropdown-menu { border-radius: 10px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); }
        .dropdown-item:hover { background-color: #f8f9fa; }


        .hamburger { display: none; cursor: pointer; }
        .bar { display: block; width: 25px; height: 3px; margin: 5px auto; background-color: var(--light-color); transition: all 0.3s ease-in-out; }

        /* Sisa CSS tetap sama */
        .hero-section { height: 100vh; display: flex; justify-content: center; align-items: center; text-align: center; color: var(--light-color); position: relative; background: url('assets/img/background.jpg') no-repeat center center/cover; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 82, 155, 0.65); }
        .hero-content { z-index: 2; max-width: 800px; animation: fadeIn 1.5s ease-in-out; }
        .hero-title { font-size: 3.5rem; font-weight: 700; margin-bottom: 20px; }
        .hero-subtitle { font-size: 1.2rem; margin-bottom: 40px; font-weight: 300; }
        #layanan { background-color: var(--gray-color); }
        .layanan-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .layanan-card { background-color: var(--light-color); padding: 40px 30px; text-align: center; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; justify-content: space-between; }
        .layanan-card:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0, 82, 155, 0.15); }
        .card-icon { font-size: 3rem; color: var(--primary-color); margin-bottom: 20px; transition: color 0.3s ease; }
        .layanan-card:hover .card-icon { color: var(--secondary-color); }
        .layanan-card h3 { font-size: 1.4rem; color: var(--dark-color); margin-bottom: 10px; }
        .statistik-section { background: url('https://images.unsplash.com/photo-1560121131-2323c2a865b2?q=80&w=1974&auto=format&fit=crop') no-repeat center center/cover; position: relative; padding: 100px 5%; color: var(--light-color); text-align: center; }
        .statistik-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(44, 62, 80, 0.85); }
        .statistik-content { position: relative; z-index: 2; }
        .section-title-light { font-size: 2.5rem; color: var(--light-color); margin-bottom: 50px; font-weight: 700; }
        .statistik-wrapper { display: flex; justify-content: space-around; flex-wrap: wrap; gap: 30px; }
        .stat-item { flex: 1; min-width: 200px; }
        .stat-icon { font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 15px;}
        .stat-number { font-size: 3.5rem; font-weight: 700; }
        .stat-item p { font-size: 1.1rem; font-weight: 300; }
        .berita-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        a.berita-card-link { text-decoration: none; color: inherit; display: block; }
        .berita-card { background: var(--light-color); border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; }
        a.berita-card-link:hover .berita-card { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0, 82, 155, 0.15); }
        .berita-card img { width: 100%; height: 220px; object-fit: cover; }
        .berita-content { padding: 25px; }
        .berita-tanggal { font-size: 0.85rem; color: var(--primary-color); font-weight: 600; margin-bottom: 10px; display: block; }
        .berita-content h3 { font-size: 1.3rem; color: var(--dark-color); margin-bottom: 15px; }
        .footer { background-color: var(--dark-color); color: #ccc; padding: 60px 5% 20px; }
        .footer-container { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px; margin-bottom: 40px; }
        .footer-container h4 { color: var(--light-color); font-size: 1.2rem; margin-bottom: 20px; position: relative; }
        .footer-container h4::after { content: ''; position: absolute; left: 0; bottom: -8px; width: 40px; height: 2px; background-color: var(--secondary-color); }
        .footer-about, .footer-links, .footer-social { flex: 1; min-width: 250px; }
        .footer-links ul { list-style: none; padding-left: 0; }
        .footer-links ul li a { color: #ccc; transition: all 0.3s ease; }
        .footer-links ul li a:hover { color: var(--secondary-color); padding-left: 5px; }
        .footer-social a { color: #ccc; font-size: 1.5rem; margin-right: 15px; transition: color 0.3s ease; }
        .footer-social a:hover { color: var(--secondary-color); }
        .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid #444; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-on-scroll { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
        .animate-on-scroll.visible { opacity: 1; transform: translateY(0); }
        .layanan-container .animate-on-scroll:nth-child(2), .statistik-wrapper .animate-on-scroll:nth-child(2), .berita-container .berita-card-link:nth-child(2) { transition-delay: 0.2s; }
        .layanan-container .animate-on-scroll:nth-child(3), .statistik-wrapper .animate-on-scroll:nth-child(3), .berita-container .berita-card-link:nth-child(3) { transition-delay: 0.4s; }
        .btn-floating { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background-color: var(--secondary-color); color: var(--dark-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 15px rgba(0,0,0,0.3); text-decoration: none; transition: all 0.3s ease; z-index: 999; }
        .btn-floating:hover { transform: scale(1.1); color: var(--dark-color); }
        
        @media (max-width: 992px) { .layanan-container, .berita-container { grid-template-columns: 1fr; } .hero-title { font-size: 2.8rem; } }
        @media (max-width: 768px) {
            .nav-menu { position: fixed; z-index: 999; left: -100%; top: 80px; flex-direction: column; background-color: var(--light-color); width: 100%; height: calc(100vh - 80px); text-align: center; transition: 0.3s; gap: 0; padding: 20px 0; align-items: center; }
            .nav-menu.active { left: 0; }
            .nav-item { margin: 16px 0; width: 100%; }
            .nav-link { color: var(--dark-color); display: block; padding: 10px 0; }
            .navbar.scrolled .nav-link { color: var(--dark-color); } /* Di mobile, saat menu aktif, linknya gelap */
            .user-dropdown .dropdown-toggle { color: var(--dark-color); }
            .login-link { color: var(--light-color) !important; background-color: var(--primary-color); padding: 10px 20px; display: inline-flex; }
            .hamburger { display: block; z-index: 1001; }
            .hamburger.active .bar { background-color: var(--dark-color); } /* Saat aktif di mobile, bar jadi gelap */
            .hamburger.active .bar:nth-child(2) { opacity: 0; }
            .hamburger.active .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
            .hamburger.active .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
            .navbar.scrolled .hamburger.active .bar { background-color: var(--dark-color); }
            .hero-title { font-size: 2.2rem; }
            .hero-subtitle { font-size: 1rem; }
            .section-title, .section-title-light { font-size: 2rem; }
            .nav-logo { font-size: 1.5rem; }
            .logo-image { height: 40px; }
        }
    </style>
</head>
<body>

    <header id="header">
        <nav class="navbar">
            <a href="#" class="nav-logo">
                <img src="assets/img/logo_dishub2.png" alt="Logo Dinas Perhubungan" class="logo-image">
                <div class="logo-text">SIMKA<span>PROV</span></div>
            </a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Beranda</a></li>
                <li class="nav-item"><a href="#layanan" class="nav-link">Layanan</a></li>
                <li class="nav-item"><a href="#berita" class="nav-link">Berita</a></li>
                <li class="nav-item"><a href="#kontak" class="nav-link">Kontak</a></li>
                
                <?php if (isset($user_data)): ?>
                    <li class="nav-item dropdown user-dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            Halo, <?php echo htmlspecialchars($user_data['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if ($user_data['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="admin_dashboard.php">Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="nav-link login-link">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
    </header>

    <main>
        <section id="hero" class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1 class="hero-title">Sistem Informasi Kereta Api Provinsi Sumatera Selatan</h1>
                <p class="hero-subtitle">Mewujudkan sistem transportasi yang terintegrasi, aman, dan efisien untuk semua.</p>
                <a href="#layanan" class="btn btn-primary">Jelajahi Layanan Kami</a>
            </div>
        </section>

        <section id="layanan" class="content-section">
            <h2 class="section-title animate-on-scroll">Layanan Unggulan</h2>
            <div class="layanan-container">
                <div class="layanan-card animate-on-scroll">
                    <div>
                        <i class="fas fa-calendar-alt card-icon"></i>
                        <h3>Jadwal Kereta Api</h3>
                        <p>Lihat jadwal keberangkatan dan kedatangan kereta api terbaru di seluruh Indonesia.</p>
                    </div>
                    <a href="https://www.kai.id" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Lihat Jadwal</a>
                </div>
                
                <div class="layanan-card animate-on-scroll">
                    <div>
                        <i class="fas fa-train-subway card-icon"></i>
                        <h3>Daftar Stasiun Kereta Api</h3>
                        <p>Temukan informasi lengkap mengenai stasiun kereta api yang ada di wilayah Anda.</p>
                    </div>
                    <a href="<?php echo isset($_SESSION['username']) ? 'daftarstasiun.php' : 'login.php'; ?>" class="btn btn-secondary">Lihat Daftar</a>
                </div>
                
                <div class="layanan-card animate-on-scroll">
                    <div>
                        <i class="fas fa-route card-icon"></i>
                        <h3>Informasi Jalur Kereta Api</h3>
                        <p>Pantau lokasi dan jalur kereta api secara real-time untuk perjalanan yang lebih terencana.</p>
                    </div>
                    <a href="<?php echo isset($_SESSION['username']) ? 'railtrackerlocation.php' : 'login.php'; ?>" class="btn btn-secondary">Lihat Jalur</a>
                </div>
            </div>
        </section>
        
        <section id="statistik" class="statistik-section">
            <div class="statistik-overlay"></div>
            <div class="statistik-content">
                 <h2 class="section-title-light animate-on-scroll">Data & Fakta Perkeretaapian Sumsel</h2>
                 <div class="statistik-wrapper">
                    <div class="stat-item animate-on-scroll">
                        <i class="fas fa-building-columns stat-icon"></i>
                        <h3 class="stat-number" data-target="78">0</h3>
                        <p>Stasiun Aktif di Divre III</p>
                    </div>
                     <div class="stat-item animate-on-scroll">
                        <i class="fas fa-train stat-icon"></i>
                        <h3 class="stat-number" data-target="60000">0</h3>
                        <p>Tonase Batu Bara / Hari</p>
                    </div>
                     <div class="stat-item animate-on-scroll">
                        <i class="fas fa-route stat-icon"></i>
                        <h3 class="stat-number" data-target="865">0</h3>
                        <p>KM Jalur Kereta Aktif</p>
                    </div>
                 </div>
            </div>
        </section>

        <section id="berita" class="content-section">
            <h2 class="section-title animate-on-scroll">Berita & Informasi Terkini</h2>
            <div class="berita-container">
                <a href="https://sumsel.antaranews.com/berita/799696/kai-divre-iii-palembang-siapkan-85188-tiket-kereta-api-selama-libur-idul-adha" target="_blank" rel="noopener noreferrer" class="berita-card-link animate-on-scroll">
                    <article class="berita-card">
                        <img src="https://images.unsplash.com/photo-1527003833538-690412652b45?q=80&w=1966&auto=format&fit=crop" alt="Suasana Stasiun Kereta Api">
                        <div class="berita-content">
                            <span class="berita-tanggal">ANTARA SUMSEL - 06 JUNI 2024</span>
                            <h3>KAI Divre III Palembang siapkan 85.188 tiket kereta api selama libur Idul Adha</h3>
                        </div>
                    </article>
                </a>
                 <a href="https://www.detik.com/sumbagsel/bisnis/d-7243936/lrt-sumsel-bakal-diperpanjang-hingga-ke-terminal-alang-alang-lebar" target="_blank" rel="noopener noreferrer" class="berita-card-link animate-on-scroll">
                    <article class="berita-card">
                        <img src="https://images.unsplash.com/photo-1627883935201-3bf686252cee?q=80&w=2070&auto=format&fit=crop" alt="LRT Palembang">
                        <div class="berita-content">
                           <span class="berita-tanggal">DETIK SUMBAGSEL - 15 MARET 2024</span>
                           <h3>LRT Sumsel Bakal Diperpanjang hingga ke Terminal Alang-Alang Lebar</h3>
                        </div>
                    </article>
                 </a>
                  <a href="https://www.liputan6.com/regional/read/5477028/lokomotif-dan-gerbong-ka-babaranjang-anlok-di-muara-enim-sumsel-perjalanan-ka-bukit-serelo-dibatalkan" target="_blank" rel="noopener noreferrer" class="berita-card-link animate-on-scroll">
                     <article class="berita-card">
                        <img src="https://images.unsplash.com/photo-1601140939359-01a2a8e8cec3?q=80&w=2070&auto=format&fit=crop" alt="Kereta Api Batu Bara">
                        <div class="berita-content">
                           <span class="berita-tanggal">LIPUTAN6.COM - 11 DESEMBER 2023</span>
                           <h3>Lokomotif dan Gerbong KA Babaranjang Anjlok di Muara Enim Sumsel</h3>
                        </div>
                    </article>
                  </a>
            </div>
        </section>
    </main>

    <footer id="kontak" class="footer">
        <div class="footer-container">
            <div class="footer-about">
                <h4>SIMKA-PROV SUMSEL</h4>
                <p>Jl. Kapten A. Rivai, Palembang</p>
                <p>Email: dishubsumsel2019@gmail.com<br>Telepon: (0711) 352005 - 363125</p>
            </div>
            <div class="footer-links">
                <h4>Tautan Cepat</h4>
                <ul>
                    <li><a href="#hero">Beranda</a></li>
                    <li><a href="#layanan">Layanan</a></li>
                    <li><a href="#berita">Berita</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h4>Ikuti Kami</h4>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 SIMKA-PROV Sumatera Selatan. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
    
    <a href="<?php echo isset($_SESSION['username']) ? 'tambah_kritik.php' : 'login.php'; ?>" class="btn-floating" title="Kirim Kritik & Saran">
        <i class="fas fa-comment-dots"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
            
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            const navLinks = document.querySelectorAll('.nav-link');

            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
            });

            navLinks.forEach(link => {
                if (!link.classList.contains('dropdown-toggle')) {
                    link.addEventListener('click', () => {
                        if(navMenu.classList.contains('active')){
                            hamburger.classList.remove('active');
                            navMenu.classList.remove('active');
                        }
                    });
                }
            });
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            const elementsToAnimate = document.querySelectorAll('.animate-on-scroll');
            elementsToAnimate.forEach(el => observer.observe(el));
            
            const statObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        const target = +counter.getAttribute('data-target');
                        let count = 0;
                        const speed = 200; 
                        
                        const updateCount = () => {
                            const increment = target / speed;
                            if(count < target) {
                                count += increment;
                                if(count > target) count = target;
                                counter.innerText = Math.ceil(count).toLocaleString('id-ID');
                                requestAnimationFrame(updateCount);
                            } else {
                                counter.innerText = target.toLocaleString('id-ID');
                            }
                        };

                        updateCount();
                        observer.unobserve(counter);
                    }
                });
            }, {
                threshold: 0.5
            });

            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(number => statObserver.observe(number));

        });
    </script>
</body>
</html>