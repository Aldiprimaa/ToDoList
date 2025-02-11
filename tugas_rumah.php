<?php
session_start();

// Koneksi database
$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$todos = [];
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? '';
$current_date = date('l, d F Y');

// Periksa apakah kolom task ada
$check_columns = $conn->query("SHOW COLUMNS FROM tugas_rumah LIKE 'task'");
if ($check_columns->num_rows == 0) {
    die("Kolom 'task' tidak ditemukan di tabel tugas_rumah. Silakan periksa database Anda.");
}

// Query statistik tugas rumah
$query = "SELECT 
            COUNT(*) AS total_tasks, 
            SUM(completed) AS completed_tasks,
            SUM(CASE WHEN deadline < NOW() AND completed = 0 THEN 1 ELSE 0 END) AS overdue_tasks
          FROM tugas_rumah 
          WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

// Query untuk tugas rumah
$query = "SELECT id, task, description, deadline, priority, completed FROM tugas_rumah WHERE user_id = ? AND task LIKE ?";
if ($sort_by) {
    $query .= match($sort_by) {
        'completed' => " ORDER BY completed DESC",
        'deadline' => " ORDER BY deadline ASC",
        'priority' => " ORDER BY FIELD(priority, 'Penting', 'Sedang', 'Biasa') ASC",
        default => " ORDER BY created_at DESC"
    };
}

$stmt = $conn->prepare($query);
$like_search = "%" . ($search ?? '') . "%";
$stmt->bind_param('is', $user_id, $like_search);
$stmt->execute();
$todos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $task = $_POST['task'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];

    // Update tugas di database
    $query = "UPDATE tugas_rumah SET task = ?, description = ?, deadline = ?, priority = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssii', $task, $description, $deadline, $priority, $task_id, $user_id);
    $stmt->execute();

    header('Location: tugas_rumah.php'); // Refresh halaman setelah edit
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas Rumah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5dc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #8FBC8F !important;
        }
        .card {
            border-radius: 15px;
        }
        .list-group-item {
            background-color: #ffffff;
            border-radius: 15px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-sm:hover {
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
        .priority-indicator {
            font-weight: bold;
        }
        .priority-indicator.Penting {
            color: red;
        }
        .priority-indicator.Sedang {
            color: orange;
        }
        .priority-indicator.Biasa {
            color: green;
        }
        .modal-header {
            background-color: #8FBC8F;
            color: white;
        }
        .tooltip-inner {
            background-color: #333 !important;
        }
        .card-header {
            background-color: #8FBC8F;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 10px;
            background-color: #8FBC8F;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">🏡 Tugas Rumah</a>
        <div class="d-flex">
            <span class="text-white me-3"> <?= $current_date ?> </span>
            <a href="profile.php" class="btn btn-light me-2"><i class="bi bi-person-circle"></i> Profil</a>
            <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h1 class="text-center mb-4">Daftar Tugas Rumah</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Cari tugas rumah..." aria-describedby="searchHelp">
        <small id="searchHelp" class="form-text text-muted">Cari berdasarkan nama tugas rumah.</small>
    </form>

    <div class="d-flex justify-content-between mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bi bi-plus-circle"></i> Tambah Tugas Rumah
        </button>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Urutkan
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="?sort_by=completed">Tugas Selesai</a></li>
                <li><a class="dropdown-item" href="?sort_by=deadline">Deadline Terdekat</a></li>
                <li><a class="dropdown-item" href="?sort_by=priority">Prioritas</a></li>
            </ul>
        </div>
    </div>

    <h3 class="text-center mb-4">Statistik Tugas Rumah</h3>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-primary shadow-sm">
                <div class="card-header">
                    <h5 class="card-title">📋 Total Tugas</h5>
                </div>
                <div class="card-body text-center">
                    <p class="card-text"> <?= $data['total_tasks'] ?> tugas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-header">
                    <h5 class="card-title">✅ Tugas Selesai</h5>
                </div>
                <div class="card-body text-center">
                    <p class="card-text"> <?= $data['completed_tasks'] ?> tugas selesai</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-header">
                    <h5 class="card-title">⏳ Tugas Terlambat</h5>
                </div>
                <div class="card-body text-center">
                    <p class="card-text"> <?= $data['overdue_tasks'] ?> tugas terlambat</p>
                </div>
            </div>
        </div>
    </div>

    <div class="list-group">
        <?php foreach ($todos as $todo): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($todo['task']) ?></h5>
                    <p class="mb-1"><?= htmlspecialchars($todo['description']) ?></p>
                    <small class="text-muted">Deadline: <?= $todo['deadline'] ?></small>
                    <span class="priority-indicator <?= $todo['priority'] ?>"><?= $todo['priority'] ?></span>
                </div>
                <div>
                    <!-- Edit Button with Tooltip -->
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal" onclick="populateEditForm(<?= $todo['id'] ?>)" title="Edit Tugas">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <!-- Delete Button -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="task_id" value="<?= $todo['id'] ?>">
                        <button type="submit" name="delete_task" class="btn btn-danger btn-sm" title="Hapus Tugas">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function populateEditForm(task_id) {
        const todos = <?php echo json_encode($todos); ?>;
        const task = todos.find(todo => todo.id == task_id);

        if (task) {
            document.getElementById('task_id').value = task.id;
            document.getElementById('task').value = task.task;
            document.getElementById('description').value = task.description;
            document.getElementById('deadline').value = task.deadline;
            document.getElementById('priority').value = task.priority;
        }

        // Tampilkan modal edit
        var editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        editModal.show();
    }
</script>

</body>
</html>
