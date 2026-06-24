<?php
include 'connection.php';
include '../../_function_i/cView.php';
include '../../_function_i/cInsert.php'; // Needed if we use cInsert/Update classes, though we might not need them for just displaying the form.
include '../../_function_i/cUpdate.php';
include '../../_function_i/cDelete.php';
include '../../_function_i/inc_f_object.php';

$action = $_POST['action'] ?? '';
$idPasien = $_POST['id'] ?? '';

if (empty($idPasien)) {
    echo "ID Pasien tidak ditemukan.";
    exit;
}

// Fetch Patient Data
$sql = "SELECT p.*, sd.namaDisabilitas, jd.idJenisDisabilitas, jd.jenisDisabilitas, kel.idKelurahan, kel.namaKelurahan, kec.idKecamatan, kec.namaKecamatan, kk.idKotaKabupaten, kk.namaKotaKabupaten, prov.idProvinsi, prov.namaProvinsi 
        FROM pasien p
        LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
        LEFT JOIN jenis_disabilitas jd ON sd.idJenisDisabilitas = jd.idJenisDisabilitas
        LEFT JOIN kelurahan kel ON p.idKelurahanDomisili = kel.idKelurahan
        LEFT JOIN kecamatan kec ON kel.idKecamatan = kec.idKecamatan
        LEFT JOIN kotakabupaten kk ON kec.idKotaKabupaten = kk.idKotaKabupaten
        LEFT JOIN provinsi prov ON kk.idProvinsi = prov.idProvinsi
        WHERE p.idPasien = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idPasien]);
$datapasien = $stmt->fetch();

if (!$datapasien) {
    echo "Data Pasien tidak ditemukan.";
    exit;
}

// Helper function to get Enum values
function getEnumValues($pdo, $column)
{
    $stmt = $pdo->query("SHOW COLUMNS FROM pasien LIKE '$column'");
    $row = $stmt->fetch();
    $enum = [];
    if (preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enum = explode(",", str_replace("'", "", $matches[1]));
    }
    return $enum;
}

if ($action == 'view') {
    ?>
    <!-- Modal View Detil -->
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog text-start modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">DETAIL PASIEN
                            <?= $datapasien["idPasien"]; ?>
                        </blockquote>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed">
                        <tr>
                            <td width="39%">Nama Lengkap</td>
                            <td width="1%">:</td>
                            <td width="60%">
                                <?= $datapasien["namaLengkap"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Panggilan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaPanggilan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["nik"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tempat Lahir</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["tempatLahir"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["tanggalLahir"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Kelompok Usia</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["kelompokUsia"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["jenisKelamin"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Golongan Darah</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["golonganDarah"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nomor Telepon Pasien</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["noTeleponPasien"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat Lengkap (KTP)</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["alamatLengkap"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat Domisili</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["alamatDomisili"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Mulai Aktif</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["tanggalAktif"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Pasien</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["statusPasien"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Alasan Tidak Aktif</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["alasanTidakAktif"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis Disabilitas</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["jenisDisabilitas"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Subjenis Disabilitas</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaDisabilitas"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Alat Bantu</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["alatBantu"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Kebutuhan Khusus</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["kebutuhanKhusus"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Riwayat Penyakit Pribadi</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["riwayatPenyakitPribadi"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Riwayat Penyakit Keluarga</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["riwayatPenyakitKeluarga"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Alergi</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["alergi"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Trauma/Cedera</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["trauma"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Orang Tua</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaOrangTua"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>No Telepon Orang Tua</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["noTelpOrangTua"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Pendamping</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaPendamping"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>No Telepon Pendamping</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["noTelpPendamping"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nama Jalan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaJalan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Provinsi</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaProvinsi"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Kota/Kabupaten</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaKotaKabupaten"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaKecamatan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Kelurahan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["namaKelurahan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Kode Pos Domisili</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["kodePosDomisili"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>RT/RW Domisili</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["RTDomisili"]; ?>/
                                <?= $datapasien["RWDomisili"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["agama"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Suku</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["suku"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Bahasa Dikuasai</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["bahasaDikuasai"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Pendidikan Terakhir</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["pendidikan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Pekerjaan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["pekerjaan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Status Pernikahan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["statusPernikahan"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>:</td>
                            <td>
                                <?= $datapasien["keterangan"]; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                        style="border-radius: 25px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <?php
} elseif ($action == 'edit') {
    // Fetch Enums
    $enumKelompokUsia = getEnumValues($pdo, 'kelompokUsia');
    $enumJK = getEnumValues($pdo, 'jenisKelamin');
    $enumGoldar = getEnumValues($pdo, 'golonganDarah');
    $enumAgama = getEnumValues($pdo, 'agama');
    $enumPendidikan = getEnumValues($pdo, 'pendidikan');
    $enumPekerjaan = getEnumValues($pdo, 'pekerjaan');
    $enumNikah = getEnumValues($pdo, 'statusPernikahan');

    $riwayatPenyakitPribadiTerpilih = explode(", ", $datapasien["riwayatPenyakitPribadi"]);
    $riwayatPenyakitKeluargaTerpilih = explode(", ", $datapasien["riwayatPenyakitKeluarga"]);
    ?>
    <!-- Modal UPDATE -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-left">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">EDIT PASIEN</blockquote>
                        <figcaption class="blockquote-footer">
                            <?= $datapasien["idPasien"]; ?>
                        </figcaption>
                        <figcaption class="blockquote-footer">
                            <?= $datapasien["namaLengkap"]; ?>
                        </figcaption>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <FORM method="post" enctype="multipart/form-data" action="">
                    <!-- Action URL is empty to submit to current page, but we should probably handle it in incDataPasien.php via normal POST -->
                    <!-- Wait, if we use incDataPasien.php to handle the POST, we need to ensure the form submits there. -->
                    <!-- Since this modal is loaded INTO incDataPasien.php, the action="" will submit to incDataPasien.php. Perfect. -->

                    <div class="modal-body">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="idPasien" value="<?= $datapasien["idPasien"]; ?>"
                                hidden>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Nama Lengkap Pasien <span class="required">*</span></label>
                                <input class="form-control" type="text" name="namaLengkap"
                                    value="<?= $datapasien["namaLengkap"]; ?>" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Nama Panggilan <span class="required">*</span></label>
                                <input class="form-control" type="text" name="namaPanggilan"
                                    value="<?= $datapasien["namaPanggilan"]; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>NIK <span class="required">*</span></label>
                            <input class="form-control" type="number" name="nik" value="<?= $datapasien["nik"]; ?>"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Tempat Lahir <span class="required">*</span></label>
                                <input class="form-control" type="text" name="tempatLahir"
                                    value="<?= $datapasien["tempatLahir"]; ?>" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Tanggal Lahir <span class="required">*</span></label>
                                <input class="form-control" type="date" name="tanggalLahir"
                                    value="<?= $datapasien["tanggalLahir"]; ?>" required>
                            </div>
                        </div>

                        <!-- Enums -->
                        <div class="mb-3">
                            <label>Kelompok Usia <span class="required">*</span></label>
                            <select name="kelompokUsia" class="form-control" required>
                                <option value="<?= $datapasien["kelompokUsia"]; ?>">
                                    <?= $datapasien["kelompokUsia"]; ?>
                                </option>
                                <?php foreach ($enumKelompokUsia as $opt)
                                    echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                            </select>
                        </div>

                        <!-- JK & Goldar -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Jenis Kelamin <span class="required">*</span></label>
                                <select name="jenisKelamin" class="form-control" required>
                                    <option value="<?= $datapasien["jenisKelamin"]; ?>">
                                        <?= $datapasien["jenisKelamin"]; ?>
                                    </option>
                                    <?php foreach ($enumJK as $opt)
                                        echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Golongan Darah <span class="required">*</span></label>
                                <select name="golonganDarah" class="form-control" required>
                                    <option value="<?= $datapasien["golonganDarah"]; ?>">
                                        <?= $datapasien["golonganDarah"]; ?>
                                    </option>
                                    <?php foreach ($enumGoldar as $opt)
                                        echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Nomor Telepon Pasien <span class="required">*</span></label>
                            <input class="form-control" type="number" name="noTeleponPasien"
                                placeholder="Nomor telepon pasien" value="<?= $datapasien["noTeleponPasien"]; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Alamat Lengkap (sesuai KTP) <span class="required">*</span></label>
                            <textarea class="form-control" name="alamatLengkap"
                                required><?= $datapasien["alamatLengkap"]; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Alamat Lengkap Domisili <span class="required">*</span></label>
                            <textarea class="form-control" name="alamatDomisili"
                                required><?= $datapasien["alamatDomisili"]; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Mulai Aktif <span class="required">*</span></label>
                            <input class="form-control" type="date" name="tanggalAktif"
                                value="<?= $datapasien["tanggalAktif"]; ?>" required>
                        </div>

                        <!-- Status Pasien & Alasan -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Status Pasien <span class="required">*</span></label>
                                <select name="statusPasien" class="form-control statusPasien"
                                    id="statusPasien<?= $datapasien['idPasien']; ?>" required>
                                    <option value="<?= $datapasien["statusPasien"]; ?>">
                                        <?= $datapasien["statusPasien"]; ?>
                                    </option>
                                    <option value="Aktif" <?= ($datapasien["statusPasien"] == 1) ? "selected" : ""; ?>>Aktif
                                    </option>
                                    <option value="Tidak Aktif" <?= ($datapasien["statusPasien"] == 0) ? "selected" : ""; ?>>
                                        Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Alasan Tidak Aktif</label>
                                <input class="form-control alasanTidakAktif" type="text" name="alasanTidakAktif"
                                    id="alasanTidakAktif<?= $datapasien['idPasien']; ?>"
                                    value="<?= $datapasien["alasanTidakAktif"]; ?>" <?= ($datapasien["statusPasien"] == 1) ? "disabled" : ""; ?>>
                            </div>
                        </div>

                        <!-- Disabilitas -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Jenis Disabilitas</label>
                                <select id="jenisDisabilitas_edit" name="jenisDisabilitas[<?= $datapasien['idPasien']; ?>]"
                                    class="form-control jd" data-id="<?= $datapasien['idPasien']; ?>">
                                    <option value="<?= $datapasien["idJenisDisabilitas"]; ?>">
                                        <?= $datapasien["jenisDisabilitas"] ?>
                                    </option>
                                    <?php
                                    $sqld = $pdo->query("SELECT * FROM jenis_disabilitas ORDER BY idJenisDisabilitas");
                                    while ($d = $sqld->fetch()) {
                                        echo "<option value='" . $d['idJenisDisabilitas'] . "'>" . $d['jenisDisabilitas'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Sub Jenis Disabilitas <span id="reqSubDis_edit" class="required"
                                        <?= empty($datapasien['idJenisDisabilitas']) ? 'style="display:none;"' : '' ?>>*</span></label>
                                <select id="subDisabilitas_edit" name="subDisabilitas[<?= $datapasien['idPasien']; ?>]"
                                    class="form-control sd" data-id="<?= $datapasien['idPasien']; ?>"
                                    <?= empty($datapasien['idJenisDisabilitas']) ? '' : 'required' ?>>
                                    <option value="<?= $datapasien["idSubDisabilitas"]; ?>">
                                        <?= $datapasien["namaDisabilitas"] ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <script>
                            document.getElementById('jenisDisabilitas_edit').addEventListener('change', function () {
                                var val = this.value;
                                var subSelect = document.getElementById('subDisabilitas_edit');
                                var reqSpan = document.getElementById('reqSubDis_edit');

                                if (val && val.trim() !== "") {
                                    subSelect.setAttribute('required', 'required');
                                    reqSpan.style.display = 'inline-block';
                                } else {
                                    subSelect.removeAttribute('required');
                                    reqSpan.style.display = 'none';
                                }
                            });
                        </script>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Alat Bantu</label>
                                <textarea class="form-control" name="alatBantu"><?= $datapasien["alatBantu"]; ?></textarea>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Kebutuhan Khusus</label>
                                <textarea class="form-control"
                                    name="kebutuhanKhusus"><?= $datapasien["kebutuhanKhusus"]; ?></textarea>
                            </div>
                        </div>

                        <!-- Riwayat Penyakit Pribadi -->
                        <div class="mb-3">
                            <label class="form-label">Riwayat Penyakit Pribadi</label><br>
                            <div class="row">
                                <?php
                                $penyakits = ["Diabetes Mellitus", "Hipertensi", "Jantung", "Kanker", "Rhematik", "Asma", "Asam Lambung", "Lain-lain"];
                                foreach ($penyakits as $index => $p) {
                                    if ($index % 3 == 0)
                                        echo '<div class="col-md-4">';
                                    $checked = in_array($p, $riwayatPenyakitPribadiTerpilih) ? "checked" : "";
                                    echo '<div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="riwayatPenyakitPribadi[]" value="' . $p . '" id="rp_pribadi_' . $index . '" ' . $checked . '>
                                            <label class="form-check-label" for="rp_pribadi_' . $index . '">' . $p . '</label>
                                          </div>';
                                    if ($index % 3 == 2 || $index == count($penyakits) - 1)
                                        echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Riwayat Penyakit Keluarga -->
                        <div class="mb-3">
                            <label class="form-label">Riwayat Penyakit Keluarga</label><br>
                            <div class="row">
                                <?php
                                foreach ($penyakits as $index => $p) {
                                    if ($index % 3 == 0)
                                        echo '<div class="col-md-4">';
                                    $checked = in_array($p, $riwayatPenyakitKeluargaTerpilih) ? "checked" : "";
                                    echo '<div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="riwayatPenyakitKeluarga[]" value="' . $p . '" id="rp_keluarga_' . $index . '" ' . $checked . '>
                                            <label class="form-check-label" for="rp_keluarga_' . $index . '">' . $p . '</label>
                                          </div>';
                                    if ($index % 3 == 2 || $index == count($penyakits) - 1)
                                        echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Other fields -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3"><label>Alergi</label><input class="form-control" type="text"
                                    name="alergi" placeholder="Alergi pasien" value="<?= $datapasien["alergi"]; ?>"></div>
                            <div class="col-12 col-md-6 mb-3"><label>Trauma/Cedera</label><input class="form-control"
                                    type="text" name="trauma" placeholder="Trauma/Cedera pasien"
                                    value="<?= $datapasien["trauma"]; ?>"></div>
                            <div class="col-12 col-md-6 mb-3"><label>Nama Orang Tua</label><input class="form-control"
                                    type="text" name="namaOrtu" placeholder="Nama Orang Tua"
                                    value="<?= $datapasien["namaOrangTua"]; ?>"></div>
                            <div class="col-12 col-md-6 mb-3"><label>No Telepon Orang Tua</label><input class="form-control"
                                    type="number" name="noTelpOrtu" placeholder="Nomor Telepon Orang Tua"
                                    value="<?= $datapasien["noTelpOrangTua"]; ?>"></div>
                            <div class="col-12 col-md-6 mb-3"><label>Nama Pendamping</label><input class="form-control"
                                    type="text" name="namaPendamping" placeholder="Nama Pendamping"
                                    value="<?= $datapasien["namaPendamping"]; ?>"></div>
                            <div class="col-12 col-md-6 mb-3"><label>No Telepon Pendamping</label><input
                                    class="form-control" type="number" name="noTelpPendamping"
                                    placeholder="Nomor Telepon Pendamping" value="<?= $datapasien["noTelpPendamping"]; ?>">
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body border border-2">
                                <p><strong>Alamat Domisili Pasien</strong></p>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3"><label>Nama Jalan</label><input class="form-control"
                                            type="text" name="namaJalan" placeholder="Nama jalan"
                                            value="<?= $datapasien["namaJalan"]; ?>"></div>

                                    <!-- Wilayah dropdowns -->
                                    <div class="col-12 col-md-6 mb-3"><label>Provinsi</label>
                                        <select name="provinsi[<?= $datapasien['idPasien']; ?>]" class="form-control prov"
                                            data-id="<?= $datapasien['idPasien']; ?>">
                                            <option value="<?= $datapasien['idProvinsi']; ?>">
                                                <?= !empty($datapasien['namaProvinsi']) ? $datapasien['namaProvinsi'] : '- pilihan -'; ?>
                                            </option>
                                            <?php
                                            $sqlp = $pdo->query("SELECT * FROM provinsi ORDER BY namaProvinsi");
                                            while ($rowp = $sqlp->fetch())
                                                echo "<option value='" . $rowp['idProvinsi'] . "'>" . $rowp['namaProvinsi'] . "</option>";
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3"><label>Kota/Kabupaten</label>
                                        <select name="kota[<?= $datapasien['idPasien']; ?>]" class="form-control kotaKab"
                                            data-id="<?= $datapasien['idPasien']; ?>">
                                            <option value="<?= $datapasien['idKotaKabupaten']; ?>">
                                                <?= !empty($datapasien['namaKotaKabupaten']) ? $datapasien['namaKotaKabupaten'] : '- pilihan -'; ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3"><label>Kecamatan</label>
                                        <select name="kecamatan[<?= $datapasien['idPasien']; ?>]" class="form-control kec"
                                            data-id="<?= $datapasien['idPasien']; ?>">
                                            <option value="<?= $datapasien['idKecamatan']; ?>">
                                                <?= !empty($datapasien['namaKecamatan']) ? $datapasien['namaKecamatan'] : '- pilihan -'; ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3"><label>Kelurahan</label>
                                        <select name="kelurahan[<?= $datapasien['idPasien']; ?>]" class="form-control kel"
                                            data-id="<?= $datapasien['idPasien']; ?>">
                                            <option value="<?= $datapasien['idKelurahan']; ?>">
                                                <?= !empty($datapasien['namaKelurahan']) ? $datapasien['namaKelurahan'] : '- pilihan -'; ?>
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 mb-3"><label>Kode Pos Domisili</label><input
                                            class="form-control" type="number" name="kodePosDomisili"
                                            value="<?= $datapasien["kodePosDomisili"]; ?>">
                                    </div>
                                    <div class="col-12 col-md-6 mb-3"><label>RT Domisili</label><input class="form-control"
                                            type="number" name="RTDomisili" value="<?= $datapasien["RTDomisili"]; ?>"></div>
                                    <div class="col-12 col-md-6 mb-3"><label>RW Domisili</label><input class="form-control"
                                            type="number" name="RWDomisili" value="<?= $datapasien["RWDomisili"]; ?>"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Enums again -->
                        <div class="row">
                            <div class="col-12 col-md-6 my-3"><label>Agama</label>
                                <select name="agama" class="form-control">
                                    <option value="<?= $datapasien["agama"]; ?>">
                                        <?= !empty($datapasien["agama"]) ? $datapasien["agama"] : '- pilihan -'; ?>
                                    </option>
                                    <?php foreach ($enumAgama as $opt)
                                        echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 my-3"><label>Suku</label><input class="form-control" type="text"
                                    name="suku" value="<?= $datapasien["suku"]; ?>"></div>
                        </div>
                        <div class="mb-3"><label>Bahasa Dikuasai</label><input class="form-control" type="text"
                                name="bahasaDikuasai" value="<?= $datapasien["bahasaDikuasai"]; ?>"></div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3"><label>Pendidikan Terakhir</label>
                                <select name="pendidikan" class="form-control">
                                    <option value="<?= $datapasien["pendidikan"]; ?>">
                                        <?= !empty($datapasien["pendidikan"]) ? $datapasien["pendidikan"] : '- pilihan -'; ?>
                                    </option>
                                    <?php foreach ($enumPendidikan as $opt)
                                        echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3"><label>Pekerjaan</label>
                                <select name="pekerjaan" class="form-control">
                                    <option value="<?= $datapasien["pekerjaan"]; ?>">
                                        <?= !empty($datapasien["pekerjaan"]) ? $datapasien["pekerjaan"] : '- pilihan -'; ?>
                                    </option>
                                    <?php foreach ($enumPekerjaan as $opt)
                                        echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3"><label>Status Pernikahan</label>
                            <select name="statusPernikahan" class="form-control">
                                <option value="<?= $datapasien["statusPernikahan"]; ?>">
                                    <?= !empty($datapasien["statusPernikahan"]) ? $datapasien["statusPernikahan"] : '- pilihan -'; ?>
                                </option>
                                <?php foreach ($enumNikah as $opt)
                                    echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label>Keterangan</label><textarea class="form-control"
                                name="keterangan"><?= $datapasien["keterangan"]; ?></textarea></div>

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

    <!-- Script to handle Status Pasien change -->
    <script>
        $(document).ready(function () {
            // We need to re-bind this since it's loaded dynamically
            $(".statusPasien").on("change", function () {
                let id = $(this).attr("id").replace("statusPasien", "");
                let alasanField = $("#alasanTidakAktif" + id);
                if ($(this).val() == "Tidak Aktif" || $(this).val() == "0") {
                    alasanField.prop("disabled", false);
                } else {
                    alasanField.prop("disabled", true);
                    alasanField.val("");
                }
            });
        });
    </script>
    <?php
} elseif ($action == 'delete') {
    ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="" method="post">
                    <input type="hidden" name="hiddendeletevalue[0][field]" value="idPasien">
                    <input type="hidden" name="hiddendeletevalue[0][value]" value="<?= $datapasien["idPasien"]; ?>">
                    <input type="hidden" name="hiddendeletevalue[0][table]" value="pasien">

                    <div class="modal-header">
                        <figure>
                            <blockquote class="blockquote">
                                <h5 class="modal-title">HAPUS</h5>
                            </blockquote>
                            <figcaption class="blockquote-footer">Pasien
                                <?= $datapasien["namaLengkap"] ?>
                            </figcaption>
                        </figure>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus pasien <strong>
                                <?= $datapasien["namaLengkap"] ?>
                            </strong>?</p>
                        <div class="form-group">
                            <label>Masukkan Password Ketua:</label>
                            <input type="password" name="password_verif" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="btnhapus" class="btn btn-danger btn-sm"
                            style="border-radius: 25px;">HAPUS</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                            style="border-radius: 25px;">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>