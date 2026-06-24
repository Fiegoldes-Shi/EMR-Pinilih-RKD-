<?php
include_once("../_function_i/cConnect.php");
$connObj = new cConnect();
$connObj->goConnect();
$db = $GLOBALS["conn"];

$result = mysqli_query($db, "SHOW COLUMNS FROM jenis_disabilitas LIKE 'is_utama'");
if (mysqli_num_rows($result) == 0) {
    mysqli_query($db, "ALTER TABLE jenis_disabilitas ADD is_utama TINYINT(1) DEFAULT 0");
    echo "Column is_utama added. ";
    mysqli_query($db, "UPDATE jenis_disabilitas SET is_utama = 1 WHERE jenisDisabilitas IN ('Fisik', 'Mental', 'Intelektual', 'Sensorik', 'Ganda', 'Non Disabilitas')");
    echo "Default items set to utama.";
} else {
    echo "Column is_utama already exists.";
}
?>