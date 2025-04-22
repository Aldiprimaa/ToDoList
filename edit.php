<?php
session_start();
include 'koneksi.php'; // pastikan koneksi sesuai

// Function untuk simpan pesan toast ke session
function setToast($message, $type = 'success') {
    $_SESSION['toast_message'] = ['message' => $message, 'type' => $type];
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Jika GET: Tampilkan form edit
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM tasks WHERE id = $id");

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Edit Tugas</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="container mt-5">
            <h2>Edit Tugas</h2>
            <form method="POST" action="edit.php">
                <input type="hidden" name="task_id" value="<?= $data['id'] ?>">
                <label>Nama Tugas:</label><br>
                <input type="text" name="task" class="form-control mb-3" value="<?= htmlspecialchars($data['task']) ?>" required><br>
                <label>Deskripsi:</label><br>
                <textarea name="description" class="form-control mb-3" required><?= htmlspecialchars($data['description']) ?></textarea><br>
                <label>Prioritas:</label><br>
                <select name="priority" class="form-select mb-3" required>
                    <option value="Low" <?= $data['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                    <option value="Medium" <?= $data['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="High" <?= $data['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                </select><br>
                <button type="submit" name="edit_task" class="btn btn-primary">Simpan</button>
                <a href="todo.php" class="btn btn-secondary">Batal</a>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Tugas tidak ditemukan.";
    }
}
// Jika POST: Simpan perubahan
elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $task_name = $_POST['task'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];

    if (!empty($task_name) && !empty($description) && !empty($priority)) {
        $stmt = $conn->prepare("UPDATE tasks SET task = ?, description = ?, priority = ? WHERE id = ?");
        $stmt->bind_param('sssi', $task_name, $description, $priority, $task_id);
        $stmt->execute();

        setToast("Tugas berhasil diperbarui!", "success");
        header("Location: todo.php");
        exit;
    } else {
        echo "Harap isi semua kolom!";
    }
}
?>
