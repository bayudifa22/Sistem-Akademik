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
    $nim = $_POST['nim'];
    $kode_matakuliah = $_POST['kode_matakuliah'];
    $nilai = $_POST['nilai'];

    //CEK APAKAH NIDN SUDAH ADA
    $cek = mysqli_query($koneksi, "SELECT * FROM nilai WHERE nim='$nim'");

    if (mysqli_num_rows($cek) > 0) {
        // NIDN sudah ada
        $alert = "NIDN sudah terdaftar! Silakan gunakan NIDN lain.";
    } else {
        // NIDN belum ada → simpan
        $query = "INSERT INTO nilai (nim, kode_matakuliah, nilai) VALUES ('$nim', '$kode_matakuliah', '$nilai')";
        mysqli_query($koneksi, $query);
         header("location:nilai.php?status=tambah");
        exit;
    }
}

// --- LOGIKA 2: UPDATE DATA ---
if (isset($_POST['update'])) {
    $nim = $_POST['nim']; // NIDN jadi kunci (hidden input)
    $kode_matakuliah = $_POST['kode_matakuliah'];
    $nilai = $_POST['nilai'];

    // Query update hanya mengubah Nilai
    $query = "UPDATE nilai SET nim='$nim', kode_matakuliah='$kode_matakuliah', nilai='$nilai' WHERE nim='$nim'";
    
    mysqli_query($koneksi, $query);
     header("location:nilai.php?status=update");
}

// --- LOGIKA 3: HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $nim = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM nilai WHERE nim='$nim'");
    header("location:nilai.php?status=hapus");
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Data Nilai Mahasiswa | SIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i> Sistem Akademik
        </div>
        <div class="nav-item">
            <a href="dashboard.php">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="dosen.php">
                <i class="fa-solid fa-chalkboard-user"></i> Dosen
            </a>
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
            <h3><i class="fa-solid fa-file-contract"></i> Data Nilai Mahasiswa</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus"></i> Tambah Nilai
            </button>
        </div>

        <!-- Table -->
        <div class="table-container">
            <?php
            $data = mysqli_query($koneksi, "SELECT * FROM nilai ORDER BY nim ASC");
            $count = mysqli_num_rows($data);
            
            if ($count > 0) {
            ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center"><i class="fa-solid fa-hashtag"></i> No</th>
                        <th width="20%" class="text-center"><i class="fa-solid fa-id-card"></i> NIM</th>
                        <th width="30%" class="text-center"><i class="fa-solid fa-code"></i> Kode Matakuliah</th>
                        <th width="15%" class="text-center"><i class="fa-solid fa-star"></i> Nilai</th>
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
                        <td class="text-center"><?= htmlspecialchars($row['nim']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['kode_matakuliah']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['nilai']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['nim'] ?>" title="Edit">
                                <i class="fa-solid fa-edit"></i> Edit
                            </button>
                            <a href="?hapus=<?= $row['nim'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus nilai ini?')" title="Hapus">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['nim'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title"><i class="fa-solid fa-edit"></i> Edit Data Nilai</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">NIM</label>
                                            <input type="text" class="form-control" value="<?= $row['nim'] ?>" disabled>
                                            <small class="text-muted">NIM tidak dapat diubah</small>
                                            <input type="hidden" name="nim" value="<?= $row['nim'] ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kode Matakuliah</label>
                                            <input type="text" name="kode_matakuliah" class="form-control" value="<?= $row['kode_matakuliah'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nilai</label>
                                            <input type="text" name="nilai" class="form-control" value="<?= $row['nilai'] ?>" required>
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
                echo '<div class="no-data"><i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i><p>Tidak ada data nilai</p></div>';
            }
            ?>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-plus"></i> Tambah Nilai Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" class="form-control" placeholder="Nomor Induk Mahasiswa" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Matakuliah</label>
                            <input type="text" name="kode_matakuliah" class="form-control" placeholder="Kode Matakuliah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai</label>
                            <input type="text" name="nilai" class="form-control" placeholder="Nilai" required>
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
    <script src="assets/js/script.js"></script>
<!-- SweetAlert2 POP UP -->
<script>
    <?php if ($_GET['status'] == 'tambah') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nilai Berhasil Ditambahkan',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'edit') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nilai Berhasil Diperbarui',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'hapus') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nilai Berhasil Dihapus',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } ?>
</script>
</body>
</html>