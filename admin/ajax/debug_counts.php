<?php
include 'connection.php';

if (!isset($dbName)) {
    $dbName = getenv('DB_NAME') ?: 'emr_pinilih';
}

echo "Database Name: " . $dbName . "\n";
echo "Testing Connection...\n";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pasien");
    $count = $stmt->fetchColumn();
    echo "Total Pasien Count: " . $count . "\n";

    $stmt2 = $pdo->query("SELECT COUNT(*) FROM pasien p LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas");
    $count2 = $stmt2->fetchColumn();
    echo "Total Joined Count: " . $count2 . "\n";

    // Check if fetchColumn works with explicit index
    $stmt3 = $pdo->query("SELECT COUNT(*) FROM pasien");
    $row = $stmt3->fetch(PDO::FETCH_NUM);
    echo "Count via fetch(NUM): " . $row[0] . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>