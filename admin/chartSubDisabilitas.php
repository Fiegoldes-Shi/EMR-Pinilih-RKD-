<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

header("Content-Type: application/json");

$idJenisDisabilitas = isset($_GET['idJenisDisabilitas']) ? intval($_GET['idJenisDisabilitas']) : 0;

if ($idJenisDisabilitas == 0) {
    echo json_encode(["labels" => [], "datas" => []]);
    exit;
}

$sql = "SELECT sd.namaDisabilitas, COUNT(p.idPasien) AS jumlah 
        FROM pasien p
        JOIN sub_disabilitas sd ON sd.idSubDisabilitas = p.idSubDisabilitas 
        WHERE sd.idJenisDisabilitas = $idJenisDisabilitas
        GROUP BY sd.idSubDisabilitas";

$view = new cView();
$data = $view->vViewData($sql);

$result = ["labels" => [], "datas" => []];
foreach ($data as $row) {
    $result["labels"][] = $row["namaDisabilitas"];
    $result["datas"][] = $row["jumlah"];
}

echo json_encode($result);
?>
