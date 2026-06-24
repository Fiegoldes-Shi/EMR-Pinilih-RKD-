<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['alamatEmail'];
    $passwordBaru = md5($_POST['passwordBaru']);

    $stmt = $conn->prepare("UPDATE user SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
    if ($stmt->execute([$passwordBaru, $email])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>