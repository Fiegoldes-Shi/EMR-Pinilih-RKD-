<?php
include 'connection.php';

$idPeserta = $_POST['id'] ?? '';

if (empty($idPeserta)) {
    echo "ID Peserta tidak ditemukan.";
    exit;
}

$sql = "SELECT p.*, sd.namaDisabilitas
        FROM peserta p
        LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
        WHERE p.idPeserta = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idPeserta]);
$datapeserta = $stmt->fetch();

if (!$datapeserta) {
    echo "Data Peserta tidak ditemukan.";
    exit;
}
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