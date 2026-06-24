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
    0 => 'p.nama',
    1 => 'p.nama',
    2 => 'p.asalLembaga',
    3 => 'p.usia',
    4 => 'p.jenisKelamin',
    5 => 'p.alamat',
    6 => 'sd.namaDisabilitas',
    7 => 'p.idPeserta', // View
    8 => 'p.idPeserta', // Edit
    9 => 'p.idPeserta'  // Delete
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'p.nama';

$baseFrom = "FROM peserta p
             LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
             LEFT JOIN jenis_disabilitas jd ON sd.idJenisDisabilitas = jd.idJenisDisabilitas";

$stmt = $pdo->query("SELECT COUNT(*) $baseFrom");
$totalRecords = $stmt->fetchColumn();

$whereClause = "";
$params = [];

if (!empty($searchValue)) {
    $whereClause .= " WHERE (p.nama LIKE :search
                      OR p.asalLembaga LIKE :search
                      OR p.jenisKelamin LIKE :search
                      OR p.alamat LIKE :search
                      OR sd.namaDisabilitas LIKE :search)";
    $params[':search'] = "%$searchValue%";
}

$stmt = $pdo->prepare("SELECT COUNT(*) $baseFrom" . $whereClause);
$stmt->execute($params);
$totalFiltered = $stmt->fetchColumn();

$query = "SELECT p.*, sd.namaDisabilitas $baseFrom" . $whereClause . " ORDER BY $orderBy $orderDir LIMIT :start, :length";
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
    $btnView = '<button type="button" class="btn btn-info btn-view" style="border-radius: 8px;" data-id="' . $row['idPeserta'] . '" title="View">
                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                </button>';
    $btnEdit = '<button type="button" class="btn btn-warning btn-edit" style="border-radius: 8px;" data-id="' . $row['idPeserta'] . '" title="Edit">
                    <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                </button>';
    $btnDelete = '<button type="button" class="btn btn-danger btn-delete" style="border-radius: 8px;" data-id="' . $row['idPeserta'] . '" title="Delete">
                    <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
                  </button>';

    $response[] = [
        $no++,
        htmlspecialchars($row['nama']),
        htmlspecialchars($row['asalLembaga']),
        htmlspecialchars($row['usia']),
        htmlspecialchars($row['jenisKelamin']),
        htmlspecialchars($row['alamat']),
        htmlspecialchars($row['namaDisabilitas']),
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