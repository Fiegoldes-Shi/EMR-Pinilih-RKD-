<?php
include 'connection.php';

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

$enumJTMapping = [
    "Tenaga Medis" => "Tenaga Medis - Dokter, Psikiater",
    "Tenaga Paramedis" => "Tenaga Paramedis - Perawat",
    "Tenaga Non Medis" => "Tenaga Non Medis - Psikolog, Terapis, Volunteer, dll"
];
$labelJenisTerapis = $enumJTMapping[$dataterapis["jenisTerapis"]] ?? $dataterapis["jenisTerapis"];

// Dokumen disimpan secara fisik di folder admin/uploads/
$pathForCheck = __DIR__ . "/../../admin/uploads/sertifikasi/" . basename((string) $dataterapis["dokumenSertifikasi"]);
if (!empty($dataterapis["dokumenSertifikasi"]) && file_exists($pathForCheck)) {
    $dokumenSertifikasi = '<a href="' . htmlspecialchars($baseurl . '/admin/uploads/sertifikasi/' . basename((string) $dataterapis["dokumenSertifikasi"])) . '" target="_blank">Lihat Dokumen</a>';
} else {
    $dokumenSertifikasi = '<span>Tidak ada dokumen</span>';
}

$pathForCheck2 = __DIR__ . "/../../admin/uploads/dokumenLain/" . basename((string) $dataterapis["dokumenLainnya"]);
if (!empty($dataterapis["dokumenLainnya"]) && file_exists($pathForCheck2)) {
    $dokumenLainnya = '<a href="' . htmlspecialchars($baseurl . '/admin/uploads/dokumenLain/' . basename((string) $dataterapis["dokumenLainnya"])) . '" target="_blank">Lihat Dokumen</a>';
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
                        <?= htmlspecialchars($dataterapis["idTerapis"]); ?>
                    </blockquote>
                </figure>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-condensed">
                    <tr>
                        <td width="39%">Nama Lengkap</td>
                        <td width="1%">:</td>
                        <td width="60%"><?= htmlspecialchars($dataterapis["namaTerapis"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["jenisKelamin"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["alamat"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nomor Telepon</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["noTelepon"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Terapis</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($labelJenisTerapis); ?></td>
                    </tr>
                    <tr>
                        <td>Spesialisasi</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["spesialisasi"]); ?></td>
                    </tr>
                    <tr>
                        <td>Asal Instansi</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["instansi"]); ?></td>
                    </tr>
                    <tr>
                        <td>Pendidikan Terakhir</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["pendidikanTerakhir"]); ?></td>
                    </tr>
                    <tr>
                        <td>Pendidikan Non Formal</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["pendidikanNonFormal"]); ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Mulai Aktif</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["tanggalAktif"]); ?></td>
                    </tr>
                    <tr>
                        <td>Status Pekerja</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["statusPekerja"]); ?></td>
                    </tr>
                    <tr>
                        <td>Status Terapis</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["statusTerapis"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alasan Tidak Aktif</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["alasanTidakAktif"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nomor Izin Praktek</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($dataterapis["noIzinPraktek"]); ?></td>
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
                        <td><?= htmlspecialchars($dataterapis["keterangan"]); ?></td>
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