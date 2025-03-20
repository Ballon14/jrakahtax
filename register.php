<?php 
    include "database/db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan, pilih username lain.";
        } elseif ($password !== $confirm_password) {
            $error = "Password dan Konfirmasi Password tidak cocok.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                header("Location: index.php?register=success");
                exit();
            } else {
                $error = "Terjadi kesalahan, coba lagi.";
            }
        }

        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Registrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* Animasi untuk fade-in */
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
    <div id="register-form" class="bg-gray-800 bg-opacity-80 p-8 rounded-lg shadow-lg w-96 fade-in">
        <h2 class="text-2xl font-bold text-white mb-4 text-center">Register</h2>

        <?php if (isset($error)): ?>
            <p class="text-red-400 mb-4"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-400 mb-2">Username:</label>
                <input type="text" name="username" id="username" class="w-full p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-400 mb-2">Password:</label>
                <input type="password" name="password" id="password" class="w-full p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300" required>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-400 mb-2">Konfirmasi Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="w-full p-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded transition duration-300">Daftar</button>
        </form>

        <p class="mt-4 text-gray-400 text-sm text-center">Sudah punya akun? <a href="index.php" class="text-blue-400 hover:underline">Login di sini</a></p>
    </div>

    <!-- Tambahkan JavaScript untuk animasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.getElementById('register-form');
            setTimeout(() => {
                registerForm.classList.add('show');
            }, 100);
        });
    </script>
</body>

</html>
