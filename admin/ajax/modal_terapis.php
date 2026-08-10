<?php
include 'connection.php';
include '../../_function_i/cView.php';

// === BASEURL DINAMIS (tanpa hardcode localhost) ===
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/\\');
$baseurl = $protocol . $host . $path;

$action = $_POST['action'] ?? '';
$idTerapis = $_POST['id'] ?? '';

if (empty($idTerapis)) {
    echo "ID Terapis tidak ditemukan.";
    exit;
}

$sql = "SELECT * FROM terapis WHERE idTerapis = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idTerapis]);
$dataterapis = $stmt->fetch();

if (!$dataterapis) {
    echo "Data Terapis tidak ditemukan.";
    exit;
}

function getEnumValuesTerapis($pdo, $column)
{
    $stmt = $pdo->query("SHOW COLUMNS FROM terapis LIKE '$column'");
    $row = $stmt->fetch();
    $enum = [];
    if ($row && preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
        $enum = explode(",", str_replace("'", "", $matches[1]));
    }
    return $enum;
}

$enumJTMapping = [
    "Tenaga Medis" => "Tenaga Medis - Dokter, Psikiater",
    "Tenaga Paramedis" => "Tenaga Paramedis - Perawat",
    "Tenaga Non Medis" => "Tenaga Non Medis - Psikolog, Terapis, Volunteer, dll"
];

if ($action == 'view') {
    $labelJenisTerapis = $enumJTMapping[$dataterapis["jenisTerapis"]] ?? $dataterapis["jenisTerapis"];

    $dokumenPath = "uploads/sertifikasi/" . basename((string) $dataterapis["dokumenSertifikasi"]);
    if (!empty($dataterapis["dokumenSertifikasi"]) && file_exists(__DIR__ . '/../' . $dokumenPath)) {
        $dokumenSertifikasi = '<a href="' . $baseurl . '/admin/' . htmlspecialchars($dokumenPath) . '" target="_new">Lihat Dokumen</a>';
    } else {
        $dokumenSertifikasi = '<span>Tidak ada dokumen</span>';
    }

    $dokumenPath2 = "uploads/dokumenLain/" . basename((string) $dataterapis["dokumenLainnya"]);
    if (!empty($dataterapis["dokumenLainnya"]) && file_exists(__DIR__ . '/../' . $dokumenPath2)) {
        $dokumenLainnya = '<a href="' . $baseurl . '/admin/' . htmlspecialchars($dokumenPath2) . '" target="_new">Lihat Dokumen</a>';
    } else {
        $dokumenLainnya = '<span>Tidak ada dokumen</span>';
    }
    ?>
    <!-- Modal View Detil -->
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog text-start modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">DETAIL TERAPIS
                            <?= $dataterapis["idTerapis"]; ?>
                        </blockquote>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed">
                        <tr>
                            <td width="39%">Nama Lengkap</td>
                            <td width="1%">:</td>
                            <td width="60%"><?= $dataterapis["namaTerapis"]; ?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td><?= $dataterapis["jenisKelamin"]; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td><?= $dataterapis["alamat"]; ?></td>
                        </tr>
                        <tr>
                            <td>Nomor Telepon</td>
                            <td>:</td>
                            <td><?= $dataterapis["noTelepon"]; ?></td>
                        </tr>
                        <tr>
                            <td>Jenis Terapis</td>
                            <td>:</td>
                            <td><?= $labelJenisTerapis; ?></td>
                        </tr>
                        <tr>
                            <td>Spesialisasi</td>
                            <td>:</td>
                            <td><?= $dataterapis["spesialisasi"]; ?></td>
                        </tr>
                        <tr>
                            <td>Asal Instansi</td>
                            <td>:</td>
                            <td><?= $dataterapis["instansi"]; ?></td>
                        </tr>
                        <tr>
                            <td>Pendidikan Terakhir</td>
                            <td>:</td>
                            <td><?= $dataterapis["pendidikanTerakhir"]; ?></td>
                        </tr>
                        <tr>
                            <td>Pendidikan Non Formal</td>
                            <td>:</td>
                            <td><?= $dataterapis["pendidikanNonFormal"]; ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Mulai Aktif</td>
                            <td>:</td>
                            <td><?= $dataterapis["tanggalAktif"]; ?></td>
                        </tr>
                        <tr>
                            <td>Status Pekerja</td>
                            <td>:</td>
                            <td><?= $dataterapis["statusPekerja"]; ?></td>
                        </tr>
                        <tr>
                            <td>Status Terapis</td>
                            <td>:</td>
                            <td><?= $dataterapis["statusTerapis"]; ?></td>
                        </tr>
                        <tr>
                            <td>Alasan Tidak Aktif</td>
                            <td>:</td>
                            <td><?= $dataterapis["alasanTidakAktif"]; ?></td>
                        </tr>
                        <tr>
                            <td>Nomor Izin Praktek</td>
                            <td>:</td>
                            <td><?= $dataterapis["noIzinPraktek"]; ?></td>
                        </tr>
                        <tr>
                            <td>Dokumen Sertifikasi</td>
                            <td>:</td>
                            <td><?= $dokumenSertifikasi; ?></td>
                        </tr>
                        <tr>
                            <td>Dokumen Lainnya</td>
                            <td>:</td>
                            <td><?= $dokumenLainnya; ?></td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>:</td>
                            <td><?= $dataterapis["keterangan"]; ?></td>
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
    $enumJK = getEnumValuesTerapis($pdo, 'jenisKelamin');
    $enumJT = getEnumValuesTerapis($pdo, 'jenisTerapis');
    $enumJTForm = $enumJTMapping;
    $enumPT = getEnumValuesTerapis($pdo, 'pendidikanTerakhir');
    $enumstatusPekerja = getEnumValuesTerapis($pdo, 'statusPekerja');

    $dokSertifikasiPath = "uploads/sertifikasi/" . basename((string) $dataterapis["dokumenSertifikasi"]);
    $dokLainnyaPath = "uploads/dokumenLain/" . basename((string) $dataterapis["dokumenLainnya"]);
    ?>
    <!-- Modal UPDATE -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-left">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">EDIT DATA TENAGA MEDIS / PARAMEDIS / NON MEDIS</blockquote>
                        <figcaption class="blockquote-footer"><?= $dataterapis["idTerapis"]; ?></figcaption>
                        <figcaption class="blockquote-footer"><?= $dataterapis["namaTerapis"]; ?></figcaption>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="idTerapis" value="<?= $dataterapis["idTerapis"]; ?>" hidden>
                        </div>
                        <div class="mb-3">
                            <label>Nama Lengkap <span class="required">*</span></label>
                            <input class="form-control" type="text" name="namaTerapis" value="<?= $dataterapis["namaTerapis"]; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Kelamin <span class="required">*</span></label>
                            <select name="jenisKelamin" class="form-control" required>
                                <option value="<?= $dataterapis["jenisKelamin"]; ?>"><?= $dataterapis["jenisKelamin"]; ?></option>
                                <?php foreach ($enumJK as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Alamat <span class="required">*</span></label>
                            <textarea class="form-control" name="alamat" required><?= $dataterapis["alamat"]; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Nomor Telepon <span class="required">*</span></label>
                            <input class="form-control" type="text" name="noTelepon" value="<?= $dataterapis["noTelepon"]; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Terapis <span class="required">*</span></label>
                            <select name="jenisTerapis" class="form-control" required>
                                <option value="<?= $dataterapis["jenisTerapis"]; ?>"><?= $dataterapis["jenisTerapis"]; ?></option>
                                <?php foreach ($enumJTForm as $key => $label) echo '<option value="' . $key . '">' . $label . '</option>'; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Spesialisasi <span class="required">*</span></label>
                                <input class="form-control" type="text" name="spesialisasi" value="<?= $dataterapis["spesialisasi"]; ?>" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Asal Instansi <span class="required">*</span></label>
                                <input class="form-control" type="text" name="instansi" value="<?= $dataterapis["instansi"]; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Pendidikan Terakhir <span class="required">*</span></label>
                                <select name="pendidikanTerakhir" class="form-control" required>
                                    <option value="<?= $dataterapis["pendidikanTerakhir"]; ?>"><?= $dataterapis["pendidikanTerakhir"]; ?></option>
                                    <?php foreach ($enumPT as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Pendidikan Non Formal</label>
                                <input class="form-control" type="text" name="pendidikanNonFormal" value="<?= $dataterapis["pendidikanNonFormal"]; ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Aktif <span class="required">*</span></label>
                            <input class="form-control" type="date" name="tanggalAktif" value="<?= $dataterapis["tanggalAktif"]; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Status Terapis <span class="required">*</span></label>
                                <select name="statusTerapis" class="form-control statusTerapis" id="statusTerapis<?= $dataterapis['idTerapis']; ?>" required>
                                    <option value="<?= $dataterapis["statusTerapis"]; ?>"><?= $dataterapis["statusTerapis"]; ?></option>
                                    <option value="Aktif" <?= ($dataterapis["statusTerapis"] == 1) ? "selected" : ""; ?>>Aktif</option>
                                    <option value="Tidak Aktif" <?= ($dataterapis["statusTerapis"] == 0) ? "selected" : ""; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Alasan Tidak Aktif</label>
                                <input class="form-control alasanTidakAktif" type="text" name="alasanTidakAktif"
                                    id="alasanTidakAktif<?= $dataterapis['idTerapis']; ?>"
                                    value="<?= $dataterapis["alasanTidakAktif"]; ?>"
                                    <?= ($dataterapis["statusTerapis"] == 1) ? "disabled" : ""; ?>>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Status Pekerja</label>
                            <select name="statusPekerja" class="form-control">
                                <option value="<?= $dataterapis["statusPekerja"]; ?>"><?= $dataterapis["statusPekerja"]; ?></option>
                                <?php foreach ($enumstatusPekerja as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nomor Izin Praktek</label>
                            <input class="form-control" type="text" name="noIzinPraktek" value="<?= $dataterapis["noIzinPraktek"]; ?>">
                        </div>
                        <div class="mb-3">
                            <label>Dokumen Sertifikasi</label>
                            <?php if (!empty($dataterapis["dokumenSertifikasi"]) && file_exists(__DIR__ . '/../' . $dokSertifikasiPath)): ?>
                                <div class="mb-2"><small style="font-weight: 500;">File tersimpan: <a href="<?= $baseurl . '/admin/' . htmlspecialchars($dokSertifikasiPath); ?>" target="_blank" style="text-decoration: underline; color: #0d6efd;">Lihat Dokumen</a></small></div>
                            <?php else: ?>
                                <div class="mb-2"><small class="text-muted"><i class="fa-solid fa-circle-xmark"></i> Belum ada dokumen yang diunggah</small></div>
                            <?php endif; ?>
                            <input class="form-control" type="file" name="dokumenSertifikasi">
                            <small class="text-muted" style="font-size: 0.8em;">*Biarkan kosong apabila tidak ingin mengubah dokumen ini.</small>
                        </div>
                        <div class="mb-3">
                            <label>Dokumen Lainnya</label>
                            <?php if (!empty($dataterapis["dokumenLainnya"]) && file_exists(__DIR__ . '/../' . $dokLainnyaPath)): ?>
                                <div class="mb-2"><small style="font-weight: 500;">File tersimpan: <a href="<?= $baseurl . '/admin/' . htmlspecialchars($dokLainnyaPath); ?>" target="_blank" style="text-decoration: underline; color: #0d6efd;">Lihat Dokumen</a></small></div>
                            <?php else: ?>
                                <div class="mb-2"><small class="text-muted"><i class="fa-solid fa-circle-xmark"></i> Belum ada dokumen yang diunggah</small></div>
                            <?php endif; ?>
                            <input class="form-control" type="file" name="dokumenLainnya">
                            <small class="text-muted" style="font-size: 0.8em;">*Biarkan kosong apabila tidak ingin mengubah dokumen ini.</small>
                        </div>
                        <div class="mb-3">
                            <label>Keterangan</label>
                            <textarea class="form-control" name="keterangan"><?= $dataterapis["keterangan"]; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="editbtn" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px;">SIMPAN</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(".statusTerapis").on("change", function () {
                let id = $(this).attr("id").replace("statusTerapis", "");
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
                    <input type="hidden" name="hiddendeletevalue[0][field]" value="idTerapis">
                    <input type="hidden" name="hiddendeletevalue[0][value]" value="<?= $dataterapis["idTerapis"]; ?>">
                    <input type="hidden" name="hiddendeletevalue[0][table]" value="terapis">

                    <div class="modal-header">
                        <figure>
                            <blockquote class="blockquote">
                                <h5 class="modal-title">HAPUS</h5>
                            </blockquote>
                            <figcaption class="blockquote-footer">Terapis <?= htmlspecialchars($dataterapis["namaTerapis"]) ?></figcaption>
                        </figure>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus terapis <strong><?= $dataterapis["namaTerapis"] ?></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="btnhapus" class="btn btn-danger btn-sm" style="border-radius: 25px;">HAPUS</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>