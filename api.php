<?php
include 'config.php';  // Menghubungkan ke database

// Query untuk mengambil semua data jalur kereta api
$sql = "SELECT * FROM jalur_kereta";
$result = $conn->query($sql);

$jalur = array(); // Array untuk menampung hasil query
while($row = $result->fetch_assoc()) {
    $jalur[] = $row; // Menambahkan data ke array
}

// Mengembalikan data dalam format JSON
echo json_encode($jalur);

$conn->close();  // Menutup koneksi database
?>
