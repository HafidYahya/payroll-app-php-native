<?php 
session_start();
if(empty($_SESSION['role'])){
    header('location:../index.php');
    exit();
}
require '../config/koneksi.php';

if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $now = date('Y-m-d H:i:s', time());
    $role = $_POST['role'];
    $error_konfirmasi = "";

    // Jika role karyawan → wajib pilih ID karyawan
    $id_karyawan = ($role === "karyawan") ? $_POST['id_karyawan'] : NULL;

    $cek = $conn->prepare('SELECT * FROM users WHERE username=?');
    $cek->bind_param('s', $username);
    $cek->execute();
    $cek->store_result();
    if($cek->num_rows() > 0){
        header('location:create.php?error=username');
        exit();
    }
    
    if($password !== $konfirmasi_password){
        header('location:create.php?error=password');
        exit();
    }else{
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    // Insert dengan id_karyawan
    $stmt = $conn->prepare('
        INSERT INTO users (nama, username, password, created_at, updated_at, role, id_karyawan)
        VALUES (?,?,?,?,?,?,?)
    ');
    $stmt->bind_param('ssssssi',
        $nama,
        $username,
        $password_hash,
        $now,
        $now,
        $role,
        $id_karyawan
    );
    $stmt->execute();

    if($stmt->affected_rows > 0){
        header('location:index.php?success=insert');
        exit();
    }
}
$karyawan = $conn->query("SELECT id, nama, jabatan FROM karyawan");


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAMBAH PENGGUNA - PAYROLL</title>

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
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <a href="../karyawan/index.php">
            <i class="fa-solid fa-address-card me-2"></i> Karyawan
        </a>
        <?php endif; ?>
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Transaksi</div>
        <a href="../payroll/index.php">
            <i class="fa-solid fa-wallet me-2"></i> Penggajian Karyawan
        </a>
        <?php endif; ?>
        <?php if(!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') :?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading mb-2 ms-2">Pengaturan</div>
        <a href="index.php" class="active">
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

            <span class="navbar-brand fw-semibold">Tambah Pengguna</span>

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
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class=" mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="konfirmasi_password" required>
                    <?php if(!empty($error_konfirmasi)): ?>
                    <div class="text-danger mt-1"><?= $error_konfirmasi ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select " aria-label="Default select example" required>
                        <option value="" selected disabled>
                            Pilih Role
                        </option>
                        <option value="admin">Admin</option>
                        <option value="hr">HR</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                </div>
                <div class="mb-3" id="field_karyawan" style="display:none;">
                    <label class="form-label">Pilih Karyawan</label>
                    <select name="id_karyawan" id="id_karyawan" class="form-select">
                        <option selected disabled value="">Pilih Karyawan</option>
                        <?php while($row = $karyawan->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= $row['nama'] ?> - <?= $row['jabatan'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <button type="submit" name="tambah" class="btn btn-dark btn-md rounded shadow-sm">Tambah
                        Pengguna</button>
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

    <!-- ===== -->
    <script>
    document.querySelector("select[name='role']").addEventListener("change", function() {
        const field = document.getElementById("field_karyawan");
        const selectKaryawan = document.getElementById("id_karyawan");

        if (this.value === "karyawan") {
            field.style.display = "block";
            selectKaryawan.setAttribute("required", true);
        } else {
            field.style.display = "none";
            selectKaryawan.removeAttribute("required");
            selectKaryawan.value = "";
        }
    });
    </script>


</body>

</html>