<?php
session_start(); // Mulai sesi untuk mendapatkan user_id

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Arahkan ke halaman login jika belum login
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari sesi

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "todo_app");

// Periksa apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data tugas berdasarkan user_id
$query = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Binding user_id ke query
$stmt->execute();
$result = $stmt->get_result();

// Proses setiap baris hasil query
if (!$result) {
    die("Query gagal: " . $conn->error);
}

$totalTugas = $result->num_rows; // Total tugas yang ada
$selesai = 0;
$belum = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['status'] == 'selesai') {
        $belum++; // Tugas selesai masuk ke "belum"
    } else {
        $selesai++; // Tugas belum selesai masuk ke "selesai"
    }
}

$persentase = $totalTugas > 0 ? round(($selesai / $totalTugas) * 100) : 0;
$poin = $selesai * 10; // Misalnya 10 poin per tugas selesai

// Tentukan penghargaan berdasarkan poin
if ($poin >= 100) {
    $penghargaan = "<span class='badge bg-warning text-dark'><i class='bi bi-star-fill'></i> Master Tugas! Kamu sangat rajin!</span>";
    $penghargaanClass = "award-master";
} elseif ($poin >= 50) {
    $penghargaan = "<span class='badge bg-success text-dark'><i class='bi bi-trophy-fill'></i> Penuntun Tugas! Kamu sudah hebat!</span>";
    $penghargaanClass = "award-penuntun";
} elseif ($poin >= 20) {
    $penghargaan = "<span class='badge bg-primary text-white'><i class='bi bi-gem'></i> Penyelesai Tugas! Terus semangat!</span>";
    $penghargaanClass = "award-penyelesai";
} else {
    $penghargaan = "<span class='badge bg-secondary text-white'><i class='bi bi-emoji-smile'></i> Ayo semangat! Masih banyak yang harus dikerjakan!</span>";
    $penghargaanClass = "award-semangat";
}

// Tutup koneksi database
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Statistik Tugas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Animasi Fade-In untuk card */
        .card {
            opacity: 0;
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-size {
            font-size: 3rem;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .icon-size:hover {
            transform: rotate(360deg);
        }

        .badge-custom {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .btn-custom {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }

        .penghargaan {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            border-radius: 15px;
            display: inline-block;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .award-master {
            background-color: #ffd700;
            color: #000;
        }

        .award-penuntun {
            background-color: #28a745;
            color: white;
        }

        .award-penyelesai {
            background-color: #007bff;
            color: white;
        }

        .award-semangat {
            background-color: #6c757d;
            color: white;
        }

        .penghargaan:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Kembali ke background putih */
        body {
            background-color: #ffffff;
            color: #000;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .text-center {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4 text-primary">📊 Statistik Tugas Kamu</h2>

        <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Total Tugas</h5>
                        <h2 class="text-dark"><?= $totalTugas ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-success">
                    <div class="card-body">
                        <h5><i class="bi bi-check-circle icon-size"></i> Selesai</h5>
                        <h2><?= $selesai ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-warning">
                    <div class="card-body">
                        <h5><i class="bi bi-hourglass-split icon-size"></i> Belum Selesai</h5>
                        <h2><?= $belum ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-primary">
                    <div class="card-body">
                        <h5>Persentase</h5>
                        <h2><?= $persentase ?>%</h2>
                    </div>
                </div>
            </div>
        </div>

        <canvas id="taskChart" height="100"></canvas>

        <div class="mt-5 text-center">
            <h4>🔥 Poin Kamu: <span class="badge bg-danger badge-custom"><?= $poin ?></span></h4>
            <p class="lead">
                <?= ($persentase >= 80) ? "Keren! Kamu rajin banget!" : (($persentase >= 50) ? "Lumayan! Tetap semangat!" : "Ayo semangat lagi! 💪") ?>
            </p>
            <div class="penghargaan">
                <?= $penghargaan ?>
            </div>
            <a href="todo.php" class="btn btn-outline-primary btn-custom mt-3">← Kembali ke Halaman Tugas</a>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('taskChart').getContext('2d');
        const taskChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Belum Selesai'],
                datasets: [{
                    label: 'Jumlah Tugas',
                    data: [<?= $selesai ?>, <?= $belum ?>],
                    backgroundColor: ['#198754', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    </script>
</body>
</html>
