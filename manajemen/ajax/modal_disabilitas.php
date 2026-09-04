<?php
include 'connection.php';

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
                        <td width="60%"><?= htmlspecialchars($datadisabilitas["idSubDisabilitas"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Disabilitas</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datadisabilitas["jenisDisabilitas"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nama Disabilitas</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datadisabilitas["namaDisabilitas"]); ?></td>
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