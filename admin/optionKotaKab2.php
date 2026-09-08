<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

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

header('Content-Type: application/json'); // Pastikan respons dalam format JSON

// KOTA KABUPATEN
// Ambil data ID provinsi yang dikirim via ajax post
$idProv = $_POST['idProv'];

// Buat query untuk menampilkan data sesuai yang dipilih user pada form
$sql = $pdo->prepare("SELECT * FROM kotakabupaten WHERE idProvinsi = :idProvinsi ORDER BY idKotaKabupaten");
$sql->execute([':idProvinsi' => $idProv]); // Eksekusi querynya

$html = "<option value=''>- pilihan -</option>";
while($data = $sql->fetch()){ // Ambil semua data dari hasil eksekusi $sql
  $html .= "<option value='".$data['idKotaKabupaten']."'>".htmlspecialchars($data['namaKotaKabupaten'])."</option>"; // Tambahkan tag option ke variabel $html
}

$callback = array('data_kotaKab'=>$html); // Masukan variabel html tadi ke dalam array $callback dengan index array : data_subDisabilitas
echo json_encode($callback); // konversi variabel $callback menjadi JSON
?>