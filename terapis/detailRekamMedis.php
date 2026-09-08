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
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/inc_f_object.php");

if (!isset($baseurl)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    $baseurl = $protocol . $host . $path;
}

// Ambil idPasien dari session jika tersedia
if (isset($_POST['idPasien'])) {
    $_SESSION['idPasien'] = $_POST['idPasien']; // Simpan ke session
}

// Ambil dari session
$idPasien = $_SESSION['idPasien'] ?? null;

// Jika idPasien tidak ditemukan, kembalikan ke halaman sebelumnya
if (!$idPasien) {
    die("ID Pasien tidak ditemukan.");
}

$conn = new cConnect();
$conn->goConnect();

$view = new cView();

// Ambil ID User dari sesi atau database
$idUser = $_SESSION['idUser'] ?? null;
if (!$idUser) {
    $sqlUser = "SELECT idUser FROM user LIMIT 1";
    $dataUser = $view->vViewData($sqlUser);
    $idUser = $dataUser[0]["idUser"] ?? null;
}

// Query data
$sqlPasien = "SELECT p.*, sd.*, jd.* FROM pasien p
            JOIN sub_disabilitas sd ON sd.idSubDisabilitas = p.idSubDisabilitas
            JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas
            WHERE p.idPasien = ?";

$datahasil = $view->vViewDataPrepared($sqlPasien, [$idPasien], "i");

if (empty($datahasil)) {
    die("Data tidak ditemukan.");
}

$datahasil = $datahasil[0]; // Ambil hasil pertama
?>

<style>
    .button-container {
        display: flex;
        gap: 10px;
        /* Jarak antara tombol */
    }
</style>

<div class="row mx-2">
    <div class="col-12 col-md-2 col-lg-1 mb-3" style="width: min-content; align-content: center;">
        <a href="<?= $baseurl ?>/terapis/rekam-medis"><ion-icon name="chevron-back-outline" size="large" style="color: black;"></ion-icon></a>
    </div>
    <div class="col-12 col-md-10 col-lg-11">
        <?php _myHeader("DETAIL REKAM MEDIS PASIEN " . htmlspecialchars(strtoupper($datahasil['namaLengkap'] ?? '')), "Detail Hasil Rekam Medis"); ?>
    </div>
</div>

<!-- Form Pencarian -->
<div class="row mx-2 mt-5">
    <div class="col-md-12">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 col-md-5 mb-3">
                    <div class="input-group">
                        <span class="input-group-text">Mulai</span>
                        <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai">
                    </div>
                </div>
                <div class="col-12 col-md-5 mb-3">
                    <div class="input-group">
                        <span class="input-group-text">Sampai</span>
                        <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai">
                    </div>
                </div>
                <div class="col-12 col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px;" name="searchbtn"
                        value="true"><b>CARI</b></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mx-2">
    <div class="col-md-12">
        <?php

        // Ambil tanggal dari form jika ada
        $tanggalMulai = $_POST['tanggalMulai'] ?? null;
        $tanggalSelesai = $_POST['tanggalSelesai'] ?? null;

        // Query daftar program layanan yang telah diikuti pasien
        $sqlhasil = "SELECT hasil.*, jp.*, pr.*, p.*, t.*
                    FROM hasil_layanan hasil 
                    JOIN jadwal_program jp ON jp.idJadwal = hasil.idJadwal 
                    JOIN program pr ON pr.idProgram = jp.idProgram
                    JOIN pasien p ON p.idPasien = hasil.idPasien
                    JOIN terapis t ON t.idTerapis = hasil.idTerapis
                    WHERE hasil.idPasien = ?";

        $params = [$idPasien];
        $types = "i";

        // Tambahkan filter tanggal jika diisi
        if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
            $sqlhasil .= " AND jp.tanggalKegiatan BETWEEN ? AND ?";
            $params[] = $tanggalMulai;
            $params[] = $tanggalSelesai;
            $types .= "ss";
        } elseif (!empty($tanggalMulai)) {
            $sqlhasil .= " AND jp.tanggalKegiatan >= ?";
            $params[] = $tanggalMulai;
            $types .= "s";
        } elseif (!empty($tanggalSelesai)) {
            $sqlhasil .= " AND jp.tanggalKegiatan <= ?";
            $params[] = $tanggalSelesai;
            $types .= "s";
        }

        $sqlhasil .= " ORDER BY jp.tanggalKegiatan DESC";

        // Eksekusi query
        $arrayhasil = $view->vViewDataPrepared($sqlhasil, $params, $types);
        ?>
        <div id="" class='table-responsive'>
            <table id='example' class='table table-condensed'>
                <thead>
                    <tr>
                        <th width='5%' class="text-right">No.</th>
                        <th width='10%'>Tanggal</th>
                        <th width=''>Program Layanan</th>
                        <th width=''>Terapis</th>
                        <th width='20%'>Keluhan</th>
                        <th width='10%'>Diagnosis</th>
                        <th width='20%'>Rencana Tindakan</th>
                        <th width='5%'>DETAIL</th>
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
                            <td><?= htmlspecialchars($datahasil["tanggalKegiatan"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["namaProgram"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["namaTerapis"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["keluhan"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["diagnosis"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["catatanTindakan"] ?? ''); ?></td>
                            <td>
                                <?php
                                // Ambil idHasilLayanan dari data hasil layanan yang sedang di-loop
                                $idHasilLayanan = $datahasil["idHasilLayanan"];
                                $linkurl = "detailRekamMedis.php?idHasilLayanan=" . $idHasilLayanan;

                                // Query untuk mengambil idProgram berdasarkan idHasilLayanan
                                $sqlProgram = "SELECT jp.idProgram 
                                            FROM hasil_layanan hl
                                            JOIN jadwal_program jp ON hl.idJadwal = jp.idJadwal
                                            WHERE hl.idHasilLayanan = ?";
                                $view = new cView();
                                $dataProgram = $view->vViewDataPrepared($sqlProgram, [$idHasilLayanan], "i");

                                if (!empty($dataProgram)) {
                                    $idProgram = $dataProgram[0]["idProgram"];
                                } else {
                                    $idProgram = null; // Jika tidak ada data
                                }

                                // Path file LAMPIRAN
                                $lampiranFisio = "uploads/lampiranFisio/" . basename((string)$datahasil["lampiran"]);
                                if (!empty($datahasil["lampiran"]) && file_exists($lampiranFisio)) {
                                    $lampiranFisio = '<a href="' . $baseurl . '/admin/' . htmlspecialchars($lampiranFisio) . '" target="_new">Lihat Dokumen</a>';
                                } else {
                                    $lampiranFisio = '<span>Tidak ada dokumen</span>';
                                }

                                // Path file LAMPIRAN
                                $lampiranKinesio = "uploads/lampiranKinesio/" . basename((string)$datahasil["lampiran"]);
                                if (!empty($datahasil["lampiran"]) && file_exists($lampiranKinesio)) {
                                    $lampiranKinesio = '<a href="' . $baseurl . '/admin/' . htmlspecialchars($lampiranKinesio) . '" target="_new">Lihat Dokumen</a>';
                                } else {
                                    $lampiranKinesio = '<span>Tidak ada dokumen</span>';
                                }

                                // Menentukan tampilan detail berdasarkan idProgram
                                switch ($idProgram) {
                                    case '1': // Detail Fisioterapi
                                        $datadetail = array(
                                            array("NAMA TERAPIS", "namaTerapis", htmlspecialchars($datahasil["namaTerapis"] ?? ''), 1),
                                            array("KELUHAN", "keluhan", htmlspecialchars($datahasil["keluhan"] ?? ''), 1),
                                            array("HASIL PEMERIKSAAN", "hasilPemeriksaan", htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''), 1),
                                            array("AREA TUBUH YANG DITERAPI", "areaTubuh", htmlspecialchars($datahasil["areaTubuh"] ?? ''), 1),
                                            array("DIAGNOSIS", "diagnosis", htmlspecialchars($datahasil["diagnosis"] ?? ''), 1),
                                            array("CATATAN RENCANA TINDAKAN", "catatanTindakan", htmlspecialchars($datahasil["catatanTindakan"] ?? ''), 1),
                                            array("LAMPIRAN", "lampiran", $lampiranFisio, 1, "")
                                        );
                                        _CreateWindowModalDetil($datahasil["idHasilLayanan"], "view", "viewsasaran-form", "viewsasaran-button", "", 600, "DETAIL#HASIL FISIOTERAPI", "", $datadetail, "", $linkurl, "");
                                        break;

                                    case '2': // Detail Kinesioterapi
                                        $datadetail = array(
                                            array("NAMA TERAPIS", "namaTerapis", htmlspecialchars($datahasil["namaTerapis"] ?? ''), 1),
                                            array("KELUHAN", "keluhan", htmlspecialchars($datahasil["keluhan"] ?? ''), 1),
                                            array("TINGKAT NYERI", "tingkatNyeri", htmlspecialchars($datahasil["tingkatNyeri"] ?? ''), 1),
                                            array("WAKTU MUNCUL KELUHAN", "waktuMunculKeluhan", htmlspecialchars($datahasil["waktuMunculKeluhan"] ?? ''), 1),
                                            array("SIFAT SAKIT", "sifatSakit", htmlspecialchars($datahasil["sifatSakit"] ?? ''), 1),
                                            array("OBAT", "obat", htmlspecialchars($datahasil["obat"] ?? ''), 1),
                                            array("POSTUR TUBUH", "posturTubuh", htmlspecialchars($datahasil["posturTubuh"] ?? ''), 1),
                                            array("ROM", "ROM", htmlspecialchars($datahasil["ROM"] ?? ''), 1),
                                            array("POSITIVE", "positive", htmlspecialchars($datahasil["positive"] ?? ''), 1),
                                            array("MANAGEMENT", "management", htmlspecialchars($datahasil["management"] ?? ''), 1),
                                            array("HASIL PEMERIKSAAN", "hasilPemeriksaan", htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''), 3),
                                            array("DIAGNOSIS", "diagnosis", htmlspecialchars($datahasil["diagnosis"] ?? ''), 1),
                                            array("CATATAN RENCANA TINDAKAN", "catatanTindakan", htmlspecialchars($datahasil["catatanTindakan"] ?? ''), 1),
                                            array("LAMPIRAN", "lampiran", $lampiranKinesio, 1, "")
                                        );
                                        _CreateWindowModalDetil($datahasil["idHasilLayanan"], "view", "viewsasaran-form", "viewsasaran-button", "", 600, "DETAIL#HASIL KINESIOTERAPI", "", $datadetail, "", $linkurl, "");
                                        break;

                                    case '3': // Detail Screening
                                        $datadetail = array(
                                            array("NAMA TERAPIS", "namaTerapis", htmlspecialchars($datahasil["namaTerapis"] ?? ''), 1),
                                            array("KELUHAN", "keluhan", htmlspecialchars($datahasil["keluhan"] ?? ''), 1),
                                            array("TANGGAL RUJUKAN", "tanggalRujukan", htmlspecialchars($datahasil["tanggalRujukan"] ?? ''), 1),
                                            array("ALASAN RUJUKAN", "alasanRujukan", htmlspecialchars($datahasil["alasanRujukan"] ?? ''), 1),
                                            array("TINGGI BADAN", "tinggiBadan", htmlspecialchars($datahasil["tinggiBadan"] ?? ''), 1),
                                            array("BERAT BADAN", "beratBadan", htmlspecialchars($datahasil["beratBadan"] ?? ''), 1),
                                            array("TEKANAN DARAH", "tekananDarah", htmlspecialchars($datahasil["tekananDarah"] ?? ''), 1),
                                            array("GULA DARAH", "gulaDarah", htmlspecialchars($datahasil["gulaDarah"] ?? ''), 1),
                                            array("KOLESTEROL", "kolesterol", htmlspecialchars($datahasil["kolesterol"] ?? ''), 1),
                                            array("TRIGLISERIDA", "trigliserida", htmlspecialchars($datahasil["trigliserida"] ?? ''), 1),
                                            array("BENJOLAN PAYUDARA", "benjolanPayudara", htmlspecialchars($datahasil["benjolanPayudara"] ?? ''), 1),
                                            array("INSPEKSI VISUAL ASAM ASETAT", "inspeksiVisualAsamAsetat", htmlspecialchars($datahasil["inspeksiVisualAsamAsetat"] ?? ''), 1),
                                            array("KADAR ALKOHOL PERNAFASAN", "kadarAlkoholPernafasan", htmlspecialchars($datahasil["kadarAlkoholPernafasan"] ?? ''), 1),
                                            array("TES AMFETAMIN URIN", "tesAmfetaminUrin", htmlspecialchars($datahasil["tesAmfetaminUrin"] ?? ''), 1),
                                            array("ARUS PERNAFASAN EKSPIRASI", "arusPernafasanEkspirasi", htmlspecialchars($datahasil["arusPernafasanEkspirasi"] ?? ''), 1),
                                            array("FAKTOR RESIKO PERILAKU", "faktorResikoPerilaku", htmlspecialchars($datahasil["faktorResikoPerilaku"] ?? ''), 1),
                                            array("DIAGNOSIS", "diagnosis", htmlspecialchars($datahasil["diagnosis"] ?? ''), 1),
                                            array("HASIL PEMERIKSAAN", "hasilPemeriksaan", htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''), 1),
                                            array("CATATAN RENCANA TINDAKAN", "catatanTindakan", htmlspecialchars($datahasil["catatanTindakan"] ?? ''), 1),
                                        );
                                        _CreateWindowModalDetil($datahasil["idHasilLayanan"], "view", "viewsasaran-form", "viewsasaran-button", "", 600, "DETAIL#HASIL SCREENING", "", $datadetail, "", $linkurl, "");

                                        break;

                                    case '4': // Detail Konsultasi
                                        $datadetail = array(
                                            array("NAMA TERAPIS", "namaTerapis", htmlspecialchars($datahasil["namaTerapis"] ?? ''), 1),
                                            array("KELUHAN", "keluhan", htmlspecialchars($datahasil["keluhan"] ?? ''), 1),
                                            array("HASIL PEMERIKSAAN", "hasilPemeriksaan", htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''), 1),
                                            array("DIAGNOSIS", "diagnosis", htmlspecialchars($datahasil["diagnosis"] ?? ''), 1),
                                            array("SARAN DAN RUJUKAN", "saranRujukan", htmlspecialchars($datahasil["saranRujukan"] ?? ''), 1),
                                            array("CATATAN RENCANA TINDAKAN", "catatanTindakan", htmlspecialchars($datahasil["catatanTindakan"] ?? ''), 1),
                                        );
                                        _CreateWindowModalDetil($datahasil["idHasilLayanan"], "view", "viewsasaran-form", "viewsasaran-button", "", 600, "DETAIL#HASIL KONSULTASI", "", $datadetail, "", $linkurl, "");
                                        break;

                                    case '5': // Detail Edukasi
                                        $idJadwal = (int) $datahasil["idJadwal"];
                                        include("detailEdukasi.php");
                                        break;

                                    default:
                                        echo "<p>Detail layanan tidak ditemukan.</p>";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br><br><br>
        </div>
    </div>
</div>