<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $keterangan = $_POST['keterangan'];
    $tipe = $_POST['tipe'];

    $sql = "UPDATE keterangan_gaji SET keterangan='$keterangan', tipe='$tipe' WHERE id='$id'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: index.php?success=update"); // kembali ke halaman data pengguna
    } else {
        header("Location: index.php?error=sql");
        exit;
    }
}
?>