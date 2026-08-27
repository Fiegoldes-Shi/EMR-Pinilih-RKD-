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

// FIX: Use dynamic search for '351' (Menu ID) to handle different URL depths
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
        <a href="<?= $baseurl ?>/terapis/beranda"><ion-icon name="chevron-back-outline" size="large" style="color: black;"></ion-icon></a>
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
                        <th>Nama Kegiatan</th>
                        <td><?= $datajadwal['namaKegiatan'] ?></td>
                    </tr>
                    <tr>
                        <th>Topik</th>
                        <td><?= $datajadwal['topik'] ?></td>
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

<!-- HASIL EDUKASI -->

<?php
// insert
if (!empty($_POST["savebtn"])) {
    $linkurl = $idJadwal;

    // Upload file
    $allowedDokumentasiExt = ["jpg", "jpeg", "png", "pdf"];
    $targetDir = "uploads/hasilEdukasi/"; // Folder penyimpanan file
    $fileName = basename((string)$_FILES["dokumentasi"]["name"]);
    $filePath = $targetDir . time() . "_" . $fileName; // Buat nama unik

    if (!empty($_FILES["dokumentasi"]["tmp_name"]) && _isAllowedUploadExtension($fileName, $allowedDokumentasiExt)) {
        if (move_uploaded_file($_FILES["dokumentasi"]["tmp_name"], $filePath)) {
            $dokumentasi = $filePath;
        } else {
            $dokumentasi = null; // Jika gagal upload
        }
    } else {
        $dokumentasi = null;
    }

    // field
    $datafield_hasil = array("idJadwal", "idUser", "hasilKegiatan", "dokumentasi");

    // value
    $datavalue_hasil = array($_POST["idJadwal"], $_POST["idUser"], $_POST["hasilKegiatan"], $dokumentasi);

    $insert = new cInsert();
    $insert->vInsertDataPrepared("program_edukasi", $datafield_hasil, $datavalue_hasil);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = $idJadwal;

    $sql = "SELECT dokumentasi FROM program_edukasi WHERE idEdukasi = ?";
    $view = new cView();
    $arrayEdukasi = $view->vViewDataPrepared($sql, [$_POST["idEdukasi"]], "i");

    $dokumentasiLama = $arrayEdukasi[0]["dokumentasi"];

    // Upload file
    $allowedDokumentasiExt = ["jpg", "jpeg", "png", "pdf"];
    $targetDir = "uploads/hasilEdukasi/"; // Folder penyimpanan file
    $fileName = basename((string)$_FILES["dokumentasi"]["name"]);
    $filePath = $targetDir . time() . "_" . $fileName; // Buat nama unik

    if (!empty($_FILES["dokumentasi"]["tmp_name"]) && _isAllowedUploadExtension($fileName, $allowedDokumentasiExt)) {
        if (move_uploaded_file($_FILES["dokumentasi"]["tmp_name"], $filePath)) {
            $dokumentasi = $filePath;
        } else {
            $dokumentasi = $dokumentasiLama; // Jika gagal upload
        }
    } else {
        $dokumentasi = $dokumentasiLama;
    }

    $datafield_hasil = array("idJadwal", "idUser", "hasilKegiatan", "dokumentasi");
    $datavalue_hasil = array($_POST["idJadwal"], $_POST["idUser"], $_POST["hasilKegiatan"], $dokumentasi);

    $update = new cUpdate();
    $update->vUpdateDataPrepared("program_edukasi", $datafield_hasil, $datavalue_hasil, "idEdukasi", $_POST["idEdukasi"]);
}
?>

<?php
// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    foreach ($_POST["hiddendeletevalue"] as $data) {
        $delete->vDeleteDataPrepared($data["table"], $data["field"], $data["value"]);
    }
}
?>

<?php
// Siapkan data dulu
$sqlhasil = "SELECT pe.*, jp.*, u.* FROM program_edukasi pe 
            JOIN jadwal_program jp ON jp.idJadwal = pe.idJadwal 
            JOIN user u ON pe.idUser = u.idUser
            WHERE pe.idJadwal = ?";

$view = new cView();
$datahasil = $view->vViewDataPrepared($sqlhasil, [$idJadwal], "i");

$idUser = $_SESSION["idUser"];

$idEdukasi = null;
$hasilKegiatan = 'Belum ada hasil';
$dokumentasi = 'Belum ada dokumentasi';

if (!empty($datahasil)) {
    $datahasil = $datahasil[0];
    $idEdukasi = $datahasil["idEdukasi"];
    $hasilKegiatan = $datahasil['hasilKegiatan'] ?? 'Belum ada hasil';
    $dokumentasi = $datahasil['dokumentasi'] ?? 'Belum ada dokumentasi';
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

<!-- Modal (hidden for terapis - read only) -->
<div class="modal fade" id="modalFormEdukasi<?= $idEdukasi; ?>" tabindex="-1"
    aria-labelledby="modalFormEdukasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormEdukasiLabel">Hasil Program Edukasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $idJadwal ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="text" name="idEdukasi" value="<?= $idEdukasi ?>" hidden>
                    <input type="text" name="idJadwal" value="<?= $idJadwal ?>" hidden>
                    <input type="text" name="idUser" value="<?= $idUser ?>" hidden>
                    <div class="mb-3">
                        <label for="hasilKegiatan" class="form-label">Hasil Kegiatan <span
                                class="required">*</span></label>
                        <textarea name="hasilKegiatan" id="hasilKegiatan" class="form-control" rows="4"
                            required><?= htmlspecialchars($hasilKegiatan) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="dokumentasi" class="form-label">Dokumentasi (PNG/JPG/PDF)</label>
                        <?php if ($dokumentasi != "Belum ada dokumentasi"): ?>
                            <p>File sebelumnya: <a href="<?= $baseurl ?>/admin/<?= $dokumentasi ?>"
                                    target="_blank"><?= basename((string)$dokumentasi) ?></a></p>
                        <?php endif; ?>
                        <input type="file" name="dokumentasi" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="editbtn" value="true" class="btn btn-primary btn-sm"
                        style="border-radius: 25px;">SIMPAN</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                        style="border-radius: 25px;">TUTUP</button>
                </div>
            </form>
        </div>
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
                        <td><?= $hasilKegiatan ?></td>
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

<!-- PESERTA -->

<?php
// insert
if (!empty($_POST["simpanbtn"])) {
    $linkurl = $idJadwal;

    // Data untuk peserta_edukasi
    $datafield_pesertaEdukasi = array("idEdukasi", "idPeserta");

    // Cek apakah peserta sudah ada
    if (!empty($_POST["idPeserta"]) && $_POST["idPeserta"] !== "baru") {
        // Jika peserta sudah ada, langsung masukkan ke peserta_edukasi
        $idPeserta = $_POST["idPeserta"];
    } else {
        // Jika peserta baru, insert ke tabel peserta dulu
        $datafield_peserta = array("nama", "asalLembaga", "jenisKelamin", "usia", "alamat");
        $datavalue_peserta = array($_POST["nama"], $_POST["asalLembaga"] === '' ? "" : $_POST["asalLembaga"], $_POST["jenisKelamin"], $_POST["usia"], $_POST["alamat"] === '' ? "" : $_POST["alamat"]);

        ob_start(); // Tahan output Swal.fire pertama agar tidak bentrok
        $insert1 = new cInsert();
        $idPeserta = $insert1->vInsertDataPrepared("peserta", $datafield_peserta, $datavalue_peserta);
        ob_end_clean(); // Hapus output Swal.fire pertama
    }

    // Insert ke peserta_edukasi jika $idPeserta valid
    if ($idPeserta) {
        $datavalue_pesertaEdukasi = array($idEdukasi, $idPeserta);
        $insert2 = new cInsert();
        $insert2->vInsertDataPrepared("peserta_edukasi", $datafield_pesertaEdukasi, $datavalue_pesertaEdukasi);
    }
}
?>

<?php
// update
if (!empty($_POST["ubahbtn"])) {
    $linkurl = $idJadwal;

    $datafield_peserta = array("nama", "asalLembaga", "jenisKelamin", "usia", "alamat");
    $datavalue_peserta = array($_POST["nama"], $_POST["asalLembaga"] === '' ? "" : $_POST["asalLembaga"], $_POST["jenisKelamin"], $_POST["usia"], $_POST["alamat"] === '' ? "" : $_POST["alamat"]);

    $update = new cUpdate();
    $update->vUpdateDataPrepared("peserta", $datafield_peserta, $datavalue_peserta, "idPeserta", $_POST["idPeserta"]);
}
?>

<?php
// delete
if (!empty($_POST["btndelete"])) {
    $delete = new cDelete();
    foreach ($_POST["hiddendeletevalue"] as $data) {
        $delete->vDeleteDataPrepared($data["table"], $data["field"], $data["value"]);
    }
}
?>

<!-- PESERTA EDUKASI -->
<br>
<p></p>
<div class="row mx-2">
    <div class="col-12 col-md-10 col-lg-11">
        <h4>Peserta Program Edukasi</h4>
        <br>
    </div>
    <div class="col-12 col-md-2 col-lg-1 mb-3">
        <!-- Tombol tambah peserta disembunyikan untuk terapis (read only) -->
        <?php
        // Query ENUM 'kelompokUsia'
        $usia = "SHOW COLUMNS FROM peserta LIKE 'usia'";
        $view = new cView();
        $arrayUsia = $view->vViewData($usia);
        $enumUsia = [];
        if (!empty($arrayUsia)) {
            $row = $arrayUsia[0]; // Ambil hasil pertama
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
            $row = $arrayJK[0]; // Ambil hasil pertama
            if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                $enumJK = explode(",", str_replace("'", "", $matches[1]));
            }
        }

        // Ambil data peserta yang sudah ada
        // Ambil data peserta yang sudah ada
        $queryPeserta = "SELECT idPeserta, nama, asalLembaga FROM peserta ORDER BY nama ASC";
        $view = new cView();
        $pesertaList = $view->vViewData($queryPeserta);

        // add new data
        $linkurl = $idJadwal;
        ?>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5" id="exampleModalLabel">
                            <blockquote class="blockquote">
                                <p>Tambah Data Peserta</p>
                            </blockquote>
                        </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="" method="post" action="<?= $idJadwal ?>" enctype="multipart/form-data">
                        <div class="modal-body">
                            <!-- Pilihan Peserta -->
                            <div class="mb-3" id="selectPesertaContainer">
                                <label for="pilihPeserta">Pilih Peserta</label>
                                <select id="pilihPeserta" name="idPeserta" class="form-control">
                                    <option value="">- Pilih Peserta -</option>
                                    <?php foreach ($pesertaList as $peserta): ?>
                                        <option value="<?= $peserta['idPeserta'] ?>">
                                            <?= $peserta['nama'] ?> - <?= $peserta['asalLembaga'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <!-- <option value="baru">Nama tidak ada di daftar</option> -->
                                </select>
                                <br>
                                <label for="pesertaBaru">Belum pernah daftar sebelumnya?</label>
                                <br>
                                <button type="button" id="btnDaftarBaru" class="btn btn-success btn-sm mt-2">Daftar
                                    Peserta Baru</button>
                            </div>

                            <!-- Form Peserta Baru (Hidden secara default) -->
                            <div id="formPesertaBaru" style="display: none;">
                                <div class="mb-3">
                                    <button type="button" id="btnBatalBaru" class="btn btn-secondary btn-sm mb-2" style="border-radius: 25px;">
                                        <i class="fa-solid fa-arrow-left"></i> Kembali Pilih Peserta
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label for="nama">NAMA PESERTA <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="nama" id="nama" value=""
                                        placeholder="Nama Lengkap" maxlength="255" size="" required disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="asalLembaga">ASAL LEMBAGA <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="asalLembaga" id="asalLembaga" value=""
                                        placeholder="Asal Lembaga" maxlength="255" size="" required disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="jenisKelamin">JENIS KELAMIN <span class="required">*</span></label>
                                    <select name="jenisKelamin" class="form-control" required disabled>
                                        <option value="">- pilihan -</option>
                                        <?php
                                        foreach ($enumJK as $option) {
                                            $trimmedValue = trim($option);
                                            echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="usia">USIA <span class="required">*</span></label>
                                    <select name="usia" class="form-control" required disabled>
                                        <option value="">- pilihan -</option>
                                        <?php
                                        foreach ($enumUsia as $option) {
                                            $trimmedValue = trim($option);
                                            echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat">ALAMAT <span class="required">*</span></label>
                                    <input class="form-control" type="text" name="alamat" id="alamat" value=""
                                        placeholder="Alamat Peserta" maxlength="255" size="" required disabled>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 25px;"
                                name="simpanbtn" value="true">Simpan</button>
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

<script>
    function resetPilihPeserta() {
        // Tampilkan kembali / Enable dropdown peserta
        document.getElementById("pilihPeserta").removeAttribute("disabled");
        document.getElementById("selectPesertaContainer").style.display = "block";

        // Sembunyikan form peserta baru
        document.getElementById("formPesertaBaru").style.display = "none";

        // Disable input dalam form peserta baru agar tidak ikut tersubmit
        document.querySelectorAll("#formPesertaBaru input, #formPesertaBaru select").forEach(el => {
            el.setAttribute("disabled", "true");
        });
    }

    document.getElementById("btnDaftarBaru").addEventListener("click", function () {
        // Sembunyikan / Disable dropdown peserta
        document.getElementById("pilihPeserta").setAttribute("disabled", "true");
        document.getElementById("selectPesertaContainer").style.display = "none";

        // Tampilkan form peserta baru
        document.getElementById("formPesertaBaru").style.display = "block";

        // Aktifkan input dalam form peserta baru
        document.querySelectorAll("#formPesertaBaru input, #formPesertaBaru select").forEach(el => {
            el.removeAttribute("disabled");
        });
        
        // Reset Select2 value to avoid leftover selection
        $('#pilihPeserta').val('').trigger('change');
    });

    document.getElementById("btnBatalBaru").addEventListener("click", resetPilihPeserta);

    // Kembalikan ke tampilan default setiap kali modal ditutup
    $('#exampleModal').on('hidden.bs.modal', function () {
        resetPilihPeserta();
    });
</script>


<p></p>
<div class="row mx-2">
    <div iv class="col-md-12">
        <?php
        $sqlpeserta = "SELECT p.*, pe.* FROM peserta p
                        JOIN peserta_edukasi pe ON pe.idPeserta = p.idPeserta
                        WHERE pe.idEdukasi = ?
                        ORDER BY pe.idPeserta DESC";
        $view = new cView();
        $arraypeserta = $view->vViewDataPrepared($sqlpeserta, [$idEdukasi], "i");
        ?>
        <div id="" class='table-responsive'>
            <table id='example' class='table table-condensed'>
                <thead>
                    <tr>
                        <th width='5%' class="text-right">No.</th>
                        <th width=''>Nama</th>
                        <th width=''>Asal Lembaga</th>
                        <th width=''>Jenis Kelamin</th>
                        <th width=''>Usia</th>
                        <th width=''>Alamat</th>
                        <!-- Kolom EDIT dan HAPUS disembunyikan untuk terapis (read only) -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    foreach ($arraypeserta as $datapeserta) {
                        $cnourut = $cnourut + 1;
                        ?>
                        <tr class=''>
                            <td class="text-right"><?= $cnourut; ?></td>
                            <td><?= $datapeserta["nama"]; ?></td>
                            <td><?= $datapeserta["asalLembaga"]; ?></td>
                            <td><?= $datapeserta["jenisKelamin"]; ?></td>
                            <td><?= $datapeserta["usia"]; ?></td>
                            <td><?= $datapeserta["alamat"]; ?></td>
                            <?php /* Kolom EDIT dan HAPUS disembunyikan untuk terapis (read only) */ ?>
                            <?php if (false): ?>
                            <td>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#formedit<?= $datapeserta["idPeserta"]; ?>" style="border-radius: 8px;">
                                    <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                                </button>

                                <?php
                                // Query ENUM 'kelompokUsia'
                                $usia = "SHOW COLUMNS FROM peserta LIKE 'usia'";
                                $view = new cView();
                                $arrayUsia = $view->vViewData($usia);
                                $enumUsia = [];
                                if (!empty($arrayUsia)) {
                                    $row = $arrayUsia[0]; // Ambil hasil pertama
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
                                    $row = $arrayJK[0]; // Ambil hasil pertama
                                    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
                                        $enumJK = explode(",", str_replace("'", "", $matches[1]));
                                    }
                                }

                                // add new data
                                $linkurl = $idJadwal;
                                ?>
                                <div class="modal fade" id="formedit<?= $datapeserta["idPeserta"]; ?>" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog text-start">
                                        <div class="modal-content text-left">
                                            <div class="modal-header">
                                                <figure class="text-left">
                                                    <blockquote class="blockquote">
                                                        <p>EDIT PESERTA</p>
                                                    </blockquote>
                                                    <figcaption class="blockquote-footer"><?= $datapeserta["idPeserta"]; ?>
                                                    </figcaption>
                                                    <figcaption class="blockquote-footer"><?= $datapeserta["nama"]; ?>
                                                    </figcaption>
                                                </figure>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form class="" method="post" action="<?= $idJadwal ?>"
                                                enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <input class="form-control" type="text" name="idPeserta"
                                                            id="idPeserta" value="<?= $datapeserta["idPeserta"]; ?>"
                                                            placeholder="id Peserta" maxlength="255" size="" hidden>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="nama">NAMA PESERTA <span
                                                                class="required">*</span></label>
                                                        <input class="form-control" type="text" name="nama" id="nama"
                                                            value="<?= $datapeserta["nama"]; ?>" placeholder="Nama Lengkap"
                                                            maxlength="255" size="" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="asalLembaga">ASAL LEMBAGA <span
                                                                class="required">*</span></label>
                                                        <input class="form-control" type="text" name="asalLembaga"
                                                            id="asalLembaga" value="<?= $datapeserta["asalLembaga"]; ?>"
                                                            placeholder="Asal Lembaga" maxlength="255" size="" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="jenisKelamin">JENIS KELAMIN <span
                                                                class="required">*</span></label>
                                                        <select name="jenisKelamin" class="form-control" required>
                                                            <option value="<?= $datapeserta["jenisKelamin"]; ?>">
                                                                <?= $datapeserta["jenisKelamin"]; ?>
                                                            </option>
                                                            <?php
                                                            foreach ($enumJK as $option) {
                                                                $trimmedValue = trim($option);
                                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="usia">USIA <span class="required">*</span></label>
                                                        <select name="usia" class="form-control" required>
                                                            <option value="<?= $datapeserta["usia"]; ?>">
                                                                <?= $datapeserta["usia"]; ?>
                                                            </option>
                                                            <?php
                                                            foreach ($enumUsia as $option) {
                                                                $trimmedValue = trim($option);
                                                                echo '<option value="' . $trimmedValue . '">' . $trimmedValue . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="alamat">ALAMAT <span class="required">*</span></label>
                                                        <input class="form-control" type="text" name="alamat" id="alamat"
                                                            value="<?= $datapeserta["alamat"]; ?>"
                                                            placeholder="Alamat Peserta" maxlength="255" size="" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="ubahbtn" value="true"
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
                                    data-bs-target="#formdelete<?= $datapeserta["idPeserta"]; ?>"
                                    style="border-radius: 8px;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="formdelete<?= $datapeserta["idPeserta"]; ?>" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header text-start">
                                                <figure>
                                                    <blockquote class="blockquote">
                                                        <p>HAPUS</p>
                                                    </blockquote>
                                                    <figcaption class="blockquote-footer"><?= $datapeserta["idPeserta"] ?>
                                                    </figcaption>
                                                    <figcaption class="blockquote-footer">
                                                        <?= $datapeserta["nama"] . " - " . $datapeserta["asalLembaga"] ?>
                                                    </figcaption>
                                                </figure>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <h6 class="">Yakin akan menghapus <ion-icon name="help-outline"></ion-icon>
                                                </h6>
                                            </div>
                                            <form action="" method="post">
                                                <div class="modal-footer">
                                                    <?php
                                                    $datadelete = array(
                                                        array("idPesertaEdukasi", $datapeserta["idPesertaEdukasi"], "peserta_edukasi")
                                                    );
                                                    foreach ($datadelete as $key => $val) {
                                                        ?>
                                                        <input type="hidden" name="hiddendeletevalue[<?= $key ?>][field]"
                                                            value="<?= htmlspecialchars($val[0]) ?>">
                                                        <input type="hidden" name="hiddendeletevalue[<?= $key ?>][value]"
                                                            value="<?= htmlspecialchars($val[1]) ?>">
                                                        <input type="hidden" name="hiddendeletevalue[<?= $key ?>][table]"
                                                            value="<?= htmlspecialchars($val[2]) ?>">
                                                    <?php } ?>
                                                    <button type="submit" name="btndelete" value="true"
                                                        class="btn btn-danger btn-sm"
                                                        style="border-radius: 25px;">HAPUS</button>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <?php endif; ?>
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
