<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $jabatan = $_POST['jabatan'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $no_rekening = $_POST['no_rekening'];
    $rek_bank = $_POST['rek_bank'];
    $id_perusahaan = $_POST['id_perusahaan'];

    $sql = "UPDATE karyawan SET nama='$nama', alamat='$alamat', jabatan='$jabatan', no_hp='$no_hp', email='$email', no_rekening='$no_rekening', rek_bank='$rek_bank', id_perusahaan='$id_perusahaan' WHERE id='$id'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: index.php?success=update"); // kembali ke halaman data pengguna
    } else {
        header("Location: index.php?error=sql");
        exit;
    }
}
?>