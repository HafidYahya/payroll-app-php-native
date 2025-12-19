<?php
session_start();
if(!empty($_SESSION['role'])){
    header('location:dashboard.php');
    exit();
}
require 'config/koneksi.php';

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika user ditemukan
    if ($result->num_rows === 1) {

        $data = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $data['password'])) {
            $_SESSION["nama"] = $data['nama'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['id_karyawan'] = $data['id_karyawan'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errorPassword = "Password salah";
        }

    } else {
        $errorUsername = "Username tidak terdaftar";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOGIN | PAYROLL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="body-login">
    <div class="container-fluid justify-content-center d-flex align-items-center vh-100">
        <div class="card shadow card-login ">
            <div class="card-title bg-light">
                <h2 class="fw-bold text-center text-dark mt-5 mb-5">APLIKASI <span
                        class="fst-italic text-secondary">PAYROLL
                    </span><i class="fa-solid fa-coins"></i>
                </h2>
            </div>
            <div class="card-body ">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username*</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Masukan username">
                        <?php if(isset($errorUsername)): ?>
                        <p class="text-danger fst-italic"><?= $errorUsername ?></p>
                        <?php endif; ?>

                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password*</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <?php if(isset($errorPassword)): ?>
                        <p class="text-danger fst-italic"><?= $errorPassword ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="showPassword">
                        <label class="form-check-label" for="showPassword">Show Password</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-light shadow border-dark rounded-pill"
                            name="login">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script>
    document.getElementById("showPassword").addEventListener("change", function() {
        const passwordInput = document.getElementById("password");
        passwordInput.type = this.checked ? "text" : "password";
    });
    </script>
</body>

</html>