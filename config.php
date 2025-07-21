<?php
$host = "localhost";      // Nama host (localhost atau IP server)
$user = "root";           // Username database Anda
$password = "";           // Password database Anda (kosong jika belum ada)
$dbname = "kereta_api_db";    // Nama database yang digunakan, sesuaikan dengan database Anda

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 agar mendukung karakter internasional dan emoji
$conn->set_charset("utf8mb4");
?>
