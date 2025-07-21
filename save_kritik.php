<?php
// save_kritik.php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "kereta_api_db";

// koneksi database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// ambil data dari POST
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

// validasi sederhana
if (!$nama || !$email || !$pesan) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Email tidak valid']);
    exit;
}

// prepared statement untuk insert
$stmt = $conn->prepare("INSERT INTO kritik_saran (nama, email, pesan) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $email, $pesan);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Terima kasih atas kritik dan saran Anda!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
}

$stmt->close();
$conn->close();
