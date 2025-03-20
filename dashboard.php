<?php
include "database/db.php";
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? 'user';

try {
    // Query default
    $query = "SELECT 
                COUNT(*) as total_wajib_pajak,
                SUM(CASE WHEN status_pembayaran = 'Lunas' THEN 1 ELSE 0 END) as total_lunas,
                SUM(CASE WHEN status_pembayaran = 'Belum Lunas' THEN 1 ELSE 0 END) as total_belum_lunas,
                SUM(pbb_terutang) as total_pbb,
                SUM(CASE WHEN status_pembayaran = 'Lunas' THEN pbb_terutang ELSE 0 END) as total_terbayar,
                SUM(CASE WHEN status_pembayaran = 'Belum Lunas' THEN pbb_terutang ELSE 0 END) as total_belum_terbayar
              FROM tb_datas";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc() ?? [
        'total_wajib_pajak' => 0,
        'total_lunas' => 0,
        'total_belum_lunas' => 0,
        'total_pbb' => 0,
        'total_terbayar' => 0,
        'total_belum_terbayar' => 0
    ];
} catch (Exception $e) {
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1F2937">
    <title>Dashboard PBB</title>
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
                    <a href="dashboard.php" class="block py-2 px-3 rounded-lg bg-gray-700">Home</a>
                    <a href="data_pbb.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Data PBB</a>
                    <a href="tambah.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Tambah Data</a>
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
        <main class="flex-1 p-4 sm:p-6 w-full sm:ml-64">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-6">Dashboard PBB</h1>
            
            <!-- Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg">
                    <h3 class="text-base sm:text-lg font-semibold">Total Wajib Pajak</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-400 mt-2"><?= $data['total_wajib_pajak'] ?></p>
                </div>
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg">
                    <h3 class="text-base sm:text-lg font-semibold">Status Pembayaran</h3>
                    <p class="mt-2">Lunas: <span class="text-green-400"><?= $data['total_lunas'] ?></span></p>
                    <p>Belum Lunas: <span class="text-red-400"><?= $data['total_belum_lunas'] ?></span></p>
                </div>
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg">
                    <h3 class="text-base sm:text-lg font-semibold">Total PBB</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-400 mt-2 overflow-wrap-anywhere"><?= formatRupiah($data['total_pbb']) ?></p>
                </div>
            </div>

            <!-- Second Row Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg">
                    <h3 class="text-base sm:text-lg font-semibold">Total Sudah Terbayar</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-green-400 mt-2 overflow-wrap-anywhere"><?= formatRupiah($data['total_terbayar']) ?></p>
                </div>
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg">
                    <h3 class="text-base sm:text-lg font-semibold">Total Belum Terbayar</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-red-400 mt-2 overflow-wrap-anywhere"><?= formatRupiah($data['total_belum_terbayar']) ?></p>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-gray-800 p-4 sm:p-6 rounded-lg mt-4 sm:mt-6 overflow-x-auto">
                <h3 class="text-xl sm:text-2xl font-semibold mb-4">Rincian Pembayaran</h3>
                <div class="min-w-full inline-block align-middle">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="py-3 px-4 text-sm sm:text-base">Kategori</th>
                                <th class="py-3 px-4 text-right text-sm sm:text-base">Jumlah</th>
                                <th class="py-3 px-4 text-right text-sm sm:text-base">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-700">
                                <td class="py-3 px-4 text-sm sm:text-base">Total Terbayar</td>
                                <td class="py-3 px-4 text-right text-green-400 text-sm sm:text-base overflow-wrap-anywhere">
                                    <?= formatRupiah($data['total_terbayar']) ?>
                                </td>
                                <td class="py-3 px-4 text-right text-green-400 text-sm sm:text-base">
                                    <?= ($data['total_pbb'] > 0) ? number_format(($data['total_terbayar'] / $data['total_pbb']) * 100, 1) : '0' ?>%
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 text-sm sm:text-base">Belum Terbayar</td>
                                <td class="py-3 px-4 text-right text-red-400 text-sm sm:text-base overflow-wrap-anywhere">
                                    <?= formatRupiah($data['total_belum_terbayar']) ?>
                                </td>
                                <td class="py-3 px-4 text-right text-red-400 text-sm sm:text-base">
                                    <?= ($data['total_pbb'] > 0) ? number_format(($data['total_belum_terbayar'] / $data['total_pbb']) * 100, 1) : '0' ?>%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript untuk toggle sidebar -->
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