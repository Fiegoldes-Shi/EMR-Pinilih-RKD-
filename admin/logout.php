<?php
session_start();
session_destroy(); // Hapus semua sesi

// BASEURL DINAMIS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'];
$path     = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); 
$baseurl  = $protocol . $host . $path;

// Redirect ke halaman login (root project)
header("Location: $baseurl/");
exit();

