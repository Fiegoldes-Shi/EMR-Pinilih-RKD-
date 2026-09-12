<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
?>
<?php
// Ambil data pengguna berdasarkan sesi login
$idUser = $_SESSION['idUser'];
$sql = "SELECT * FROM user WHERE idUser = ?";
$view = new cView();
$arrayprofil = $view->vViewDataPrepared($sql, [$idUser], "i");

if (!empty($arrayprofil)) {
    $dataprofil = $arrayprofil[0];
}

// Cek apakah data POST dengan key 'nilai' ada
if (isset($_POST['nama'])) {
    // Ubah nilai session sesuai data yang dikirim melalui POST
    $_SESSION['nama'] = $_POST['nama'];
}
?>


<div class="container mt-5">
    <h3 class="mb-3 fw-bold">Profil</h3>
    <div class="row align-items-stretch">
        <!-- Kartu Profil -->
        <div class="col-12 col-md-5 mb-3">
            <div class="card shadow-sm p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                <ion-icon name="person-circle-outline" class="display-1"></ion-icon>
                <h4 class="mt-3"><?= htmlspecialchars($dataprofil['username'] ?? ''); ?></h4>
                <button class="btn btn-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#ubahPasswordModal">
                    Ubah Password
                </button>
            </div>
        </div>

        <!-- Form Edit Profil -->
        <div class="col-md-6">
            <div class="h-100 d-flex flex-column justify-content-between">
                <form method="POST" class="h-100">
                    <input type="hidden" name="idUser" value="<?= $dataprofil['idUser']; ?>">

                    <div class="mb-3">
                        <label class="fw-bold">Username <span class="required">*</span></label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($dataprofil['username'] ?? ''); ?>"
                            readonly>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($dataprofil['nama'] ?? ''); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($dataprofil['email'] ?? ''); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nomor Telepon <span class="required">*</span></label>
                        <input type="text" class="form-control" name="noTelp" value="<?= htmlspecialchars($dataprofil['noTelp'] ?? ''); ?>"
                            required>
                    </div>

                    <button type="submit" name="editProfil" class="btn btn-primary w-100">SIMPAN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Password -->
<div class="modal fade" id="ubahPasswordModal" tabindex="-1" aria-labelledby="ubahPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="ubahPasswordModalLabel">Ubah Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="idUser" value="<?= $dataprofil['idUser']; ?>">

                    <div class="mb-3">
                        <label class="fw-bold">Password Lama <span class="required">*</span></label>
                        <input type="password" class="form-control" name="password_lama" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Password Baru <span class="required">*</span></label>
                        <input type="password" class="form-control" name="password_baru" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Konfirmasi Password Baru <span class="required">*</span></label>
                        <input type="password" class="form-control" name="konfirmasi_password" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="ubahPassword" class="btn btn-primary w-100">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Proses Update Profil
if (isset($_POST["editProfil"])) {
    $datafield = array("nama", "email", "noTelp");
    $datavalue = array(
        $_POST["nama"],
        $_POST["email"],
        $_POST["noTelp"]
    );

    $idUser = $_SESSION["idUser"];
    $update = new cUpdate();
    $update->vUpdateDataPrepared("user", $datafield, $datavalue, "idUser", $idUser);

    // Redirect manually since vUpdateDataPrepared doesn't handle it
    echo "<script>window.location.href='profil.php';</script>";
}

// Proses Ubah Password
if (isset($_POST["ubahPassword"])) {
    $passwordLama = md5($_POST["password_lama"]); // Enkripsi password lama
    $passwordBaru = md5($_POST["password_baru"]);
    $konfirmasiPassword = md5($_POST["konfirmasi_password"]);

    // Ambil password lama dari database
    $sqlPassword = "SELECT password FROM user WHERE idUser = ?";
    $arrayPassword = $view->vViewDataPrepared($sqlPassword, [$idUser], "i");
    $passwordDB = !empty($arrayPassword) ? $arrayPassword[0]['password'] : '';

    // Cek apakah password lama benar
    if ($passwordLama !== $passwordDB) {
        echo "<script>alert('Password lama salah!');</script>";
    } elseif ($passwordBaru !== $konfirmasiPassword) {
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    } else {
        // Update password jika benar
        $datafield = array("password");
        $datavalue = array($passwordBaru);
        $idUser = $_SESSION["idUser"];
        $update = new cUpdate();
        $update->vUpdateDataPrepared("user", $datafield, $datavalue, "idUser", $idUser);
        echo "<script>alert('Password berhasil diubah!'); window.location.href='profil.php';</script>";
    }
}
?>

<!-- backup data -->
<div class="container mt-5">
    <h3 class="mb-3 fw-bold">Backup Data</h3>
    <div class="col-12 col-md-10 col-lg-11">
        <div class="card shadow-sm p-4">
            <h6>Klik tombol di bawah untuk mencadangkan seluruh data sistem ke dalam file .sql</h6>
            <form action="backupData.php" method="post">
                <button type="submit" class="btn btn-success btn-lg"
                    onclick="return confirm('Apakah Anda yakin ingin mencadangkan data sekarang?')">
                    <i class="fa-solid fa-download" style="color: #ffffff;"></i>
                    <b>BACK UP DATA</b>
                </button>
            </form>
        </div>
    </div>
</div>

<div>
    <br><br><br><br>
</div>