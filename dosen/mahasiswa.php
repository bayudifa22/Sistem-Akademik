<?php
session_start();
// KONEKSI
include '../database/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("location:../login.php");
    exit;
}

$alert = "";

// --- LOGIKA 1: TAMBAH DATA ---
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');

    // Validasi input
    if (empty($nama) || empty($nim) || empty($prodi) || empty($tanggal_lahir)) {
        $alert = "Semua field harus diisi!";
    } else {
        // CEK APAKAH NIM SUDAH ADA (menggunakan prepared statement)
        $stmt = mysqli_prepare($koneksi, "SELECT nim FROM mahasiswa WHERE nim = ?");
        mysqli_stmt_bind_param($stmt, "s", $nim);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // NIM sudah ada
            $alert = "NIM sudah terdaftar! Silakan gunakan NIM lain.";
        } else {
            // NIM belum ada → simpan
            $stmt2 = mysqli_prepare($koneksi, "INSERT INTO mahasiswa (nama, nim, prodi, tanggal_lahir) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "ssss", $nama, $nim, $prodi, $tanggal_lahir);
            
            if (mysqli_stmt_execute($stmt2)) {
                mysqli_query($koneksi, "ALTER TABLE mahasiswa AUTO_INCREMENT = 1");
                 header("Location: mahasiswa.php?status=tambah");
                exit;
            } else {
                $alert = "Gagal menambah data mahasiswa: " . mysqli_error($koneksi);
            }
        }
    }
}

// --- LOGIKA 2: UPDATE DATA (Edit) ---
if (isset($_POST['update'])) {
    $nim_lama = trim($_POST['nim_lama'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');

    // Validasi input
    if (empty($nim_lama) || empty($nim) || empty($nama) || empty($prodi) || empty($tanggal_lahir)) {
        $alert = "Semua field harus diisi!";
    } else {
        // Jika NIM berubah, cek apakah NIM baru sudah ada
        if ($nim != $nim_lama) {
            $stmt = mysqli_prepare($koneksi, "SELECT nim FROM mahasiswa WHERE nim = ? AND nim != ?");
            mysqli_stmt_bind_param($stmt, "ss", $nim, $nim_lama);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $alert = "NIM baru sudah terdaftar! Silakan gunakan NIM lain.";
            } else {
                // NIM baru belum ada, lanjutkan update
                $stmt2 = mysqli_prepare($koneksi, "UPDATE mahasiswa SET nim = ?, nama = ?, prodi = ?, tanggal_lahir = ? WHERE nim = ?");
                mysqli_stmt_bind_param($stmt2, "sssss", $nim, $nama, $prodi, $tanggal_lahir, $nim_lama);
                
                if (mysqli_stmt_execute($stmt2)) {
                mysqli_query($koneksi, "ALTER TABLE mahasiswa AUTO_INCREMENT = 1");
                 header("Location: mahasiswa.php?status=edit");
                exit;
            } else {
                    $alert = "Gagal mengupdate data mahasiswa: " . mysqli_error($koneksi);
                }
            }
        } else {
            // NIM tidak berubah, langsung update
            $stmt2 = mysqli_prepare($koneksi, "UPDATE mahasiswa SET nama = ?, prodi = ?, tanggal_lahir = ? WHERE nim = ?");
            mysqli_stmt_bind_param($stmt2, "ssss", $nama, $prodi, $tanggal_lahir, $nim);
            
            if (mysqli_stmt_execute($stmt2)) {
                header("location:mahasiswa.php");
                exit;
            } else {
                $alert = "Gagal mengupdate data mahasiswa: " . mysqli_error($koneksi);
            }
        }
    }
}

// --- LOGIKA 3: HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $nim = trim($_GET['hapus']);
    
    $stmt = mysqli_prepare($koneksi, "DELETE FROM mahasiswa WHERE nim = ?");
    mysqli_stmt_bind_param($stmt, "s", $nim);
    
    if (mysqli_stmt_execute($stmt)) {
                mysqli_query($koneksi, "ALTER TABLE mahasiswa AUTO_INCREMENT = 1");
                 header("Location: mahasiswa.php?status=hapus");
                exit;
            } else {
        $alert = "Gagal menghapus data mahasiswa: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mahasiswa | SIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
       
    </style>
</head>
<body>
    
<!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i> Sistem Akademik
        </div>
        <div class="nav-item">
            <a href="index.php">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="matakuliah.php">
                <i class="fa-solid fa-book"></i> Matakuliah
            </a>
        </div>
        <div class="nav-item">
            <a href="../nilai.php">
                <i class="fa-solid fa-file-contract"></i> Nilai
            </a>
        </div>
        <div class="nav-item logout">
            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fa-solid fa-chalkboard-user"></i> Data Mahasiswa</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus"></i> Tambah Mahasiswa
            </button>
        </div>

        <!-- Alert -->
        <?php if (!empty($alert)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($alert) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="table-container">
            <?php
            $data = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY nama DESC");
            $count = mysqli_num_rows($data);
            
            if ($count > 0) {
            ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="text-center">
                        <th><i class="fa-solid fa-user"></i> Nama Mahasiswa</th>
                        <th class="text-center"><i class="fa-solid fa-id-card"></i> NIM</th>
                        <th class="text-center"><i class="fa-solid fa-book"></i> Prodi</th>
                        <th class="text-center"><i class="fa-solid fa-calendar"></i> Tanggal Lahir</th>
                        <th class="text-center"><i class="fa-solid fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($data, 0);
                    while ($row = mysqli_fetch_assoc($data)) {
                    ?>
                    <tr>
                        <td class="text-center"><?= $row['nama'] ?></td>
                        <td class="text-center"><?= $row['nim'] ?></td>
                        <td class="text-center"><?= $row['prodi'] ?></td>
                        <td class="text-center"><?= $row['tanggal_lahir'] ?></td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['nim'] ?>">
                                <i class="fa-solid fa-edit"></i> Edit
                            </button>
                            <a href="?hapus=<?= $row['nim'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus dosen ini?')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['nim'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title"><i class="fa-solid fa-edit"></i> Edit Data Mahasiswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="nim_lama" value="<?= $row['nim'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">NIM</label>
                                            <input type="text" name="nim" class="form-control" value="<?= $row['nim'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Mahasiswa</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Program Studi</label>
                                            <input type="text" name="prodi" class="form-control" value="<?= $row['prodi'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input type="date" name="tanggal_lahir" class="form-control" value="<?= $row['tanggal_lahir'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            } else {
                echo '<div class="no-data"><i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i><p>Tidak ada data mahasiswa</p></div>';
            }
            ?>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-plus"></i> Tambah Mahasiswa Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Mahasiswa</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Mahasiswa Lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" class="form-control" placeholder="Nomor Induk Mahasiswa" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prodi</label>
                            <input type="text" name="prodi" class="form-control" placeholder="Program Studi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <!-- sweet alert / pop up notifications -->
<script>
    <?php if ($_GET['status'] == 'tambah') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data Mahasiswa berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'edit') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data Mahasiswa berhasil diperbarui',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'hapus') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data Mahasiswa berhasil dihapus',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } ?>
</script>

</body>
</html>