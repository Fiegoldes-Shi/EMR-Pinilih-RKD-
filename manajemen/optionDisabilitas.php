<?php
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
// SUB DISABILITAS
  $id_jenisDisabilitas = $_POST['jenisDisabilitas'];

  // Buat query untuk menampilkan data subDisabilitas dengan jenisDisabilitas tertentu (sesuai yang dipilih user pada form)
  $sqlDisabilitas = $pdo->prepare("SELECT * FROM sub_disabilitas WHERE idJenisDisabilitas='" .$id_jenisDisabilitas. "'ORDER BY idSubDisabilitas");
  $sqlDisabilitas->execute(); // Eksekusi querynya

  $html = "<option value=''>- pilihan -</option>";
  while($data = $sqlDisabilitas->fetch()){ // Ambil semua data dari hasil eksekusi $sql
    $html .= "<option value='".$data['idSubDisabilitas']."'>".$data['namaDisabilitas']."</option>"; // Tambahkan tag option ke variabel $html
  }

  $callback = array('data_disabilitas'=>$html); // Masukan variabel html tadi ke dalam array $callback dengan index array : data_subDisabilitas
  echo json_encode($callback); // konversi varibael $callback menjadi JSON
?>