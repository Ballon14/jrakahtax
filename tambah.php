<?php
include "database/db.php";
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Pastikan hanya admin yang dapat menambahkan data
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Anda tidak memiliki izin untuk menambahkan data!'); window.location.href = 'dashboard.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $luas_tanah = $_POST['luas_tanah_m2'];
    $luas_bangunan = $_POST['luas_bangunan_m2'];
    $njop = $_POST['njop'];
    $njop_per_m2 = $_POST['njop_per_m2'];
    $njkp = $_POST['njkp'];
    $pbb_terutang = $_POST['pbb_terutang'];
    $status_pembayaran = $_POST['status_pembayaran'];

    $sql = "INSERT INTO tb_datas (nama, alamat, luas_tanah_m2, luas_bangunan_m2, total_njop, njop_per_m2, njkp, pbb_terutang, status_pembayaran) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiiiis", $nama, $alamat, $luas_tanah, $luas_bangunan, $njop, $njop_per_m2, $njkp, $pbb_terutang, $status_pembayaran);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href = 'data_pbb.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data!');</script>";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1F2937">
    <title>Tambah Data PBB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        .overflow-wrap-anywhere {
            overflow-wrap: anywhere;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-300">
    <div class="flex flex-col sm:flex-row min-h-screen">
        <!-- Sidebar - Hidden on mobile by default -->
        <aside class="no-print fixed w-full sm:w-64 bg-gray-800 h-screen transform sm:transform-none transition-transform duration-200 ease-in-out sidebar-fixed mobile-sidebar -translate-x-full sm:translate-x-0" 
               id="sidebar">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <a href="dashboard.php"><img src="assets/images/logo.png" alt="logo" class="max-h-12"></a>
                    <button class="sm:hidden text-gray-300 hover:text-white" onclick="toggleSidebar()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="mt-4 space-y-2">
                    <a href="dashboard.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Home</a>
                    <a href="data_pbb.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Data PBB</a>
                    <a href="tambah.php" class="block py-2 px-3 rounded-lg bg-gray-700">Tambah Data</a>
                    <a href="laporan.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Laporan</a>
                    <a href="tentang.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Tentang Kami</a>
                    <div class="pt-4 mt-4 border-t border-gray-700">
                        <a href="logout.php" class="block py-2 px-3 text-red-400 hover:bg-gray-700 rounded-lg transition">Logout</a>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Mobile Menu Button -->
        <button class="sm:hidden fixed top-4 right-4 z-50 bg-gray-800 p-2 rounded-lg" onclick="toggleSidebar()">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>

        <!-- Main Content -->
        <div class="flex-1 flex justify-center items-center">
            <main class="flex-1 p-4 sm:p-6 w-full max-w-4xl">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-6">Tambah Data PBB</h1>
            
            <div class="bg-gray-800 rounded-lg p-4 sm:p-6 w-full max-w-2xl mx-auto">
                <form method="POST" class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Nama</label>
                        <input type="text" name="nama" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Alamat</label>
                        <input type="text" name="alamat" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Luas Tanah (m²)</label>
                        <input type="number" name="luas_tanah_m2" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Luas Bangunan (m²)</label>
                        <input type="number" name="luas_bangunan_m2" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">NJOP</label>
                        <input type="number" name="njop" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">NJOP per m²</label>
                        <input type="number" name="njop_per_m2" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">NJKP</label>
                        <input type="number" name="njkp" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">PBB Terutang</label>
                        <input type="number" name="pbb_terutang" required 
                            class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Status Pembayaran</label>
                        <select name="status_pembayaran" required 
                                class="w-full p-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" 
                                class="w-full sm:w-auto px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            Tambah Data
                        </button>
                        <a href="dashboard.php" 
                        class="w-full sm:w-auto px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition text-center">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
            </main>
         </div>
        
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarButton = event.target.closest('button');
            
            if (!sidebar.contains(event.target) && !sidebarButton && window.innerWidth < 640) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
</body>
</html>