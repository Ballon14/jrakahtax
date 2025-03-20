<?php
include "database/db.php";
if (!isset($conn)) {
    die("Database connection failed. Please check your configuration.");
}
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Arahkan ke dashboard sesuai peran
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
        @keyframes backgroundAnimation {
            0% { filter: brightness(1); }
            100% { filter: brightness(0.8); }
        }
    </style>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen text-gray-300">
    <div id="login-form" class="bg-gray-800 bg-opacity-80 p-8 rounded-lg shadow-lg w-96 fade-in">
        <h2 class="text-2xl font-bold text-white mb-4 text-center">Login</h2>

        <?php if (isset($error)): ?>
            <p class="text-red-400 mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-400 mb-2">Username:</label>
                <input type="text" name="username" id="username" class="w-full p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-400 mb-2">Password:</label>
                <input type="password" name="password" id="password" class="w-full p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded transition duration-300">Login</button>
            <p class="mt-4 text-gray-400 text-sm text-center">Belum punya akun? <a href="register.php" class="text-blue-400 hover:underline">Daftar di sini</a></p>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('login-form');
            setTimeout(() => {
                loginForm.classList.add('show');
            }, 100);
        });
    </script>
</body>
</html>
