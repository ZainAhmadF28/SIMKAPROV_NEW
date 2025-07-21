<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data jalur dari database
$sql = "SELECT * FROM jalur_kereta ORDER BY nama";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jalur Kereta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('img/b2.svg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        
        .container {
            max-width: 90%;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 26px;
            color: #0078A8;
            font-weight: 600;
            animation: slideIn 1s ease-out;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            animation: fadeUp 1s ease-in-out;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #0078A8;
            color: white;
            text-transform: uppercase;
        }

        tr:hover {
            background: #f2f2f2;
            transition: 0.3s;
        }

        .back-btn {
            display: block;
            text-align: center;
            background: #0078A8;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            width: 150px;
            margin: 20px auto 0;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #005f87;
        }

        /* Animasi */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Data Jalur Kereta</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Jalur</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['latitude']}</td>
                            <td>{$row['longitude']}</td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='4' style='text-align: center;'>Tidak ada data jalur kereta</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="back-btn"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>

</body>
</html>
