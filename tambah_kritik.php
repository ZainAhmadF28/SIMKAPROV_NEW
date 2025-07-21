<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username_session = $_SESSION['username'];
$user_nav = null;
$message = '';

// Ambil data user dari session untuk ditampilkan di navbar
$stmt_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt_user->bind_param("s", $username_session);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_nav = $result_user->fetch_assoc();
$stmt_user->close();

// Proses untuk menambah kritik dan saran
if (isset($_POST['submit'])) {
    // Menggunakan username dari sesi yang aktif
    $nama = $username_session; 
    $email = $_POST['email'];
    $kritik = $_POST['kritik'];
    $saran = $_POST['saran'];

    // Validasi form
    if (empty($email) || (empty($kritik) && empty($saran))) {
        $message = ['type' => 'danger', 'text' => 'Email dan salah satu dari Kritik atau Saran wajib diisi.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = ['type' => 'danger', 'text' => 'Format email tidak valid.'];
    } else {
        // [PERBAIKAN KEAMANAN] Menggunakan prepared statements
        $stmt = $conn->prepare("INSERT INTO kritik_saran (username, email, kritik, saran) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $kritik, $saran);

        if ($stmt->execute()) {
            $message = ['type' => 'success', 'text' => 'Terima kasih! Kritik dan saran Anda telah berhasil dikirim.'];
        } else {
            $message = ['type' => 'danger', 'text' => 'Gagal menyimpan data.'];
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kritik dan Saran - SIMKAPROV</title>
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

        .form-card {
            background: var(--light-color);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-card h1 {
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
        }
        
        .form-control {
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 82, 155, 0.25);
        }

        .btn-submit {
            background: var(--primary-color);
            color: var(--light-color);
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 30px;
            transition: all 0.3s ease;
            border: 2px solid var(--primary-color);
        }
        .btn-submit:hover {
            background: transparent;
            border-color: var(--secondary-color);
            color: var(--secondary-color);
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

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card" data-aos="fade-up">
                <h1 class="text-center">Formulir Kritik & Saran</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?>">
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Anda</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_nav['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="kritik" class="form-label">Kritik</label>
                        <textarea class="form-control" id="kritik" name="kritik" rows="4" placeholder="Tuliskan kritik Anda untuk sistem atau layanan..."></textarea>
                    </div>
                     <div class="mb-3">
                        <label for="saran" class="form-label">Saran</label>
                        <textarea class="form-control" id="saran" name="saran" rows="4" placeholder="Tuliskan saran Anda untuk pengembangan ke depan..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" name="submit" class="btn btn-submit">
                            <i class="bi bi-send-fill me-2"></i>Kirim Masukan
                        </button>
                    </div>
                </form>
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