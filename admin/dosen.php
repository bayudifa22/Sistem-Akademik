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
    $nidn = trim($_POST['nidn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');

    // Validasi input
    if (empty($nidn) || empty($nama)) {
        $alert = "NIDN dan Nama harus diisi!";
    } else {
        // CEK APAKAH NIDN SUDAH ADA (menggunakan prepared statement)
        $stmt = mysqli_prepare($koneksi, "SELECT nidn FROM dosen WHERE nidn = ?");
        mysqli_stmt_bind_param($stmt, "s", $nidn);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $alert = "NIDN sudah terdaftar! Silakan gunakan NIDN lain.";
        } else {
            // NIDN belum ada → simpan
            $stmt2 = mysqli_prepare($koneksi, "INSERT INTO dosen (nidn, nama) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt2, "ss", $nidn, $nama);
            
           if (mysqli_stmt_execute($stmt2)) {
                mysqli_query($koneksi, "ALTER TABLE mahasiswa AUTO_INCREMENT = 1");
                 header("Location: dosen.php?status=tambah");
                exit;
            } else {
                $alert = "Gagal menambah data dosen: " . mysqli_error($koneksi);
            }
        }
    }
}

// --- LOGIKA 2: UPDATE DATA ---
if (isset($_POST['update'])) {
    $nidn = trim($_POST['nidn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');

    // Validasi input
    if (empty($nidn) || empty($nama)) {
        $alert = "Semua field harus diisi!";
    } else {
        $stmt = mysqli_prepare($koneksi, "UPDATE dosen SET nama = ? WHERE nidn = ?");
        mysqli_stmt_bind_param($stmt, "ss", $nama, $nidn);
        
       if (mysqli_stmt_execute($stmt)) {
                mysqli_query($koneksi, "ALTER TABLE dosen AUTO_INCREMENT = 1");
                 header("Location: dosen.php?status=upadate");
                exit;
            } else {
            $alert = "Gagal mengupdate data dosen: " . mysqli_error($koneksi);
        }
    }
}

// --- LOGIKA 3: HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $nidn = trim($_GET['hapus']);
    
    $stmt = mysqli_prepare($koneksi, "DELETE FROM dosen WHERE nidn = ?");
    mysqli_stmt_bind_param($stmt, "s", $nidn);
    
   if (mysqli_stmt_execute($stmt)) {
                mysqli_query($koneksi, "ALTER TABLE dosen AUTO_INCREMENT = 1");
                 header("Location: dosen.php?status=hapus");
                exit;
            } else {
        $alert = "Gagal menghapus data dosen: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Dosen | SIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
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
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fa-solid fa-chalkboard-user"></i> Data Dosen</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus"></i> Tambah Dosen
            </button>
        </div>

        <!-- Alert -->
        <?php if (isset($alert)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $alert ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="table-container">
            <?php
            $data = mysqli_query($koneksi, "SELECT * FROM dosen");
            $count = mysqli_num_rows($data);
            
            if ($count > 0) {
            ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="text-center">
                        <th width="20%" class="text-center"><i class="fa-solid fa-id-card"></i> NIDN</th>
                        <th width="20%" class="text-center"><i class="fa-solid fa-user"></i> Nama Dosen</th>
                        <th width="20%" class="text-center"><i class="fa-solid fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($data, 0);
                    while ($row = mysqli_fetch_assoc($data)) {
                    ?>
                    <tr>
                        <td class="text-center"><?= $row['nidn'] ?></td>
                        <td class="text-center"><?= $row['nama'] ?></td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['nidn'] ?>">
                                <i class="fa-solid fa-edit"></i> Edit
                            </button>
                            <a href="?hapus=<?= $row['nidn'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus dosen ini?')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['nidn'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title"><i class="fa-solid fa-edit"></i> Edit Data Dosen</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="nidn" value="<?= $row['nidn'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">NIDN</label>
                                            <input type="text" class="form-control" value="<?= $row['nidn'] ?>" disabled>
                                            <small class="text-muted">NIDN tidak dapat diubah.</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Dosen</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
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
                echo '<div class="no-data"><i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i><p>Tidak ada data dosen</p></div>';
            }
            ?>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-plus"></i> Tambah Dosen Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIDN</label>
                            <input type="text" name="nidn" class="form-control" placeholder="Nomor Induk Dosen Nasional" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Dosen</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap & Gelar" required>
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
<script>
    <?php if ($_GET['status'] == 'tambah') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data Dosen berhasil ditambahkan',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'edit') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data Dosen berhasil diperbarui',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } elseif ($_GET['status'] == 'hapus') { ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data Dosen Berhasil Dihapus',
            timer: 2000,
            showConfirmButton: false
        });
    <?php } ?>
</script>
</body>
</html>