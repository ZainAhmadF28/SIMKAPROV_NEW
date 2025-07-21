<?php
session_start();
include 'config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validasi dasar
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        // Cek apakah email atau username sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username atau email sudah digunakan.";
        } else {
            // Simpan data baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($insert->execute()) {
                $success = "Registrasi berhasil. Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat menyimpan data.";
            }

            $insert->close();
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi | SIMKAPROV</title>
    <style>
        body {
            background: #eef2f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            width: 100%;
            max-width: 460px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .register-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #28a745;
            outline: none;
        }

        .btn-register {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-register:hover {
            background: #218838;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error-message {
            color: #dc3545;
        }

        .success-message {
            color: #28a745;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            margin-top: 15px;
            color: #777;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Registrasi SIMKAPROV</h2>

    <?php if (!empty($error)) : ?>
        <div class="message error-message"><?= htmlspecialchars($error); ?></div>
    <?php elseif (!empty($success)) : ?>
        <div class="message success-message"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>
        </div>

        <div class="form-group">
            <label for="email">Email Aktif</label>
            <input type="email" id="email" name="email" placeholder="Contoh: user@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Konfirmasi Kata Sandi</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-register">Daftar Sekarang</button>
    </form>

    <div class="footer">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
    </div>
</div>

</body>
</html>
