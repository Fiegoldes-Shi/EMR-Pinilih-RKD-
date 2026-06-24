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
    0 => 'nama',
    1 => 'nama',
    2 => 'jbtn',
    3 => 'noTelp',
    4 => 'username',
    5 => 'email',
    6 => 'idUser' // View
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'idUser';

$stmt = $pdo->query("SELECT COUNT(*) FROM user");
$totalRecords = $stmt->fetchColumn();

$whereClause = "";
$params = [];

if (!empty($searchValue)) {
    $whereClause .= " WHERE (nama LIKE :search
                      OR jbtn LIKE :search
                      OR noTelp LIKE :search
                      OR username LIKE :search
                      OR email LIKE :search)";
    $params[':search'] = "%$searchValue%";
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM user" . $whereClause);
$stmt->execute($params);
$totalFiltered = $stmt->fetchColumn();

$query = "SELECT * FROM user" . $whereClause . " ORDER BY $orderBy $orderDir LIMIT :start, :length";
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
    $btnView = '<button type="button" class="btn btn-info btn-view" style="border-radius: 8px;" data-id="' . $row['idUser'] . '" title="View">
                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                </button>';

    $response[] = [
        $no++,
        htmlspecialchars($row['nama']),
        htmlspecialchars($row['jbtn']),
        htmlspecialchars($row['noTelp']),
        htmlspecialchars($row['username']),
        htmlspecialchars($row['email']),
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