<?php
session_start();
include 'koneksi.php';

function setToast($message, $type = 'success') {
    $_SESSION['toast_message'] = ['message' => $message, 'type' => $type];
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task = trim($_POST['task']);
    $description = trim($_POST['description']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority']; 
    $user_id = $_SESSION['user_id'];

    if (empty($task) || empty($description) || empty($deadline) || empty($priority)) {
        setToast("Harap isi semua kolom!", "danger");
        header('Location: todo.php');
        exit;
    }

    $today = date('Y-m-d');
    if ($deadline < $today) {
        setToast("Tanggal deadline tidak boleh di masa lalu!", "danger");
        header('Location: todo.php');
        exit;
    }

    $allowed_priorities = ['Penting', 'Sedang', 'Biasa'];
    if (!in_array($priority, $allowed_priorities)) {
        setToast("Prioritas tidak valid!", "danger");
        header('Location: todo.php');
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task, description, deadline, priority) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $user_id, $task, $description, $deadline, $priority);

    if ($stmt->execute()) {
        setToast("Tugas berhasil ditambahkan!", "success");
    } else {
        setToast("Gagal menambahkan tugas!", "danger");
    }

    header('Location: todo.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Tambah Tugas</h2>

    <?php if (isset($_SESSION['toast_message'])): ?>
        <div class="alert alert-<?= $_SESSION['toast_message']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['toast_message']['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['toast_message']); ?>
    <?php endif; ?>

    <form method="POST" action="tambahtugas.php">
        <label>Nama Tugas:</label><br>
        <input type="text" name="judul" class="form-control mb-3" required><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" class="form-control mb-3" required></textarea><br>

        <label>Deadline:</label><br>
        <input type="date" name="deadline" class="form-control mb-3" required><br>

        <label>Prioritas:</label><br>
        <select name="prioritas" class="form-select mb-3" required>
            <option value="Low">Biasa</option>
            <option value="Medium">Sedang</option>
            <option value="High">Penting</option>
        </select><br>

        <button type="submit" class="btn btn-success">Tambah</button>
        <a href="todo.php" class="btn btn-secondary">Batal</a>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
