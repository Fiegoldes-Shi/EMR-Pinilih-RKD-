<?php
include 'connection.php';

$idUser = $_POST['id'] ?? '';

if (empty($idUser)) {
    echo "ID Pengguna tidak ditemukan.";
    exit;
}

$sql = "SELECT * FROM user WHERE idUser = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idUser]);
$datapengguna = $stmt->fetch();

if (!$datapengguna) {
    echo "Data Pengguna tidak ditemukan.";
    exit;
}

$enumSPMapping = [
    "1" => "1 - Admin/Operator",
    "2" => "2 - Manajemen RKD",
    "3" => "3 - Terapis"
];
$roleLabel = $enumSPMapping[$datapengguna["role"]] ?? $datapengguna["role"];
?>
<!-- Modal View Detil -->
<div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog text-start modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <figure class="text-left">
                    <blockquote class="blockquote">DETAIL PENGGUNA
                        <?= htmlspecialchars($datapengguna["idUser"]); ?>
                    </blockquote>
                </figure>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-condensed">
                    <tr>
                        <td width="39%">Nama Lengkap</td>
                        <td width="1%">:</td>
                        <td width="60%"><?= htmlspecialchars($datapengguna["nama"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["jenisKelamin"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["jbtn"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["alamat"]); ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Mulai Aktif</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["tglMulaiAktif"]); ?></td>
                    </tr>
                    <tr>
                        <td>No Telepon</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["noTelp"]); ?></td>
                    </tr>
                    <tr>
                        <td>Status Pengguna</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($roleLabel); ?></td>
                    </tr>
                    <tr>
                        <td>Status Pekerja</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["statusPekerja"]); ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["username"]); ?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["email"]); ?></td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapengguna["keterangan"]); ?></td>
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