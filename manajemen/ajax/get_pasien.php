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
    0 => 'namaLengkap',
    1 => 'namaLengkap',
    2 => 'kelompokUsia',
    3 => 'jenisKelamin',
    4 => 'namaKelurahan',
    5 => 'namaDisabilitas',
    6 => 'idPasien' // View
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'idPasien';

$baseFrom = "FROM pasien p
             LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
             LEFT JOIN kelurahan kel ON p.idKelurahanDomisili = kel.idKelurahan";

$stmt = $pdo->query("SELECT COUNT(*) $baseFrom");
$totalRecords = $stmt->fetchColumn();

$whereClause = "";
$params = [];

if (!empty($searchValue)) {
    $whereClause .= " WHERE (p.namaLengkap LIKE :search
                      OR p.kelompokUsia LIKE :search
                      OR p.jenisKelamin LIKE :search
                      OR kel.namaKelurahan LIKE :search
                      OR sd.namaDisabilitas LIKE :search)";
    $params[':search'] = "%$searchValue%";
}

$stmt = $pdo->prepare("SELECT COUNT(*) $baseFrom" . $whereClause);
$stmt->execute($params);
$totalFiltered = $stmt->fetchColumn();

$query = "SELECT p.*, sd.namaDisabilitas, kel.namaKelurahan $baseFrom" . $whereClause . " ORDER BY $orderBy $orderDir LIMIT :start, :length";
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
    $btnView = '<button type="button" class="btn btn-info btn-view" style="border-radius: 8px;" data-id="' . $row['idPasien'] . '" title="View">
                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                </button>';

    $response[] = [
        $no++,
        htmlspecialchars($row['namaLengkap']),
        htmlspecialchars($row['kelompokUsia']),
        htmlspecialchars($row['jenisKelamin']),
        htmlspecialchars($row['namaKelurahan']),
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