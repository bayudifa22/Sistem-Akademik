<?php
session_start();
include 'database/koneksi.php';

$pesan = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $level = trim($_POST['level'] ?? 'mahasiswa');

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $pesan = "Semua field harus diisi!";
    } elseif (strlen($password) < 6) {
        $pesan = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm_password) {
        $pesan = "Password tidak sesuai!";
    } else {
        // Cek username sudah ada
        $cek = mysqli_query($koneksi, "SELECT username FROM user WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = "Username sudah terdaftar!";
        } else {
            // Simpan user baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO user (username, password, level) VALUES ('$username', '$hashed_password', '$level')";
            if (mysqli_query($koneksi, $query)) {
                $pesan = "Registrasi berhasil! Silakan login.";
                // Clear form
                $username = '';
            } else {
                $pesan = "Gagal registrasi: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #007bff; /* Warna biru */ 
            padding: 20px 0;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .register-container img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .form-group label {
            float: left;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .register-container h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .alert {
            margin-bottom: 20px;
        }
        .register-link {
            margin-top: 15px;
            text-align: center;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <i class="fa-solid fa-graduation-cap" style="font-size: 100px; color: #007bff;"></i>
        <h2>Register Sistem Akademik</h2>

        <?php if (!empty($pesan)) { ?>
            <div class="alert alert-<?= (strpos($pesan, 'berhasil') !== false) ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" value="" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
            </div>
            <div class="form-group">
                <label for="level">Level</label>
                <select class="form-control" id="level" name="level" required>
                    <option value="">Pilih Level</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>
            <button type="submit" name="register" class="btn btn-success btn-block">Register</button>
        </form>

        <div class="register-link">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
