<?php
session_start();
if(empty($_SESSION['role'])){
    header('location:../index.php');
    exit();
}
require '../config/koneksi.php';

if (isset($_POST['tambah'])) {

    $no_ref       = $_POST['no_ref'];
    $tanggal      = $_POST['tanggal'];
    $id_karyawan  = $_POST['id_karyawan'];
    $total_gaji   = $_POST['total_gaji'];
    $detail_gaji  = $_POST['detail_gaji']; // array repeater

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {

        /*
        1. INSERT KE TABEL SLIP_GAJI
        */
        $querySlip = "INSERT INTO slip_gaji (no_ref, tanggal, total_gaji, id_karyawan)
                      VALUES ('$no_ref', '$tanggal', '$total_gaji', '$id_karyawan')";
        mysqli_query($conn, $querySlip);


        /*
    2. INSERT DETAIL_GAJI (loop dari repeater)
        */
        foreach ($detail_gaji as $detail) {

            $id_keterangan = $detail['id_keterangan_gaji'];
            $nominal       = $detail['nominal'];

            $queryDetail = "INSERT INTO detail_gaji (id_keterangan_gaji, no_ref, nominal)
                            VALUES ('$id_keterangan', '$no_ref', '$nominal')";
            mysqli_query($conn, $queryDetail);
        }

        // Commit jika semua berhasil
        mysqli_commit($conn);

        // Redirect sukses
        header('location:index.php?success=insert');
        exit();


    } catch (Exception $e) {

        // Rollback jika ada yang gagal
        mysqli_rollback($conn);

        header('location:create.php?fail=insertFail');
        exit();
    }
}
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

            <span class="navbar-brand fw-semibold">Buat Payroll</span>

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

        <!-- Form -->
        <!-- Form Slip Gaji -->
        <form method="POST" id="slipGajiForm">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">No. Ref</label>
                    <input type="text" class="form-control" name="no_ref" value="SLP<?= time() ?>" readonly>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" required>
                </div>
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Karyawan</label>
                    <select name="id_karyawan" class="form-select" required>
                        <option value="" selected disabled>Pilih Karyawan</option>
                        <?php
                $karyawan = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY nama ASC");
                while($row = mysqli_fetch_assoc($karyawan)):
                ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nama'] ?> - <?= $row['jabatan'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Total Gaji</label>
                    <input type="number" class="form-control" name="total_gaji" id="total_gaji" readonly>
                </div>
            </div>

            <hr>
            <h5>Detail Gaji</h5>
            <table class="table table-bordered" id="detailGajiTable">
                <thead>
                    <tr>
                        <th>Keterangan Gaji</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="detail_gaji[0][id_keterangan_gaji]" class="form-select" required>
                                <option value="" selected disabled>Pilih Keterangan</option>
                                <?php
                        $keterangan = mysqli_query($conn, "SELECT * FROM keterangan_gaji ORDER BY keterangan ASC");
                        while($row = mysqli_fetch_assoc($keterangan)):
                        ?>
                                <option value="<?= $row['id'] ?>"><?= $row['keterangan'] ?> (<?= $row['tipe'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="detail_gaji[0][nominal]" class="form-control nominal" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary btn-sm mb-3" id="addRow">Tambah Detail</button>

            <div class="mb-3">
                <button type="submit" name="tambah" class="btn btn-dark btn-md rounded shadow-sm">Simpan Slip
                    Gaji</button>
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

    <?php if (isset($_GET['fail']) && $_GET['fail'] == 'insertFail'): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Data gagal ditambahkan!',
    });
    </script>
    <?php endif; ?>


    <!-- ============ -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        // HITUNG TOTAL GAJI REAL-TIME
        function updateTotal() {
            let total = 0;

            // Loop semua baris detail
            document.querySelectorAll("#detailGajiTable tbody tr").forEach(tr => {
                let select = tr.querySelector("select");
                let nominalInput = tr.querySelector(".nominal");

                if (!select || !nominalInput) return;

                let nominal = parseFloat(nominalInput.value) || 0;

                // Ambil tipe dari option terpilih → contoh "Bonus (Pendapatan)"
                let selectedText = select.options[select.selectedIndex]?.text || "";

                // Cek apakah tipe-nya Potongan atau Pendapatan
                let isPotongan = selectedText.toLowerCase().includes("potongan");

                if (isPotongan) {
                    total -= nominal;
                } else {
                    total += nominal;
                }
            });

            document.getElementById("total_gaji").value = total;
        }


        // TAMBAH BARIS
        let rowIndex = 1;
        document.getElementById('addRow').addEventListener('click', function() {
            const tableBody = document.querySelector('#detailGajiTable tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
            <td>
                <select name="detail_gaji[${rowIndex}][id_keterangan_gaji]" class="form-select keteranganSelect" required>
                    <option selected disabled>Pilih Keterangan</option>
                    <?php
                    $keterangan = mysqli_query($conn, "SELECT * FROM keterangan_gaji ORDER BY keterangan ASC");
                    while($row = mysqli_fetch_assoc($keterangan)):
                    ?>
                        <option value="<?= $row['id'] ?>"><?= $row['keterangan'] ?> (<?= $row['tipe'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </td>
            <td>
                <input type="number" name="detail_gaji[${rowIndex}][nominal]" class="form-control nominal" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
        `;

            tableBody.appendChild(newRow);
            rowIndex++;

            updateTotal(); // hitung ulang
        });


        // HAPUS BARIS
        document.querySelector('#detailGajiTable').addEventListener('click', function(e) {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('tr').remove();
                updateTotal();
            }
        });


        // HITUNG ULANG SETIAP NOMINAL BERUBAH
        document.querySelector('#detailGajiTable').addEventListener('input', function(e) {
            if (e.target.classList.contains('nominal')) {
                updateTotal();
            }
        });

        // HITUNG ULANG SETIAP GANTI KETERANGAN
        document.querySelector('#detailGajiTable').addEventListener('change', function(e) {
            if (e.target.classList.contains('form-select')) {
                updateTotal();
            }
        });

    });
    </script>


</body>

</html>