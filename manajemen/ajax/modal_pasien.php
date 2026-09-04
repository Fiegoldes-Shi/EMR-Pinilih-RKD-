<?php
include 'connection.php';

$idPasien = $_POST['id'] ?? '';

if (empty($idPasien)) {
    echo "ID Pasien tidak ditemukan.";
    exit;
}

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
?>
<!-- Modal View Detil -->
<div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog text-start modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <figure class="text-left">
                    <blockquote class="blockquote">DETAIL PASIEN
                        <?= htmlspecialchars($datapasien["idPasien"]); ?>
                    </blockquote>
                </figure>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-condensed">
                    <tr>
                        <td width="39%">Nama Lengkap</td>
                        <td width="1%">:</td>
                        <td width="60%"><?= htmlspecialchars($datapasien["namaLengkap"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nama Panggilan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaPanggilan"]); ?></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["nik"]); ?></td>
                    </tr>
                    <tr>
                        <td>Tempat Lahir</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["tempatLahir"]); ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["tanggalLahir"]); ?></td>
                    </tr>
                    <tr>
                        <td>Kelompok Usia</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["kelompokUsia"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["jenisKelamin"]); ?></td>
                    </tr>
                    <tr>
                        <td>Golongan Darah</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["golonganDarah"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nomor Telepon Pasien</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["noTeleponPasien"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alamat Lengkap (KTP)</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["alamatLengkap"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alamat Domisili</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["alamatDomisili"]); ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Mulai Aktif</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["tanggalAktif"]); ?></td>
                    </tr>
                    <tr>
                        <td>Status Pasien</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["statusPasien"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alasan Tidak Aktif</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["alasanTidakAktif"]); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Disabilitas</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["jenisDisabilitas"]); ?></td>
                    </tr>
                    <tr>
                        <td>Subjenis Disabilitas</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaDisabilitas"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alat Bantu</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["alatBantu"]); ?></td>
                    </tr>
                    <tr>
                        <td>Kebutuhan Khusus</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["kebutuhanKhusus"]); ?></td>
                    </tr>
                    <tr>
                        <td>Riwayat Penyakit Pribadi</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["riwayatPenyakitPribadi"]); ?></td>
                    </tr>
                    <tr>
                        <td>Riwayat Penyakit Keluarga</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["riwayatPenyakitKeluarga"]); ?></td>
                    </tr>
                    <tr>
                        <td>Alergi</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["alergi"]); ?></td>
                    </tr>
                    <tr>
                        <td>Trauma/Cedera</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["trauma"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nama Orang Tua</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaOrangTua"]); ?></td>
                    </tr>
                    <tr>
                        <td>No Telepon Orang Tua</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["noTelpOrangTua"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nama Pendamping</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaPendamping"]); ?></td>
                    </tr>
                    <tr>
                        <td>No Telepon Pendamping</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["noTelpPendamping"]); ?></td>
                    </tr>
                    <tr>
                        <td>Nama Jalan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaJalan"]); ?></td>
                    </tr>
                    <tr>
                        <td>Provinsi</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaProvinsi"]); ?></td>
                    </tr>
                    <tr>
                        <td>Kota/Kabupaten</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaKotaKabupaten"]); ?></td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaKecamatan"]); ?></td>
                    </tr>
                    <tr>
                        <td>Kelurahan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["namaKelurahan"]); ?></td>
                    </tr>
                    <tr>
                        <td>Kode Pos Domisili</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["kodePosDomisili"]); ?></td>
                    </tr>
                    <tr>
                        <td>RT/RW Domisili</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["RTDomisili"]); ?>/<?= htmlspecialchars($datapasien["RWDomisili"]); ?></td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["agama"]); ?></td>
                    </tr>
                    <tr>
                        <td>Suku</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["suku"]); ?></td>
                    </tr>
                    <tr>
                        <td>Bahasa Dikuasai</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["bahasaDikuasai"]); ?></td>
                    </tr>
                    <tr>
                        <td>Pendidikan Terakhir</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["pendidikan"]); ?></td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["pekerjaan"]); ?></td>
                    </tr>
                    <tr>
                        <td>Status Pernikahan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["statusPernikahan"]); ?></td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($datapasien["keterangan"]); ?></td>
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