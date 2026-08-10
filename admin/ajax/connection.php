<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"])) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'emr_pinilih';

// PDO Connection
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

// MySQLi Connection (for legacy support if needed)
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (mysqli_connect_error()) {
    die("MySQLi Connection failed: " . mysqli_connect_error());
}

// Make $conn available globally for cView
$GLOBALS['conn'] = $conn;