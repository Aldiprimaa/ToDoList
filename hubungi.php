<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load file PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$sukses = $gagal = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $pesan = htmlspecialchars($_POST['pesan']);

    $mail = new PHPMailer(true);

    try {
        // Pengaturan SMTP Gmail (ubah sesuai email pengirim)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'primanugrahaaldi@gmail.com'; // GANTI
        $mail->Password = 'iioa zaeq gxsj jnfm';     // GANTI dengan app password dari Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Pengirim & Penerima
        $mail->setFrom($email, $nama);
        $mail->addAddress('primanugrahaaldi@gmail.com');

        // Konten Email
        $mail->isHTML(false);
        $mail->Subject = "Pesan dari $nama";
        $mail->Body = "Nama: $nama\nEmail: $email\n\nPesan:\n$pesan";

        $mail->send();
        $sukses = "Pesan berhasil dikirim!";
    } catch (Exception $e) {
        $gagal = "Pesan gagal dikirim. Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ02B3u4xcY5rIm0sYDbMNxpn+XsmcVYm8HztxOMW9L8S5rfUmx9b4HUmkfr" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
        }
        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
        }
        .form-container .form-group label {
            font-weight: bold;
        }
        .form-container .form-control {
            height: 45px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .form-container textarea.form-control {
            height: 120px;
            resize: none;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border-radius: 8px;
            border: none;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .alert-custom {
            font-size: 1.2rem;
            text-align: center;
            margin-top: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .note-container {
            max-width: 300px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            font-size: 16px;
        }
        .note-container h5 {
            font-size: 18px;
            font-weight: bold;
        }
        .note-container p {
            font-size: 14px;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
                padding: 30px;
            }
            .form-container h2 {
                font-size: 24px;
            }
            .btn-submit {
                padding: 12px;
                font-size: 16px;
            }
            .note-container {
                max-width: 100%;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="form-container">
        <div class="form-section" style="flex: 1;">
            <h2>📩 Hubungi Kami</h2>

            <?php if ($sukses): ?>
                <div class="alert alert-success alert-custom"><?= $sukses ?></div>
            <?php elseif ($gagal): ?>
                <div class="alert alert-danger alert-custom"><?= $gagal ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group mb-4">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label for="email">Email Anda</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label for="pesan">Pesan</label>
                    <textarea name="pesan" id="pesan" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn-submit">Kirim Pesan</button>
            </form>
        </div>

        <!-- Note Peraturan -->
        <div class="note-container">
            <h5>Peraturan Penggunaan</h5>
            <p>Pastikan Anda menggunakan bahasa yang sopan dan sesuai saat mengirimkan pesan ke kami. Kami tidak akan memproses pesan yang mengandung kata-kata kasar atau tidak pantas.</p>
            <p>Web by Aldi Primaa</p>
        </div>
    </div>

    <div class="back-link">
        <a href="todo.php">← Kembali ke Halaman Utama</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-wEmeXPyBrrQOKvFqBQ0taGDTJ1H1v6zS23zkP2axnp5yqmtlRdf7sdq1Bgy8Jjow" crossorigin="anonymous"></script>
</body>
</html>
