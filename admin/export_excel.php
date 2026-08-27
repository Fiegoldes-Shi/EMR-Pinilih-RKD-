<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
date_default_timezone_set('Asia/Jakarta');

include_once("../_function_i/cView.php");
include_once("../_function_i/cConnect.php");

$conn = new cConnect();
$conn->goConnect();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Data_Pasien.xls");
header("Pragma: no-cache");
header("Expires: 0");

$view = new cView();
$where = [];
$params = [];
$types = "";

// Filter berdasarkan input
if (!empty($_POST['kelompokUsia'])) {
    $where[] = "p.kelompokUsia = ?";
    $params[] = $_POST['kelompokUsia'];
    $types .= "s";
}
if (!empty($_POST['jenisKelamin'])) {
    $where[] = "p.jenisKelamin = ?";
    $params[] = $_POST['jenisKelamin'];
    $types .= "s";
}
if (!empty($_POST['golonganDarah'])) {
    $where[] = "p.golonganDarah = ?";
    $params[] = $_POST['golonganDarah'];
    $types .= "s";
}
if (!empty($_POST['jenisDisabilitas'])) {
    $where[] = "jd.jenisDisabilitas = ?";
    $params[] = $_POST['jenisDisabilitas'];
    $types .= "s";
}
if (!empty($_POST['idKelurahan'])) {
    $where[] = "p.idKelurahanDomisili = ?";
    $params[] = (int) $_POST['idKelurahan'];
    $types .= "i";
}

$sql = "SELECT p.*, sd.*, jd.*, k.namaKelurahan
        FROM pasien p
        JOIN sub_disabilitas sd ON sd.idSubDisabilitas = p.idSubDisabilitas
        JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas
        LEFT JOIN kelurahan k ON k.idKelurahan = p.idKelurahanDomisili";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " GROUP BY p.idPasien";

$dataPasien = $view->vViewDataPrepared($sql, $params, $types);

// Mendapatkan tanggal cetak
$tanggalCetak = date("d-m-Y H:i");

// Menampilkan judul laporan
echo "<table border='1'>";
echo "<tr><th colspan='10' style='text-align:center; font-weight:bold;'>LAPORAN DATA PASIEN</th></tr>";
echo "<tr><th colspan='10' style='text-align:center;'>Tanggal Cetak: $tanggalCetak</th></tr>";
echo "<tr><td colspan='10'></td></tr>";

// Header tabel
echo "<tr>
        <th>No</th>
        <th>Nama Pasien</th>
        <th>Jenis Kelamin</th>
        <th>Kelompok Usia</th>
        <th>Golongan Darah</th>
        <th>Alamat</th>
        <th>Kelurahan</th>
        <th>Jenis Disabilitas</th>
        <th>Sub Jenis Disabilitas</th>
        <th>Alat Bantu</th>
      </tr>";

$no = 1;
if (empty($dataPasien)) {
    echo "<tr><td colspan='10' style='text-align:center;'>Data rekam medis tidak tersedia untuk filter ini.</td></tr>";
} else {
    foreach ($dataPasien as $row) {
        echo "<tr>
                <td>" . $no++ . "</td>
                <td>" . htmlspecialchars($row['namaLengkap'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['jenisKelamin'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['kelompokUsia'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['golonganDarah'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['alamatDomisili'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['namaKelurahan'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['jenisDisabilitas'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['namaDisabilitas'] ?? '') . "</td>
                <td>" . htmlspecialchars($row['alatBantu'] ?? '') . "</td>
              </tr>";
    }
}

echo "</table>";
exit;
?>