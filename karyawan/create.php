<?php 
session_start();
if(empty($_SESSION['role'])){
    header('location:../index.php');
    exit();
}
require '../config/koneksi.php';

if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $jabatan = $_POST['jabatan'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $no_rekening = $_POST['no_rekening'];
    $rek_bank = $_POST['rek_bank'];
    $id_perusahaan = $_POST['id_perusahaan'];


    $stmt = $conn->prepare('INSERT INTO karyawan (nama, alamat, jabatan, no_hp, email, no_rekening, rek_bank, id_perusahaan) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->bind_param('sssssssi', $nama, $alamat, $jabatan, $no_hp, $email, $no_rekening, $rek_bank, $id_perusahaan);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        header('location:index.php?success=insert');
        exit();
    }
}
$perusahaan = mysqli_query($conn, "SELECT * FROM perusahaan ORDER BY nama ASC");

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAMBAH KARYAWAN - PAYROLL</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <h4 class="text-center mb-4 fw-bold brand">Payroll App <i class="fa-solid fa-coins"></i></h4>
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <a href="../dashboard.php">
            <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
        </a>
        <?php endif; ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Master</div>
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <a href="../perusahaan/index.php">
            <i class="fa-solid fa-building me-2"></i> Perusahaan
        </a>
        <?php endif; ?>
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <a href="../keterangan-gaji/index.php">
            <i class="fa-solid fa-money-bill-transfer me-2"></i> Keterangan Gaji
        </a>
        <?php endif; ?>
        <a href="index.php" class="active">
            <i class="fa-solid fa-address-card me-2"></i> Karyawan
        </a>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Transaksi</div>
        <a href="../payroll/index.php">
            <i class="fa-solid fa-wallet me-2"></i> Penggajian Karyawan
        </a>
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Pengaturan</div>
        <a href="../users/index.php">
            <i class="fa-solid fa-gear me-2"></i> Pengaturan Pengguna
        </a>
        <?php endif; ?>
    </div>

    <!-- Overlay (for mobile when sidebar open) -->
    <div id="overlay" class="overlay hidden"></div>

    <!-- CONTENT -->
    <div class="content" id="content">

        <!-- NAVBAR -->
        <nav
            class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm px-3 mb-4 top-navbar d-flex align-items-center">
            <!-- Toggle button placed in navbar and visible for ALL sizes -->
            <button class="btn btn-sm me-3 toggle-btn" id="toggleSidebar" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <span class="navbar-brand fw-semibold">Tambah Karyawan</span>

            <div class="ms-auto d-flex align-items-center gap-3">
                <?php if(isset($_SESSION['nama'])) :?>
                <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <?php else: ?>
                <span class="d-none d-md-inline">Hallo, Selamat Datang</span>
                <?php endif; ?>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </nav>

        <!-- Form -->
        <form method="POST">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" required>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">No. HP</label>
                    <input type="number" class="form-control" name="no_hp" required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">No. Rekening</label>
                    <input type="number" class="form-control" name="no_rekening" required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Bank</label>
                    <select name="rek_bank" class="form-select " aria-label="Default select " required>
                        <option value="" selected disabled>
                            Pilih Bank</option>
                        <option value="Bank Central Asia"> Bank Central Asia | BCA</option>
                        <option value="Bank Rakyat Indonesia"> Bank Rakyat Indonesia | BRI</option>
                        <option value="Bank Negara Indonesia"> Bank Negara Indonesia | BNI</option>
                        <option value="Bank Tabungan Negara"> Bank Tabungan Negara | BTN</option>
                        <option value="Mandiri"> Mandiri</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" required></textarea>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Perusahaan</label>
                    <select name="id_perusahaan" class="form-select " aria-label="Default select" required>
                        <option selected disabled value="">
                            Pilih Perusahaan
                        </option>
                        <?php while($row=mysqli_fetch_assoc($perusahaan)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <button type="submit" name="tambah" class="btn btn-dark btn-md rounded shadow-sm">Tambah
                    Karyawan</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CUSTOM JS -->
    <script>
    // script.js
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleSidebar");
        const content = document.getElementById("content");
        const overlay = document.getElementById("overlay");

        // Helper: check current viewport
        function isMobile() {
            return window.matchMedia("(max-width: 991px)").matches;
        }

        // Initial state:
        // on desktop: sidebar visible (no classes)
        // on mobile: sidebar hidden (use class 'collapsed' or rely on CSS transform)
        if (isMobile()) {
            sidebar.classList.add("collapsed");
            content.classList.add("full");
        } else {
            sidebar.classList.remove("collapsed");
            content.classList.remove("full");
        }

        // Toggle function
        function toggleSidebar() {
            if (isMobile()) {
                // Mobile behavior: slide in/out with overlay
                if (sidebar.classList.contains("collapsed")) {
                    sidebar.classList.remove("collapsed");
                    sidebar.classList.add("open");
                    overlay.classList.remove("hidden");
                } else {
                    sidebar.classList.add("collapsed");
                    sidebar.classList.remove("open");
                    overlay.classList.add("hidden");
                }
                // content remains full width on mobile
                content.classList.add("full");
            } else {
                // Desktop behavior: collapse to full-width content or show sidebar
                sidebar.classList.toggle("collapsed");
                content.classList.toggle("full");
            }
        }

        toggleBtn.addEventListener("click", toggleSidebar);

        // click overlay to close (mobile)
        overlay.addEventListener("click", function() {
            sidebar.classList.add("collapsed");
            sidebar.classList.remove("open");
            overlay.classList.add("hidden");
        });

        // Close sidebar on resize to keep consistent state
        let resizeTimer;
        window.addEventListener("resize", function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (isMobile()) {
                    sidebar.classList.add("collapsed");
                    sidebar.classList.remove("open");
                    overlay.classList.add("hidden");
                    content.classList.add("full");
                } else {
                    sidebar.classList.remove("collapsed");
                    sidebar.classList.remove("open");
                    overlay.classList.add("hidden");
                    content.classList.remove("full");
                }
            }, 120);
        });
    });
    </script>
</body>

</html>