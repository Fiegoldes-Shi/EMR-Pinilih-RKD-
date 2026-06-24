<?php
// Uncomment untuk debugging (Sesuai Panduan Pengembangan)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu server

// Konfigurasi database
$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbname =  getenv('DB_NAME') ?: 'emr_pinilih';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

try {
    // Membuat koneksi menggunakan PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set mode error agar mudah debug jika terjadi kesalahan
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error
    die("Koneksi database gagal: " . $e->getMessage());
}
?>