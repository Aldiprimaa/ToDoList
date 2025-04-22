<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM profil WHERE user_id = '$user_id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    mysqli_query($conn, "INSERT INTO profil (user_id, nama_lengkap, bio, foto, skor) VALUES ('$user_id', '', '', '', 0)");
    $query = mysqli_query($conn, "SELECT * FROM profil WHERE user_id = '$user_id'");
    $data = mysqli_fetch_assoc($query);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST['nama']);
    $bio = htmlspecialchars($_POST['bio']);

    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $file_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowTypes)) {
            move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
            $updateFoto = ", foto = '$target_file'";
        } else {
            $updateFoto = '';
        }
    } else {
        $updateFoto = '';
    }

    mysqli_query($conn, "UPDATE profil SET nama_lengkap='$nama', bio='$bio' $updateFoto WHERE user_id='$user_id'");

    $query = mysqli_query($conn, "SELECT * FROM profil WHERE user_id = '$user_id'");
    $data = mysqli_fetch_assoc($query);
    $sukses = "Profil berhasil diperbarui!";
}

// Hitung level berdasarkan skor
$skor = $data['skor'] ?? 0;
$level = min(floor($skor / 100) + 1, 10);
$maxSkor = 1000;
$progressPersen = min(($skor / $maxSkor) * 100, 100);

// Tentukan warna border berdasarkan level
function getLevelColor($level) {
    if ($level >= 7) return '#ffd700'; // emas
    if ($level >= 4) return '#c0c0c0'; // perak
    return '#cd7f32'; // perunggu
}
$borderColor = getLevelColor($level);

// Menghitung jumlah tugas selesai dari tabel 'tasks'
$query_tugas_selesai = mysqli_query($conn, "SELECT COUNT(*) AS selesai FROM tasks WHERE user_id = '$user_id' AND status = 'belum'");
$data_tugas_selesai = mysqli_fetch_assoc($query_tugas_selesai);
$selesai = $data_tugas_selesai['selesai'] ?? 0; // Menyimpan jumlah tugas selesai


// Buat avatar generator jika tidak ada foto
function generateAvatar($nama) {
    global $borderColor;
    
    $huruf = strtoupper(substr($nama, 0, 1)); // Ambil huruf pertama dari nama
    $warna = ['#ffadad', '#ffd6a5', '#fdffb6', '#caffbf', '#9bf6ff', '#a0c4ff', '#bdb2ff', '#ffc6ff'];
    $bg = $warna[ord($huruf) % count($warna)];
    return "<div style='width:140px;height:140px;border-radius:50%;background:$bg;color:#fff;font-size:60px;display:flex;align-items:center;justify-content:center;font-weight:bold;border:4px solid $borderColor;'>$huruf</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: 0.3s;
            position: relative;
        }
        .profile-img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 24px;
            font-weight: 500;
        }
        .edit-form-card {
            background: #e3f2fd;
            border-radius: 15px;
            padding: 25px;
            margin-top: 25px;
            display: none;
            animation: slideDown 0.4s ease-in-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .badge-info {
            font-size: 14px;
            background-color: #e3f2fd;
            color: #007bff;
            padding: 5px 12px;
            border-radius: 50px;
            margin: 5px;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 profile-card text-center">

            <h3 class="mb-3 text-primary">👤 Profil Saya</h3>

            <?php if (isset($sukses)): ?>
                <div class="alert alert-success"><?= $sukses ?></div>
            <?php endif; ?>

            <!-- Avatar / Foto -->
            <?php if (!empty($data['foto'])): ?>
                <img src="<?= $data['foto'] ?>" alt="Foto Profil" class="profile-img" style="border: 4px solid <?= $borderColor ?>;">
            <?php else: ?>
                <?= generateAvatar($data['nama_lengkap'] ?: 'U') ?>
            <?php endif; ?>

            <h4 class="mt-3 font-weight-bold"><?= $data['nama_lengkap'] ?: 'Belum diisi' ?></h4>
            <p class="text-muted mb-2"><?= $data['bio'] ?: 'Belum ada bio.' ?></p>

            <!-- Level dan Progress -->
            <p><strong>Level <?= $level ?></strong></p>
            <div class="progress mb-3" style="height: 20px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: <?= $progressPersen ?>%;" aria-valuenow="<?= $progressPersen ?>" aria-valuemin="0" aria-valuemax="100">
                    <?= round($progressPersen) ?>%
                </div>
            </div>

            <div class="mb-3">
            <span class="badge badge-info">🎯 Tugas selesai: <?= $selesai ?></span>
                <span class="badge badge-info">🏆 Skor: <?= $skor ?></span>
            </div>

            <div class="mb-3">
                <button class="btn btn-outline-primary btn-custom" onclick="toggleEdit()">✏️ Edit Profil</button>
                <a href="todo.php" class="btn btn-outline-secondary btn-custom">← Kembali</a>
            </div>

            <!-- Form Edit -->
            <div id="formEdit" class="edit-form-card text-left">
                <h5 class="text-primary mb-3">Edit Profil</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" class="form-control"><?= htmlspecialchars($data['bio']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" name="foto" class="form-control-file">
                    </div>
                    <button type="submit" class="btn btn-success btn-block mt-3">💾 Simpan Perubahan</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function toggleEdit() {
        const form = document.getElementById('formEdit');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
</script>
</body>
</html>
