<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");


// Ambil URL path dari permintaan
$request = $_SERVER['REQUEST_URI'];
$request = trim($request, '/');
$segments = explode('/', (string)$request);

// FIX: Use dynamic search for '331' (Menu ID) to handle different URL depths
$posMenu = array_search('detail', $segments);
if ($posMenu !== false && isset($segments[$posMenu + 1])) {
    $idJadwal = $segments[$posMenu + 1];
} else {
    // Fallback for unexpected URL structures
    $idJadwal = isset($segments[3]) ? $segments[3] : 0;
}

$conn = new cConnect();
$conn->goConnect();

$view = new cView();

// Ambil data jadwal berdasarkan ID
$sqlDetail = "SELECT jp.*, prog.*, u.* FROM jadwal_program jp
              LEFT JOIN program prog ON jp.idProgram = prog.idProgram
              LEFT JOIN user u ON jp.idUser = u.idUser
              WHERE jp.idJadwal = ?";
$datajadwal = $view->vViewDataPrepared($sqlDetail, [$idJadwal], "i");

if (empty($datajadwal)) {
    die("Data tidak ditemukan.");
}

$datajadwal = $datajadwal[0]; // Ambil hasil pertama
?>

<div class="row mx-2">
    <div class="col-12 col-md-2 col-lg-1 mb-3" style="width: min-content; align-content: center;">
        <a href="<?= $baseurl ?>/manajemen/screening"><ion-icon name="chevron-back-outline" size="large" style="color: black;"></ion-icon></a>
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
                        <th>Tanggal Kegiatan</th>
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
                    WHERE hasil.idJadwal = $idJadwal
                    ORDER BY hasil.idHasilLayanan DESC";
        $view = new cView();
        $arrayhasil = $view->vViewData($sqlhasil);
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    foreach ($arrayhasil as $datahasil) {
                        $cnourut = $cnourut + 1;
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