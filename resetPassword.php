<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['alamatEmail'];
    $otp = $_POST['otp'] ?? '';
    $passwordBaru = md5($_POST['passwordBaru']);

    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM user WHERE email = ? AND otp = ?");
    $stmt->execute([$email, $otp]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "invalid";
        exit();
    }

    $current_time = new DateTime();
    $otp_expiry = new DateTime($row['otp_expiry']);
    if ($current_time > $otp_expiry) {
        echo "expired";
        exit();
    }

    $stmt = $conn->prepare("UPDATE user SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
    if ($stmt->execute([$passwordBaru, $email])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>