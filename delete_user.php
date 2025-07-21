<?php
session_start();
require 'config.php';

// Cek login & role admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil ID user dari parameter GET
$id = $_GET['id'] ?? '';

if (empty($id) || !is_numeric($id)) {
    $_SESSION['error'] = "ID user tidak valid.";
    header("Location: manage_users.php");
    exit();
}

// Jangan izinkan admin menghapus akun sendiri (opsional)
if ($id == $_SESSION['id']) {
    $_SESSION['error'] = "Anda tidak dapat menghapus akun sendiri.";
    header("Location: manage_users.php");
    exit();
}

// Query hapus user berdasarkan ID
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "User berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus user. Coba lagi.";
}

$stmt->close();
header("Location: manage_users.php");
exit();
