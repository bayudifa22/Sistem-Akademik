<?php
session_start();
// KONEKSI (Gunakan ../ karena file ini ada di folder admin)
include '../database/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    header("location:login.php");
}

// --- LOGIKA 1: TAMBAH DATA ---
if (isset($_POST['tambah'])) {
    $kode_matakuliah = $_POST['kode_matakuliah'];
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];

    //CEK APAKAH NAMA MATAKULIAH SUDAH ADA
    $cek = mysqli_query($koneksi, "SELECT * FROM matakuliah WHERE nama_matkul='$nama_matkul'");

    if (mysqli_num_rows($cek) > 0) {
        // Matakuliah sudah ada
        $alert = "Matakuliah sudah terdaftar! Silakan gunakan matakuliah yang lain.";
    } else {
        // Matakuliah belum ada → simpan
        $query = "INSERT INTO matakuliah VALUES ('$kode_matakuliah', '$nama_matkul', '$sks')";
        mysqli_query($koneksi, $query);
        header("location:matakuliah.php?status=tambah");
        exit;
    }
}

// --- LOGIKA 2: UPDATE DATA ---
if (isset($_POST['update'])) {
    $kode_matakuliah = $_POST['kode_matakuliah']; // Kode matakuliah jadi kunci (hidden input)
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];

    // Query update hanya mengubah Nama
    // $query = "UPDATE matakuliah SET nama_matkul='$nama_matkul', sks='$sks' WHERE kode_matakuliah='$kode_matakuliah'";
    
    mysqli_query($koneksi, $query);
     header("location:matakuliah.php?status=update");
}

// --- LOGIKA 3: HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $kode_matakuliah = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM matakuliah WHERE kode_matakuliah='$kode_matakuliah'");
    header("location:matakuliah.php?status=hapus");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mata Kuliah | SIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- <style>
        body {
            display: flex;
            background-color: #f8f9fa;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #007bff;
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
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
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h3 {
            margin: 0;
            color: #333;
        }
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .table {
            margin-bottom: 0;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:hover {
            background-color: #f0f0f0;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
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
        .alert {
            margin-bottom: 20px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style> -->
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
            <a href="mahasiswa.php">
                <i class="fa-solid fa-users"></i> Mahasiswa
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
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fa-solid fa-book"></i> Data Mata Kuliah</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus"></i> Tambah Matakuliah
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
            $data = mysqli_query($koneksi, "SELECT * FROM matakuliah ORDER BY kode_matakuliah ASC");
            $count = mysqli_num_rows($data);
            
            if ($count > 0) {
            ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center"><i class="fa-solid fa-hashtag"></i> No</th>
                        <th width="20%" class="text-center"><i class="fa-solid fa-code"></i> Kode Matakuliah</th>
                        <th><i class="fa-solid fa-book"></i> Nama Matakuliah</th>
                        <th width="10%" class="text-center"><i class="fa-solid fa-bookmark"></i> SKS</th>
                        <th width="20%" class="text-center"><i class="fa-solid fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($data, 0);
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($data)) {
                    ?>
                    <tr>
                        <td class="text-center"><strong><?= $no++ ?></strong></td>
                        <td class="text-center"><?= htmlspecialchars($row['kode_matakuliah']) ?></td>
                        <td><?= htmlspecialchars($row['nama_matkul']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['sks']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['kode_matakuliah'] ?>" title="Edit">
                                <i class="fa-solid fa-edit"></i> Edit
                            </button>
                            <a href="?hapus=<?= $row['kode_matakuliah'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus matakuliah ini?')" title="Hapus">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['kode_matakuliah'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title"><i class="fa-solid fa-edit"></i> Edit Data Matakuliah</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="kode_matakuliah" value="<?= $row['kode_matakuliah'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Kode Matakuliah</label>
                                            <input type="text" class="form-control" value="<?= $row['kode_matakuliah'] ?>" disabled>
                                            <small class="text-muted">Kode tidak dapat diubah</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Matakuliah</label>
                                            <input type="text" name="nama_matkul" class="form-control" value="<?= $row['nama_matkul'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">SKS</label>
                                            <input type="text" name="sks" class="form-control" value="<?= $row['sks'] ?>" required>
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
                echo '<div class="no-data"><i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i><p>Tidak ada data matakuliah</p></div>';
            }
            ?>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-plus"></i> Tambah Matakuliah Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Matakuliah</label>
                            <input type="text" name="kode_matakuliah" class="form-control" placeholder="Kode Matakuliah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Matakuliah</label>
                            <input type="text" name="nama_matkul" class="form-control" placeholder="Nama Matakuliah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKS</label>
                            <input type="text" name="sks" class="form-control" placeholder="Jumlah SKS" required>
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
<!-- SweetAlert2 POP UP -->
<script>
    <?php if ($_GET['status'] == 'tambah') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Matakuliah Berhasil Ditambahkan',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'edit') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Matakuliah Berhasil Diperbarui',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'hapus') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Matakuliah Berhasil Dihapus',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } ?>
</script>
</body>
</html>