<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'todo_app');
if ($conn->connect_error) die("Koneksi database gagal: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profile_picture = "default.jpg"; // Default foto profil

    // Periksa apakah email sudah ada
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param('s', $email);
    $check_email->execute();
    if ($check_email->get_result()->num_rows > 0) {
        $error = "Email sudah digunakan, coba email lain.";
    } else {
        // Jika email belum ada, simpan ke database
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $full_name, $email, $password, $profile_picture);
        $success = $stmt->execute() ? "Registrasi berhasil! Silakan login." : "Gagal mendaftar, coba lagi nanti.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #4a90e2, #007aff); 
            font-family: 'Poppins', sans-serif; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container { 
            max-width: 420px; 
            padding: 30px; 
            background: white;
            border-radius: 12px; 
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15); 
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }
        .welcome-text { 
            color: #007aff; 
            font-size: 2rem; 
            font-weight: bold; 
            margin-bottom: 10px;
        }
        .form-control, .btn-primary { border-radius: 8px; }
        .btn-primary { 
            background-color: #007aff; 
            border: none; 
            transition: all 0.3s;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            background-color: #005ecb;
        }
        .alert { margin-bottom: 15px; font-weight: bold; }
        .input-group-text { background: #007aff; color: white; border-radius: 8px 0 0 8px; }
        .toggle-password i { font-size: 1.25rem; cursor: pointer; }

        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(-20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="welcome-text">Buat Akun</h1>
        <p class="text-muted">Kelola tugas harianmu dengan lebih efisien.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    <span class="input-group-text toggle-password" id="togglePassword"><i class="bi bi-eye"></i></span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2"><i class="bi bi-box-arrow-in-right"></i> Daftar</button>
        </form>
        <p class="mt-3 text-muted">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function () {
            let passwordField = document.getElementById("password");
            let icon = this.querySelector("i");
            passwordField.type = passwordField.type === "password" ? "text" : "password";
            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        });
    </script>
</body>
</html>
