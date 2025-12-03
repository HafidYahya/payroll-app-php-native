<?php 
session_start();
if(empty($_SESSION['role'])){
    header('location:../index.php');
    exit();
}
require '../config/koneksi.php';

if(isset($_POST['tambah'])){
    $keterangan = $_POST['keterangan'];
    $tipe = $_POST['tipe'];


    $stmt = $conn->prepare('INSERT INTO keterangan_gaji (keterangan, tipe) VALUES (?,?)');
    $stmt->bind_param('ss', $keterangan, $tipe);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        header('location:index.php?success=insert');
        exit();
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAMBAH KETERANGAN GAJI - PAYROLL</title>

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

        <a href="../dashboard.php">
            <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
        </a>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Master</div>
        <a href="../perusahaan/index.php">
            <i class="fa-solid fa-building me-2"></i> Perusahaan
        </a>

        <a href="index.php" class="active">
            <i class="fa-solid fa-money-bill-transfer me-2"></i> Keterangan Gaji
        </a>

        <a href="../karyawan/index.php">
            <i class="fa-solid fa-address-card me-2"></i> Karyawan
        </a>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Transaksi</div>
        <a href="../payroll/index.php">
            <i class="fa-solid fa-wallet me-2"></i> Penggajian Karyawan
        </a>

        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Pengaturan</div>
        <a href="../users/index.php">
            <i class="fa-solid fa-gear me-2"></i> Pengaturan Pengguna
        </a>
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

            <span class="navbar-brand fw-semibold">Tambah Keterangan Gaji</span>

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

        <!-- FORM -->
        <!-- Form -->
        <form method="POST">
            <div class="modal-body">

                <!-- ID hidden -->
                <div class="mb-3">
                    <label class="form-label">Keterangan Gaji</label>
                    <input type="text" class="form-control" name="keterangan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select " aria-label="Default select" required>
                        <option value="" selected disabled>
                            Pilih Tipe Gaji
                        </option>
                        <option value="potongan">Potongan</option>
                        <option value="pemasukan">Pemasukan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <button type="submit" name="tambah" class="btn btn-dark btn-md rounded shadow-sm">Tambah
                        Keterangan Gaji</button>
                </div>
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
    <?php if (isset($_GET['error']) && $_GET['error'] == 'username'): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Username sudah dipakai',
    });
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'password'): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Password konfirmasi tidak sesuai',
    });
    </script>
    <?php endif; ?>
</body>

</html>