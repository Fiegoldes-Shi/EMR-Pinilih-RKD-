<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

header("Content-Type: application/json");

$sql = "SELECT DISTINCT YEAR(tanggalKegiatan) AS tahun FROM jadwal_program 
        WHERE idProgram = 1 OR idProgram = 2 OR idProgram = 3 OR idProgram = 4
        ORDER BY tahun DESC";
$view = new cView();
$data = $view->vViewData($sql);

$years = [];
foreach ($data as $row) {
    $years[] = $row["tahun"];
}

echo json_encode($years);
?>
