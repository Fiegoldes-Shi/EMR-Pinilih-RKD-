<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
date_default_timezone_set('Asia/Jakarta');

include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();
$view = new cView();

$idPasien = $_POST['idPasien'] ?? null;
$tanggalMulai = $_POST['tanggalMulai'] ?? null;
$tanggalSelesai = $_POST['tanggalSelesai'] ?? null;

if (!$idPasien) {
    die("ID Pasien tidak ditemukan.");
}

$sqlhasil = "SELECT hasil.*, jp.*, pr.*, p.*, t.*
            FROM hasil_layanan hasil 
            JOIN jadwal_program jp ON jp.idJadwal = hasil.idJadwal 
            JOIN program pr ON pr.idProgram = jp.idProgram
            JOIN pasien p ON p.idPasien = hasil.idPasien
            JOIN terapis t ON t.idTerapis = hasil.idTerapis
            WHERE hasil.idPasien = ?";

$params = [$idPasien];
$types = "i";

if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
    $sqlhasil .= " AND jp.tanggalKegiatan BETWEEN ? AND ?";
    $params[] = $tanggalMulai;
    $params[] = $tanggalSelesai;
    $types .= "ss";
} elseif (!empty($tanggalMulai)) {
    $sqlhasil .= " AND jp.tanggalKegiatan >= ?";
    $params[] = $tanggalMulai;
    $types .= "s";
} elseif (!empty($tanggalSelesai)) {
    $sqlhasil .= " AND jp.tanggalKegiatan <= ?";
    $params[] = $tanggalSelesai;
    $types .= "s";
}

$sqlhasil .= " ORDER BY jp.tanggalKegiatan DESC";
$datahasil = $view->vViewDataPrepared($sqlhasil, $params, $types);

// if (empty($datahasil)) {
//     die("Data tidak ditemukan.");
// }

// Definisi kolom hasil layanan per program
$kolomPerProgram = [
    1 => ["areaTubuh"],
    2 => ["tingkatNyeri", "waktuMunculKeluhan", "sifatSakit", "obat", "posturTubuh", "ROM", "positive", "management"],
    3 => ["tanggalRujukan", "alasanRujukan", "tinggiBadan", "beratBadan", "tekananDarah", "gulaDarah", "kolesterol", "trigliserida", "benjolanPayudara", "inspeksiVisualAsamAsetat", "kadarAlkoholPernafasan", "tesAmfetaminUrin", "arusPernafasanEkspirasi", "faktorResikoPerilaku"],
    4 => ["saranRujukan"]
];

// Mapping nama kolom ke teks deskriptif
$namaKolom = [
    "areaTubuh" => "Area Terapi Tubuh",
    "tingkatNyeri" => "Tingkat Nyeri",
    "waktuMunculKeluhan" => "Waktu Muncul Keluhan",
    "sifatSakit" => "Sifat Sakit",
    "obat" => "Obat",
    "posturTubuh" => "Postur Tubuh",
    "ROM" => "Range of Motion (ROM)",
    "positive" => "Positive",
    "management" => "Management",
    "tanggalRujukan" => "Tanggal Rujukan",
    "alasanRujukan" => "Alasan Rujukan",
    "tinggiBadan" => "Tinggi Badan (cm)",
    "beratBadan" => "Berat Badan (kg)",
    "tekananDarah" => "Tekanan Darah (mmHg)",
    "gulaDarah" => "Gula Darah (mg/dL)",
    "kolesterol" => "Kolesterol (mg/dL)",
    "trigliserida" => "Trigliserida (mg/dL)",
    "benjolanPayudara" => "Benjolan Payudara",
    "inspeksiVisualAsamAsetat" => "Inspeksi Visual Asam Asetat",
    "kadarAlkoholPernafasan" => "Kadar Alkohol Pernafasan",
    "tesAmfetaminUrin" => "Tes Amfetamin Urin",
    "arusPernafasanEkspirasi" => "Arus Pernafasan Ekspirasi",
    "faktorResikoPerilaku" => "Faktor Resiko Perilaku",
    "saranRujukan" => "Saran Rujukan"
];

// Ambil nama pasien dari database berdasarkan idPasien
$sqlPasien = "SELECT namaLengkap FROM pasien WHERE idPasien = ?";
$dataPasien = $view->vViewDataPrepared($sqlPasien, [$idPasien], "i");
$namaPasien = $dataPasien[0]['namaLengkap'] ?? 'Tidak Diketahui';

// Ambil tanggal saat ini
$tanggalCetak = date("d-m-Y H:i");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Detail_Rekam_Medis_Pasien.xls");
header("Pragma: no-cache");
header("Expires: 0");

$allKolom = array_merge(...array_values($kolomPerProgram));
$totalColumns = 8 + count($allKolom);

echo "<table border='1'>";
echo "<tr><th colspan='$totalColumns' style='text-align:center; font-size:16px; font-weight:bold;'>Detail Rekam Medis Pasien: " . htmlspecialchars($namaPasien) . "</th></tr>";
echo "<tr><th colspan='$totalColumns' style='text-align:center;'>Dicetak pada: $tanggalCetak</th></tr>";
echo "<tr><td colspan='$totalColumns'></td></tr>";

// Header tabel
echo "<tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Program Layanan</th>
        <th>Terapis</th>
        <th>Keluhan</th>
        <th>Hasil Pemeriksaan</th>
        <th>Diagnosis</th>
        <th>Rencana Tindakan</th>";

foreach ($allKolom as $kolom) {
    echo "<th>" . $namaKolom[$kolom] . "</th>";
}
echo "</tr>";

$no = 1;
if (empty($datahasil)) {
    echo "<tr><td colspan='$totalColumns' style='text-align:center;'>Data rekam medis tidak tersedia untuk filter ini.</td></tr>";
} else {
    foreach ($datahasil as $data) {
        echo "<tr>
                <td style='text-align:center;'>" . $no++ . "</td>
                <td>" . htmlspecialchars($data["tanggalKegiatan"] ?? '') . "</td>
                <td>" . htmlspecialchars($data["namaProgram"] ?? '') . "</td>
                <td>" . htmlspecialchars($data["namaTerapis"] ?? '') . "</td>
                <td>" . htmlspecialchars($data["keluhan"] ?? '') . "</td>
                <td>" . htmlspecialchars($data["hasilPemeriksaan"] ?? '') . "</td>
                <td>" . htmlspecialchars($data["diagnosis"] ?? '') . "</td>
                <td>" . htmlspecialchars($data["catatanTindakan"] ?? '') . "</td>";

        foreach ($allKolom as $kolom) {
            $nilai = isset($data[$kolom]) && !empty($data[$kolom]) ? $data[$kolom] : "-";
            echo "<td>" . htmlspecialchars($nilai) . "</td>";
        }
        echo "</tr>";
    }
}

echo "</table>";
exit;
?>