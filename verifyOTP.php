<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['alamatEmail'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM user WHERE email = ? AND otp = ?");
    $stmt->execute([$email, $otp]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $current_time = new DateTime();
        $otp_expiry = new DateTime($row['otp_expiry']);

        if ($current_time <= $otp_expiry) {
            echo "valid";
        } else {
            echo "expired";
        }
    } else {
        echo "invalid";
    }
}
?>