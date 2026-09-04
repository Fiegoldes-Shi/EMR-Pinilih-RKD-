<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["idUser"]) || !isset($_SESSION['role']) || ($_SESSION['role'] !== '1' && $_SESSION['role'] !== '2')) {
    http_response_code(401);
    die("Akses ditolak. Silakan login terlebih dahulu.");
}
date_default_timezone_set('Asia/Jakarta');

set_time_limit(0);

// === KONFIG DATABASE DINAMIS ===
$dbHost = getenv('DB_HOST') ?: '127.0.0.1'; 
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'emr_pinilih';

// === PATH MYSQLDUMP DINAMIS ===
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows (contoh XAMPP)
    $mysqldumpPath = getenv('MYSQLDUMP_PATH') ?: (file_exists('C:\\xampp\\mysql\\bin\\mysqldump.exe') ? 'C:\\xampp\\mysql\\bin\\mysqldump.exe' : 'D:\\xampp\\mysql\\bin\\mysqldump.exe');
} else {
    // Linux (server deploy biasanya sudah ada di PATH)
    $mysqldumpPath = getenv('MYSQLDUMP_PATH') ?: '/usr/bin/mysqldump';
}

// === NAMA FILE BACKUP ===
$date = date("Y-m-d_H-i-s");
$filename = "backup_emr_rkd_pinilih_{$date}.sql";

// === HEADER DOWNLOAD ===
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"$filename\"");

// === BANGUN COMMAND ===
$stderr = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? ' 2> NUL' : ' 2> /dev/null';
$command = "\"$mysqldumpPath\" --single-transaction --default-character-set=utf8mb4 --hex-blob -h $dbHost -u $dbUser " .
    (!empty($dbPass) ? "-p$dbPass " : "") .
    "$dbName" . $stderr;

// === JALANKAN BACKUP ===
passthru($command, $result);

if ($result !== 0) {
    echo "Backup gagal. Kode error: $result";
    exit;
}

exit;
