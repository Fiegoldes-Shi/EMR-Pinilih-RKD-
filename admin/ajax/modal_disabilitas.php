<?php
include 'connection.php';

$action = $_POST['action'] ?? '';
$idSubDisabilitas = $_POST['id'] ?? '';

if (empty($idSubDisabilitas)) {
    echo "ID Disabilitas tidak ditemukan.";
    exit;
}

$sql = "SELECT sd.idSubDisabilitas, jd.idJenisDisabilitas, jd.jenisDisabilitas, sd.namaDisabilitas
        FROM sub_disabilitas sd
        JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas
        WHERE sd.idSubDisabilitas = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idSubDisabilitas]);
$datadisabilitas = $stmt->fetch();

if (!$datadisabilitas) {
    echo "Data Disabilitas tidak ditemukan.";
    exit;
}

if ($action == 'view') {
    ?>
    <!-- Modal View Detil -->
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog text-start modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">DETIL#DISABILITAS</blockquote>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed">
                        <tr>
                            <td width="39%">ID</td>
                            <td width="1%">:</td>
                            <td width="60%"><?= $datadisabilitas["idSubDisabilitas"]; ?></td>
                        </tr>
                        <tr>
                            <td>Jenis Disabilitas</td>
                            <td>:</td>
                            <td><?= $datadisabilitas["jenisDisabilitas"]; ?></td>
                        </tr>
                        <tr>
                            <td>Nama Disabilitas</td>
                            <td>:</td>
                            <td><?= $datadisabilitas["namaDisabilitas"]; ?></td>
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
    $sqlJD = $pdo->query("SELECT * FROM jenis_disabilitas ORDER BY idJenisDisabilitas");
    $resultJD = $sqlJD->fetchAll();
    ?>
    <!-- Modal UPDATE -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-left">
                <div class="modal-header">
                    <figure class="text-left">
                        <blockquote class="blockquote">EDIT DISABILITAS</blockquote>
                        <figcaption class="blockquote-footer"><?= $datadisabilitas["idSubDisabilitas"]; ?></figcaption>
                        <figcaption class="blockquote-footer"><?= $datadisabilitas["namaDisabilitas"]; ?></figcaption>
                    </figure>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="idSubDisabilitas" value="<?= $datadisabilitas["idSubDisabilitas"]; ?>" hidden>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Disabilitas <span class="required">*</span></label>
                            <select name="idJenisDisabilitas" class="form-control" required>
                                <?php foreach ($resultJD as $row): ?>
                                    <option value="<?= $row['idJenisDisabilitas'] ?>" <?= ($row['idJenisDisabilitas'] == $datadisabilitas['idJenisDisabilitas']) ? 'selected' : ''; ?>>
                                        <?= $row['jenisDisabilitas'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nama Disabilitas <span class="required">*</span></label>
                            <input class="form-control" type="text" name="namaDisabilitas" value="<?= $datadisabilitas["namaDisabilitas"]; ?>" required>
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
    <?php
} elseif ($action == 'delete') {
    ?>
    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="" method="post">
                    <input type="hidden" name="hiddendeletevalue[0][field]" value="idSubDisabilitas">
                    <input type="hidden" name="hiddendeletevalue[0][value]" value="<?= $datadisabilitas["idSubDisabilitas"]; ?>">
                    <input type="hidden" name="hiddendeletevalue[0][table]" value="sub_disabilitas">

                    <div class="modal-header">
                        <figure>
                            <blockquote class="blockquote">
                                <h5 class="modal-title">HAPUS</h5>
                            </blockquote>
                            <figcaption class="blockquote-footer">Disabilitas <?= $datadisabilitas["jenisDisabilitas"] ?> - <?= $datadisabilitas["namaDisabilitas"] ?></figcaption>
                        </figure>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus <strong><?= $datadisabilitas["jenisDisabilitas"] ?> - <?= $datadisabilitas["namaDisabilitas"] ?></strong>?</p>
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