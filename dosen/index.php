<?php
session_start();
if(!isset($_SESSION['username'])){
    header("location :login.php");
    exit;
}
include '../database/koneksi.php';
    // Hitung jumlah data
    $jumlah_mahasiswa  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM mahasiswa"))['total'];
    $jumlah_matakuliah = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM matakuliah"))['total'];
?>
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel = "stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- <style>
        body {
            display: flex;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background-color: #007bff;
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 0;
        }
        .sidebar-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
        }
        .sidebar .nav-item {
            padding: 10px 20px;
            margin: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-item a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        .sidebar .nav-item a:hover {
            color: white;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
        }
        .user-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .nav-item.logout {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            margin: 0;
            width: calc(100% - 40px);
            background-color: #dc3545;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
        }
        .nav-item.logout a {
            justify-content: center;
        }
        .nav-item.logout:hover {
            background-color: #c82333;
        }
    </style> -->
</head>
<body>
    <!-- Sidebar -->
    <!-- <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i> Sistem Akademik
        </div>
        <div class="nav-item">
            <a href="mahasiswa.php">
                <i class="fa-solid fa-users"></i> Mahasiswa
            </a>
        </div>
        <div class="nav-item">
            <a href="matakuliah.php">
                <i class="fa-solid fa-book"></i> Matakuliah
            </a>
        </div>
        <div class="nav-item">
            <a href="nilai.php">
                <i class="fa-solid fa-file-contract"></i> Nilai
            </a>
        </div>
        <div class="nav-item logout">
            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div> -->

    <!-- Main Content -->
    <div class="main-content">
    <div class="user-info">
            <h4>Selamat datang, <?= $_SESSION['username'] ?>!</h4>
            <p class="mb-0">Gunakan menu di samping untuk mengelola data akademik.</p>
        </div>
    <div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-users fa-2x text-primary mb-2"></i>
                <h5 class="card-title">Mahasiswa</h5>
                <h3><?= $jumlah_mahasiswa ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-book fa-2x text-warning mb-2"></i>
                <h5 class="card-title">Mata Kuliah</h5>
                <h3><?= $jumlah_matakuliah ?></h3>
            </div>
        </div>
    </div>
</div>
</body>
</html>