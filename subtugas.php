<?php
session_start();

// Fungsi untuk simpan toast ke session
function setToast($message, $type = 'success') {
    $_SESSION['toast_message'] = ['message' => $message, 'type' => $type];
}

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil ID subtugas dari URL
$id = $_GET['id_sub'] ?? null;

if (!$id) {
    echo "ID subtugas tidak ditemukan!";
    exit;
}

// Ambil data subtugas dari database
$result = $conn->query("SELECT * FROM subtasks WHERE id = $id");
$subtask = $result->fetch_assoc();

if (!$subtask) {
    echo "Subtugas tidak ditemukan!";
    exit;
}

// Jika form disubmit (update subtugas)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['subtask_name'] ?? '';
    if (!empty($new_name)) {
        $stmt = $conn->prepare("UPDATE subtasks SET subtask = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $id);
        $stmt->execute();
        $_SESSION['toast_message'] = [
            'message' => 'Subtugas berhasil diperbarui!',
            'type' => 'success'
        ];        
        header("Location: todo.php");
        exit;
    } else {
        setToast("Nama subtugas tidak boleh kosong!", "danger");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Subtugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h3>Edit Subtugas</h3>

<form method="POST">
    <div class="mb-3">
        <label for="subtask_name" class="form-label">Nama Subtugas</label>
        <input type="text" name="subtask_name" id="subtask_name" class="form-control" required
               value="<?= htmlspecialchars($subtask['subtask']) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="todo.php" class="btn btn-secondary">Batal</a>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
