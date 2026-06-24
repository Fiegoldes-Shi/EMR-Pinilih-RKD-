<?php
require 'config.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['alamatEmail'];

    $query = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $otp = rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $conn->prepare("UPDATE user SET otp = ?, otp_expiry = ? WHERE email = ?");
        $stmt->execute([$otp, $expires_at, $email]);

        $mail = new PHPMailer();
        $mail->isSMTP();

        $mail->Host = getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USERNAME');
        $mail->Password = getenv('SMTP_PASSWORD');

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('emrpinilihsedayu@gmail.com', 'Sistem Rekam Medis - EMR Pinilih');
        $mail->addAddress($email);
        $mail->Subject = 'Kode Verifikasi OTP Reset Password';

        $mail->Body = "Yth. Pengguna,\n\n" .
            "Kami telah menerima permintaan untuk mengatur ulang kata sandi pada akun Rekam Medis EMR Pinilih Anda.\n\n" .
            "Sebagai langkah keamanan, silakan gunakan Kode Verifikasi (OTP) berikut ini:\n\n" .
            "Kode Anda: $otp\n\n" .
            "Mohon diperhatikan bahwa kode ini bersifat rahasia dan hanya berlaku selama 10 menit ke depan. Jangan berikan kode ini kepada siapa pun demi keamanan akun Anda.\n\n" .
            "Apabila Anda merasa tidak pernah meminta pengaturan ulang kata sandi, mohon abaikan email ini. Akun Anda akan tetap aman.\n\n" .
            "Hormat kami,\n" .
            "Tim Administrator EMR Pinilih\n" .
            "Yayasan Pinilih Sedayu";

        if ($mail->send()) {
            echo "success";
        } else {
            echo "smtp_error";
        }
    } else {
        echo "not_found";
    }
}
?>