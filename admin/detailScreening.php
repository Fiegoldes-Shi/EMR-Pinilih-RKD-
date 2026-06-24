<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");

if (!isset($baseurl)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    $baseurl = $protocol . $host . $path;
}

// Ambil URL path dari permintaan
$request = $_SERVER['REQUEST_URI'];
$request = trim($request, '/');
$segments = explode('/', (string)$request);

// FIX: Use dynamic search for '331' (Menu ID) to handle different URL depths
$posMenu = array_search('331', $segments);
if ($posMenu !== false && isset($segments[$posMenu + 1])) {
    $idJadwal = $segments[$posMenu + 1];
} else {
    // Fallback
    $idJadwal = isset($segments[3]) ? $segments[3] : 0;
}

// echo $idJadwal;

$conn = new cConnect();
$conn->goConnect();

$view = new cView();

// Ambil data jadwal berdasarkan ID
$sqlDetail = "SELECT jp.*, prog.*, u.* FROM jadwal_program jp
              JOIN program prog ON jp.idProgram = prog.idProgram
              JOIN user u ON jp.idUser = u.idUser
              WHERE jp.idJadwal = ?";
$datajadwal = $view->vViewDataPrepared($sqlDetail, [$idJadwal], "i");

if (empty($datajadwal)) {
    die("Data tidak ditemukan.");
}

$datajadwal = $datajadwal[0]; // Ambil hasil pertama
?>

<div class="row mx-2">
    <div class="col-12 col-md-2 col-lg-1 mb-3" style="width: min-content; align-content: center;">
        <a href="<?= $baseurl ?>/admin/33"><ion-icon name="chevron-back-outline" size="large" style="color: black;"></ion-icon></a>
    </div>
    <div class="col-12 col-md-10 col-lg-11">
        <?php
        _myHeader("DETAIL SCREENING", "Detail Hasil Screening");
        ?>
    </div>
    <div class="col-md-12">
        <div class="card border-success border border-4">
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 200px;">Tanggal Kegiatan</th>
                        <td><?= $datajadwal['tanggalKegiatan'] ?></td>
                    </tr>
                    <tr>
                        <th>Waktu Mulai</th>
                        <td><?= $datajadwal['waktuMulai'] ?></td>
                    </tr>
                    <tr>
                        <th>Waktu Selesai</th>
                        <td><?= $datajadwal['waktuSelesai'] ?></td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td><?= $datajadwal['lokasi'] ?></td>
                    </tr>
                    <tr>
                        <th>Instansi</th>
                        <td><?= $datajadwal['instansi'] ?></td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td><?= nl2br($datajadwal['catatan']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// insert
if (!empty($_POST["savebtn"])) {
    $idUser = $_SESSION["idUser"];
    $linkurl = $idJadwal;

    // Menangani input angka (jika kosong, set NULL / null for prepared statement)
    $tinggiBadan = !empty($_POST["tinggiBadan"]) ? $_POST["tinggiBadan"] : null;
    $beratBadan = !empty($_POST["beratBadan"]) ? $_POST["beratBadan"] : null;
    $gulaDarah = !empty($_POST["gulaDarah"]) ? $_POST["gulaDarah"] : null;
    $kolesterol = !empty($_POST["kolesterol"]) ? $_POST["kolesterol"] : null;
    $trigliserida = !empty($_POST["trigliserida"]) ? $_POST["trigliserida"] : null;

    // Menggabungkan checkbox yang dipilih menjadi string dengan koma
    $faktorResikoPerilaku = !empty($_POST["faktorResikoPerilaku"]) ? implode(",", $_POST["faktorResikoPerilaku"]) : "";

    // Daftar field yang akan dimasukkan
    $datafield_hasil = array(
        "idJadwal",
        "idUser",
        "idPasien",
        "idTerapis",
        "keluhan",
        "tanggalRujukan",
        "alasanRujukan",
        "tinggiBadan",
        "beratBadan",
        "tekananDarah",
        "gulaDarah",
        "kolesterol",
        "trigliserida",
        "benjolanPayudara",
        "inspeksiVisualAsamAsetat",
        "kadarAlkoholPernafasan",
        "tesAmfetaminUrin",
        "arusPernafasanEkspirasi",
        "faktorResikoPerilaku",
        "hasilPemeriksaan",
        "diagnosis",
        "catatanTindakan"
    );

    // Daftar nilai yang akan dimasukkan
    $datavalue_hasil = array(
        $idJadwal,
        $idUser,
        $_POST["idPasien"],
        $_POST["idTerapis"],
        $_POST["keluhan"],
        $_POST["tanggalRujukan"] === '' ? "" : $_POST["tanggalRujukan"],
        $_POST["alasanRujukan"] === '' ? "" : $_POST["alasanRujukan"],
        $tinggiBadan,
        $beratBadan,
        $_POST["tekananDarah"] === '' ? "" : $_POST["tekananDarah"],
        $gulaDarah,
        $kolesterol,
        $trigliserida,
        $_POST["benjolanPayudara"] === '' ? "" : $_POST["benjolanPayudara"],
        $_POST["inspeksiVisualAsamAsetat"] === '' ? "" : $_POST["inspeksiVisualAsamAsetat"],
        $_POST["kadarAlkoholPernafasan"] === '' ? "" : $_POST["kadarAlkoholPernafasan"],
        $_POST["tesAmfetaminUrin"] === '' ? "" : $_POST["tesAmfetaminUrin"],
        $_POST["arusPernafasanEkspirasi"] === '' ? "" : $_POST["arusPernafasanEkspirasi"],
        $faktorResikoPerilaku,
        $_POST["hasilPemeriksaan"],
        $_POST["diagnosis"],
        $_POST["catatanTindakan"]
    );

    $insert = new cInsert();
    $insert->vInsertDataPrepared("hasil_layanan", $datafield_hasil, $datavalue_hasil);
}
?>



<?php
// update
if (!empty($_POST["editbtn"])) {
    $idUser = $_SESSION["idUser"];
    $linkurl = $_POST["idJadwal"]; // Ambil idJadwal dari form POST

    if (!empty($_POST["faktorResikoPerilaku"])) {
        $faktorResikoPerilaku = implode(",", $_POST["faktorResikoPerilaku"]); // Gabungkan nilai dengan koma
    } else {
        $faktorResikoPerilaku = ""; // Jika tidak ada yang dicentang, kosongkan
    }

    // Daftar field yang akan dimasukkan
    $datafield_hasil = array(
        "idJadwal",
        "idUser",
        "idPasien",
        "idTerapis",
        "keluhan",
        "tanggalRujukan",
        "alasanRujukan",
        "tinggiBadan",
        "beratBadan",
        "tekananDarah",
        "gulaDarah",
        "kolesterol",
        "trigliserida",
        "benjolanPayudara",
        "inspeksiVisualAsamAsetat",
        "kadarAlkoholPernafasan",
        "tesAmfetaminUrin",
        "arusPernafasanEkspirasi",
        "faktorResikoPerilaku",
        "hasilPemeriksaan",
        "diagnosis",
        "catatanTindakan"
    );

    // Daftar nilai yang akan dimasukkan
    $datavalue_hasil = array(
        $idJadwal,
        $idUser,
        $_POST["idPasien"],
        $_POST["idTerapis"],
        $_POST["keluhan"],
        $_POST["tanggalRujukan"] === '' ? "" : $_POST["tanggalRujukan"],
        $_POST["alasanRujukan"] === '' ? "" : $_POST["alasanRujukan"],
        $_POST["tinggiBadan"] === '' ? "" : $_POST["tinggiBadan"],
        $_POST["beratBadan"] === '' ? "" : $_POST["beratBadan"],
        $_POST["tekananDarah"] === '' ? "" : $_POST["tekananDarah"],
        $_POST["gulaDarah"] === '' ? "" : $_POST["gulaDarah"],
        $_POST["kolesterol"] === '' ? "" : $_POST["kolesterol"],
        $_POST["trigliserida"] === '' ? "" : $_POST["trigliserida"],
        $_POST["benjolanPayudara"] === '' ? "" : $_POST["benjolanPayudara"],
        $_POST["inspeksiVisualAsamAsetat"] === '' ? "" : $_POST["inspeksiVisualAsamAsetat"],
        $_POST["kadarAlkoholPernafasan"] === '' ? "" : $_POST["kadarAlkoholPernafasan"],
        $_POST["tesAmfetaminUrin"] === '' ? "" : $_POST["tesAmfetaminUrin"],
        $_POST["arusPernafasanEkspirasi"] === '' ? "" : $_POST["arusPernafasanEkspirasi"],
        $faktorResikoPerilaku,
        $_POST["hasilPemeriksaan"],
        $_POST["diagnosis"],
        $_POST["catatanTindakan"]
    );

    // Inisialisasi objek cUpdate untuk melakukan update
    $update = new cUpdate();
    // $update->vUpdateDataTrial($datafield_hasil, "hasil_layanan", $datavalue_hasil, $datakey, $linkurl);
    $update->vUpdateDataPrepared("hasil_layanan", $datafield_hasil, $datavalue_hasil, "idHasilLayanan", $_POST["idHasilLayanan"]);
}
?>



<?php
// delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btnhapus"])) {
    $passwordInput = trim($_POST["password_verif"] ?? '');

    if ($passwordInput === '') {
        echo "<script>alert('Password harus diisi.');</script>";
    } else {
        $sql = "SELECT password FROM user WHERE role = 2 AND jbtn = 'Ketua' LIMIT 1";
        $result = $view->vViewData($sql)[0] ?? null;

        // Bandingkan menggunakan md5 jika memang disimpan dengan md5
        $hashedInput = md5($passwordInput);

        if ($result && $hashedInput === $result["password"]) {
            if (!empty($_POST["hiddendeletevalue"])) {
                $delete = new cDelete();
                foreach ($_POST["hiddendeletevalue"] as $data) {
                    // $delete->_dDeleteDataTrial($data["field"], $data["value"], $data["table"]);
                    $delete->vDeleteDataPrepared($data["table"], $data["field"], $data["value"]);
                }
            }
        } else {
            echo "<script>alert('Password salah');</script>";
        }
    }
}
?>

<?php
// Query ENUM 'benjolanPayudara'
$benjolanpayudara = "SHOW COLUMNS FROM hasil_layanan LIKE 'benjolanPayudara'";
$view = new cView();
$arraybenjolanpayudara = $view->vViewData($benjolanpayudara);
$enumbenjolanpayudara = [];
if (!empty($arraybenjolanpayudara)) {
    $row = $arraybenjolanpayudara[0]; // Ambil hasil pertama
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumbenjolanpayudara = explode(",", str_replace("'", "", $matches[1]));
    }
}
// Query ENUM 'inspeksiVisualAsamAsetat'
$inspeksiVisualAsamAsetat = "SHOW COLUMNS FROM hasil_layanan LIKE 'tesAmfetaminUrin'";
$view = new cView();
$arrayinspeksiVisualAsamAsetat = $view->vViewData($inspeksiVisualAsamAsetat);
$enuminspeksiVisualAsamAsetat = [];
if (!empty($arrayinspeksiVisualAsamAsetat)) {
    $row = $arrayinspeksiVisualAsamAsetat[0]; // Ambil hasil pertama
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enuminspeksiVisualAsamAsetat = explode(",", str_replace("'", "", $matches[1]));
    }
}
// Query ENUM 'kadarAlkoholPernafasan'
$kadarAlkoholPernafasan = "SHOW COLUMNS FROM hasil_layanan LIKE 'kadarAlkoholPernafasan'";
$view = new cView();
$arraykadarAlkoholPernafasan = $view->vViewData($kadarAlkoholPernafasan);
$enumkadarAlkoholPernafasan = [];
if (!empty($arraykadarAlkoholPernafasan)) {
    $row = $arraykadarAlkoholPernafasan[0]; // Ambil hasil pertama
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumkadarAlkoholPernafasan = explode(",", str_replace("'", "", $matches[1]));
    }
}
// Query ENUM 'tesAmfetaminUrin'
$tesAmfetaminUrin = "SHOW COLUMNS FROM hasil_layanan LIKE 'tesAmfetaminUrin'";
$view = new cView();
$arraytesAmfetaminUrin = $view->vViewData($tesAmfetaminUrin);
$enumtesAmfetaminUrin = [];
if (!empty($arraytesAmfetaminUrin)) {
    $row = $arraytesAmfetaminUrin[0]; // Ambil hasil pertama
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumtesAmfetaminUrin = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'arusPernafasanEkspirasi'
$arusPernafasanEkspirasi = "SHOW COLUMNS FROM hasil_layanan LIKE 'arusPernafasanEkspirasi'";
$view = new cView();
$arrayarusPernafasanEkspirasi = $view->vViewData($arusPernafasanEkspirasi);
$enumarusPernafasanEkspirasi = [];
if (!empty($arrayarusPernafasanEkspirasi)) {
    $row = $arrayarusPernafasanEkspirasi[0]; // Ambil hasil pertama
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumarusPernafasanEkspirasi = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query SET 'faktorResikoPerilaku'
$faktorResikoPerilaku = "SHOW COLUMNS FROM hasil_layanan LIKE 'faktorResikoPerilaku'";
$view = new cView();
$arrayfaktorResikoPerilaku = $view->vViewData($faktorResikoPerilaku);
$setfaktorResikoPerilaku = [];
if (!empty($arrayfaktorResikoPerilaku)) {
    $row = $arrayfaktorResikoPerilaku[0]; // Ambil hasil pertama
    if (preg_match("/^set\((.*)\)$/", $row['Type'], $matches)) {
        $setfaktorResikoPerilaku = explode(",", str_replace("'", "", $matches[1]));
    }
}
?>


<p></p>
<div class="row">
    <div class="col-md-12">
        <br><br>
    </div>
</div>
<div class="row mx-2">
    <div class="col-12 col-md-10 col-lg-11">
        <?php
        _myHeader("HASIL SCREENING", "Hasil Screening");
        ?>
    </div>
    <div class="col-12 col-md-2 col-lg-1 mb-3">
        <button type="button" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center"
            data-bs-toggle="modal" data-bs-target="#exampleModal"
            style="border-radius: 10px; width: 100%; height: 60%; display: flex;">
            <i class="fa-solid fa-plus fa-lg" style="color: #ffffff;"></i>

        </button>

        <!-- Modal Insert -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5" id="exampleModalLabel">
                            <blockquote class="blockquote">
                                <p>Tambah Hasil Screening</p>
                            </blockquote>
                        </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="" method="post" action="<?= $idJadwal ?>" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="idPasien">ID Pasien <span class="required">*</span></label>
                                <select name="idPasien" id="idPasien" class="form-control" required>
                                    <option value="">- pilihan -</option>
                                    <?php
                                    $sqlpasien = "SELECT * FROM pasien WHERE statusPasien = 'Aktif' ORDER BY namaLengkap ASC";
                                    $view = new cView();
                                    $arraypasien = $view->vViewData($sqlpasien);

                                    foreach ($arraypasien as $datapasien) { // Ambil semua data dari hasil eksekusi $sql
                                        echo "<option value='" . $datapasien['idPasien'] . "'>" . $datapasien['namaLengkap'] . " - " . $datapasien['idPasien'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="idTerapis">ID Terapis <span class="required">*</span></label>
                                <select name="idTerapis" id="idTerapis" class="form-control" required>
                                    <option value="">- pilihan -</option>
                                    <?php
                                    $sqlterapis = "SELECT * FROM terapis WHERE statusTerapis = 'Aktif' ORDER BY namaTerapis ASC";
                                    $view = new cView();
                                    $arrayterapis = $view->vViewData($sqlterapis);

                                    foreach ($arrayterapis as $dataterapis) { // Ambil semua data dari hasil eksekusi $sql
                                        echo "<option value='" . $dataterapis['idTerapis'] . "'>" . $dataterapis['namaTerapis'] . " - " . $dataterapis['idTerapis'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="keluhan">Keluhan <span class="required">*</span></label>
                                <input class="form-control" type="text" name="keluhan" id="keluhan" value=""
                                    placeholder="Keluhan yang dirasakan pasien" maxlength="255" size="" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggalRujukan">Tanggal Rujukan </label>
                                <input class="form-control" type="date" name="tanggalRujukan" id="tanggalRujukan"
                                    value="" placeholder="Tanggal Rujukan pasien" maxlength="255" size="">
                            </div>
                            <div class="mb-3">
                                <label for="alasanRujukan">Alasan Rujukan </label>
                                <textarea class="form-control" id="alasanRujukan" name="alasanRujukan"
                                    placeholder="Alasan Rujukan" rows="3" cols="" id="floatingTextarea"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="tinggiBadan">Tinggi Badan </label>
                                    <input class="form-control" type="number" name="tinggiBadan" id="tinggiBadan"
                                        value="" placeholder="Tinggi Badan pasien" maxlength="255" size="">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="beratBadan">Berat Badan </label>
                                    <input class="form-control" type="number" name="beratBadan" id="beratBadan" value=""
                                        placeholder="Berat Badan pasien" maxlength="255" size="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="tekananDarah">Tekanan Darah</label>
                                    <input class="form-control" type="text" name="tekananDarah" id="tekananDarah"
                                        value="" placeholder="Tekanan Darah Pasien" maxlength="255" size="">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="gulaDarah">Gula Darah </label>
                                    <input class="form-control" type="number" name="gulaDarah" id="gulaDarah" value=""
                                        placeholder="Gula Darah Pasien" maxlength="255" size="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="kolesterol">kolesterol</label>
                                    <input class="form-control" type="number" name="kolesterol" id="kolesterol" value=""
                                        placeholder="kolesterol pasien" maxlength="255" size="">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="trigliserida">Trigliserida </label>
                                    <input class="form-control" type="number" name="trigliserida" id="trigliserida"
                                        value="" placeholder="Trigliserida Pasien" maxlength="255" size="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="benjolanPayudara">Benjolan Payudara</label>
                                    <select name="benjolanPayudara" id="benjolanPayudara" class="form-control">
                                        <option value="">- pilihan -</option>
                                        <?php
                                        foreach ($enumbenjolanpayudara as $option) {
                                            $trimmedValue = trim($option);
                                            echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="inspeksiVisualAsamAsetat">Inspeksi Visual Asam Asetat</label>
                                    <select name="inspeksiVisualAsamAsetat" id="inspeksiVisualAsamAsetat"
                                        class="form-control">
                                        <option value="">- pilihan -</option>
                                        <?php
                                        foreach ($enuminspeksiVisualAsamAsetat as $option) {
                                            $trimmedValue = trim($option);
                                            echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="kadarAlkoholPernafasan">Kadar Alkohol Pernafasan</label>
                                    <select name="kadarAlkoholPernafasan" id="kadarAlkoholPernafasan"
                                        class="form-control">
                                        <option value="">- pilihan -</option>
                                        <?php
                                        foreach ($enumkadarAlkoholPernafasan as $option) {
                                            $trimmedValue = trim($option);
                                            echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="tesAmfetaminUrin">Tes Amfetamin Urin</label>
                                    <select name="tesAmfetaminUrin" id="tesAmfetaminUrin" class="form-control">
                                        <option value="">- pilihan -</option>
                                        <?php
                                        foreach ($enumtesAmfetaminUrin as $option) {
                                            $trimmedValue = trim($option);
                                            echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="arusPernafasanEkspirasi">Arus Pernafasan Ekspirasi</label>
                                <select name="arusPernafasanEkspirasi" id="arusPernafasanEkspirasi"
                                    class="form-control">
                                    <option value="">- pilihan -</option>
                                    <?php
                                    foreach ($enumarusPernafasanEkspirasi as $option) {
                                        $trimmedValue = trim($option);
                                        echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class=" mb-3">
                                <label class="form-label mb-0" for="faktorResikoPerilaku">Faktor Resiko Perilaku</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="faktorResikoPerilaku[]" value="Merokok" id="merokok">
                                            <label class="form-check-label" for="merokok">Merokok</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="faktorResikoPerilaku[]" value="Makan Sayur Buah"
                                                id="makanSayurBuah">
                                            <label class="form-check-label" for="makanSayurBuah">Makan Sayur
                                                Buah</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="faktorResikoPerilaku[]" value="Insomia" id="insomia">
                                            <label class="form-check-label" for="insomia">Insomia</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="faktorResikoPerilaku[]" value="Kurang Aktifitas Fisik"
                                                id="kurangAktifitasFisik">
                                            <label class="form-check-label" for="kurangAktifitasFisik">Kurang Aktifitas
                                                Fisik</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="faktorResikoPerilaku[]" value="Konsumsi Minuman Beralkohol"
                                                id="konsumsiMinumanBeralkohol">
                                            <label class="form-check-label" for="konsumsiMinumanBeralkohol">Konsumsi
                                                Minuman Beralkohol</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="hasilPemeriksaan">Hasil Pemeriksaan <span class="required">*</span></label>
                                <textarea class="form-control" id="hasilPemeriksaan" name="hasilPemeriksaan"
                                    placeholder="Hasil pemeriksaan oleh terapis" rows="3" cols="" id="floatingTextarea"
                                    required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="diagnosis">Diagnosis <span class="required">*</span></label>
                                <textarea class="form-control" id="diagnosis" name="diagnosis"
                                    placeholder="Diagnosis dari terapis" rows="3" cols="" id="floatingTextarea"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="catatanTindakan">Catatan dan Rencana Tindakan <span
                                        class="required">*</span></label>
                                <textarea class="form-control" id="catatanTindakan" name="catatanTindakan"
                                    placeholder="Catatan dan rekomendasi rencana tindakan dari terapis" rows="3" cols=""
                                    id="floatingTextarea" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;"
                                name="savebtn" value="true">Simpan</button>
                            <button type="reset" class="btn btn-warning btn-sm" style="border-radius: 25px;" name=""
                                value="true">Ulang</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                                style="border-radius: 25px;">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<p></p>
<div class="row mx-2">
    <div class="col-md-12">
        <?php
        $sqlhasil = "SELECT hasil.*, jp.*, u.*, p.*, t.* FROM hasil_layanan hasil 
                    JOIN jadwal_program jp ON jp.idJadwal = hasil.idJadwal 
                    JOIN user u ON jp.idUser = u.idUser
                    JOIN pasien p ON p.idPasien = hasil.idPasien
                    JOIN terapis t ON t.idTerapis = hasil.idTerapis
                    WHERE hasil.idJadwal = ?
                    ORDER BY hasil.idHasilLayanan DESC";
        $view = new cView();
        $arrayhasil = $view->vViewDataPrepared($sqlhasil, [$idJadwal], "i");
        ?>
        <div id="" class='table-responsive'>
            <table id='example' class='table table-condensed'>
                <thead>
                    <tr>
                        <th width='5%' class="text-right">No.</th>
                        <th width=''>Pasien</th>
                        <th width=''>Keluhan</th>
                        <th width=''>BB (kg)</th>
                        <th width=''>TB (cm)</th>
                        <th width=''>Tekanan Darah</th>
                        <th width=''>Gula Darah</th>
                        <th width='5%'>VIEW</th>
                        <th width='5%'>EDIT</th>
                        <th width='5%'>HAPUS</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    foreach ($arrayhasil as $datahasil) {
                        $cnourut = $cnourut + 1;
                        $idHapus = $datahasil["idHasilLayanan"];
                        ?>
                        <tr class=''>
                            <td class="text-right"><?= $cnourut; ?></td>
                            <td><?= $datahasil["namaLengkap"]; ?></td>
                            <td><?= $datahasil["keluhan"]; ?></td>
                            <td><?= $datahasil["beratBadan"]; ?></td>
                            <td><?= $datahasil["tinggiBadan"]; ?></td>
                            <td><?= $datahasil["tekananDarah"]; ?></td>
                            <td><?= $datahasil["gulaDarah"]; ?></td>
                            <td>
                                <?php
                                $datadetail = array(
                                    // array("ID HASIL LAYANAN", "idHasilLayanan", $datahasil["idHasilLayanan"], 2, ""),
                                    // array("ID JADWAL", "idJadwal", $idJadwal, 2, $idJadwal),
                                    // array("ID USER", "idUser", $idUser, 2, $idUser),
                                    array("PASIEN", "namaLengkap", $datahasil["namaLengkap"], 1),
                                    array("TERAPIS", "namaTerapis", $datahasil["namaTerapis"], 1),
                                    array("KELUHAN", "keluhan", $datahasil["keluhan"], 1),
                                    array("TANGGAL RUJUKAN", "tanggalRujukan", $datahasil["tanggalRujukan"], 1),
                                    array("ALASAN RUJUKAN", "alasanRujukan", $datahasil["alasanRujukan"], 1),
                                    array("TINGGI BADAN", "tinggiBadan", $datahasil["tinggiBadan"], 1),
                                    array("BERAT BADAN", "beratBadan", $datahasil["beratBadan"], 1),
                                    array("TEKANAN DARAH", "tekananDarah", $datahasil["tekananDarah"], 1),
                                    array("GULA DARAH", "gulaDarah", $datahasil["gulaDarah"], 1),
                                    array("KOLESTEROL", "kolesterol", $datahasil["kolesterol"], 1),
                                    array("TRIGLISERIDA", "trigliserida", $datahasil["trigliserida"], 1),
                                    array("BENJOLAN PAYUDARA", "benjolanPayudara", $datahasil["benjolanPayudara"], 1),
                                    array("INSPEKSI VISUAL ASAM ASETAT", "inspeksiVisualAsamAsetat", $datahasil["inspeksiVisualAsamAsetat"], 1),
                                    array("KADAR ALKOHOL PERNAFASAN", "kadarAlkoholPernafasan", $datahasil["kadarAlkoholPernafasan"], 1),
                                    array("TES AMFETAMIN URIN", "tesAmfetaminUrin", $datahasil["tesAmfetaminUrin"], 1),
                                    array("ARUS PERNAFASAN EKSPIRASI", "arusPernafasanEkspirasi", $datahasil["arusPernafasanEkspirasi"], 1),
                                    array("FAKTOR PERNAFASAN EKSPIRASI", "faktorResikoPerilaku", $datahasil["faktorResikoPerilaku"], 1),
                                    array("HASIL PEMERIKSAAN", "hasilPemeriksaan", $datahasil["hasilPemeriksaan"], 1),
                                    array("DIAGNOSIS", "diagnosis", $datahasil["diagnosis"], 1),
                                    array("CATATAN RENCANA TINDAKAN", "catatanTindakan", $datahasil["catatanTindakan"], 1),
                                );
                                _CreateWindowModalDetil($datahasil["idHasilLayanan"], "view", "viewsasaran-form", "viewsasaran-button", "", 600, "DETAIL#HASIL SCREENING " . $datahasil["idHasilLayanan"], "", $datadetail, "", $idJadwal, "");
                                ?>

                            </td>
                            <!-- Modal Update -->
                            <td>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#formedit<?= $datahasil["idHasilLayanan"]; ?>"
                                    style="border-radius: 8px;">
                                    <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                                </button>

                                <div class="modal fade" id="formedit<?= $datahasil["idHasilLayanan"]; ?>" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content text-left">
                                            <div class="modal-header">
                                                <figure class="text-left">
                                                    <blockquote class="blockquote">EDIT HASIL SCREENING</blockquote>
                                                    <figcaption class="blockquote-footer">
                                                        <?= $datahasil["idHasilLayanan"]; ?>
                                                    </figcaption>
                                                    <figcaption class="blockquote-footer"><?= $datahasil["namaLengkap"]; ?>
                                                        (<?= $datahasil["tanggalKegiatan"]; ?>)</figcaption>
                                                </figure>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <FORM method="post" enctype="multipart/form-data" action="<?= $idJadwal ?>">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <input class="form-control" type="text" name="idHasilLayanan"
                                                            id="idHasilLayanan" value="<?= $datahasil["idHasilLayanan"]; ?>"
                                                            maxlength="255" size="" hidden>
                                                        <input class="form-control" type="text" name="idJadwal"
                                                            id="idJadwal" value="<?= $datahasil["idJadwal"]; ?>"
                                                            maxlength="255" size="" hidden>
                                                        <input class="form-control" type="text" name="idUser" id="idUser"
                                                            value="<?= $datahasil["idUser"]; ?>" maxlength="255" size=""
                                                            hidden>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="idPasien">ID Pasien <span
                                                                class="required">*</span></label>
                                                        <select name="idPasien" id="idPasien" class="form-control" required>
                                                            <option value="<?= $datahasil["idPasien"]; ?>">
                                                                <?= $datahasil["namaLengkap"] . " - " . $datahasil["idPasien"]; ?>
                                                            </option>
                                                            <?php
                                                            $sqlpasien = "SELECT * FROM pasien WHERE statusPasien = 'Aktif' ORDER BY namaLengkap ASC";
                                                            $view = new cView();
                                                            $arraypasien = $view->vViewData($sqlpasien);

                                                            foreach ($arraypasien as $datapasien) { // Ambil semua data dari hasil eksekusi $sql
                                                                echo "<option value='" . $datapasien['idPasien'] . "'>" . $datapasien['namaLengkap'] . " - " . $datapasien['idPasien'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="idTerapis">ID Terapis <span
                                                                class="required">*</span></label>
                                                        <select name="idTerapis" id="idTerapis" class="form-control"
                                                            required>
                                                            <option value="<?= $datahasil["idTerapis"]; ?>">
                                                                <?= $datahasil["namaTerapis"] . " - " . $datahasil["idTerapis"]; ?>
                                                            </option>
                                                            <?php
                                                            $sqlterapis = "SELECT * FROM terapis WHERE statusTerapis = 'Aktif' ORDER BY namaTerapis ASC";
                                                            $view = new cView();
                                                            $arrayterapis = $view->vViewData($sqlterapis);

                                                            foreach ($arrayterapis as $dataterapis) { // Ambil semua data dari hasil eksekusi $sql
                                                                echo "<option value='" . $dataterapis['idTerapis'] . "'>" . $dataterapis['namaTerapis'] . " - " . $dataterapis['idTerapis'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="keluhan">Keluhan <span class="required">*</span></label>
                                                        <input class="form-control" type="text" name="keluhan" id="keluhan"
                                                            value="<?= $datahasil["keluhan"]; ?>"
                                                            placeholder="Keluhan yang dirasakan pasien" maxlength="255"
                                                            size="" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="tanggalRujukan">Tanggal Rujukan</label>
                                                        <input class="form-control" type="date" name="tanggalRujukan"
                                                            id="tanggalRujukan" value="<?= $datahasil["tanggalRujukan"]; ?>"
                                                            placeholder="Tanggal Rujukan" maxlength="255" size="">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="alasanRujukan">Alasan Rujukan </label>
                                                        <textarea class="form-control" id="alasanRujukan"
                                                            name="alasanRujukan" placeholder="Alasan rujukan" rows="3"
                                                            cols=""
                                                            id="floatingTextarea"><?= $datahasil["alasanRujukan"]; ?></textarea>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="tinggiBadan">Tinggi Badan </label>
                                                            <input class="form-control" type="number" name="tinggiBadan"
                                                                id="tinggiBadan" value="<?= $datahasil["tinggiBadan"]; ?>"
                                                                placeholder="Tinggi Badan pasien" maxlength="255" size="">
                                                        </div>
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="beratBadan">Berat Badan </label>
                                                            <input class="form-control" type="number" name="beratBadan"
                                                                id="beratBadan" value="<?= $datahasil["beratBadan"]; ?>"
                                                                placeholder="Berat Badan pasien" maxlength="255" size="">
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="tekananDarah">Tekanan Darah</label>
                                                            <input class="form-control" type="text" name="tekananDarah"
                                                                id="tekananDarah" value="<?= $datahasil["tekananDarah"]; ?>"
                                                                placeholder="Tekanan Darah pasien" maxlength="255" size="">
                                                        </div>
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="gulaDarah">Gula Darah</label>
                                                            <input class="form-control" type="number" name="gulaDarah"
                                                                id="gulaDarah" value="<?= $datahasil["gulaDarah"]; ?>"
                                                                placeholder="Gula Darah pasien" maxlength="255" size="">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="kolesterol">Kolesterol</label>
                                                            <input class="form-control" type="text" name="kolesterol"
                                                                id="kolesterol" value="<?= $datahasil["kolesterol"]; ?>"
                                                                placeholder="Kolesterol pasien" maxlength="255" size="">
                                                        </div>
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="trigliserida">Trigliserida</label>
                                                            <input class="form-control" type="number" name="trigliserida"
                                                                id="trigliserida" value="<?= $datahasil["trigliserida"]; ?>"
                                                                placeholder="trigliserida pasien" maxlength="255" size="">
                                                        </div>
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="benjolanPayudara">Benjolan Payudara</label>
                                                            <select name="benjolanPayudara" class="form-control">
                                                                <option value="<?= $datahasil["benjolanPayudara"]; ?>">
                                                                    <?= $datahasil["benjolanPayudara"]; ?>
                                                                </option>
                                                                <?php
                                                                foreach ($enumbenjolanpayudara as $option) {
                                                                    $trimmedValue = trim($option);
                                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="inspeksiVisualAsamAsetat">Inspeksi Visual Asam
                                                                Asetat</label>
                                                            <select name="inspeksiVisualAsamAsetat" class="form-control">
                                                                <option
                                                                    value="<?= $datahasil["inspeksiVisualAsamAsetat"]; ?>">
                                                                    <?= $datahasil["inspeksiVisualAsamAsetat"]; ?>
                                                                </option>
                                                                <?php
                                                                foreach ($enuminspeksiVisualAsamAsetat as $option) {
                                                                    $trimmedValue = trim($option);
                                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="kadarAlkoholPernafasan">Kadar Alkohol
                                                                Pernafasan</label>
                                                            <select name="kadarAlkoholPernafasan" class="form-control">
                                                                <option
                                                                    value="<?= $datahasil["kadarAlkoholPernafasan"]; ?>">
                                                                    <?= $datahasil["kadarAlkoholPernafasan"]; ?>
                                                                </option>
                                                                <?php
                                                                foreach ($enumkadarAlkoholPernafasan as $option) {
                                                                    $trimmedValue = trim($option);
                                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <label for="tesAmfetaminUrin">Tes Amfetamin Urin</label>
                                                            <select name="tesAmfetaminUrin" class="form-control">
                                                                <option value="<?= $datahasil["tesAmfetaminUrin"]; ?>">
                                                                    <?= $datahasil["tesAmfetaminUrin"]; ?>
                                                                </option>
                                                                <?php
                                                                foreach ($enumtesAmfetaminUrin as $option) {
                                                                    $trimmedValue = trim($option);
                                                                    echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="arusPernafasanEkspirasi">Arus Pernafasan
                                                            Ekspiarasi</label>
                                                        <select name="arusPernafasanEkspirasi" class="form-control">
                                                            <option value="<?= $datahasil["arusPernafasanEkspirasi"]; ?>">
                                                                <?= $datahasil["arusPernafasanEkspirasi"]; ?>
                                                            </option>
                                                            <?php
                                                            foreach ($enumarusPernafasanEkspirasi as $option) {
                                                                $trimmedValue = trim($option);
                                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <?php
                                                    $idHasilLayanan = $datahasil["idHasilLayanan"];
                                                    // Query untuk mendapatkan riwayat penyakit pasien tertentu
                                                    $sqlfaktorResikoPerilaku = "SELECT faktorResikoPerilaku FROM hasil_layanan WHERE idHasilLayanan = ?";
                                                    $view = new cView();
                                                    $arrayfaktorResikoPerilaku = $view->vViewDataPrepared($sqlfaktorResikoPerilaku, [$idHasilLayanan], "i");
                                                    // Pastikan data ditemukan
                                                    $faktorResikoPerilakuTerpilih = [];
                                                    if (!empty($arrayfaktorResikoPerilaku)) {
                                                        $datafaktorResikoPerilaku = $arrayfaktorResikoPerilaku[0]; // Ambil baris pertama
                                                        $faktorResikoPerilakuTerpilih = explode(",", $datafaktorResikoPerilaku["faktorResikoPerilaku"]); // Ubah ke array
                                                    }
                                                    ?>
                                                    <div class="mb-3">
                                                        <label class="form-label mb-0" for="faktorResikoPerilaku">Faktor
                                                            Resiko Perilaku</label>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="faktorResikoPerilaku[]" value="Merokok"
                                                                        id="merokok" <?= in_array("Merokok", $faktorResikoPerilakuTerpilih) ? "checked" : ""; ?>>
                                                                    <label class="form-check-label"
                                                                        for="merokok">Merokok</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="faktorResikoPerilaku[]"
                                                                        value="Makan Sayur Buah" id="makanSayurBuah"
                                                                        <?= in_array("Makan Sayur Buah", $faktorResikoPerilakuTerpilih) ? "checked" : ""; ?>>
                                                                    <label class="form-check-label"
                                                                        for="makanSayurBuah">Makan Sayur Buah</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="faktorResikoPerilaku[]" value="Insomia"
                                                                        id="insomia" <?= in_array("Insomia", $faktorResikoPerilakuTerpilih) ? "checked" : ""; ?>>
                                                                    <label class="form-check-label"
                                                                        for="insomia">Insomia</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="faktorResikoPerilaku[]"
                                                                        value="Kurang Aktifitas Fisik"
                                                                        id="kurangAktifitasFisik" <?= in_array("Kurang Aktifitas Fisik", $faktorResikoPerilakuTerpilih) ? "checked" : ""; ?>>
                                                                    <label class="form-check-label"
                                                                        for="kurangAktifitasFisik">Kurang Aktifitas
                                                                        Fisik</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="faktorResikoPerilaku[]"
                                                                        value="Konsumsi Minuman Beralkohol"
                                                                        id="konsumsiMinumanBeralkohol" <?= in_array("Konsumsi Minuman Beralkohol", $faktorResikoPerilakuTerpilih) ? "checked" : ""; ?>>
                                                                    <label class="form-check-label"
                                                                        for="konsumsiMinumanBeralkohol">Konsumsi Minuman
                                                                        Beralkohol</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="hasilPemeriksaan">Hasil Pemeriksaan <span
                                                                class="required">*</span></label>
                                                        <textarea class="form-control" id="hasilPemeriksaan"
                                                            name="hasilPemeriksaan"
                                                            placeholder="Hasil pemeriksaan oleh terapis" rows="3" cols=""
                                                            id="floatingTextarea"
                                                            required><?= $datahasil["hasilPemeriksaan"]; ?></textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="diagnosis">Diagnosis <span
                                                                class="required">*</span></label>
                                                        <textarea class="form-control" id="diagnosis" name="diagnosis"
                                                            placeholder="Diagnosis dari terapis" rows="3" cols=""
                                                            id="floatingTextarea"
                                                            required><?= $datahasil["diagnosis"]; ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="catatanTindakan">Catatan dan Rencana Tindakan <span
                                                                class="required">*</span></label>
                                                        <textarea class="form-control" id="catatanTindakan"
                                                            name="catatanTindakan"
                                                            placeholder="Catatan dan rekomendasi rencana tindakan dari terapis"
                                                            rows="3" cols="" id="floatingTextarea"
                                                            required><?= $datahasil["catatanTindakan"]; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="editbtn" value="true"
                                                        class="btn btn-primary btn-sm"
                                                        style="border-radius: 25px;">SIMPAN</button>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Modal Delete -->
                            <td>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#formdelete<?= $idHapus; ?>" style="border-radius: 8px;">
                                    <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
                                </button>

                                <!-- Modal Delete -->
                                <div class="modal fade" id="formdelete<?= $idHapus; ?>" tabindex="-1"
                                    aria-labelledby="modalLabel<?= $idHapus; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="" method="post">
                                                <input type="hidden" name="hiddendeletevalue[0][field]"
                                                    value="idHasilLayanan">
                                                <input type="hidden" name="hiddendeletevalue[0][value]"
                                                    value="<?= $idHapus ?>">
                                                <input type="hidden" name="hiddendeletevalue[0][table]"
                                                    value="hasil_layanan">

                                                <div class="modal-header">
                                                    <figure>
                                                        <blockquote class="blockquote">
                                                            <h5 class="modal-title" id="modalLabel<?= $idHapus; ?>">HAPUS
                                                            </h5>
                                                        </blockquote>
                                                        <figcaption class="blockquote-footer">Hasil Screening
                                                            <?= $datahasil["tanggalKegiatan"] ?>
                                                        </figcaption>
                                                    </figure>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus hasil screening
                                                        <strong><?= $datahasil["namaLengkap"] ?></strong>?
                                                    </p>
                                                    <div class="form-group">
                                                        <label for="password_verif<?= $idHapus; ?>">Masukkan Password
                                                            Ketua:</label>
                                                        <input type="password" name="password_verif"
                                                            id="password_verif<?= $idHapus; ?>" class="form-control"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="btnhapus" class="btn btn-danger btn-sm"
                                                        style="border-radius: 25px;">HAPUS</button>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<p></p>
<div class="row">
    <div class="col-md-12">
        <p><br><br><br><br><br></p>
    </div>
</div>