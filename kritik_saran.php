<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari session untuk ditampilkan di navbar
$username = $_SESSION['username'];
$stmt_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();


// Ambil semua kritik dan saran dari database, urutkan berdasarkan tanggal DESC
$sql = "SELECT * FROM kritik_saran ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kritik dan Saran - SIMKAPROV</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #00529B;
            --secondary-color: #FFC107;
            --light-color: #FFFFFF;
            --dark-color: #2c3e50;
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
        .navbar-brand span { font-weight: 600; }
        
        .page-header {
            padding: 3rem 0 2rem 0;
            text-align: center;
        }
        .page-header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .feedback-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 1.5rem;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .feedback-header .username {
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        .feedback-header .username i {
            margin-right: 10px;
            color: var(--secondary-color);
        }
        .feedback-header .timestamp {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .feedback-body .feedback-item {
            margin-bottom: 1rem;
        }
        .feedback-body h6 {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }
        .feedback-body h6 i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
        .feedback-body p {
            background: rgba(0,0,0,0.2);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.95rem;
            margin: 0;
        }

        .btn-back {
            background: var(--secondary-color);
            color: #333;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 30px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }
        .btn-back:hover {
            background: var(--light-color);
            transform: scale(1.05);
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="assets/img/logo_dishub2.png" class="logo-img me-2" alt="Logo" />
            <span>SIMKAPROV</span>
        </a>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fw-bold d-none d-md-inline">Halo, <?php echo htmlspecialchars($user['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="biodata.php">Biodata</a></li>
                <li><a class="dropdown-item" href="pengaturan.php">Pengaturan</a></li>
                <li><a class="dropdown-item" href="cek_jalur_kereta.php">Cek Jalur Kereta</a></li>
                <li><a class="dropdown-item" href="kritik_saran.php">Kritik & Saran</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <header class="page-header" data-aos="fade-down">
        <h1>Daftar Kritik dan Saran</h1>
    </header>

    <div class="feedback-list">
        <?php
        if ($result->num_rows > 0) {
            $delay = 0;
            while ($row = $result->fetch_assoc()) {
                $delay += 100; // Tambah delay untuk setiap kartu
                echo "<div class='feedback-card' data-aos='fade-up' data-aos-delay='{$delay}'>
                        <div class='feedback-header'>
                            <span class='username'><i class='bi bi-person-fill'></i>" . htmlspecialchars($row['username']) . "</span>
                            <span class='timestamp'><i class='bi bi-clock-fill'></i> " . date('d M Y, H:i', strtotime($row['tanggal'])) . "</span>
                        </div>
                        <div class='feedback-body'>
                            <div class='feedback-item'>
                                <h6><i class='bi bi-chat-quote-fill'></i>Kritik</h6>
                                <p>" . htmlspecialchars($row['kritik']) . "</p>
                            </div>
                            <div class='feedback-item'>
                                <h6><i class='bi bi-lightbulb-fill'></i>Saran</h6>
                                <p>" . htmlspecialchars($row['saran']) . "</p>
                            </div>
                        </div>
                    </div>";
            }
        } else {
            echo "<div class='feedback-card text-center' data-aos='fade-up'>
                    <p class='mb-0'>Belum ada kritik dan saran yang masuk.</p>
                  </div>";
        }
        $conn->close();
        ?>
    </div>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-back"><i class="bi bi-arrow-left-circle-fill me-2"></i> Kembali ke Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
    });
</script>

</body>
</html>