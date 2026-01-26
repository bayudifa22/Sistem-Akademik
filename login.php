<?php
session_start();
include 'database/koneksi.php';

$pesan = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi,
        "SELECT * FROM user 
         WHERE username='$username'"
    );

    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['username'] = $data['username'];
        $_SESSION['level']    = $data['level'];

        if ($data['level'] == 'admin') {
             $redirect = "admin/dashboard.php";
        } elseif ($data['level'] == 'dosen') {
             $redirect = "dosen/dashboard.php";
        } elseif ($data['level'] == 'mahasiswa') {
            $redirect = "mahasiswa/index.php";
        }
        exit;
    } else {
        $pesan = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: #007bff; /* Warna biru */ 
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-container img {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .form-group label {
            float: left;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- <i class="fa-solid fa-graduation-cap"></i> -->
        <i class="fa-solid fa-graduation-cap" style="font-size: 100px; color: #007bff;"></i>
        <h2 class="text-center">Login Sistem Akademik</h2>

        <?php if ($pesan != "") { ?>
            <div class="alert alert-danger text-center"><?= $pesan ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
        </form>
        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar disini</a></p>
    </div>
    <script src="../assets/js/script.js"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
        text: 'Selamat datang <?= $_SESSION['username']; ?>',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = "<?= $redirect ?>";
    });
</script>
</body>
</html>