<?php
session_start();
require 'config.php';

// Cek sesi admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data admin untuk navbar
$username_session = $_SESSION['username'];
$stmt_nav = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt_nav->bind_param("s", $username_session);
$stmt_nav->execute();
$result_nav = $stmt_nav->get_result();
$user_nav = $result_nav->fetch_assoc();
$stmt_nav->close();

$error = '';
$success = '';

// Ambil ID user dari query string
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: user_list.php");
    exit();
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || ($role !== 'admin' && $role !== 'user')) {
        $error = "Username, Email, dan Role wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Cek username/email sudah ada untuk user lain
        $stmtCheck = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmtCheck->bind_param("ssi", $username, $email, $id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($resCheck->num_rows > 0) {
            $error = "Username atau email sudah digunakan oleh user lain.";
        } else {
            if ($password !== '') {
                if(strlen($password) < 6) {
                    $error = "Password baru minimal 6 karakter.";
                } else {
                    // Update dengan password baru
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmtUpdate = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
                    $stmtUpdate->bind_param("ssssi", $username, $email, $role, $hashed_password, $id);
                }
            } else {
                // Update tanpa password
                $stmtUpdate = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
                $stmtUpdate->bind_param("sssi", $username, $email, $role, $id);
            }

            if (empty($error) && isset($stmtUpdate) && $stmtUpdate->execute()) {
                $_SESSION['success'] = "Data user '" . htmlspecialchars($username) . "' berhasil diperbarui.";
                header("Location: user_list.php");
                exit();
            } else if(empty($error)) {
                $error = "Gagal memperbarui data user.";
            }
        }
    }
}

// Ambil data user yang akan diedit
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "User tidak ditemukan.";
    header("Location: user_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - SIMKAPROV</title>
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
        
        .form-control, .form-select {
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .form-control:focus, .form-select:focus {
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

        .btn-back {
            background: #f1f1f1;
            color: #555;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 30px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid #f1f1f1;
        }
        .btn-back:hover {
            background: #e9ecef;
            border-color: #e9ecef;
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="admin_dashboard.php">
            <img src="assets/img/logo_dishub2.png" class="logo-img me-2" alt="Logo" />
            <span>SIMKAPROV ADMIN</span>
        </a>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fw-bold d-none d-md-inline">Halo, <?php echo htmlspecialchars($user_nav['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card" data-aos="fade-up">
                <h1 class="text-center">Edit Data Pengguna</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="(Kosongkan jika tidak diubah)">
                        <div class="form-text">Isi hanya jika Anda ingin mengganti password pengguna ini.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="user_list.php" class="btn btn-back">Batal</a>
                        <button type="submit" name="update" class="btn btn-submit">
                            <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

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