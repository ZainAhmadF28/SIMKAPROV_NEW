<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Kalau belum login atau bukan admin, arahkan ke login
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin - SIMKAPROV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      body { background-color: #f4f6f9; }
      .navbar { background-color: #003566; }
      .navbar-brand, .nav-link, .logout-btn { color: white !important; }
      .logout-btn:hover { color: #ffc107 !important; }
    </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">Admin Dashboard</a>
      <div class="d-flex">
        <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="btn btn-outline-warning logout-btn">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <h1>Selamat datang di Dashboard Admin SIMKAPROV</h1>
    <p>Di sini Anda dapat mengelola data dan fitur aplikasi.</p>

    <!-- Contoh link ke RailTracker Admin -->
    <a href="railtracker.php" class="btn btn-primary mb-3">Kelola RailTracker</a>

    <!-- Tambahkan konten admin lain di sini -->
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
