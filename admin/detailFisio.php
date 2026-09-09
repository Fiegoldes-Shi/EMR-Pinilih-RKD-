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

if (!isset($baseurl)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    $baseurl = $protocol . $host . $path;
}

// Ambil URL path dari permintaan, lalu menghapus / di awal dan akhir, lalu memisahkan berdasarkan /
$request = $_SERVER['REQUEST_URI'];
$request = parse_url($request, PHP_URL_PATH);
$request = trim($request, '/');
$segments = explode('/', $request);

// Cari posisi slug 'detail' di URL untuk menangani kedalaman URL yang berbeda-beda
$idJadwal = 0;
$posMenu = array_search('detail', $segments);
if ($posMenu !== false && isset($segments[$posMenu + 1])) {
    $idJadwal = (int) $segments[$posMenu + 1];
} else {
    // Fallback ekstrim jika tidak ditemukan slug 'detail' di URL
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
        <a href="<?= $baseurl ?>/admin/fisioterapi"><ion-icon name="chevron-back-outline" size="large" style="color: black;"></ion-icon></a>
    </div>
    <div class="col-12 col-md-10 col-lg-11">
        <?php
        _myHeader("DETAIL FISIOTERAPI", "Detail Hasil Fisioterapi");
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
                        <th>Catatan</th>
                        <td><?= nl2br(htmlspecialchars($datajadwal['catatan'] ?? '')) ?></td>
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

    //Upload File
    $allowedLampiranExt = ["jpg", "jpeg", "png", "pdf"];
    $targetDir = "uploads/lampiranFisio/"; // Folder penyimpanan file
    $fileName = basename((string)$_FILES["lampiran"]["name"]);
    $filePath = $targetDir . time() . "_" . $fileName; // Buat nama unik

    if (!empty($_FILES["lampiran"]["tmp_name"]) && _isAllowedUploadExtension($fileName, $allowedLampiranExt)) {
        if (move_uploaded_file($_FILES["lampiran"]["tmp_name"], $filePath)) {
            $lampiran = $filePath;
        } else {
            $lampiran = null; // Jika gagal upload
        }
    } else {
        $lampiran = null; // Tidak ada file yang diupload
    }

    // field
    $datafield_hasil = array("idJadwal", "idUser", "idPasien", "idTerapis", "keluhan", "hasilPemeriksaan", "areaTubuh", "diagnosis", "catatanTindakan", "lampiran");

    // value
    $datavalue_hasil = array($idJadwal, $idUser, $_POST["idPasien"], $_POST["idTerapis"], $_POST["keluhan"], $_POST["hasilPemeriksaan"], $_POST["areaTubuh"], $_POST["diagnosis"], $_POST["catatanTindakan"], $lampiran);

    $insert = new cInsert();
    $insert->vInsertDataPrepared("hasil_layanan", $datafield_hasil, $datavalue_hasil);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $idUser = $_SESSION["idUser"];

    $sql = "SELECT lampiran FROM hasil_layanan WHERE idHasilLayanan = ?";
    $view = new cView();
    $arrayHasil = $view->vViewDataPrepared($sql, [$_POST["idHasilLayanan"]], "i");

    $lampiranLama = $arrayHasil[0]["lampiran"];

    //Upload File
    $allowedLampiranExt = ["jpg", "jpeg", "png", "pdf"];
    $targetDir = "uploads/lampiranFisio/"; // Folder penyimpanan file
    $fileName = basename((string)$_FILES["lampiran"]["name"]);
    $filePath = $targetDir . time() . "_" . $fileName; // Buat nama unik

    if (!empty($_FILES["lampiran"]["tmp_name"]) && _isAllowedUploadExtension($fileName, $allowedLampiranExt)) {
        if (move_uploaded_file($_FILES["lampiran"]["tmp_name"], $filePath)) {
            $lampiran = $filePath;
        } else {
            $lampiran = $lampiranLama;
        }
    } else {
        $lampiran = $lampiranLama;
    }

    $datafield_hasil = array("idJadwal", "idUser", "idPasien", "idTerapis", "keluhan", "hasilPemeriksaan", "areaTubuh", "diagnosis", "catatanTindakan", "lampiran");

    $datavalue_hasil = array($_POST["idJadwal"], $_POST["idUser"], $_POST["idPasien"], $_POST["idTerapis"], $_POST["keluhan"], $_POST["hasilPemeriksaan"], $_POST["areaTubuh"], $_POST["diagnosis"], $_POST["catatanTindakan"], $lampiran);

    $update = new cUpdate();
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
        $sql = "SELECT password FROM user WHERE role = 2 AND jbtn = 'Ketua' ORDER BY idUser ASC LIMIT 1";
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
        _myHeader("HASIL FISIOTERAPI", "Hasil Fisioterapi");
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
                                <p>Tambah Hasil Fisioterapi</p>
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
                                        echo "<option value='" . htmlspecialchars($datapasien['idPasien']) . "'>" . htmlspecialchars($datapasien['namaLengkap']) . " - " . htmlspecialchars($datapasien['idPasien']) . "</option>";
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
                                        echo "<option value='" . htmlspecialchars($dataterapis['idTerapis']) . "'>" . htmlspecialchars($dataterapis['namaTerapis']) . " - " . htmlspecialchars($dataterapis['idTerapis']) . "</option>";
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
                                <label for="hasilPemeriksaan">Hasil Pemeriksaan <span class="required">*</span></label>
                                <textarea class="form-control" id="hasilPemeriksaan" name="hasilPemeriksaan"
                                    placeholder="Hasil pemeriksaan oleh terapis" rows="3" cols="" id="floatingTextarea"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="areaTubuh">Area Tubuh <span class="required">*</span></label>
                                <input class="form-control" type="text" name="areaTubuh" id="areaTubuh" value=""
                                    placeholder="Area tubuh yang diterapi" maxlength="255" size="" required>
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
                            <div class="mb-3">
                                <label for="lampiran">Dokumen Lampiran</label>
                                <input type="file" class="form-control" id="lampiran" name="lampiran">
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
                    JOIN pasien p ON p.idPasien = hasil.idPasien
                    LEFT JOIN user u ON jp.idUser = u.idUser
                    LEFT JOIN terapis t ON t.idTerapis = hasil.idTerapis
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
                        <th width='10%'>Pasien</th>
                        <th width=''>Keluhan</th>
                        <th width=''>Hasil Pemeriksaan</th>
                        <th width=''>Diagnosis</th>
                        <th width=''>Rencana Tindakan</th>
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
                            <td><?= htmlspecialchars($datahasil["namaLengkap"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["keluhan"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["diagnosis"] ?? ''); ?></td>
                            <td><?= htmlspecialchars($datahasil["catatanTindakan"] ?? ''); ?></td>
                            <td>
                                <?php
                                // Path file LAMPIRAN
                                $lampiranPath = "uploads/lampiranFisio/" . basename((string)$datahasil["lampiran"]);

                                if (!empty($datahasil["lampiran"]) && file_exists($lampiranPath)) {
                                    $lampiran = '<a href="' . $baseurl . '/admin/' . htmlspecialchars($lampiranPath) . '" target="_new">Lihat Dokumen</a>';
                                } else {
                                    $lampiran = '<span>Tidak ada dokumen</span>';
                                }

                                $datadetail = array(
                                    array("ID PASIEN", "idPasien", htmlspecialchars($datahasil["namaLengkap"] ?? ''), 1),
                                    array("ID TERAPIS", "idTerapis", htmlspecialchars($datahasil["namaTerapis"] ?? ''), 1),
                                    array("KELUHAN", "keluhan", htmlspecialchars($datahasil["keluhan"] ?? ''), 1),
                                    array("HASIL PEMERIKSAAN", "hasilPemeriksaan", htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''), 1),
                                    array("AREA TUBUH YANG DITERAPI", "areaTubuh", htmlspecialchars($datahasil["areaTubuh"] ?? ''), 1),
                                    array("DIAGNOSIS", "diagnosis", htmlspecialchars($datahasil["diagnosis"] ?? ''), 1),
                                    array("CATATAN RENCANA TINDAKAN", "catatanTindakan", htmlspecialchars($datahasil["catatanTindakan"] ?? ''), 1),
                                    array("DOKUMEN LAMPIRAN", "lampiran", $lampiran, 1, "")
                                );
                                _CreateWindowModalDetil($datahasil["idHasilLayanan"], "view", "viewsasaran-form", "viewsasaran-button", "", 600, "DETAIL#HASIL FISIOTERAPI " . $datahasil['idHasilLayanan'], "", $datadetail, "", "", "");
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#formedit<?= $datahasil["idHasilLayanan"]; ?>"
                                    style="border-radius: 8px;">
                                    <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                                </button>
                                <!-- Modal UPDATE -->
                                <div class="modal fade" id="formedit<?= $datahasil["idHasilLayanan"]; ?>" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content text-left">
                                            <div class="modal-header">
                                                <figure class="text-left">
                                                    <blockquote class="blockquote">EDIT HASIL FISIOTERAPI</blockquote>
                                                    <figcaption class="blockquote-footer">
                                                        <?= htmlspecialchars($datahasil["idHasilLayanan"] ?? ''); ?>
                                                    </figcaption>
                                                    <figcaption class="blockquote-footer"><?= htmlspecialchars($datahasil["namaLengkap"] ?? ''); ?>
                                                        (<?= htmlspecialchars($datahasil["tanggalKegiatan"] ?? ''); ?>)</figcaption>
                                                </figure>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <FORM method="post" enctype="multipart/form-data" action="<?= $idJadwal ?>">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <input class="form-control" type="text" name="idHasilLayanan"
                                                            id="idHasilLayanan" value="<?= htmlspecialchars($datahasil["idHasilLayanan"] ?? ''); ?>"
                                                            maxlength="255" size="" hidden>
                                                        <input class="form-control" type="text" name="idJadwal"
                                                            id="idJadwal" value="<?= htmlspecialchars($datahasil["idJadwal"] ?? ''); ?>"
                                                            maxlength="255" size="" hidden>
                                                        <input class="form-control" type="text" name="idUser" id="idUser"
                                                            value="<?= htmlspecialchars($datahasil["idUser"] ?? ''); ?>" maxlength="255" size=""
                                                            hidden>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="idPasien">ID Pasien <span
                                                                class="required">*</span></label>
                                                        <select name="idPasien" id="idPasien" class="form-control" required>
                                                            <option value="<?= htmlspecialchars($datahasil["idPasien"] ?? ''); ?>">
                                                                <?= htmlspecialchars(($datahasil["namaLengkap"] ?? '') . " - " . ($datahasil["idPasien"] ?? '')); ?>
                                                            </option>
                                                            <?php
                                                            $sqlpasien = "SELECT * FROM pasien WHERE statusPasien = 'Aktif' ORDER BY idPasien";
                                                            $view = new cView();
                                                            $arraypasien = $view->vViewData($sqlpasien);

                                                            foreach ($arraypasien as $datapasien) { // Ambil semua data dari hasil eksekusi $sql
                                                                echo "<option value='" . htmlspecialchars($datapasien['idPasien']) . "'>" . htmlspecialchars($datapasien['namaLengkap']) . " - " . htmlspecialchars($datapasien['idPasien']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="idTerapis">ID Terapis <span
                                                                class="required">*</span></label>
                                                        <select name="idTerapis" id="idTerapis" class="form-control"
                                                            required>
                                                            <option value="<?= htmlspecialchars($datahasil["idTerapis"] ?? ''); ?>">
                                                                <?= htmlspecialchars(($datahasil["namaTerapis"] ?? '') . " - " . ($datahasil["idTerapis"] ?? '')); ?>
                                                            </option>
                                                            <?php
                                                            $sqlterapis = "SELECT * FROM terapis WHERE statusTerapis = 'Aktif' ORDER BY namaTerapis ASC";
                                                            $view = new cView();
                                                            $arrayterapis = $view->vViewData($sqlterapis);

                                                            foreach ($arrayterapis as $dataterapis) { // Ambil semua data dari hasil eksekusi $sql
                                                                echo "<option value='" . htmlspecialchars($dataterapis['idTerapis']) . "'>" . htmlspecialchars($dataterapis['namaTerapis']) . " - " . htmlspecialchars($dataterapis['idTerapis']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="keluhan">Keluhan <span class="required">*</span></label>
                                                        <input class="form-control" type="text" name="keluhan" id="keluhan"
                                                            value="<?= htmlspecialchars($datahasil["keluhan"] ?? ''); ?>"
                                                            placeholder="Keluhan yang dirasakan pasien" maxlength="255"
                                                            size="" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="hasilPemeriksaan">Hasil Pemeriksaan <span
                                                                class="required">*</span></label>
                                                        <textarea class="form-control" id="hasilPemeriksaan"
                                                            name="hasilPemeriksaan"
                                                            placeholder="Hasil pemeriksaan oleh terapis" rows="3" cols=""
                                                            id="floatingTextarea"
                                                            required><?= htmlspecialchars($datahasil["hasilPemeriksaan"] ?? ''); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="areaTubuh">Area Tubuh <span
                                                                class="required">*</span></label>
                                                        <input class="form-control" type="text" name="areaTubuh"
                                                            id="areaTubuh" value="<?= htmlspecialchars($datahasil["areaTubuh"] ?? ''); ?>"
                                                            placeholder="Area tubuh yang diterapi" maxlength="255" size=""
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="diagnosis">Diagnosis <span
                                                                class="required">*</span></label>
                                                        <textarea class="form-control" id="diagnosis" name="diagnosis"
                                                            placeholder="Diagnosis dari terapis" rows="3" cols=""
                                                            id="floatingTextarea"
                                                            required><?= htmlspecialchars($datahasil["diagnosis"] ?? ''); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="catatanTindakan">Catatan dan Rencana Tindakan <span
                                                                class="required">*</span></label>
                                                        <textarea class="form-control" id="catatanTindakan"
                                                            name="catatanTindakan"
                                                            placeholder="Catatan dan rekomendasi rencana tindakan dari terapis"
                                                            rows="3" cols="" id="floatingTextarea"
                                                            required><?= htmlspecialchars($datahasil["catatanTindakan"] ?? ''); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="lampiran">Dokumen Lampiran</label>
                                                        <input class="form-control" type="file" name="lampiran"
                                                            id="lampiran">
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
                                                        <figcaption class="blockquote-footer">Hasil Fisioterapi
                                                            <?= htmlspecialchars($datahasil["tanggalKegiatan"] ?? '') ?>
                                                        </figcaption>
                                                    </figure>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus hasil fisioterapi
                                                        <strong><?= htmlspecialchars($datahasil["namaLengkap"] ?? '') ?></strong>?
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
