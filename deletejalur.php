<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['error'] = "ID pengguna tidak valid.";
        header("Location: manage_users.php");
        exit();
    }

    // Cek apakah user dengan id tersebut ada dan bukan user yang sedang login
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        $_SESSION['error'] = "Pengguna tidak ditemukan.";
        header("Location: manage_users.php");
        exit();
    }

    $user = $res->fetch_assoc();

    if ($user['username'] === $_SESSION['username']) {
        $_SESSION['error'] = "Anda tidak bisa menghapus akun yang sedang digunakan.";
        header("Location: manage_users.php");
        exit();
    }

    // Delete user
    $stmtDel = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmtDel->bind_param("i", $id);
    if ($stmtDel->execute()) {
        $_SESSION['success'] = "Pengguna '{$user['username']}' berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus pengguna.";
    }

    header("Location: manage_users.php");
    exit();
} else {
    // Jika akses langsung tanpa POST, redirect
    header("Location: manage_users.php");
    exit();
}
