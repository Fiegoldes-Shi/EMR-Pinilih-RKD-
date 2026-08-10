<?php
include 'connection.php';

$action = $_POST['action'] ?? '';
$idPeserta = $_POST['id'] ?? '';

if (empty($idPeserta)) {
    echo "ID Peserta tidak ditemukan.";
    exit;
}

$sql = "SELECT p.*, sd.namaDisabilitas, jd.idJenisDisabilitas, jd.jenisDisabilitas
        FROM peserta p
        LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
        LEFT JOIN jenis_disabilitas jd ON sd.idJenisDisabilitas = jd.idJenisDisabilitas
        WHERE p.idPeserta = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idPeserta]);
$datapeserta = $stmt->fetch();

if (!$datapeserta) {
    echo "Data Peserta tidak ditemukan.";
    exit;
}

function getEnumValuesPeserta($pdo, $column)
{
    $stmt = $pdo->query("SHOW COLUMNS FROM peserta LIKE '$column'");
    $row = $stmt->fetch();
    $enum = [];
    if ($row && preg_match("/^enum\((.*)\)$/", $row['Type'], $matches)) {
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
                        <blockquote class="blockquote">DETAIL PESERTA
                            <?= $datapeserta["idPeserta"]; ?>
                        </blockquote>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed">
                        <tr>
                            <td width="39%">Nama Lengkap</td>
                            <td width="1%">:</td>
                            <td width="60%"><?= $datapeserta["nama"]; ?></td>
                        </tr>
                        <tr>
                            <td>Asal Lembaga</td>
                            <td>:</td>
                            <td><?= $datapeserta["asalLembaga"]; ?></td>
                        </tr>
                        <tr>
                            <td>Usia</td>
                            <td>:</td>
                            <td><?= $datapeserta["usia"]; ?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td><?= $datapeserta["jenisKelamin"]; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td><?= $datapeserta["alamat"]; ?></td>
                        </tr>
                        <tr>
                            <td>Disabilitas</td>
                            <td>:</td>
                            <td><?= $datapeserta["namaDisabilitas"]; ?></td>
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
    $enumJK = getEnumValuesPeserta($pdo, 'jenisKelamin');
    $enumKelompokUsia = getEnumValuesPeserta($pdo, 'usia');
    $sqlJD = $pdo->query("SELECT * FROM jenis_disabilitas ORDER BY idJenisDisabilitas");
    $resultJD = $sqlJD->fetchAll();
    ?>
    <!-- Modal UPDATE -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-left">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">EDIT PESERTA</blockquote>
                        <figcaption class="blockquote-footer"><?= $datapeserta["idPeserta"]; ?></figcaption>
                        <figcaption class="blockquote-footer"><?= $datapeserta["nama"]; ?> - <?= $datapeserta["asalLembaga"]; ?></figcaption>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="idPeserta" value="<?= $datapeserta["idPeserta"]; ?>" hidden>
                        </div>
                        <div class="mb-3">
                            <label>Nama Lengkap Peserta <span class="required">*</span></label>
                            <input class="form-control" type="text" name="nama" value="<?= $datapeserta["nama"]; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Asal Lembaga</label>
                            <input class="form-control" type="text" name="asalLembaga" value="<?= $datapeserta["asalLembaga"]; ?>">
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Jenis Kelamin <span class="required">*</span></label>
                                <select name="jenisKelamin" class="form-control" required>
                                    <option value="<?= $datapeserta["jenisKelamin"]; ?>"><?= $datapeserta["jenisKelamin"]; ?></option>
                                    <?php foreach ($enumJK as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Usia <span class="required">*</span></label>
                                <select name="usia" class="form-control" required>
                                    <option value="<?= $datapeserta["usia"]; ?>"><?= $datapeserta["usia"]; ?></option>
                                    <?php foreach ($enumKelompokUsia as $opt) echo '<option value="' . trim($opt) . '">' . trim($opt) . '</option>'; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Alamat Peserta</label>
                            <input class="form-control" type="text" name="alamat" value="<?= $datapeserta["alamat"]; ?>">
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label>Jenis Disabilitas</label>
                                <select id="jenisDisabilitas_editPeserta" class="form-control jd-peserta-edit">
                                    <option value="<?= $datapeserta["idJenisDisabilitas"]; ?>"><?= $datapeserta["jenisDisabilitas"] ?></option>
                                    <?php foreach ($resultJD as $row): ?>
                                        <option value="<?= $row['idJenisDisabilitas'] ?>"><?= $row['jenisDisabilitas'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label>Sub Jenis Disabilitas <span id="reqSubDisPesertaEdit" class="required" <?= empty($datapeserta['idJenisDisabilitas']) ? 'style="display:none;"' : '' ?>>*</span></label>
                                <select name="subDisabilitas" id="subDisabilitas_editPeserta" class="form-control sd-peserta-edit" <?= empty($datapeserta['idJenisDisabilitas']) ? '' : 'required' ?>>
                                    <option value="<?= $datapeserta["idSubDisabilitas"]; ?>"><?= $datapeserta["namaDisabilitas"] ?></option>
                                </select>
                            </div>
                        </div>
                        <script>
                            document.getElementById('jenisDisabilitas_editPeserta').addEventListener('change', function () {
                                var val = this.value;
                                var subSelect = document.getElementById('subDisabilitas_editPeserta');
                                var reqSpan = document.getElementById('reqSubDisPesertaEdit');

                                if (val && val.trim() !== "") {
                                    subSelect.setAttribute('required', 'required');
                                    reqSpan.style.display = 'inline-block';
                                } else {
                                    subSelect.removeAttribute('required');
                                    reqSpan.style.display = 'none';
                                }

                                $.ajax({
                                    url: 'optionDisabilitas2.php',
                                    type: 'POST',
                                    data: { idJenis: val },
                                    dataType: 'json',
                                    success: function (response) {
                                        $(subSelect).html(response.data_disabilitas);
                                    }
                                });
                            });
                        </script>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="editbtn" value="true" class="btn btn-primary btn-sm" style="border-radius: 25px;">SIMPAN</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 25px;">TUTUP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
} elseif ($action == 'delete') {
    ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="" method="post">
                    <input type="hidden" name="hiddendeletevalue[0][field]" value="idPeserta">
                    <input type="hidden" name="hiddendeletevalue[0][value]" value="<?= $datapeserta["idPeserta"]; ?>">
                    <input type="hidden" name="hiddendeletevalue[0][table]" value="peserta">

                    <div class="modal-header">
                        <figure>
                            <blockquote class="blockquote">
                                <h5 class="modal-title">HAPUS</h5>
                            </blockquote>
                            <figcaption class="blockquote-footer">Peserta <?= htmlspecialchars($datapeserta["nama"]) ?> - <?= htmlspecialchars($datapeserta["asalLembaga"] ?? '') ?></figcaption>
                        </figure>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus peserta <strong><?= $datapeserta["nama"] ?></strong>?</p>
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