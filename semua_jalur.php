<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login dan memiliki role 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data admin dari session untuk ditampilkan di navbar
$username = $_SESSION['username'];
$stmt_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

// Proses untuk menghapus data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Gunakan prepared statement untuk keamanan
    $stmtDelete = $conn->prepare("DELETE FROM jalur_kereta_admin WHERE id = ?");
    $stmtDelete->bind_param("i", $id);
    if ($stmtDelete->execute()) {
        header("Location: semua_jalur.php");
        exit();
    } else {
        echo "Error: " . $stmtDelete->error;
    }
    $stmtDelete->close();
}

// Ambil data jalur dari database
$sql = "SELECT * FROM jalur_kereta_admin ORDER BY nama_jalan";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jalur Kereta - SIMKAPROV</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #00529B;
            --secondary-color: #FFC107;
            --light-color: #FFFFFF;
            --dark-color: #2c3e50;
            --page-bg: #4A89DC; 
            --font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--page-bg);
            font-family: var(--font-family);
            color: var(--dark-color);
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.75rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .logo-img { height: 45px; width: auto; }
        .navbar-brand span { font-weight: 600; color: var(--light-color); }
        .navbar .dropdown-toggle { color: var(--light-color); }

        .page-header {
            background-color: var(--light-color);
            padding: 1.5rem 2.5rem;
            margin-top: 1.5rem;
            border-radius: 20px 20px 0 0;
        }
        .page-header h1 {
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--primary-color);
        }
        .btn-add {
            background: var(--secondary-color);
            color: #333;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-add:hover {
            background: #e9b207;
        }
        
        .table-container {
            background: var(--light-color);
            border-radius: 0 0 20px 20px;
            padding: 10px 2.5rem 2.5rem 2.5rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0 1rem;
        }

        .table thead th {
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #888;
            font-weight: 600;
        }

        .table tbody tr {
            background-color: #f8f9fa;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .table tbody td {
            border: none;
            vertical-align: middle;
            padding: 1rem 1.25rem;
            font-weight: 500;
        }
        .table tbody td:first-child { border-radius: 10px 0 0 10px; }
        .table tbody td:last-child { border-radius: 0 10px 10px 0; }
        
        .btn-action {
            padding: 6px 12px;
            font-size: 0.85rem;
            margin: 2px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #fff;
            border: none;
        }
        .btn-edit { background-color: var(--secondary-color); color: var(--dark-color); }
        .btn-edit:hover { background-color: #e9b207; }
        .btn-delete { background-color: #e74c3c; }
        .btn-delete:hover { background-color: #c0392b; }

        .btn-floating {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .btn-floating:hover {
            transform: scale(1.1) rotate(10deg);
            color: var(--dark-color);
            background-color: #ffd447;
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="admin_dashboard.php">
            <img src="assets/img/logo_dishub2.png" class="logo-img me-2" alt="Logo" />
            <span>SIMKAPROV ADMIN</span>
        </a>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fw-bold d-none d-md-inline">Halo, <?php echo htmlspecialchars($user['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="page-header d-flex justify-content-between align-items-center" data-aos="fade-down">
        <h1>Manajemen Jalur Kereta</h1>
        <a href="tambah_jalur.php" class="btn-add"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Jalur Baru</a>
    </div>

    <div class="table-container" data-aos="fade-up" data-aos-delay="200">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jalur</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td><strong>{$no}</strong></td>
                                    <td>" . htmlspecialchars($row['nama_jalan']) . "</td>
                                    <td>" . htmlspecialchars($row['latitude']) . "</td>
                                    <td>" . htmlspecialchars($row['longitude']) . "</td>
                                    <td class='text-center'>
                                        <a href='edit_jalur.php?id={$row['id']}' class='btn btn-edit btn-sm btn-action'>
                                            <i class='bi bi-pencil-fill'></i> Edit
                                        </a>
                                        <a href='?delete={$row['id']}' class='btn btn-delete btn-sm btn-action' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>
                                            <i class='bi bi-trash-fill'></i> Hapus
                                        </a>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-5'>Tidak ada data jalur kereta. Silakan tambahkan data baru.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<a href="admin_dashboard.php" class="btn-floating" title="Kembali ke Dashboard">
    <i class="bi bi-arrow-left"></i>
</a>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800
    });
</script>

</body>
</html>