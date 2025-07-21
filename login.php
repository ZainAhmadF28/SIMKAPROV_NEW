<?php
session_start();
require 'config.php'; // Pastikan file koneksi database ini ada dan benar

// Fungsi untuk mengenkripsi data
function encryptData($data, $encryption_key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($iv . $encryptedData);
}

// Fungsi untuk mendekripsi data
function decryptData($encryptedData, $encryption_key) {
    $data = base64_decode($encryptedData);
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, 'aes-256-cbc', $encryption_key, 0, $iv);
}

$encryption_key = 'my-secret-key-123';

// 1. LOGIC REDIRECT JIKA SUDAH LOGIN
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'user') {
        header("Location: index.php"); // Jika user sudah login, arahkan ke index
        exit();
    }
}

// ... (sisa kode PHP tetap sama) ...
$login_error = '';
$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        if (empty($username) || empty($email) || empty($password)) {
            $register_error = "Username, Email, dan Password wajib diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_error = "Format email tidak valid.";
        } elseif (strlen($password) < 6) {
            $register_error = "Password minimal 6 karakter.";
        } else {
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $register_error = "Username sudah digunakan.";
            } else {
                $encrypted_email = encryptData($email, $encryption_key);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = "user";
                $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt_insert->bind_param("ssss", $username, $encrypted_email, $hashed_password, $role);
                if ($stmt_insert->execute()) {
                    $register_success = "Registrasi berhasil! Silakan login.";
                } else {
                    if ($conn->errno == 1062) {
                         $register_error = "Email sudah digunakan.";
                    } else {
                         $register_error = "Terjadi kesalahan saat menyimpan data.";
                    }
                }
                $stmt_insert->close();
            }
            $stmt_check->close();
        }
    }

    if (isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $login_error = "Username dan Password wajib diisi!";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            if ($stmt === false) {
                $login_error = "Terjadi kesalahan pada database.";
            } else {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        if ($user['role'] === 'admin') {
                            header("Location: admin_dashboard.php");
                            exit();
                        } else {
                            // [PERUBAHAN UTAMA] Arahkan user ke index.php setelah login
                            header("Location: index.php");
                            exit();
                        }
                    } else {
                        $login_error = "Password salah!";
                    }
                } else {
                    $login_error = "Username tidak ditemukan!";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login & Registrasi - SIMKA PROV</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #00529B;
            --secondary-color: #FFC107;
            --light-color: #FFFFFF;
            --gray-color: #f4f7f6;
            --text-color: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
            list-style: none;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: var(--gray-color);
        }

        .container {
            position: relative;
            width: 850px;
            height: 550px;
            background: #fff;
            margin: 20px;
            border-radius: 30px;
            box-shadow: 0 0 30px rgba(0, 0, 0, .2);
            overflow: hidden;
        }

        .container h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .container p {
            font-size: 14.5px;
            margin: 15px 0;
            color: #555;
        }

        form {
            width: 100%;
        }

        .form-box {
            position: absolute;
            right: 0;
            width: 50%;
            height: 100%;
            background: #fff;
            display: flex;
            align-items: center;
            color: var(--text-color);
            text-align: center;
            padding: 40px;
            z-index: 1;
            transition: .6s ease-in-out;
        }

        .container.active .form-box.login {
            right: 50%;
            visibility: hidden;
            transition-delay: 0s;
        }

        .form-box.register {
            visibility: hidden;
        }

        .container.active .form-box.register {
            visibility: visible;
            right: 50%;
        }


        .input-box {
            position: relative;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: var(--text-color);
            font-weight: 500;
        }

        .input-box input::placeholder {
            color: #888;
            font-weight: 400;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .forgot-link {
            margin: -15px 0 15px;
        }

        .forgot-link a {
            font-size: 14.5px;
            color: var(--text-color);
        }

        .btn {
            width: 100%;
            height: 48px;
            background: var(--primary-color);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #00417a;
        }

        .toggle-box {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .toggle-box::before {
            content: '';
            position: absolute;
            left: -250%;
            width: 300%;
            height: 100%;
            background: var(--primary-color);
            border-radius: 150px;
            z-index: 2;
            transition: 1.8s ease-in-out;
        }

        .container.active .toggle-box::before {
            left: 50%;
        }

        .toggle-panel {
            position: absolute;
            width: 50%;
            height: 100%;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 40px;
            z-index: 2;
            transition: .6s ease-in-out;
        }
        
        .toggle-panel h1 { color: var(--light-color); }
        .toggle-panel p { color: rgba(255,255,255,0.9); }

        .toggle-panel.toggle-left {
            left: 0;
            transition-delay: 1.2s;
        }

        .container.active .toggle-panel.toggle-left {
            left: -50%;
            transition-delay: .6s;
        }

        .toggle-panel.toggle-right {
            right: -50%;
            transition-delay: .6s;
        }

        .container.active .toggle-panel.toggle-right {
            right: 0;
            transition-delay: 1.2s;
        }

        .toggle-panel p {
            margin-bottom: 20px;
        }

        .toggle-panel .btn {
            width: 160px;
            height: 46px;
            background: transparent;
            border: 2px solid #fff;
            box-shadow: none;
        }
        .toggle-panel .btn:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #000;
        }

        /* Pesan Error & Sukses */
        .message {
            padding: 8px;
            margin: -15px 0 15px 0;
            border-radius: 5px;
            color: white;
            font-size: 13px;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }
        .error { background-color: #e74c3c; }
        .success { background-color: #2ecc71; }


        @media screen and (max-width: 850px) {
            .container {
                width: 100%;
                height: 100%;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }
        }

        @media screen and (max-width: 650px) {
            .form-box {
                width: 100%;
                height: auto;
                padding: 40px 30px;
                position: absolute;
                bottom: 0;
                transition: 1s ease-in-out;
            }

            .form-box.login {
                height: 70%;
            }
            .form-box.register {
                height: 80%;
            }
            
            .container.active .form-box.login {
                bottom: -100%;
                right: 0;
            }

            .form-box.register {
                bottom: -100%;
                right: 0;
            }

            .container.active .form-box.register {
                bottom: 0;
            }


            .toggle-box::before {
                left: 0;
                top: -270%;
                width: 100%;
                height: 300%;
                border-radius: 100px;
            }

            .container.active .toggle-box::before {
                top: 70%;
            }

            .toggle-panel {
                width: 100%;
                height: 30%;
            }

            .container.active .toggle-panel.toggle-left {
                top: -40%;
                left: 0;
            }

            .toggle-panel.toggle-left {
                top: 0;
            }

            .toggle-panel.toggle-right {
                bottom: -40%;
                right: 0;
            }

            .container.active .toggle-panel.toggle-right {
                bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="form-box login">
            <form method="post" action="">
                <h1>Login</h1>
                <?php if ($login_error): ?>
                    <div class="message error"><?= htmlspecialchars($login_error) ?></div>
                <?php endif; ?>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required />
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required />
                    <i class="fas fa-lock"></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Lupa Password?</a>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>

        <div class="form-box register">
            <form method="post" action="">
                <h1>Registrasi</h1>
                 <?php if (!empty($register_error)) : ?>
                    <div class="message error"><?= htmlspecialchars($register_error); ?></div>
                <?php elseif (!empty($register_success)) : ?>
                    <div class="message success"><?= htmlspecialchars($register_success); ?></div>
                <?php endif; ?>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required />
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required />
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password (min. 6 karakter)" required />
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" name="register" class="btn">Daftar</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Halo, Selamat Datang!</h1>
                <p>Belum punya akun? Daftarkan diri Anda dan mulailah perjalanan bersama kami.</p>
                <button class="btn register-btn">Daftar</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Selamat Datang Kembali!</h1>
                <p>Untuk tetap terhubung dengan kami, silakan login dengan akun Anda.</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.querySelector('.container');
        const registerBtn = document.querySelector('.register-btn');
        const loginBtn = document.querySelector('.login-btn');

        registerBtn.addEventListener('click', () => {
            container.classList.add('active');
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove('active');
        });
        
        // Otomatis tampilkan panel registrasi jika ada pesan error/sukses dari PHP
        <?php if (!empty($register_error) || !empty($register_success)): ?>
            container.classList.add('active');
        <?php endif; ?>
    </script>
</body>
</html>