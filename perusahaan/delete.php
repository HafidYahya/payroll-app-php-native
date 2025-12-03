<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $sql = "DELETE FROM perusahaan WHERE id='$id'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        header("Location: index.php?success=deleted");
    } else {
        header("Location: index.php?error=deletefail");
    }
    exit;
}
?>