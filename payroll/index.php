<?php 
session_start();
if(empty($_SESSION['role'])){
    header('location:../index.php');
    exit();
}

use Vtiful\Kernel\Format;
require '../config/koneksi.php';

$query = "SELECT * FROM slip_gaji ORDER BY id DESC";
$result = mysqli_query($conn, $query);


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAYROLL - PAYROLL</title>

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
        <a href="../karyawan/index.php">
            <i class="fa-solid fa-address-card me-2"></i> Karyawan
        </a>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Transaksi</div>
        <a href="index.php" class="active">
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

            <span class="navbar-brand fw-semibold">Payroll</span>

            <div class="ms-auto d-flex align-items-center gap-3">
                <?php if(isset($_SESSION['nama'])) :?>
                <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <?php else: ?>
                <span class="d-none d-md-inline">Hallo, Selamat Datang</span>
                <?php endif; ?>
                <!-- Logout Icon -->
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </nav>

        <!-- TABEL -->
        <a href="create.php" class="btn btn-dark mb-3  "><i class="fa-solid fa-cash-register me-2"></i>Buat Payroll</a>
        <div class="card p-4 shadow-sm rounded mb-4">
            <h5 class="mb-3">Slip Gaji <i class="fa-solid fa-file-invoice-dollar"></i></h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th>No. Ref</th>
                            <th>Nama Karyawan</th>
                            <th>Total Gaji</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i=1 ?>
                        <?php foreach($result as $data): ?>
                        <?php 
                        $id_karyawan = $data['id_karyawan'];
                        $karyawan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM karyawan WHERE id='$id_karyawan'"))?>
                        <tr>
                            <td><?= $data['no_ref'] ?></td>
                            <td><?= $karyawan['nama'] ?></td>
                            <td>Rp. <?=number_format($data['total_gaji'], 0, ',', '.')  ?></td>
                            <td><?= date('d F Y', strtotime($data['tanggal'])) ?></td>
                            <td>
                                <a href="../print-slip.php?no_ref=<?= $data['no_ref'] ?>" target="_blank"
                                    class="btn btn-md"><i class="fa-solid fa-print"></i></a>
                                <button type="button" class="btn btn-md" data-bs-toggle="modal"
                                    data-bs-target="#modalDelete<?= $data['id'] ?>" <?= $data['id'] ?>>
                                    <i class="fa-solid fa-trash text-danger"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="modalDelete<?= $data['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <!-- Header -->
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body">
                                        <p class="mb-0">
                                            Apakah Anda yakin ingin menghapus data dengan No. Ref:
                                            <strong><?= $data['no_ref'] ?></strong>?
                                        </p>
                                    </div>

                                    <!-- Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary rounded-pill"
                                            data-bs-dismiss="modal">Batal</button>

                                        <form action="delete.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                            <button type="submit" class="btn btn-danger rounded-pill">Hapus</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

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

    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data slip gaji berhasil dihapus!',
        timer: 1500,
        showConfirmButton: false
    });
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'deletefail'): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Data gagal dihapus!',
    });
    </script>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'insert'): ?>
    <script>
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon: "success",
        title: "Penggajian Berhasil"
    });
    </script>
    <?php endif; ?>

</body>

</html>