<?php
include 'connection.php';

// DataTables Parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$rawOrderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderDir = (strtolower($rawOrderDir) === 'desc') ? 'DESC' : 'ASC';

$columns = [
    0 => 'namaDisabilitas',
    1 => 'jenisDisabilitas',
    2 => 'namaDisabilitas',
    3 => 'idSubDisabilitas' // View
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'namaDisabilitas';

$baseFrom = "FROM sub_disabilitas sd JOIN jenis_disabilitas jd ON jd.idJenisDisabilitas = sd.idJenisDisabilitas";

$stmt = $pdo->query("SELECT COUNT(*) $baseFrom");
$totalRecords = $stmt->fetchColumn();

$whereClause = "";
$params = [];

if (!empty($searchValue)) {
    $whereClause .= " WHERE (jd.jenisDisabilitas LIKE :search
                      OR sd.namaDisabilitas LIKE :search)";
    $params[':search'] = "%$searchValue%";
}

$stmt = $pdo->prepare("SELECT COUNT(*) $baseFrom" . $whereClause);
$stmt->execute($params);
$totalFiltered = $stmt->fetchColumn();

$query = "SELECT sd.idSubDisabilitas, jd.idJenisDisabilitas, jd.jenisDisabilitas, sd.namaDisabilitas $baseFrom" . $whereClause . " ORDER BY $orderBy $orderDir LIMIT :start, :length";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':length', $length, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll();

$response = [];
$no = $start + 1;
foreach ($data as $row) {
    $btnView = '<button type="button" class="btn btn-info btn-view" style="border-radius: 8px;" data-id="' . $row['idSubDisabilitas'] . '" title="View">
                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                </button>';

    $response[] = [
        $no++,
        htmlspecialchars($row['jenisDisabilitas']),
        htmlspecialchars($row['namaDisabilitas']),
        $btnView
    ];
}

$output = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $response,
    "iTotalRecords" => intval($totalRecords),
    "iTotalDisplayRecords" => intval($totalFiltered),
    "aaData" => $response
];

echo json_encode($output);