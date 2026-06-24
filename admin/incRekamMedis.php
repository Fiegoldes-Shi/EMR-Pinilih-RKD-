<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");
?>

<style>
    .button-container {
        display: flex;
        gap: 10px;
        /* Jarak antara tombol */
    }
</style>

<div class="row mx-2">
    <div class="col-12 col-md-10 col-lg-11">
        <?php
        _myHeader("MENU REKAM MEDIS", "Data Rekam Medis");
        ?>
    </div>

    <?php
    // Proses Filtering Data
// Proses Filtering Data
    $sql = "SELECT p.*, sd.*, jd.*, k.namaKelurahan 
        FROM pasien p
        JOIN sub_disabilitas sd ON sd.idSubDisabilitas = p.idSubDisabilitas
        JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas
        LEFT JOIN kelurahan k ON k.idKelurahan = p.idKelurahanDomisili";
    $where = [];
    $params = [];
    $types = "";

    // Filter Kelompok Usia
    if (!empty($_POST["kelompokUsia"])) {
        $selectedUsia = $_POST["kelompokUsia"];
        if (is_array($selectedUsia)) {
            $imgPlaceholders = implode(',', array_fill(0, count($selectedUsia), '?'));
            $where[] = "p.kelompokUsia IN ($imgPlaceholders)";
            foreach ($selectedUsia as $us) {
                $params[] = trim($us);
                $types .= "s";
            }
        } else {
            $where[] = "p.kelompokUsia = ?";
            $params[] = trim($_POST["kelompokUsia"]);
            $types .= "s";
        }
    }

    // Filter Jenis Kelamin
    if (!empty($_POST["jenisKelamin"])) {
        $selectedJK = $_POST["jenisKelamin"];
        if (is_array($selectedJK)) {
            $jkPlaceholders = implode(',', array_fill(0, count($selectedJK), '?'));
            $where[] = "p.jenisKelamin IN ($jkPlaceholders)";
            foreach ($selectedJK as $jk) {
                $params[] = trim($jk);
                $types .= "s";
            }
        } else {
            $where[] = "p.jenisKelamin = ?";
            $params[] = trim($_POST["jenisKelamin"]);
            $types .= "s";
        }
    }

    // Filter Golongan Darah
    if (!empty($_POST["golonganDarah"])) {
        $selectedGoldar = $_POST["golonganDarah"];
        if (is_array($selectedGoldar)) {
            $gdPlaceholders = implode(',', array_fill(0, count($selectedGoldar), '?'));
            $where[] = "p.golonganDarah IN ($gdPlaceholders)";
            foreach ($selectedGoldar as $gd) {
                $params[] = trim($gd);
                $types .= "s";
            }
        } else {
            $where[] = "p.golonganDarah = ?";
            $params[] = trim($_POST["golonganDarah"]);
            $types .= "s";
        }
    }

    // Filter Jenis Disabilitas
    if (!empty($_POST["jenisDisabilitas"])) {
        $selectedDisabilitas = $_POST["jenisDisabilitas"];
        if (is_array($selectedDisabilitas)) {
            $jdPlaceholders = implode(',', array_fill(0, count($selectedDisabilitas), '?'));
            $where[] = "jd.jenisDisabilitas IN ($jdPlaceholders)";
            foreach ($selectedDisabilitas as $jd) {
                $params[] = trim($jd);
                $types .= "s";
            }
        } else {
            $where[] = "jd.jenisDisabilitas = ?";
            $params[] = trim($_POST["jenisDisabilitas"]);
            $types .= "s";
        }
    }
    // Filter Kelurahan (khusus Sedayu, ID 40579–40582)
    if (!empty($_POST["idKelurahan"])) {
        $where[] = "p.idKelurahanDomisili = ?";
        $params[] = intval($_POST["idKelurahan"]);
        $types .= "i";
    }

    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY p.namaLengkap ASC;";

    $view = new cView();
    // Gunakan vViewDataPrepared
    $arrayhasil = $view->vViewDataPrepared($sql, $params, $types);
    ?>

    <?php
    // Query ENUM 'kelompokUsia'
    $usia = "SHOW COLUMNS FROM pasien LIKE 'kelompokUsia'";
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
    $arrayGoldar = $view->vViewData($goldar);
    $enumGoldar = [];
    if (!empty($arrayGoldar)) {
        $row = $arrayGoldar[0];
        if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
            $enumGoldar = explode(",", str_replace("'", "", $matches[1]));
        }
    }

    // Daftar jenis disabilitas
    $disabilitasQuery = "SELECT DISTINCT jenisDisabilitas FROM jenis_disabilitas";
    $enumDisabilitas = $view->vViewData($disabilitasQuery);

    // Daftar kelurahan dengan id tertentu
    $kelurahanQuery = "SELECT idKelurahan, namaKelurahan FROM kelurahan WHERE idKelurahan BETWEEN 40579 AND 40582";
    $kelurahanList = $view->vViewData($kelurahanQuery);
    ?>

    <!-- FILTERING -->
    <div class="row">
        <div class="col-md-12">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-4">
                        <label for="kelompokUsia">KELOMPOK USIA</label>
                        <select name="kelompokUsia" class="form-control">
                            <option value="">- pilihan -</option>
                            <?php foreach ($enumKelompokUsia as $option) {
                                echo '<option value="' . trim($option) . '">' . trim($option) . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="jenisKelamin">JENIS KELAMIN</label>
                        <select name="jenisKelamin" class="form-control">
                            <option value="">- pilihan -</option>
                            <?php foreach ($enumJK as $option) {
                                echo '<option value="' . trim($option) . '">' . trim($option) . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div><br></div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <label for="golonganDarah">GOLONGAN DARAH</label>
                        <select name="golonganDarah" class="form-control">
                            <option value="">- pilihan -</option>
                            <?php foreach ($enumGoldar as $option) {
                                echo '<option value="' . trim($option) . '">' . trim($option) . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="jenisDisabilitas">JENIS DISABILITAS</label>
                        <select name="jenisDisabilitas" class="form-control">
                            <option value="">- pilihan -</option>
                            <?php foreach ($enumDisabilitas as $row) {
                                echo '<option value="' . trim($row["jenisDisabilitas"]) . '">' . trim($row["jenisDisabilitas"]) . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="idKelurahan">KELURAHAN (Sedayu)</label>
                        <select name="idKelurahan" class="form-control">
                            <option value="">- pilihan -</option>
                            <?php foreach ($kelurahanList as $row) {
                                echo '<option value="' . $row["idKelurahan"] . '">' . $row["namaKelurahan"] . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2 mt-3">
                        <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px;"
                            name="searchbtn" value="true"><b>CARI</b></button>
                    </div>
            </form>
        </div>
    </div>

    <!-- Tabel Hasil -->
    <div class="row">
        <div class="col-md-12">
            <br><br>
            <div class="table-responsive">
                <div class="hasil-filtering" id="hasilFilter">
                    <table id='example' class='table table-condensed'>
                        <thead>
                            <tr>
                                <th width="2%">No.</th>
                                <th class="text-center" width="">Nama Pasien</th>
                                <th class="text-center" width="">Jenis Kelamin</th>
                                <th class="text-center" width="">Usia</th>
                                <th class="text-center" width="">Golongan Darah</th>
                                <th class="text-center" width="5%">Alamat</th>
                                <th class="text-center" width="">Kelurahan</th>
                                <th class="text-center" width="">Jenis Disabilitas</th>
                                <th class="text-center" width="">Sub Jenis Disabilitas</th>
                                <th class="text-center" width="">Alat Bantu</th>
                                <th width="5%">DETAIL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cnourut = 0;
                            foreach ($arrayhasil as $data) {
                                $cnourut++;
                                ?>
                                <tr>
                                    <td class="text-right"><?= $cnourut; ?></td>
                                    <td><?= $data["namaLengkap"]; ?></td>
                                    <td><?= $data["jenisKelamin"]; ?></td>
                                    <td><?= $data["kelompokUsia"]; ?></td>
                                    <td><?= $data["golonganDarah"]; ?></td>
                                    <td><?= $data["alamatDomisili"]; ?></td>
                                    <td><?= $data["namaKelurahan"]; ?></td>
                                    <td><?= $data["jenisDisabilitas"]; ?></td>
                                    <td><?= $data["namaDisabilitas"]; ?></td>
                                    <td><?= $data["alatBantu"]; ?></td>
                                    <td>
                                        <form method="post" action="411">
                                            <input type="hidden" name="idPasien" value="<?= $data["idPasien"]; ?>">
                                            <button type="submit" class="btn btn-info" style="border-radius: 8px;"> <i
                                                    class="fa-regular fa-eye" style="color: #000000;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="button-container">
                    <form method="post" action="export_pdf.php" target="_blank">
                        <input type="hidden" name="kelompokUsia" value="<?= $_POST["kelompokUsia"] ?? ''; ?>">
                        <input type="hidden" name="jenisKelamin" value="<?= $_POST["jenisKelamin"] ?? ''; ?>">
                        <input type="hidden" name="golonganDarah" value="<?= $_POST["golonganDarah"] ?? ''; ?>">
                        <input type="hidden" name="jenisDisabilitas" value="<?= $_POST["jenisDisabilitas"] ?? ''; ?>">
                        <input type="hidden" name="idKelurahan" value="<?= $_POST["idKelurahan"] ?? ''; ?>">
                        <button type="submit" name="export_pdf" class="btn btn-danger">CETAK PDF</button>
                    </form>
                    <form method="post" action="export_excel.php">
                        <input type="hidden" name="kelompokUsia" value="<?= $_POST["kelompokUsia"] ?? ''; ?>">
                        <input type="hidden" name="jenisKelamin" value="<?= $_POST["jenisKelamin"] ?? ''; ?>">
                        <input type="hidden" name="golonganDarah" value="<?= $_POST["golonganDarah"] ?? ''; ?>">
                        <input type="hidden" name="jenisDisabilitas" value="<?= $_POST["jenisDisabilitas"] ?? ''; ?>">
                        <input type="hidden" name="idKelurahan" value="<?= $_POST["idKelurahan"] ?? ''; ?>">
                        <button type="submit" name="export_excel" class="btn btn-success">CETAK EXCEL</button>
                    </form>
                </div>
                <br><br><br>
            </div>
        </div>
    </div>