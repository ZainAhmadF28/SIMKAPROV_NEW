<?php
header('Content-Type: application/json');

// Ganti dengan konfigurasi database kamu jika berbeda
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kereta_api_db";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal koneksi ke database']);
        exit;
    }

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($email) || empty($pesan)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
        exit;
    }

    // Escape untuk keamanan
    $nama = $conn->real_escape_string($nama);
    $email = $conn->real_escape_string($email);
    $pesan = $conn->real_escape_string($pesan);

    $sql = "INSERT INTO kritik_saran (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Terima kasih atas kritik dan saran Anda!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid']);
}
