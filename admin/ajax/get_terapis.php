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

// Columns mapping for sorting
$columns = [
    0 => 'namaTerapis',
    1 => 'namaTerapis',
    2 => 'spesialisasi',
    3 => 'instansi',
    4 => 'noTelepon',
    5 => 'idTerapis', // View
    6 => 'idTerapis', // Edit
    7 => 'idTerapis'  // Delete
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'idTerapis';

// Total Records (without filtering)
$stmt = $pdo->query("SELECT COUNT(*) FROM terapis");
$totalRecords = $stmt->fetchColumn();

// Filtering
$whereClause = "";
$params = [];

if (!empty($searchValue)) {
    $whereClause .= " WHERE (namaTerapis LIKE :search
                      OR spesialisasi LIKE :search
                      OR instansi LIKE :search
                      OR noTelepon LIKE :search)";
    $params[':search'] = "%$searchValue%";
}

// Total Filtered Records
$stmt = $pdo->prepare("SELECT COUNT(*) FROM terapis" . $whereClause);
$stmt->execute($params);
$totalFiltered = $stmt->fetchColumn();

// Fetch Data with Limit and Order
$query = "SELECT * FROM terapis" . $whereClause . " ORDER BY $orderBy $orderDir LIMIT :start, :length";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':length', $length, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll();

// Format Data for DataTables
$response = [];
$no = $start + 1;
foreach ($data as $row) {
    $btnView = '<button type="button" class="btn btn-info btn-view" style="border-radius: 8px;" data-id="' . $row['idTerapis'] . '" title="View">
                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                </button>';
    $btnEdit = '<button type="button" class="btn btn-warning btn-edit" style="border-radius: 8px;" data-id="' . $row['idTerapis'] . '" title="Edit">
                    <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                </button>';
    $btnDelete = '<button type="button" class="btn btn-danger btn-delete" style="border-radius: 8px;" data-id="' . $row['idTerapis'] . '" title="Delete">
                    <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
                  </button>';

    $response[] = [
        $no++,
        htmlspecialchars($row['namaTerapis']),
        htmlspecialchars($row['spesialisasi']),
        htmlspecialchars($row['instansi']),
        htmlspecialchars($row['noTelepon']),
        $btnView,
        $btnEdit,
        $btnDelete
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