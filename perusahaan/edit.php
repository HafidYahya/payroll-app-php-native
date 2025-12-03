<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $now = date('Y-m-d H:i:s', time());

    $sql = "UPDATE perusahaan SET nama='$nama', alamat='$alamat', no_hp='$no_hp', email='$email' WHERE id='$id'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: index.php?success=update"); // kembali ke halaman data pengguna
    } else {
        header("Location: index.php?error=sql");
        exit;
    }
}
?>