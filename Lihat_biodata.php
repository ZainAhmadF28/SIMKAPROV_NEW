<?php
session_start();
require 'config.php'; // Koneksi database

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil biodata pengguna berdasarkan username
$stmt = $conn->prepare("SELECT * FROM biodata WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$biodata = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Saya</title>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: url('img/b2.svg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            width: 450px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        h2 {
            margin-bottom: 20px;
            color: #0078A8;
        }
        
        .biodata {
            text-align: left;
            margin-top: 10px;
        }
        
        .biodata p {
            font-size: 16px;
            margin: 5px 0;
        }
        
        .btn-container {
            margin-top: 20px;
        }
        
        .btn {
            background-color: #0078A8;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100px;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            margin: 5px;
        }
        
        .btn:hover {
            background-color: #005f87;
        }
        
        .btn-danger {
            background-color: red;
        }
        
        .btn-danger:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Biodata Saya</h2>

        <?php if ($biodata): ?>
            <div class="biodata">
                <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($biodata['nama_lengkap']); ?></p>
                <p><strong>Tempat Lahir:</strong> <?php echo htmlspecialchars($biodata['tempat_lahir']); ?></p>
                <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($biodata['tanggal_lahir']); ?></p>
                <p><strong>Golongan Darah:</strong> <?php echo htmlspecialchars($biodata['golongan_darah']); ?></p>
                <p><strong>Domisili:</strong> <?php echo htmlspecialchars($biodata['domisili']); ?></p>
                <p><strong>Pekerjaan:</strong> <?php echo htmlspecialchars($biodata['pekerjaan']); ?></p>
                <p><strong>Nomor HP:</strong> <?php echo htmlspecialchars($biodata['nomor_hp']); ?></p>
            </div>
        <?php else: ?>
            <p><strong>Biodata belum diisi.</strong></p>
        <?php endif; ?>

        <div class="btn-container">
            <a href="edit_biodata.php" class="btn">Edit</a>
            <a href="index.php" class="btn btn-danger">Kembali</a>
        </div>
    </div>
</body>
</html>
