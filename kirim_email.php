<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'emailkamu@gmail.com'; // Ganti dengan email kamu
    $mail->Password   = 'aplikasi_password';   // Gunakan "App Password", bukan password biasa
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Isi email
    $mail->setFrom($_POST['email'], $_POST['nama']);
    $mail->addAddress('primanugrahaaldi@gmail.com'); // Tujuan email kamu

    $mail->Subject = 'Pesan dari ' . $_POST['nama'];
    $mail->Body    = $_POST['pesan'];

    $mail->send();
    echo 'Pesan berhasil dikirim!';
} catch (Exception $e) {
    echo "Pesan gagal dikirim. Error: {$mail->ErrorInfo}";
}
?>
