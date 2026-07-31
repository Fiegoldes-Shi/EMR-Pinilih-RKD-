<?php
// === BASEURL DINAMIS (tanpa hardcode localhost) ===
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'); // ambil parent folder (../)
$baseurl = $protocol . $host . $path;

// === KONFIGURASI DATABASE (gunakan environment variable atau default) ===
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'emr_pinilih';

try {
    // Koneksi PDO
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>

<?php
// insert
if (!empty($_POST["savebtn"])) {
    $linkurl = 21;

    $idSubDisabilitas = $_POST["subDisabilitas"];
    $idKelurahanDomisili = !empty($_POST['kelurahan']) ? $_POST['kelurahan'] : NULL;
    $kodePosDomisili = !empty($_POST["kodePosDomisili"]) ? $_POST["kodePosDomisili"] : NULL;
    $RTDomisili = !empty($_POST["RTDomisili"]) ? $_POST["RTDomisili"] : NULL;
    $RWDomisili = !empty($_POST["RWDomisili"]) ? $_POST["RWDomisili"] : NULL;
    $alasanTidakAktif = isset($_POST["alasanTidakAktif"]) ? $_POST["alasanTidakAktif"] : "";

    if (!empty($_POST["riwayatPenyakitPribadi"])) {
        $riwayatPenyakitPribadi = implode(", ", $_POST["riwayatPenyakitPribadi"]);
    } else {
        $riwayatPenyakitPribadi = "";
    }
    if (!empty($_POST["riwayatPenyakitKeluarga"])) {
        $riwayatPenyakitKeluarga = implode(", ", $_POST["riwayatPenyakitKeluarga"]);
    } else {
        $riwayatPenyakitKeluarga = "";
    }

    $datafield_pasien = array("namaLengkap", "namaPanggilan", "nik", "tempatLahir", "tanggalLahir", "kelompokUsia", "jenisKelamin", "golonganDarah", "noTeleponPasien", "alamatLengkap", "alamatDomisili", "tanggalAktif", "statusPasien", "alasanTidakAktif", "idSubDisabilitas", "alatBantu", "kebutuhanKhusus", "riwayatPenyakitPribadi", "riwayatPenyakitKeluarga", "alergi", "trauma", "namaOrangTua", "noTelpOrangTua", "namaPendamping", "noTelpPendamping", "namaJalan", "idKelurahanDomisili", "kodePosDomisili", "RTDomisili", "RWDomisili", "agama", "suku", "bahasaDikuasai", "pendidikan", "pekerjaan", "statusPernikahan", "keterangan");

    // value - HAPUS QUOTE MANUAL & Handle NULLs correctly for Prepared Statements
    $datavalue_pasien = array(
        $_POST["namaLengkap"],
        $_POST["namaPanggilan"],
        $_POST["nik"],
        $_POST["tempatLahir"],
        $_POST["tanggalLahir"],
        $_POST["kelompokUsia"],
        $_POST["jenisKelamin"],
        $_POST["golonganDarah"],
        $_POST["noTeleponPasien"],
        $_POST["alamatLengkap"],
        $_POST["alamatDomisili"],
        $_POST["tanggalAktif"],
        $_POST["statusPasien"],
        $alasanTidakAktif,
        $idSubDisabilitas,
        $_POST["alatBantu"] === '' ? "" : $_POST["alatBantu"],
        $_POST["kebutuhanKhusus"] === '' ? "" : $_POST["kebutuhanKhusus"],
        $riwayatPenyakitPribadi,
        $riwayatPenyakitKeluarga,
        $_POST["alergi"] === '' ? "" : $_POST["alergi"],
        $_POST["trauma"] === '' ? "" : $_POST["trauma"],
        $_POST["namaOrtu"] === '' ? "" : $_POST["namaOrtu"],
        $_POST["noTelpOrtu"] === '' ? "" : $_POST["noTelpOrtu"],
        $_POST["namaPendamping"] === '' ? "" : $_POST["namaPendamping"],
        $_POST["noTelpPendamping"] === '' ? "" : $_POST["noTelpPendamping"],
        $_POST["namaJalan"] === '' ? "" : $_POST["namaJalan"],
        $idKelurahanDomisili, // Pass param directly (null or value)
        $kodePosDomisili,
        $RTDomisili,
        $RWDomisili,
        $_POST["agama"] === '' ? "" : $_POST["agama"],
        $_POST["suku"] === '' ? "" : $_POST["suku"],
        $_POST["bahasaDikuasai"] === '' ? "" : $_POST["bahasaDikuasai"],
        $_POST["pendidikan"] === '' ? "" : $_POST["pendidikan"],
        $_POST["pekerjaan"] === '' ? "" : $_POST["pekerjaan"],
        $_POST["statusPernikahan"] === '' ? "" : $_POST["statusPernikahan"],
        $_POST["keterangan"] === '' ? "" : $_POST["keterangan"]
    );

    $insert = new cInsert();

    // Tangkap output SweetAlert insert pasien agar tidak muncul dua pop-up
    ob_start();
    $insert->vInsertDataPrepared("pasien", $datafield_pasien, $datavalue_pasien);
    ob_end_clean();

    // Otomatis tambah ke tabel peserta dengan data dari pasien yang baru diinput
    $idSubDisabilitasPeserta = !empty($idSubDisabilitas) ? (int)$idSubDisabilitas : NULL;
    $datafield_peserta = array("nama", "asalLembaga", "jenisKelamin", "usia", "alamat", "idSubDisabilitas");
    $datavalue_peserta = array(
        $_POST["namaLengkap"],
        NULL,
        $_POST["jenisKelamin"],
        $_POST["kelompokUsia"],
        $_POST["alamatDomisili"],
        $idSubDisabilitasPeserta
    );
    // Insert peserta — SweetAlert dari sini yang ditampilkan ke pengguna
    $insert->vInsertDataPrepared("peserta", $datafield_peserta, $datavalue_peserta);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = 21;

    $idPasien = $_POST["idPasien"];
    $idSubDisabilitas = !empty($_POST["subDisabilitas"]) ? (is_array($_POST["subDisabilitas"]) ? implode(",", $_POST["subDisabilitas"]) : $_POST["subDisabilitas"]) : "NULL";

    $idKelurahanDomisili = !empty($_POST["kelurahan"][$idPasien]) ? $_POST["kelurahan"][$idPasien] : NULL;
    $kodePosDomisili = !empty($_POST["kodePosDomisili"]) ? $_POST["kodePosDomisili"] : NULL;
    $RTDomisili = !empty($_POST["RTDomisili"]) ? $_POST["RTDomisili"] : NULL;
    $RWDomisili = !empty($_POST["RWDomisili"]) ? $_POST["RWDomisili"] : NULL;

    if (!empty($_POST["riwayatPenyakitPribadi"])) {
        $riwayatPenyakitPribadi = implode(", ", $_POST["riwayatPenyakitPribadi"]);
    } else {
        $riwayatPenyakitPribadi = "";
    }
    if (!empty($_POST["riwayatPenyakitKeluarga"])) {
        $riwayatPenyakitKeluarga = implode(", ", $_POST["riwayatPenyakitKeluarga"]);
    } else {
        $riwayatPenyakitKeluarga = "";
    }

    $statusPasien = isset($_POST["statusPasien"]) ? $_POST["statusPasien"] : "0";
    $alasanTidakAktif = isset($_POST["alasanTidakAktif"]) ? $_POST["alasanTidakAktif"] : "";

    $datafield_pasien = array("namaLengkap", "namaPanggilan", "nik", "tempatLahir", "tanggalLahir", "kelompokUsia", "jenisKelamin", "golonganDarah", "noTeleponPasien", "alamatLengkap", "alamatDomisili", "tanggalAktif", "statusPasien", "alasanTidakAktif", "idSubDisabilitas", "alatBantu", "kebutuhanKhusus", "riwayatPenyakitPribadi", "riwayatPenyakitKeluarga", "alergi", "trauma", "namaOrangTua", "noTelpOrangTua", "namaPendamping", "noTelpPendamping", "namaJalan", "idKelurahanDomisili", "kodePosDomisili", "RTDomisili", "RWDomisili", "agama", "suku", "bahasaDikuasai", "pendidikan", "pekerjaan", "statusPernikahan", "keterangan");
    $datavalue_pasien = array(
        $_POST["namaLengkap"],
        $_POST["namaPanggilan"],
        $_POST["nik"],
        $_POST["tempatLahir"],
        $_POST["tanggalLahir"],
        $_POST["kelompokUsia"],
        $_POST["jenisKelamin"],
        $_POST["golonganDarah"],
        $_POST["noTeleponPasien"],
        $_POST["alamatLengkap"],
        $_POST["alamatDomisili"],
        $_POST["tanggalAktif"],
        $statusPasien,
        $alasanTidakAktif,
        $idSubDisabilitas,
        $_POST["alatBantu"] === '' ? "" : $_POST["alatBantu"],
        $_POST["kebutuhanKhusus"] === '' ? "" : $_POST["kebutuhanKhusus"],
        $riwayatPenyakitPribadi,
        $riwayatPenyakitKeluarga,
        $_POST["alergi"] === '' ? "" : $_POST["alergi"],
        $_POST["trauma"] === '' ? "" : $_POST["trauma"],
        $_POST["namaOrtu"] === '' ? "" : $_POST["namaOrtu"],
        $_POST["noTelpOrtu"] === '' ? "" : $_POST["noTelpOrtu"],
        $_POST["namaPendamping"] === '' ? "" : $_POST["namaPendamping"],
        $_POST["noTelpPendamping"] === '' ? "" : $_POST["noTelpPendamping"],
        $_POST["namaJalan"] === '' ? "" : $_POST["namaJalan"],
        $idKelurahanDomisili,
        $kodePosDomisili,
        $RTDomisili,
        $RWDomisili,
        $_POST["agama"] === '' ? "" : $_POST["agama"],
        $_POST["suku"] === '' ? "" : $_POST["suku"],
        $_POST["bahasaDikuasai"] === '' ? "" : $_POST["bahasaDikuasai"],
        $_POST["pendidikan"] === '' ? "" : $_POST["pendidikan"],
        $_POST["pekerjaan"] === '' ? "" : $_POST["pekerjaan"],
        $_POST["statusPernikahan"] === '' ? "" : $_POST["statusPernikahan"],
        $_POST["keterangan"] === '' ? "" : $_POST["keterangan"]
    );

    $whereCol = "idPasien";
    $whereVal = $_POST["idPasien"];

    $update = new cUpdate();
    // Gunakan vUpdateDataPrepared
    $update->vUpdateDataPrepared("pasien", $datafield_pasien, $datavalue_pasien, $whereCol, $whereVal);
}
?>

<?php
// delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btnhapus"])) {
    $passwordInput = trim($_POST["password_verif"] ?? '');

    $sql = "SELECT password FROM user WHERE role = 2 AND jbtn = 'Ketua' LIMIT 1";
    $view = new cView();
    $result = $view->vViewData($sql)[0] ?? null;

    // Bandingkan menggunakan md5 jika memang disimpan dengan md5
    $hashedInput = md5($passwordInput);

    if ($result && $hashedInput === $result["password"]) {
        if (!empty($_POST["hiddendeletevalue"])) {
            $delete = new cDelete();
            foreach ($_POST["hiddendeletevalue"] as $data) {
                $delete->vDeleteDataPrepared($data["table"], $data["field"], $data["value"]);
            }
        }
    } else {
        echo "<script>alert('Password salah');</script>";
    }
}
?>

<?php
// Query ENUM 'kelompokUsia'
$usia = "SHOW COLUMNS FROM pasien LIKE 'kelompokUsia'";
$view = new cView();
$arrayUsia = $view->vViewData($usia);
$enumKelompokUsia = [];
if (!empty($arrayUsia)) {
    $row = $arrayUsia[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumKelompokUsia = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'jenisKelamin'
$jk = "SHOW COLUMNS FROM pasien LIKE 'jenisKelamin'";
$view = new cView();
$arrayJK = $view->vViewData($jk);
$enumJK = [];
if (!empty($arrayJK)) {
    $row = $arrayJK[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumJK = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'golonganDarah'
$goldar = "SHOW COLUMNS FROM pasien LIKE 'golonganDarah'";
$view = new cView();
$arrayGoldar = $view->vViewData($goldar);
$enumGoldar = [];
if (!empty($arrayGoldar)) {
    $row = $arrayGoldar[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumGoldar = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'agama'
$agama = "SHOW COLUMNS FROM pasien LIKE 'agama'";
$view = new cView();
$arrayAgama = $view->vViewData($agama);
$enumAgama = [];
if (!empty($arrayAgama)) {
    $row = $arrayAgama[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumAgama = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'pendidikan'
$pendidikan = "SHOW COLUMNS FROM pasien LIKE 'pendidikan'";
$view = new cView();
$arrayPendidikan = $view->vViewData($pendidikan);
$enumPendidikan = [];
if (!empty($arrayPendidikan)) {
    $row = $arrayPendidikan[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumPendidikan = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'pekerjaan'
$pekerjaan = "SHOW COLUMNS FROM pasien LIKE 'pekerjaan'";
$view = new cView();
$arrayPekerjaan = $view->vViewData($pekerjaan);
$enumPekerjaan = [];
if (!empty($arrayPekerjaan)) {
    $row = $arrayPekerjaan[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumPekerjaan = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'statusPernikahan'
$statusPernikahan = "SHOW COLUMNS FROM pasien LIKE 'statusPernikahan'";
$view = new cView();
$arrayStatusNikah = $view->vViewData($statusPernikahan);
$enumNikah = [];
if (!empty($arrayStatusNikah)) {
    $row = $arrayStatusNikah[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumNikah = explode(",", str_replace("'", "", $matches[1]));
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Pasien</title>

    <script src="../admin/js/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-11">
                <figure>
                    <blockquote class="blockquote">
                        <p>DATA PASIEN</p>
                    </blockquote>
                    <figcaption class="blockquote-footer">Entri Data Pasien</figcaption>
                </figure>
            </div>

            <div class="col-12 col-md-2 col-lg-1 mb-3">
                <button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                    style="border-radius: 10px; width: 100%; height: 60%; display: flex;">
                    <i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>
                </button>

                <!-- Modal Insert -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title fs-5" id="exampleModalLabel">
                                    <blockquote class="blockquote">
                                        <p>Tambah Data Pasien</p>
                                    </blockquote>
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form class="" method="post" action="data-pasien" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="namaLengkap">Nama Lengkap Pasien <span
                                                    class="required">*</span></label>
                                            <input class="form-control" type="text" name="namaLengkap" id="namaLengkap"
                                                value="" placeholder="Nama lengkap pasien" maxlength="255" size=""
                                                required>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="namaPanggilan">Nama Panggilan <span
                                                    class="required">*</span></label>
                                            <input class="form-control" type="text" name="namaPanggilan"
                                                id="namaPanggilan" value="" placeholder="Nama panggilan pasien"
                                                maxlength="255" size="" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nik">NIK <span class="required">*</span></label>
                                        <input class="form-control" type="number" name="nik" id="nik" value=""
                                            placeholder="NIK Pasien" maxlength="255" size="" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="tempatLahir">Tempat Lahir <span
                                                    class="required">*</span></label>
                                            <input class="form-control" type="text" name="tempatLahir" id="tempatLahir"
                                                value="" placeholder="Tempat lahir pasien" maxlength="255" size=""
                                                required>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="tanggalLahir">Tanggal Lahir <span
                                                    class="required">*</span></label>
                                            <input class="form-control" type="date" name="tanggalLahir"
                                                id="tanggalLahir" value="" placeholder="Tanggal lahir pasien"
                                                maxlength="" size="" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kelompokUsia">Kelompok Usia <span class="required">*</span></label>
                                        <select name="kelompokUsia" class="form-control" required>
                                            <option value="">- pilihan -</option>
                                            <?php
                                            foreach ($enumKelompokUsia as $option) {
                                                $trimmedValue = trim($option);
                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="jenisKelamin">Jenis Kelamin <span
                                                    class="required">*</span></label>
                                            <select name="jenisKelamin" class="form-control" required>
                                                <option value="">- pilihan -</option>
                                                <?php
                                                foreach ($enumJK as $option) {
                                                    $trimmedValue = trim($option);
                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="golonganDarah">Golongan Darah <span
                                                    class="required">*</span></label>
                                            <select name="golonganDarah" class="form-control" required>
                                                <option value="">- pilihan -</option>
                                                <?php
                                                foreach ($enumGoldar as $option) {
                                                    $trimmedValue = trim($option);
                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="noTeleponPasien">Nomor Telepon Pasien <span
                                                class="required">*</span></label>
                                        <input class="form-control" type="number" name="noTeleponPasien"
                                            id="noTeleponPasien" value="" placeholder="Nomor telepon pasien"
                                            maxlength="16" size="" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamatLengkap">Alamat Lengkap (sesuai KTP) <span
                                                class="required">*</span></label>
                                        <textarea class="form-control" id="alamatLengkap" name="alamatLengkap"
                                            placeholder="Alamat lengkap sesuai KTP" rows="3" cols=""
                                            id="floatingTextarea" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamatDomisili">Alamat Lengkap Domisili <span
                                                class="required">*</span></label>
                                        <textarea class="form-control" id="alamatDomisili" name="alamatDomisili"
                                            placeholder="Alamat lengkap domisili" rows="3" cols="" id="floatingTextarea"
                                            required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggalAktif">Tanggal Mulai Aktif <span
                                                class="required">*</span></label>
                                        <input class="form-control" type="date" name="tanggalAktif" id="tanggalAktif"
                                            value="" placeholder="Tanggal pasien mulai aktif" maxlength="" size=""
                                            required>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="statusPasien">Status Pasien <span
                                                    class="required">*</span></label>
                                            <select name="statusPasien" class="form-control" id="statusPasien" required>
                                                <option value="">- pilihan -</option>
                                                <option value="Aktif">Aktif</option>
                                                <option value="Tidak Aktif">Tidak Aktif</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="alasanTidakAktif">Alasan Tidak Aktif</label>
                                            <input class="form-control" type="text" name="alasanTidakAktif"
                                                id="alasanTidakAktif" value="" placeholder="Alasan pasien tidak aktif"
                                                maxlength="255" disabled>
                                        </div>
                                        <script>
                                            document.getElementById("statusPasien").addEventListener("change", function () {
                                                var alasanInput = document.getElementById("alasanTidakAktif");
                                                if (this.value === "Tidak Aktif") {
                                                    alasanInput.removeAttribute("disabled"); // Aktifkan input jika "Tidak Aktif"
                                                } else {
                                                    alasanInput.setAttribute("disabled", "true"); // Nonaktifkan input jika "Aktif"
                                                    alasanInput.value = ""; // Kosongkan input ketika dinonaktifkan
                                                }
                                            });
                                        </script>

                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="jenisDisabilitas">Jenis Disabilitas</label>
                                            <select name="jenisDisabilitas" id="jenisDisabilitas" class="form-control">
                                                <option value="">- pilihan -</option>

                                                <?php
                                                // Buat query untuk menampilkan semua data siswa
                                                $sql = $pdo->prepare("SELECT * FROM jenis_disabilitas ORDER BY idJenisDisabilitas");
                                                $sql->execute(); // Eksekusi querynya
                                                
                                                while ($data = $sql->fetch()) { // Ambil semua data dari hasil eksekusi $sql
                                                    echo "<option value='" . $data['idJenisDisabilitas'] . "'>" . $data['jenisDisabilitas'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="subDisabilitas">Sub Jenis Disabilitas <span id="reqSubDis"
                                                    class="required" style="display:none;">*</span></label>
                                            <select name="subDisabilitas" id="subDisabilitas" class="form-control">
                                                <option value="">- pilihan -</option>
                                            </select>
                                        </div>

                                        <script>
                                            // Toggle kewajiban (required) Sub Disabilitas bergantung dari isian Jenis Disabilitas
                                            document.getElementById('jenisDisabilitas').addEventListener('change', function () {
                                                var val = this.value;
                                                var subSelect = document.getElementById('subDisabilitas');
                                                var reqSpan = document.getElementById('reqSubDis');

                                                if (val && val.trim() !== "") {
                                                    subSelect.setAttribute('required', 'required');
                                                    reqSpan.style.display = 'inline-block';
                                                } else {
                                                    subSelect.removeAttribute('required');
                                                    reqSpan.style.display = 'none';
                                                }
                                            });
                                        </script>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="alatBantu">Alat Bantu</label>
                                            <textarea class="form-control" id="alatBantu" name="alatBantu"
                                                placeholder="Alat bantu yang dibutuhkan pasien" rows="3" cols=""
                                                id="floatingTextarea"></textarea>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="kebutuhanKhusus">Kebutuhan Khusus</label>
                                            <textarea class="form-control" id="kebutuhanKhusus" name="kebutuhanKhusus"
                                                placeholder="Kebutuhan khusus yang mendukung kenyamanan pasien" rows="3"
                                                cols="" id="floatingTextarea"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="riwayatPenyakitPribadi">Riwayat Penyakit
                                            Pribadi</label><br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Diabetes Mellitus"
                                                        id="diabetes">
                                                    <label class="form-check-label" for="diabetes">Diabetes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Hipertensi"
                                                        id="hipertensi">
                                                    <label class="form-check-label" for="hipertensi">Hipertensi</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Jantung" id="jantung">
                                                    <label class="form-check-label" for="jantung">Jantung</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Kanker" id="kanker">
                                                    <label class="form-check-label" for="kanker">Kanker</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Rhematik" id="rhematik">
                                                    <label class="form-check-label" for="rhematik">Rhematik</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Asma" id="asma">
                                                    <label class="form-check-label" for="asma">Asma</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Asam Lambung"
                                                        id="asamLambung">
                                                    <label class="form-check-label" for="asamLambung">Asam
                                                        Lambung</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitPribadi[]" value="Lain-lain" id="lainlain">
                                                    <label class="form-check-label" for="lainlain">Lain-lain</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="riwayatPenyakitKeluarga">Riwayat Penyakit Keluarga</label><br>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Diabetes Mellitus"
                                                        id="diabetes">
                                                    <label class="form-check-label" for="diabetes">Diabetes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Hipertensi"
                                                        id="hipertensi">
                                                    <label class="form-check-label" for="hipertensi">Hipertensi</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Jantung" id="jantung">
                                                    <label class="form-check-label" for="jantung">Jantung</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Kanker" id="kanker">
                                                    <label class="form-check-label" for="kanker">Kanker</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Rhematik" id="rhematik">
                                                    <label class="form-check-label" for="rhematik">Rhematik</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Asma" id="asma">
                                                    <label class="form-check-label" for="asma">Asma</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Asam Lambung"
                                                        id="asamLambung">
                                                    <label class="form-check-label" for="asamLambung">Asam
                                                        Lambung</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="riwayatPenyakitKeluarga[]" value="Lain-lain"
                                                        id="lainlain">
                                                    <label class="form-check-label" for="lainlain">Lain-lain</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="alergi">Alergi</label>
                                            <input class="form-control" type="text" name="alergi" id="alergi" value=""
                                                placeholder="Alergi pasien" maxlength="255" size="">
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="trauma">Trauma/Cedera</label>
                                            <input class="form-control" type="text" name="trauma" id="trauma" value=""
                                                placeholder="Trauma/Cedera pasien" maxlength="255" size="">
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="namaOrtu">Nama Orang Tua</label>
                                            <input class="form-control" type="text" name="namaOrtu" id="namaOrtu"
                                                value="" placeholder="Nama Orang Tua" maxlength="255" size="">
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="noTelpOrtu">Nomor Telepon Orang Tua</label>
                                            <input class="form-control" type="number" name="noTelpOrtu" id="noTelpOrtu"
                                                value="" placeholder="Nomor Telepon Orang Tua" maxlength="16" size="">
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="namaPendamping">Nama Pendamping</label>
                                            <input class="form-control" type="text" name="namaPendamping"
                                                id="namaPendamping" value="" placeholder="Nama Pendamping"
                                                maxlength="255" size="">
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="noTelpPendamping">Nomor Telepon Pendamping</label>
                                            <input class="form-control" type="number" name="noTelpPendamping"
                                                id="noTelpPendamping" value="" placeholder="Nomor Telepon Pendamping"
                                                maxlength="16" size="">
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-body border border-2">
                                            <p><strong>Alamat Domisili Pasien</strong></p>
                                            <div class="row">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="namaJalan">Nama Jalan</label>
                                                    <input class="form-control" type="text" name="namaJalan"
                                                        id="namaJalan" value="" placeholder="Nama jalan" maxlength="255"
                                                        size="">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="provinsi">Provinsi</label>
                                                    <select name="provinsi" id="provinsi" class="form-control">
                                                        <option value="">- pilihan -</option>

                                                        <?php
                                                        $sql = $pdo->prepare("SELECT * FROM provinsi ORDER BY idProvinsi");
                                                        $sql->execute();

                                                        while ($data = $sql->fetch()) {
                                                            echo "<option value='" . $data['idProvinsi'] . "'>" . $data['namaProvinsi'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="kotaKab">Kota / Kabupaten</label>
                                                    <select name="kotaKab" id="kotaKab" class="form-control">
                                                        <option value="">- pilihan -</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="kecamatan">Kecamatan</label>
                                                    <select name="kecamatan" id="kecamatan" class="form-control">
                                                        <option value="">- pilihan -</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="kelurahan">Kelurahan</label>
                                                    <select name="kelurahan" id="kelurahan" class="form-control">
                                                        <option value="">- pilihan -</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="kodePosDomisili">Kode Pos Domisili</label>
                                                    <input type="number" name="kodePosDomisili" value=""
                                                        id="kodePosDomisili" class="form-control"
                                                        placeholder="Kode Pos Domisili" maxlength="5" size="">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="RTDomisili">RT Domisili</label>
                                                    <input type="number" name="RTDomisili" value="" id="RTDomisili"
                                                        class="form-control" placeholder="RT Domisili" maxlength="3"
                                                        size="">
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label for="RWDomisili">RW Domisili</label>
                                                    <input type="number" name="RWDomisili" value="" id="RWDomisili"
                                                        class="form-control" placeholder="RW Domisili" maxlength="3"
                                                        size="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 my-3">
                                            <label for="agama">Agama</label>
                                            <select name="agama" class="form-control">
                                                <option value="">- pilihan -</option>
                                                <?php
                                                foreach ($enumAgama as $option) {
                                                    $trimmedValue = trim($option);
                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 my-3">
                                            <label for="suku">Suku</label>
                                            <input class="form-control" type="text" name="suku" id="suku" value=""
                                                placeholder="Suku" maxlength="255" size="">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bahasaDikuasai">Bahasa Dikuasai</label>
                                        <input class="form-control" type="text" name="bahasaDikuasai"
                                            id="bahasaDikuasai" value="" placeholder="Bahasa yang dikuasai pasien"
                                            maxlength="255" size="">
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="pendidikan">Pendidikan Terakhir</label>
                                            <select name="pendidikan" class="form-control">
                                                <option value="">- pilihan -</option>
                                                <?php
                                                foreach ($enumPendidikan as $option) {
                                                    $trimmedValue = trim($option);
                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="pekerjaan">Pekerjaan</label>
                                            <select name="pekerjaan" class="form-control">
                                                <option value="">- pilihan -</option>
                                                <?php
                                                foreach ($enumPekerjaan as $option) {
                                                    $trimmedValue = trim($option);
                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="statusPernikahan">Status Pernikahan</label>
                                        <select name="statusPernikahan" class="form-control">
                                            <option value="">- pilihan -</option>
                                            <?php
                                            foreach ($enumNikah as $option) {
                                                $trimmedValue = trim($option);
                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea class="form-control" id="keterangan" name="keterangan"
                                            placeholder="Keterangan / Catatan untuk pasien" rows="3" cols=""
                                            id="floatingTextarea"></textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;"
                                        name="savebtn" value="true">Simpan</button>
                                    <button type="reset" class="btn btn-warning btn-sm" style="border-radius: 25px;"
                                        name="" value="true">Ulang</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                                        style="border-radius: 25px;">Tutup</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Data loaded via AJAX -->
                    <div id="" class='table-responsive'>
                        <table id="tablePasien" class="table table-condensed">
                            <thead>
                                <tr class=''>
                                    <th width='5%'>No.</th>
                                    <th width='17%'>Nama Pasien</th>
                                    <th width='18%'>Usia</th>
                                    <th width='15%'>Jenis Kelamin</th>
                                    <th width='15%'>Kelurahan Domisili</th>
                                    <th width=''>Disabilitas</th>
                                    <th width='5%'>VIEW</th>
                                    <th width='5%'>EDIT</th>
                                    <th width='5%'>HAPUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <p></p>
    <div class="row">
        <div class="col-md-12">
            <p><br><br><br><br><br></p>
        </div>
    </div>


    <!-- Container for dynamic modals -->
    <div id="dynamic-modal-container"></div>

    <script>
        $(document).ready(function () {
            var table = $('#tablePasien').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "ajax/get_pasien.php",
                    "type": "POST"
                },
                "columns": [
                    { "data": "0" }, // No.
                    { "data": "1" }, // Nama Pasien
                    { "data": "2" }, // Usia
                    { "data": "3" }, // Jenis Kelamin
                    { "data": "4" }, // Kelurahan Domisili
                    { "data": "5" }, // Disabilitas
                    { "data": "6", "orderable": false, "searchable": false }, // VIEW
                    { "data": "7", "orderable": false, "searchable": false }, // EDIT
                    { "data": "8", "orderable": false, "searchable": false }  // HAPUS
                ]
            });

            // Handle View Modal
            $('#tablePasien').on('click', '.btn-view', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'ajax/modal_pasien.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'view'
                    },
                    success: function (response) {
                        $('#dynamic-modal-container').html(response);
                        var myModal = new bootstrap.Modal(document.getElementById('modalView'));
                        myModal.show();
                    }
                });
            });

            // Handle Edit Modal
            $('#tablePasien').on('click', '.btn-edit', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'ajax/modal_pasien.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'edit'
                    },
                    success: function (response) {
                        $('#dynamic-modal-container').html(response);
                        var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
                        myModal.show();
                    }
                });
            });

            // Handle Delete Modal
            $('#tablePasien').on('click', '.btn-delete', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'ajax/modal_pasien.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'delete'
                    },
                    success: function (response) {
                        $('#dynamic-modal-container').html(response);
                        var myModal = new bootstrap.Modal(document.getElementById('modalDelete'));
                        myModal.show();
                    }
                });
            });
        });
    </script>
</body>

</html>