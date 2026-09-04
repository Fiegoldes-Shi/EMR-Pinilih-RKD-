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
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");

// Ambil URL path dari permintaan
$request = $_SERVER['REQUEST_URI'];
$request = parse_url($request, PHP_URL_PATH);
$request = trim($request, '/');
$segments = explode('/', $request);

// FIX: Use dynamic search for '351' (Menu ID) to handle different URL depths
$idJadwal = 0;
$posMenu = array_search('detail', $segments);
if ($posMenu !== false && isset($segments[$posMenu + 1])) {
    $idJadwal = (int) $segments[$posMenu + 1];
} else {
    // Fallback ekstrim jika tidak ditemukan menu ID di URL
    $lastSegment = end($segments);
    if (is_numeric($lastSegment)) {
        $idJadwal = (int) $lastSegment;
    } else {
        $idJadwal = isset($segments[3]) ? (int)$segments[3] : 0;
    }
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
        <a href="<?= $baseurl ?>/manajemen/edukasi"><ion-icon name="chevron-back-outline" size="large" style="color: black;"></ion-icon></a>
    </div>
    <div class="col-12 col-md-10 col-lg-11">
        <?php
        _myHeader("DETAIL EDUKASI", "Detail Hasil Edukasi");
        ?>
    </div>
    <div class="col-md-12">
        <div class="card border-success border border-4">
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 200px;">Tanggal Kegiatan</th>
                        <td><?= htmlspecialchars($datajadwal['tanggalKegiatan'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Waktu Mulai</th>
                        <td><?= htmlspecialchars($datajadwal['waktuMulai'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Waktu Selesai</th>
                        <td><?= htmlspecialchars($datajadwal['waktuSelesai'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td><?= htmlspecialchars($datajadwal['lokasi'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Instansi</th>
                        <td><?= htmlspecialchars($datajadwal['instansi'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <td><?= htmlspecialchars($datajadwal['namaKegiatan'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Topik</th>
                        <td><?= htmlspecialchars($datajadwal['topik'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td><?= nl2br(htmlspecialchars($datajadwal['catatan'] ?? '')) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- HASIL EDUKASI -->
<?php
// Siapkan data dulu
$sqlhasil = "SELECT pe.*, jp.*, u.* FROM program_edukasi pe
            JOIN jadwal_program jp ON jp.idJadwal = pe.idJadwal
            LEFT JOIN user u ON pe.idUser = u.idUser
            WHERE pe.idJadwal = ?";

$view = new cView();
$datahasil = $view->vViewDataPrepared($sqlhasil, [$idJadwal], "i");

$idUser = $_SESSION["idUser"];

$idEdukasi = null;
$hasilKegiatan = 'Belum ada hasil';
$dokumentasi = '';

if (!empty($datahasil)) {
    $datahasil = $datahasil[0];
    $idEdukasi = $datahasil["idEdukasi"];
    $hasilKegiatan = $datahasil['hasilKegiatan'] ?? 'Belum ada hasil';
    $dokumentasi = $datahasil['dokumentasi'] ?? '';
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
        <h4>Hasil Program Edukasi</h4>
    </div>
</div>

<p></p>
<div class="row mx-2">
    <div class="col-md-12">
        <div class="card border-success border border-4">
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Hasil Kegiatan</th>
                        <td><?= htmlspecialchars($hasilKegiatan ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Dokumentasi</th>
                        <td>
                            <?php if (!empty($dokumentasi) && file_exists(__DIR__ . '/../admin/' . $dokumentasi)): ?>
                                <a href="<?= $baseurl ?>/admin/<?= htmlspecialchars($dokumentasi); ?>" target="_new">Lihat Dokumentasi</a>
                            <?php else: ?>
                                <span>Tidak ada dokumentasi</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- PESERTA EDUKASI -->
<br>
<p></p>
<div class="row mx-2">
    <div class="col-12 col-md-10 col-lg-11">
        <h4>Peserta Program Edukasi</h4>
        <br>
    </div>
</div>

<?php
// Query ENUM 'kelompokUsia'
$usia = "SHOW COLUMNS FROM peserta LIKE 'usia'";
$view = new cView();
$arrayUsia = $view->vViewData($usia);
$enumUsia = [];
if (!empty($arrayUsia)) {
    $row = $arrayUsia[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumUsia = explode(",", str_replace("'", "", $matches[1]));
    }
}

// Query ENUM 'jenisKelamin'
$jk = "SHOW COLUMNS FROM peserta LIKE 'jenisKelamin'";
$view = new cView();
$arrayJK = $view->vViewData($jk);
$enumJK = [];
if (!empty($arrayJK)) {
    $row = $arrayJK[0];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enumJK = explode(",", str_replace("'", "", $matches[1]));
    }
}
?>

<p></p>
<div class="row mx-2">
    <div class="col-md-12">
        <?php
        $sqlpeserta = "SELECT p.*, pe.* FROM peserta p
                    JOIN peserta_edukasi pe ON pe.idPeserta = p.idPeserta
                    WHERE pe.idEdukasi = ?
                    ORDER BY pe.idPeserta DESC";
        $view = new cView();
        $arraypeserta = $idEdukasi ? $view->vViewDataPrepared($sqlpeserta, [$idEdukasi], "i") : [];
        ?>
        <div class='table-responsive'>
            <table id='tablePeserta' class='table table-condensed'>
                <thead>
                    <tr>
                        <th width='5%' class="text-right">No.</th>
                        <th width=''>Nama</th>
                        <th width=''>Asal Lembaga</th>
                        <th width=''>Jenis Kelamin</th>
                        <th width=''>Usia</th>
                        <th width=''>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    foreach ($arraypeserta as $datapeserta) {
                        $cnourut = $cnourut + 1;
                        ?>
                        <tr>
                            <td class="text-right"><?= $cnourut; ?></td>
                            <td><?= htmlspecialchars($datapeserta["nama"]); ?></td>
                            <td><?= htmlspecialchars($datapeserta["asalLembaga"]); ?></td>
                            <td><?= htmlspecialchars($datapeserta["jenisKelamin"]); ?></td>
                            <td><?= htmlspecialchars($datapeserta["usia"]); ?></td>
                            <td><?= htmlspecialchars($datapeserta["alamat"]); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tablePeserta').DataTable({
            "order": [[0, "asc"]],
            "paging": true,
            "searching": true,
            "info": true
        });
    });
</script>

<p></p>
<div class="row">
    <div class="col-md-12">
        <p><br><br><br><br><br></p>
    </div>
</div>
