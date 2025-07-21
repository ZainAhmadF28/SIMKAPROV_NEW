<?php
session_start();
require 'config.php'; // Menghubungkan ke database

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari form
    $username = $_SESSION['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $golongan_darah = $_POST['golongan_darah'];
    $domisili = $_POST['domisili'];
    $pekerjaan = $_POST['pekerjaan'];
    $nomor_hp = $_POST['nomor_hp'];

    // Periksa apakah data sudah ada di database
    $checkQuery = $conn->prepare("SELECT * FROM biodata WHERE username = ?");
    $checkQuery->bind_param("s", $username);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='error'>Biodata sudah ada, gunakan fitur edit.</div>";
    } else {
        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO biodata (username, nama_lengkap, tempat_lahir, tanggal_lahir, golongan_darah, domisili, pekerjaan, nomor_hp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $username, $nama_lengkap, $tempat_lahir, $tanggal_lahir, $golongan_darah, $domisili, $pekerjaan, $nomor_hp);

        if ($stmt->execute()) {
            echo "<script>alert('Biodata berhasil disimpan!'); window.location.href='index.php';</script>";
        } else {
            $message = "<div class='error'>Terjadi kesalahan: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
    $checkQuery->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Biodata</title>
    <style>
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
        }
        h2 {
            margin-bottom: 20px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #0078A8;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #005f87;
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Biodata</h2>
        <?php echo $message; ?>
        <form method="POST">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" required>
            <input type="date" name="tanggal_lahir" required>
            <select name="golongan_darah" required>
                <option value="">Pilih Golongan Darah</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>
            <input type="text" name="domisili" placeholder="Domisili" required>
            <input type="text" name="pekerjaan" placeholder="Pekerjaan">
            <input type="text" name="nomor_hp" placeholder="Nomor HP" required>
            <button type="submit">Simpan Biodata</button>
        </form>
    </div>
</body>
</html>
