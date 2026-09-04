<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

header("Content-Type: application/json");

$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : 0;
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : 0;

$sql = "SELECT namaProgram, COUNT(*) AS jumlah FROM jadwal_program jp
        JOIN program ON program.idProgram = jp.idProgram"; // Default tanpa filter

$params = [];
$types = "";
$hasWhere = false;

if ($bulan > 0) {
    $sql .= ($hasWhere ? " AND" : " WHERE") . " MONTH(jp.tanggalKegiatan) = ?"; // Sesuaikan dengan kolom tanggal di tabel
    $params[] = $bulan;
    $types .= "i";
    $hasWhere = true;
}

if ($tahun > 0) {
    $sql .= ($hasWhere ? " AND" : " WHERE") . " YEAR(jp.tanggalKegiatan) = ?"; // Sesuaikan dengan kolom tanggal di tabel
    $params[] = $tahun;
    $types .= "i";
    $hasWhere = true;
}

$sql .= " GROUP BY namaProgram ORDER BY jp.idProgram";

$view = new cView();
$arrayhasil = $view->vViewDataPrepared($sql, $params, $types);

$labels = [];
$datas = [];

foreach ($arrayhasil as $value) {
    $labels[] = $value["namaProgram"];
    $datas[] = (int)$value["jumlah"]; // Pastikan angka dalam format integer
}

echo json_encode([
    "labels" => $labels,
    "datas" => $datas
]);
?>
