<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

header('Content-Type: application/json'); // Tambahkan header JSON

// === BASEURL DINAMIS (tanpa hardcode localhost) ===
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'];
$path     = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'); // ambil parent folder (../)
$baseurl  = $protocol . $host . $path;

// === KONFIGURASI DATABASE (gunakan environment variable atau default) ===
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'emr_pinilih';

try {
    // Koneksi PDO
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
$idKec = $_POST['idKec'];

$sql = $pdo->prepare("SELECT * FROM kelurahan WHERE idKecamatan = :idKecamatan ORDER BY idKelurahan");
$sql->execute([':idKecamatan' => $idKec]);

// Buat data untuk dropdown kecamatan
$html = "<option value=''>- pilihan -</option>";
while ($data = $sql->fetch()) {
    $html .= "<option value='". $data['idKelurahan'] ."'>". $data['namaKelurahan'] ."</option>";
}

// Pastikan tidak ada output lain sebelum JSON
echo json_encode(['data_kelurahan' => $html]);
?>
