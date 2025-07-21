<?php
session_start();
require 'config.php'; // Koneksi ke database

// [PERBAIKAN] Cek apakah pengguna sudah login (role apapun boleh)
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Jika belum login sama sekali, arahkan ke halaman login
    exit();
}

// Ambil data admin dari session untuk ditampilkan di navbar
$username_session = $_SESSION['username'];
$stmt_user_nav = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt_user_nav->bind_param("s", $username_session);
$stmt_user_nav->execute();
$result_user_nav = $stmt_user_nav->get_result();
$user_nav = $result_user_nav->fetch_assoc();
$stmt_user_nav->close();

// Ambil data perlintasan kereta dari database
$queryPerlintasan = "SELECT * FROM jalur_kereta_admin";
$resultPerlintasan = mysqli_query($conn, $queryPerlintasan);
$perlintasanData = [];
while ($row = mysqli_fetch_assoc($resultPerlintasan)) {
    $perlintasanData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Jalur Kereta - SIMKAPROV</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
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
            z-index: 1030;
        }
        .logo-img { height: 45px; width: auto; }
        .navbar-brand span { font-weight: 600; color: var(--light-color); }
        .navbar .dropdown-toggle { color: var(--light-color); }

        .page-header {
            padding: 2rem 0;
            text-align: center;
            color: var(--light-color);
        }
        .page-header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .control-card {
            background: var(--light-color);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .map-card {
            background: var(--light-color);
            border-radius: 20px;
            padding: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 1.5rem;
        }

        #map {
            height: 60vh;
            width: 100%;
            border-radius: 15px;
            z-index: 1;
        }
        
        .btn-custom {
            font-weight: 600;
            border-radius: 8px;
            border: none;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        .btn-search { background-color: var(--primary-color); color: var(--light-color); }
        .btn-search:hover { background-color: #00417a; }
        .btn-location { background-color: #3498db; color: var(--light-color); }
        .btn-location:hover { background-color: #2980b9; }
        .btn-nearest { background-color: var(--secondary-color); color: var(--dark-color); }
        .btn-nearest:hover { background-color: #e9b207; }
        #start-navigation { display: none; background-color: #2ecc71; color: var(--light-color); }
        #start-navigation:hover { background-color: #27ae60; }

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
            transform: scale(1.1);
            color: var(--dark-color);
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
                <span class="fw-bold d-none d-md-inline">Halo, <?php echo htmlspecialchars($user_nav['username']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <header class="page-header" data-aos="fade-down">
        <h1>Monitoring Lokasi & Jalur Kereta Api</h1>
    </header>

    <div class="control-card mb-4" data-aos="fade-up">
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="input-group">
                    <input type="text" id="search-box" class="form-control" placeholder="Cari nama stasiun/jalur...">
                    <button onclick="searchLocation()" class="btn btn-search">Cari</button>
                </div>
            </div>
            <div class="col-lg-5">
                 <select id="route-select" class="form-select" onchange="showSelectedRoute()">
                    <option value="">Pilih Rute atau Stasiun dari Daftar</option>
                    </select>
            </div>
            <div class="col-lg-3">
                <div class="d-grid gap-2">
                    <button id="locate-me" onclick="getUserLocation()" class="btn btn-custom btn-location">Lokasi Saya</button>
                    <button id="find-nearest" onclick="findNearestCrossing()" class="btn btn-custom btn-nearest">Cari Perlintasan Terdekat</button>
                    <button id="start-navigation" onclick="startNavigation()" class="btn btn-custom">Mulai Navigasi</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="map-card" data-aos="fade-up" data-aos-delay="200">
        <div id="map"></div>
    </div>
</div>

<a href="admin_dashboard.php" class="btn-floating" title="Kembali ke Dashboard">
    <i class="bi bi-arrow-left"></i>
</a>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800
    });

    var map = L.map('map').setView([-2.9761, 104.7754], 9); // Center di Palembang

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var userCircleMarker, nearestMarker, routePolyline;
    var userLat, userLng;
    var nearestCrossing;

    var routes = <?php echo json_encode($perlintasanData); ?>;
    var routeSelect = document.getElementById("route-select");

    if (routes.length > 0) {
        routes.forEach(function(route) {
            // Mengisi pilihan rute
            var option = document.createElement("option");
            option.value = route.id;
            option.text = route.nama_jalan;
            routeSelect.add(option);

            // Menambahkan marker untuk setiap perlintasan ke peta
            L.marker([route.latitude, route.longitude])
                .addTo(map)
                .bindPopup("<b>" + route.nama_jalan + "</b>");
        });
    } else {
        document.getElementById('map').innerHTML = '<div class="alert alert-warning text-center m-5">Tidak ada data jalur untuk ditampilkan.</div>';
    }

    // [FUNGSI BARU] Untuk menampilkan rute yang dipilih dari dropdown
    function showSelectedRoute() {
        var routeId = routeSelect.value;
        if (!routeId) return;

        var selectedRoute = routes.find(route => route.id == routeId);

        if (selectedRoute) {
            map.setView([selectedRoute.latitude, selectedRoute.longitude], 15);
            L.popup()
                .setLatLng([selectedRoute.latitude, selectedRoute.longitude])
                .setContent("<b>" + selectedRoute.nama_jalan + "</b>")
                .openOn(map);
        }
    }

    function getUserLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;

                    if (userCircleMarker) map.removeLayer(userCircleMarker);

                    userCircleMarker = L.circleMarker([userLat, userLng], {
                        radius: 8, fillColor: "red", color: "#000", weight: 1, opacity: 1, fillOpacity: 0.8
                    }).addTo(map).bindPopup("Lokasi Anda").openPopup();

                    map.setView([userLat, userLng], 14);
                },
                () => { alert("Tidak dapat mendapatkan lokasi Anda. Pastikan GPS dan izin lokasi aktif."); }
            );
        } else {
            alert("Geolocation tidak didukung oleh browser ini.");
        }
    }

    function findNearestCrossing() {
        if (!userLat || !userLng) {
            alert("Harap aktifkan 'Lokasi Saya' terlebih dahulu.");
            return;
        }

        let nearest = null;
        let minDistance = Infinity;

        routes.forEach(function(crossing) {
            let distance = getDistance(userLat, userLng, crossing.latitude, crossing.longitude);
            if (distance < minDistance) {
                minDistance = distance;
                nearest = crossing;
            }
        });

        if (nearest) {
            nearestCrossing = nearest;
            document.getElementById("start-navigation").style.display = "block";

            if (nearestMarker) map.removeLayer(nearestMarker);
            nearestMarker = L.marker([nearest.latitude, nearest.longitude]).addTo(map)
                .bindPopup("<b>Perlintasan Terdekat: " + nearest.nama_jalan + "</b>").openPopup();

            calculateRoute(userLat, userLng, nearest.latitude, nearest.longitude);
        }
    }

    function calculateRoute(startLat, startLng, endLat, endLng) {
        var osrmUrl = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`;

        fetch(osrmUrl)
            .then(response => response.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    var route = data.routes[0];
                    var routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);

                    if (routePolyline) map.removeLayer(routePolyline);

                    routePolyline = L.polyline(routeCoordinates, {color: 'blue'}).addTo(map);
                    map.fitBounds(routePolyline.getBounds());
                } else {
                    alert("Tidak dapat menemukan rute.");
                }
            })
            .catch(error => console.error("Terjadi kesalahan: ", error));
    }

    function startNavigation() {
        if (nearestCrossing) {
            var googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${nearestCrossing.latitude},${nearestCrossing.longitude}&travelmode=driving`;
            window.open(googleMapsUrl, "_blank");
        } else {
            alert("Tidak ada rute yang dipilih.");
        }
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        function toRad(value) { return value * Math.PI / 180; }
        let R = 6371;
        let dLat = toRad(lat2 - lat1);
        let dLon = toRad(lon2 - lon1);
        let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function searchLocation() {
        let searchQuery = document.getElementById("search-box").value.toLowerCase();
        if (!searchQuery) return;
        let found = routes.find(jalur => jalur.nama_jalan.toLowerCase().includes(searchQuery));

        if (found) {
            map.setView([found.latitude, found.longitude], 14);
            L.popup().setLatLng([found.latitude, found.longitude]).setContent("<b>Ditemukan: " + found.nama_jalan + "</b>").openOn(map);
        } else {
            alert("Lokasi tidak ditemukan.");
        }
    }
</script>

</body>
</html>