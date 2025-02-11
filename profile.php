<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan data pengguna dan statistik tugas
$query = "SELECT username, profile_picture, full_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

$query_stats = "SELECT 
                    COUNT(*) AS total_tasks, 
                    SUM(completed) AS completed_tasks,
                    SUM(CASE WHEN deadline < NOW() AND completed = 0 THEN 1 ELSE 0 END) AS overdue_tasks
                FROM tasks 
                WHERE user_id = ?";
$stmt_stats = $conn->prepare($query_stats);
$stmt_stats->bind_param('i', $user_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

// Handle the image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = str_replace(" ", "_", $_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . basename($file_name);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
        } else {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $query_update = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $stmt_update = $conn->prepare($query_update);
                $stmt_update->bind_param('si', $target_file, $user_id);
                $stmt_update->execute();
                header("Location: profile.php"); 
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File is not an image.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['full_name']) && isset($_POST['email'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    $query_update_bio = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
    $stmt_update_bio = $conn->prepare($query_update_bio);
    $stmt_update_bio->bind_param('ssi', $full_name, $email, $user_id);
    $stmt_update_bio->execute();
    header("Location: profile.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-card {
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .dropdown-menu {
            left: -150px;
        }
        .stat-card .display-6 {
            font-size: 2.5rem;
            font-weight: bold;
            animation: countUp 1.5s ease-out;
        }
        @keyframes countUp {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        /* Modal konfirmasi logout */
        .modal-content {
            border-radius: 10px;
        }
        /* Tombol pengaturan */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="todo.php">Daftar Tugas</a>
            <button class="btn btn-danger ms-3" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-4">Profil Pengguna</h1>

        <div class="profile-card text-center mb-5">
            <img src="<?= $user_data['profile_picture'] ? $user_data['profile_picture'] : 'uploads/default-profile.png' ?>" alt="Profil" class="profile-img">
            <h4 class="card-title"><?= htmlspecialchars($user_data['username']) ?></h4>
            <p class="card-text">Username: <?= htmlspecialchars($user_data['username']) ?></p>
            <p class="card-text">Nama Lengkap: <?= htmlspecialchars($user_data['full_name']) ?></p>
            <p class="card-text">Email: <?= htmlspecialchars($user_data['email']) ?></p>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-list-task"></i> Total Tugas</h5>
                        <p class="display-6 text-primary fw-bold"><?= $stats['total_tasks'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-check-circle"></i> Tugas Selesai</h5>
                        <p class="display-6 text-success fw-bold"><?= $stats['completed_tasks'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-danger">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Tugas Terlewat</h5>
                        <p class="display-6 text-danger fw-bold"><?= $stats['overdue_tasks'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#profileModal">Pengaturan Profil</button>
            <a href="todo.php" class="btn btn-primary ms-3">Kembali ke Halaman Utama</a>
        </div>
    </div>

    <!-- Modal Pengaturan Profil -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Pengaturan Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="profile.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Ubah Gambar Profil</label>
                            <input type="file" class="form-control" name="profile_picture" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user_data['full_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Perbarui Profil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar dari aplikasi?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="logout.php" class="btn btn-danger">Keluar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
