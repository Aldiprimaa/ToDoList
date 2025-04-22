<?php
session_start();

function setToast($message)
{
    $_SESSION['toast_message'] = $message;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];
$todos = [];
$subtasks = [];
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? '';
$current_date = date('l, d F Y');

// Ambil foto profil pengguna
$queryProfil = mysqli_query($conn, "SELECT foto FROM profil WHERE user_id = '$user_id'");
$profil = mysqli_fetch_assoc($queryProfil);
$foto_profil = isset($profil['foto']) ? $profil['foto'] : 'default.jpg';
// Mengambil data tugas dari database
$tanggal_sekarang = date('Y-m-d');
$username = $_SESSION['username'] ?? '';

$query = "SELECT * FROM tasks WHERE user_id = '$user_id' AND status != 'selesai'";
$result = mysqli_query($conn, $query);

$tugasTenggat = [];
while ($row = mysqli_fetch_assoc($result)) {
    $deadline = $row['deadline'];
    $nama_tugas = $row['task'];

    // Cek jika tenggat waktu tugas dalam beberapa hari ke depan dan tugas belum selesai
    $tanggal_deadline = strtotime($deadline);
    $tanggal_sekarang = strtotime($tanggal_sekarang);
    $selisih_hari = ($tanggal_deadline - $tanggal_sekarang) / (60 * 60 * 24);

    // Menambahkan tugas yang tenggat waktunya dalam 3 hari dan belum selesai
    if ($selisih_hari > 0 && $selisih_hari <= 3) {
        $tugasTenggat[] = $nama_tugas;
    }
}

// Jika ada tugas yang hampir tenggat
if (!empty($tugasTenggat)) {
    echo "<script>
        alert('⚠️ Peringatan! Tugas hampir tenggat: " . implode(', ', $tugasTenggat) . "' );
    </script>";
}

$query = "SELECT * FROM tasks WHERE user_id = ? AND task LIKE ?";
if ($sort_by == 'completed') {
    $query .= " ORDER BY completed DESC";
} elseif ($sort_by == 'deadline') {
    $query .= " ORDER BY deadline ASC";
} elseif ($sort_by == 'priority') {
    $query .= " ORDER BY CASE 
                WHEN priority = 'Penting' THEN 1
                WHEN priority = 'Sedang' THEN 2
                WHEN priority = 'Biasa' THEN 3
                ELSE 4 
              END ASC";
} else {
    $query .= " ORDER BY created_at DESC";
}

$stmt = $conn->prepare($query);
$like_search = "%$search%";
$stmt->bind_param('is', $user_id, $like_search);
$stmt->execute();
$todos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($todos as $todo) {
    $stmt = $conn->prepare("SELECT * FROM subtasks WHERE task_id = ?");
    $stmt->bind_param('i', $todo['id']);
    $stmt->execute();
    $subtasks[$todo['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'added') {
            $_SESSION['success'] = "Tugas berhasil ditambahkan!";
        } elseif ($_GET['status'] == 'edited') {
            $_SESSION['success'] = "Tugas berhasil diedit!";
        } elseif ($_GET['status'] == 'deleted') {
            $_SESSION['success'] = "Tugas berhasil dihapus!";
        }
    }

    if (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        $stmt = $conn->prepare("DELETE FROM subtasks WHERE task_id = ?");
        $stmt->bind_param('i', $task_id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param('i', $task_id);
        $stmt->execute();

        setToast("Tugas berhasil dihapus!");
        header("Location: todo.php");
        exit;
    }

    if (isset($_POST['delete_subtask'])) {
        $stmt = $conn->prepare("DELETE FROM subtasks WHERE id = ?");
        $stmt->bind_param('i', $_POST['subtask_id']);
        $stmt->execute();

        setToast("Subtugas berhasil dihapus!");
        header('Location: todo.php');
        exit;
    }
    if (isset($_POST['complete_task'])) {
        $task_id = intval($_POST['task_id']);

        if ($task_id > 0) {
            $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ?");
            $stmt->bind_param('i', $task_id);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE subtasks SET completed = 1 WHERE task_id = ?");
            $stmt->bind_param('i', $task_id);
            $stmt->execute();

            setToast("Tugas ditandai selesai!");
        } else {
            setToast("ID tugas tidak valid!", "error");
        }

        header('Location: todo.php');
        exit;
    }
    if (isset($_POST['add_subtask'])) {
        $task_id = $_POST['task_id'];
        $subtask = $_POST['subtask'] ?? null;

        if (!empty($subtask)) {
            $stmt = $conn->prepare("INSERT INTO subtasks (task_id, subtask) VALUES (?, ?)");
            $stmt->bind_param('is', $task_id, $subtask);
            $stmt->execute();
            header('Location: todo.php');
            exit;
        } else {
            echo "Subtugas tidak boleh kosong!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom, #e3f2fd, #ffffff);
            color: #333;
            padding-bottom: 60px;
        }

        .container {
            max-width: 960px;
            margin-top: 30px;
        }

        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 12px;
            margin-bottom: 25px;
            background-color: #ffffff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
        }

        .card-header {
            font-weight: bold;
            font-size: 1.1rem;
            background: #2196f3;
            color: white;
            padding: 12px 20px;
            border-radius: 12px 12px 0 0;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #007bff;
        }

        .card-text {
            font-size: 1rem;
            color: #444;
            margin-bottom: 10px;
        }

        .statistik-sekolah .card-text {
            font-size: 1.6rem;
            font-weight: 600;
            color: #333;
        }

        .priority-label {
            display: inline-block;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 25px;
            letter-spacing: 0.5px;
        }

        .badge-important {
            background-color: #e53935;
            color: #fff;
        }

        .badge-medium {
            background-color: #fb8c00;
            color: #fff;
        }

        .badge-normal {
            background-color: #43a047;
            color: #fff;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 16px;
            font-size: 0.95rem;
        }

        .btn-primary {
            background-color: #1976d2;
            border: none;
        }

        .btn-danger {
            background-color: #d32f2f;
            border: none;
        }

        .btn-warning {
            background-color: #fbc02d;
            border: none;
            color: #333;
        }

        .btn-success {
            background-color: #388e3c;
            border: none;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.95rem;
            border: 1px solid #ced4da;
        }

        .subtask-list {
            margin-top: 15px;
            padding-left: 20px;
        }

        .subtask {
            margin-top: 15px;
            padding: 12px 15px;
            background-color: #f1f8ff;
            border-left: 5px solid #2196f3;
            border-radius: 8px;
        }

        .subtask-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .subtask-item label {
            font-size: 1rem;
            margin-left: 10px;
        }

        .subtask-actions {
            display: flex;
            gap: 6px;
        }

        .subtask-actions button {
            padding: 5px 10px;
            font-size: 0.875rem;
            border-radius: 6px;
        }

        .edit-subtask-form {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-top: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .welcome-text {
            font-size: 1.3rem;
            animation: fadeIn 1s ease-out;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="todo.php">
                📝 Daftar Tugas
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white"><?= $current_date ?></span>

                <!-- Dropdown Setting -->
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="settingsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                        <li><a class="dropdown-item" href="panduan.php">📘 Panduan Pengguna</a></li>
                        <li><a class="dropdown-item" href="statistik.php">📊 Lihat Skor</a></li>
                        <li><a class="dropdown-item" href="hubungi.php"><i class="bi bi-envelope"></i> Hubungi Kami</a>
                        </li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#aiModal">🤖
                                Assistant AI</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php">🚪 Keluar</a></li>
                    </ul>
                </div>

                <!-- Foto Profil -->
                <a href="profil.php" class="d-flex align-items-center text-white text-decoration-none ms-3">
                    <img src="<?= $foto_profil ?>" alt="Profil" width="36" height="36" class="rounded-circle me-2"
                        style="object-fit: cover; border: 2px solid white;">
                    <span class="d-none d-md-inline">Profil</span>
                </a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <h1 class="text-center mb-4">Daftar Tugas</h1>
        <form method="GET" class="mb-4">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control"
                placeholder="Cari tugas...">
        </form>
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bi bi-plus-circle"></i> Tambah Tugas
        </button>
        <div class="d-flex align-items-center">
            <label for="sort" class="form-label mb-0 me-2">Sortir Berdasarkan:</label>
            <select id="sort" class="form-select w-auto" onchange="location = this.value;">
                <option value="todo.php?sort_by=">Pilih</option>
                <option value="todo.php?sort_by=completed">Selesai</option>
                <option value="todo.php?sort_by=deadline">Tenggat Waktu</option>
                <option value="todo.php?sort_by=priority">Prioritas</option>
            </select>
        </div>
        <?php if (isset($_SESSION['full_name'])): ?>
            <div class="alert alert-success mt-3" role="alert">
                👋 Selamat datang, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>!
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['toast_message'])): ?>
            <?php
            $toast = $_SESSION['toast_message'];
            $toast_type = is_array($toast) ? $toast['type'] : 'success';
            $toast_msg = is_array($toast) ? $toast['message'] : $toast;
            ?>
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show align-items-center text-bg-<?= $toast_type ?> border-0 shadow" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?= $toast_msg ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['toast_message']); ?>
        <?php endif; ?>
        <div class="row g-3 mt-4">
            <?php if ($todos): ?>
                <?php foreach ($todos as $todo): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($todo['task']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($todo['description']) ?></p>
                                <p class="card-text"><strong>Tenggat Waktu:</strong> <?= htmlspecialchars($todo['deadline']) ?>
                                </p>
                                <p
                                    class="badge <?= $todo['priority'] == 'Penting' ? 'badge-important' : ($todo['priority'] == 'Sedang' ? 'badge-medium' : 'badge-normal') ?>">
                                    <?= $todo['priority'] == 'Penting' ? 'Penting' : ($todo['priority'] == 'Sedang' ? 'Sedang' : 'Biasa') ?>
                                </p>
                                <form method="POST" class="d-inline-block">
                                    <input type="hidden" name="task_id" value="<?= $todo['id'] ?>">

                                    <!-- Tombol Selesai -->
                                    <button type="button" class="btn btn-success btn-sm"
                                        onclick="confirmComplete(<?= $todo['id'] ?>)">
                                        <i class="bi bi-check-circle"></i> Selesai
                                    </button>

                                    <!-- Jika sudah selesai, disable tombol -->
                                    <?php if ($todo['completed']): ?>
                                        <script>
                                            document.querySelector('button[onclick="confirmComplete(<?= $todo['id'] ?>)"]').disabled = true;
                                        </script>
                                    <?php endif; ?>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm delete-task-btn" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-task_id="<?= $todo['id'] ?>">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                                <?php
                                $current_date = date("Y-m-d H:i:s");
                                if (!$todo['completed'] && $todo['deadline'] > $current_date): ?>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal"
                                        data-task_id="<?= $todo['id'] ?>" data-task="<?= $todo['task'] ?>"
                                        data-description="<?= $todo['description'] ?>" data-deadline="<?= $todo['deadline'] ?>"
                                        data-priority="<?= $todo['priority'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                <?php endif; ?>
                                <?php if (isset($subtasks[$todo['id']]) && count($subtasks[$todo['id']]) > 0): ?>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="collapse"
                                        data-bs-target="#subtasks-<?= $todo['id'] ?>" aria-expanded="false"
                                        aria-controls="subtasks-<?= $todo['id'] ?>">
                                        Lihat Subtugas
                                    </button>
                                <?php endif; ?>
                                <div class="collapse" id="subtasks-<?= $todo['id'] ?>">
                                    <div class="card card-body mt-2">
                                        <form method="POST">
                                            <ul class="list-group">
                                                <?php foreach ($subtasks[$todo['id']] as $subtask): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <?= htmlspecialchars($subtask['subtask']) ?>
                                                        <div class="subtask-actions">
                                                            <?php if (!$todo['completed'] && strtotime($todo['deadline']) >= time()): ?>
                                                                <a href="subtugas.php?id_sub=<?= $subtask['id'] ?>"
                                                                    class="btn btn-warning btn-sm">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (strtotime($todo['deadline']) >= time()): ?>
                                                                <form method="POST" class="d-inline-block">
                                                                    <input type="hidden" name="subtask_id"
                                                                        value="<?= $subtask['id'] ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        name="delete_subtask">
                                                                        <i class="bi bi-trash"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <?php if (strtotime($todo['deadline']) >= time()): ?>
                                    <form method="POST" class="d-inline-block">
                                        <input type="hidden" name="task_id" value="<?= $todo['id'] ?>">
                                        <input type="text" name="subtask" class="form-control form-control-sm"
                                            placeholder="Subtugas...">
                                        <button type="submit" class="btn btn-success btn-sm mt-2" name="add_subtask">
                                            <i class="bi bi-plus"></i> Tambah Subtugas
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada tugas yang ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Modal Tambah Tugas -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">Tambah Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="tambahtugas.php">
                        <div class="mb-3">
                            <label for="task" class="form-label">Nama Tugas</label>
                            <input type="text" class="form-control" id="task" name="task"
                                placeholder="Masukkan nama tugas" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Tenggat Waktu</label>
                            <input type="datetime-local" class="form-control" id="deadline" name="deadline" required>
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioritas</label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="Penting">Penting</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Biasa">Biasa</option>
                            </select>
                        </div>
                        <button type="submit" name="add_task" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Tambah Tugas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="todo.php"> <!-- Pastikan action menuju ke file handler -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus tugas ini?</p>
                        <input type="hidden" name="task_id" id="delete_task_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delete_task" class="btn btn-danger">Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Task -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="edit.php" onsubmit="return confirmEdit()">
                        <input type="hidden" name="task_id" id="edit_task_id">
                        <div class="mb-3">
                            <label for="edit_task" class="form-label">Nama Tugas</label>
                            <input type="text" class="form-control" id="edit_task" name="task" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deadline" class="form-label">Tenggat Waktu</label>
                            <input type="datetime-local" class="form-control" id="edit_deadline" name="deadline"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label for="edit_priority">Prioritas:</label>
                            <select id="edit_priority" name="priority" class="form-select">
                                <option value="Biasa">Biasa</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Penting">Penting</option>
                            </select>
                        </div>
                        <button type="submit" name="edit_task" class="btn btn-warning">Perbarui Tugas</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Konfirmasi Selesai -->
    <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="completeTaskForm" method="POST" action="todo.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeModalLabel">Konfirmasi Selesai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menandai tugas ini sebagai selesai?</p>
                        <input type="hidden" name="task_id" id="complete_task_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="complete_task" class="btn btn-success">Tandai Selesai</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- Include Modal Asisten AI -->
    <?php include 'ai_assistant.php'; ?>
    <script>

        function confirmComplete(taskId) {
            document.getElementById('complete_task_id').value = taskId;
            var completeModal = new bootstrap.Modal(document.getElementById('completeModal'));
            completeModal.show();
        }
        function confirmDelete(taskId) {
            document.getElementById('delete_task_id').value = taskId;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
        function setMinDeadline() {
            let now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('deadline').min = now.toISOString().slice(0, 16);
        }

        function confirmEdit() {
            return confirm("Apakah Anda yakin ingin mengedit tugas ini?");
        }

        function confirmEditSubtask() {
            return confirm("Apakah Anda yakin ingin mengedit subtugas ini?");
        }

        function confirmAddTask() {
            return confirm("Apakah Anda yakin ingin menambahkan tugas ini?");
        }

        document.addEventListener('DOMContentLoaded', () => {
            setMinDeadline();

            const editTaskModal = document.getElementById('editTaskModal');
            if (editTaskModal) {
                editTaskModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    document.getElementById('edit_task_id').value = button.getAttribute('data-task_id');
                    document.getElementById('edit_task').value = button.getAttribute('data-task');
                    document.getElementById('edit_description').value = button.getAttribute('data-description');
                    document.getElementById('edit_deadline').value = button.getAttribute('data-deadline');

                    const priority = button.getAttribute('data-priority');
                    const editPriority = document.getElementById('edit_priority');
                    const validPriorities = ["Penting", "Sedang", "Biasa"];
                    editPriority.value = validPriorities.includes(priority) ? priority : "Biasa";
                });
            }

            document.querySelectorAll('.edit-subtask-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const subtaskId = this.getAttribute('data-subtask_id');
                    const subtaskName = this.getAttribute('data-subtask');
                    document.getElementById('edit_subtask_id').value = subtaskId;
                    document.getElementById('edit_subtask_name').value = subtaskName;
                });
            });

            document.querySelectorAll('.toast').forEach(toast => {
                const toastElement = new bootstrap.Toast(toast);
                toastElement.show();
            });

            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const taskId = button.getAttribute('data-task_id');
                    document.getElementById('delete_task_id').value = taskId;
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
