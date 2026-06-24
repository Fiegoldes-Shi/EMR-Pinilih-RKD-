<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SESSION["idUser"]) && isset($_SESSION["baseurl"])) {
    header('Location: ' . $_SESSION["baseurl"]);
    exit();
}


include_once("_function_i/cConnect.php");
include_once("_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

if (empty($_POST["btnpost"])) {
    $gu = 0;
    $gp = 0;
    $alert = "";
} else {
    $gp = 1;
    if (empty($_POST["unme"])) {
        $gu = 0;
        $gp = 0;
    } else {
        if (empty($_POST["pswd"])) {
            $gu = 1;
            $gp = 0;
        } else {
            $un = strip_tags($_POST["unme"]);
            $up = md5(strip_tags($_POST["pswd"]));

            $sql = "SELECT a.* FROM user a WHERE a.username = ? AND a.password = ?";

            $view = new cView();
            $array = $view->vViewDataPrepared($sql, [$un, $up], "ss");

            $gu = 0;
            $gp = 1;
            foreach ($array as $value) {
                $gu = 1;
                $gp = 1;
                $alert = "";
                $_SESSION["idUser"] = $value["idUser"];
                $_SESSION["nama"] = $value["nama"];
                $_SESSION["role"] = $value["role"];
                $_SESSION["baseurl"] = $value["urlbase"];
                $_SESSION["jabatan"] = $value["jbtn"];
                $_SESSION["aktif"] = $value["status_aktif"];
            }
        }
    }
}

if ($gu == 0 and $gp == 1) {
    $alert = 'Username atau Password Salah !';
} elseif ($gu == 1 and $gp == 0) {
    $alert = 'Username atau Password Salah !';
} elseif ($gu == 0 and $gp == 0) {
    $alert = !empty($_POST["btnpost"]) ? 'Username dan Password tidak boleh kosong !' : '';
} elseif ($gu == 1 and $gp == 1) {
    header('Location: ' . $_SESSION["baseurl"]);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dff2fd;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
        }

        .login-image {
            max-width: 100%;
            height: auto;
        }

        .login-form {
            width: 90%;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-login {
            border-radius: 10px;
            font-weight: bold;
            padding: 10px;
            background-color: #08abea;
            color: white;
        }

        .btn-login:hover {
            background-color: #dff2fd;
            border-color: #08abea;
            border-width: 2px;
            color: #08abea;
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</head>


<body>
    <div class="container-fluid mt-3">
        <br>
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 w-100">
                <div class="container d-flex justify-content-center align-items-center">
                    <div class="login-container w-100 px-2 px-md-4">
                        <div class="row align-items-center justify-content-center" style="min-height: 75vh;">
                            <div
                                class="col-md-7 text-center text-md-start d-flex flex-column justify-content-center mb-4 mb-md-0">
                                <h1 class="fw-bold fs-3 fs-md-1">Selamat Datang!</h1>
                                <p class="text-muted small">Kelola data pasien pakai Sistem Rekam Medis Elektronik Rumah
                                    Kebugaran Difabel Yayasan Pinilih Sedayu</p>
                                <img src="_img/login.png" alt="login" class="img-fluid mx-auto mx-md-0"
                                    style="max-width: 95%; height: auto;">
                            </div>
                            <div class="col-md-5 d-flex flex-column align-items-center">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <img src="_img/ukdw.png" alt="UKDW" width="70" height="90">
                                    <img src="_img/logo.png" alt="Logo" width="90" height="90">
                                </div>
                                <br>
                                <form action="" method="post" class="login-form">
                                    <label for="username" class="fw-bold">Username</label>
                                    <input type="text" id="username" class="form-control mb-3" placeholder="username"
                                        aria-label="Username" name="unme">
                                    <label for="password" class="fw-bold">Password</label>
                                    <div class="password-container position-relative">
                                        <input type="password" id="password" class="form-control" placeholder="password"
                                            aria-label="password" name="pswd">
                                        <span class="toggle-password" onclick="togglePassword()"><ion-icon id="eyeIcon"
                                                name="eye-outline"></ion-icon></span>
                                    </div>
                                    <div class="text-end mb-3">
                                        <a href="#" class="small text-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalEmail">Lupa password?</a>
                                    </div>
                                    <br>
                                    <input class="btn btn-login w-100" name="btnpost" type="submit" value=" LOGIN ">
                                    <br>
                                    <?php if (!empty($alert)): ?>
                                        <b>
                                            <p class="text-danger text-center small"><?= $alert; ?></p>
                                        </b>
                                    <?php endif; ?>
                                </form>

                                <div class="modal fade" id="modalEmail" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">RESET PASSWORD</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="sendOTP.php" method="post" id="formOTP">
                                                <div class="modal-body">
                                                    <p>Masukkan email Anda yang terdaftar. Kami akan mengirimkan
                                                        kode
                                                        verifikasi untuk mengatur ulang kata sandi Anda.</p>
                                                    <label for="alamatEmail">EMAIL</label>
                                                    <input type="email" id="alamatEmail" name="alamatEmail"
                                                        class="form-control" placeholder="Masukkan Email"
                                                        autocomplete="email" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" id="kirimEmail"
                                                        name="verifikasi1">Verifikasi Email</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalKode" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">VERIFIKASI OTP</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="verifyOTP.php" method="post" id="formVerifyOTP">
                                                <div class="modal-body">
                                                    <p class="text-muted small">Kode OTP 6-Digit telah dikirim ke
                                                        email
                                                        Anda. Periksa kotak masuk atau folder spam Anda.</p>
                                                    <label for="otp">Kode Verifikasi (OTP)</label>
                                                    <input type="text" id="otp" name="otp" class="form-control"
                                                        placeholder="Masukkan Kode 6-Digit" autocomplete="one-time-code"
                                                        required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" id="kirimKode"
                                                        name="verifikasi2">Verifikasi OTP</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">UBAH PASSWORD BARU</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="resetPassword.php" method="post" id="formResetPassword">
                                                <div class="modal-body">
                                                    <label for="passwordBaru">Password Baru</label>
                                                    <input type="password" id="passwordBaru" name="passwordBaru"
                                                        class="form-control mb-2" placeholder="Password Baru"
                                                        autocomplete="current-password" required>
                                                    <label for="konfirmasiPassword">Konfirmasi Password Baru</label>
                                                    <input type="password" id="konfirmasiPassword"
                                                        name="konfirmasiPassword" class="form-control"
                                                        placeholder="Konfirmasi Password" autocomplete="new-password"
                                                        required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" id="ubahPassword"
                                                        name="ubahPassword">Simpan Password Baru</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <script>
        function togglePassword() {
            let passwordField = document.getElementById("password");
            let eyeIcon = document.getElementById("eyeIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.setAttribute("name", "eye-off-outline");
            } else {
                passwordField.type = "password";
                eyeIcon.setAttribute("name", "eye-outline");
            }
        }

        $(document).on('submit', '#formOTP', function (e) {
            e.preventDefault();
            let email = $('#alamatEmail').val();

            let btn = $('#kirimEmail');
            let oriText = btn.text();
            btn.text('Mengirim Email...').prop('disabled', true);

            $.post('sendOTP.php', { alamatEmail: email }, function (response) {
                btn.text(oriText).prop('disabled', false);

                if (response.trim() === "success") {
                    $('#modalEmail').modal('hide');
                    $('#modalKode').modal('show');
                } else if (response.trim() === "not_found") {
                    alert("Mohon maaf, alamat email tersebut belum terdaftar dalam sistem kami. Silakan periksa kembali ejaan email Anda atau hubungi Administrator jika Anda memerlukan bantuan.");
                } else if (response.trim() === "smtp_error") {
                    alert("Mohon maaf, sistem sedang mengalami kendala dalam mengirimkan kode OTP. Silakan hubungi Administrator untuk bantuan lebih lanjut.");
                } else {
                    alert("Terjadi kesalahan sistem: " + response);
                }
            });
        });

        $(document).on('submit', '#formVerifyOTP', function (e) {
            e.preventDefault();
            let email = $('#alamatEmail').val();
            let otp = $('#otp').val();

            $.post('verifyOTP.php', { alamatEmail: email, otp: otp }, function (response) {
                if (response.trim() === "valid") {
                    $('#modalKode').modal('hide');
                    $('#modalPassword').modal('show');
                } else {
                    alert("Kode OTP salah atau tenggat waktu 10-menit telah kedaluwarsa!");
                }
            });
        });

        $(document).on('submit', '#formResetPassword', function (e) {
            e.preventDefault();
            let email = $('#alamatEmail').val();
            let passwordBaru = $('#passwordBaru').val();
            let konfirmasiPassword = $('#konfirmasiPassword').val();

            if (passwordBaru !== konfirmasiPassword) {
                alert("Konfirmasi password tidak cocok!");
                return;
            }

            $.post('resetPassword.php', { alamatEmail: email, passwordBaru: passwordBaru }, function (response) {
                if (response.trim() === "success") {
                    alert("Password berhasil diubah secara permanen! Silakan login dengan password baru.");
                    $('#formResetPassword')[0].reset();
                    $('#formOTP')[0].reset();
                    $('#formVerifyOTP')[0].reset();
                    $('#modalPassword').modal('hide');
                } else {
                    alert("Gagal mengubah password!");
                }
            });
        });

    </script>

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
                window.location.replace(window.location.pathname);
            }
        });
    </script>
</body>

</html>