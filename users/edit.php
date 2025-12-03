<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    
    // Password
    $password = $_POST['password'];
    $old_password = $_POST['old_password'];

    if (!empty($password)) {
        $konfirmasi = $_POST['konfirmasi_password'];
        if ($password !== $konfirmasi) {
            header("Location: index.php?error=password");
            exit;
        }
        $password_db = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $password_db = $old_password; // tetap pakai password lama
    }

    $sql = "UPDATE users SET nama='$nama', username='$username', password='$password_db', role='$role' WHERE id='$id'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: index.php?success=update"); // kembali ke halaman data pengguna
    } else {
        header("Location: index.php?error=sql");
        exit;
    }
}
?>