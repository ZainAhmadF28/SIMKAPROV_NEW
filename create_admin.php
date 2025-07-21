<?php
// create_admin.php

include 'config.php'; // koneksi PDO ke database

$username = 'tias';
$password_plain = 'tiasdishub';
$role = 'admin';

// Hash password
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// Cek apakah user sudah ada
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    echo "User '$username' sudah ada di database.";
} else {
    // Insert user baru
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $insert = $stmt->execute([$username, $password_hash, $role]);

    if ($insert) {
        echo "User admin '$username' berhasil dibuat dengan password '$password_plain'.";
    } else {
        echo "Gagal membuat user admin.";
    }
}
?>
