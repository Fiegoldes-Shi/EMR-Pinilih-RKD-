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
    0 => 'namaLengkap', // No sorted alphabetically by default
    1 => 'namaLengkap',
    2 => 'kelompokUsia',
    3 => 'jenisKelamin',
    4 => 'namaKelurahan',
    5 => 'namaDisabilitas',
    6 => 'idPasien', // View
    7 => 'idPasien', // Edit
    8 => 'idPasien'  // Delete
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'idPasien';


// Base Query
$query = "SELECT p.*, sd.namaDisabilitas, kel.namaKelurahan 
          FROM pasien p
          LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
          LEFT JOIN kelurahan kel ON p.idKelurahanDomisili = kel.idKelurahan";

// Total Records (without filtering)
$stmt = $pdo->query("SELECT COUNT(*) FROM pasien");
$totalRecords = $stmt->fetchColumn();

// Filtering
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

// Total Filtered Records
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pasien p 
                       LEFT JOIN sub_disabilitas sd ON p.idSubDisabilitas = sd.idSubDisabilitas
                       LEFT JOIN kelurahan kel ON p.idKelurahanDomisili = kel.idKelurahan" . $whereClause);
$stmt->execute($params);
$totalFiltered = $stmt->fetchColumn();

// Fetch Data with Limit and Order
$query .= $whereClause . " ORDER BY $orderBy $orderDir LIMIT :start, :length";
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
    // Action Buttons
    // Action Buttons
    $btnView = '<button type="button" class="btn btn-info btn-view" style="border-radius: 8px;" data-id="' . $row['idPasien'] . '" title="View">
                    <i class="fa-regular fa-eye" style="color: #000000;"></i>
                </button>';
    $btnEdit = '<button type="button" class="btn btn-warning btn-edit" style="border-radius: 8px;" data-id="' . $row['idPasien'] . '" title="Edit">
                    <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                </button>';
    $btnDelete = '<button type="button" class="btn btn-danger btn-delete" style="border-radius: 8px;" data-id="' . $row['idPasien'] . '" title="Delete">
                    <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
                  </button>';

    $response[] = [
        $no++,
        htmlspecialchars($row['namaLengkap']),
        htmlspecialchars($row['kelompokUsia']),
        htmlspecialchars($row['jenisKelamin']),
        htmlspecialchars($row['namaKelurahan'] ?? ''),
        htmlspecialchars($row['namaDisabilitas'] ?? ''),
        $btnView,
        $btnEdit,
        $btnDelete
    ];
}

// Return JSON
// Return JSON
$output = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $response,
    // Legacy support for older DataTables
    "iTotalRecords" => intval($totalRecords),
    "iTotalDisplayRecords" => intval($totalFiltered),
    "aaData" => $response
];


echo json_encode($output);