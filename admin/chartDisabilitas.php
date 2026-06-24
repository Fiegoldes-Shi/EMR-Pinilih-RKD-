<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

header("Content-Type: application/json");

$sql = "SELECT jd.idJenisDisabilitas, jd.jenisDisabilitas, COUNT(p.idPasien) AS jumlah 
        FROM pasien p
        JOIN sub_disabilitas sd ON sd.idSubDisabilitas = p.idSubDisabilitas 
        JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas
        GROUP BY jd.idJenisDisabilitas";

$view = new cView();
$data = $view->vViewData($sql);

$result = ["labels" => [], "datas" => [], "ids" => []];
foreach ($data as $row) {
    $result["labels"][] = $row["jenisDisabilitas"];
    $result["datas"][] = $row["jumlah"];
    $result["ids"][] = $row["idJenisDisabilitas"]; // Diperlukan untuk mapping index bar ke ID asli
}

echo json_encode($result);
?>
