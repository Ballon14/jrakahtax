<?php 
include "database/db.php";
include "functions.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? 'user';
$keyword = isset($_GET['search']) ? $_GET['search'] : '';
$page_lunas = isset($_GET['page_lunas']) ? (int)$_GET['page_lunas'] : 1;
$page_belum = isset($_GET['page_belum']) ? (int)$_GET['page_belum'] : 1;
$limit = 10;

// Handle export request
if (isset($_GET['export'])) {
    $status = $_GET['export'];
    $searchParam = "%$keyword%";
    
    if ($status === 'lunas') {
        $query = "SELECT * FROM tb_datas WHERE status_pembayaran = 'Lunas' AND (nama LIKE ? OR alamat LIKE ?) ORDER BY nama ASC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        exportToExcel($result, 'Data_PBB_Lunas');
    } 
    elseif ($status === 'belum_lunas') {
        $query = "SELECT * FROM tb_datas WHERE status_pembayaran = 'Belum Lunas' AND (nama LIKE ? OR alamat LIKE ?) ORDER BY nama ASC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        exportToExcel($result, 'Data_PBB_Belum_Lunas');
    }
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

try {
    if (!$conn) {
        throw new Exception("Koneksi database gagal: " . mysqli_connect_error());
    }
    
    // Query untuk data yang sudah lunas
    $offset_lunas = ($page_lunas - 1) * $limit;
    $query_lunas = "SELECT * FROM tb_datas WHERE status_pembayaran = 'Lunas' AND (nama LIKE ? OR alamat LIKE ?) ORDER BY nama ASC LIMIT ? OFFSET ?";
    $stmt_lunas = $conn->prepare($query_lunas);
    $searchParam = "%$keyword%";
    $stmt_lunas->bind_param("ssii", $searchParam, $searchParam, $limit, $offset_lunas);
    $stmt_lunas->execute();
    $result_lunas = $stmt_lunas->get_result();
    
    // Query untuk data yang belum lunas
    $offset_belum = ($page_belum - 1) * $limit;
    $query_belum = "SELECT * FROM tb_datas WHERE status_pembayaran = 'Belum Lunas' AND (nama LIKE ? OR alamat LIKE ?) ORDER BY nama ASC LIMIT ? OFFSET ?";
    $stmt_belum = $conn->prepare($query_belum);
    $stmt_belum->bind_param("ssii", $searchParam, $searchParam, $limit, $offset_belum);
    $stmt_belum->execute();
    $result_belum = $stmt_belum->get_result();
    
    // Hitung total untuk pagination
    $count_lunas = $conn->prepare("SELECT COUNT(*) as total FROM tb_datas WHERE status_pembayaran = 'Lunas' AND (nama LIKE ? OR alamat LIKE ?)");
    $count_lunas->bind_param("ss", $searchParam, $searchParam);
    $count_lunas->execute();
    $total_lunas = $count_lunas->get_result()->fetch_assoc()['total'];
    $total_pages_lunas = ceil($total_lunas / $limit);
    
    $count_belum = $conn->prepare("SELECT COUNT(*) as total FROM tb_datas WHERE status_pembayaran = 'Belum Lunas' AND (nama LIKE ? OR alamat LIKE ?)");
    $count_belum->bind_param("ss", $searchParam, $searchParam);
    $count_belum->execute();
    $total_belum = $count_belum->get_result()->fetch_assoc()['total'];
    $total_pages_belum = ceil($total_belum / $limit);
    
} catch (Exception $e) {
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1F2937">
    <title>Data PBB</title>
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
        @media (min-width: 640px) {
            .sidebar-fixed {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 16rem;
                overflow-y: auto;
                z-index: 40;
            }
            .main-content-with-sidebar {
                margin-left: 16rem;
                width: calc(100% - 16rem);
                min-height: 100vh;
            }
        }
        /* Tambahan style untuk mobile sidebar */
        @media (max-width: 639px) {
            .mobile-sidebar {
                width: 100%;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 50;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-300">
    <div class="flex flex-col sm:flex-row">
        <!-- Sidebar -->
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
                    <a href="dashboard.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700">Home</a>
                    <a href="data_pbb.php" class="block py-2 px-3 rounded-lg bg-gray-700 transition">Data PBB</a>
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
        <main class="flex-1 p-4 sm:p-6 main-content-with-sidebar">
            <?php if (isset($success_message)): ?>
                <div class="bg-green-500 text-white p-4 mb-6 rounded-lg">
                    <?= $success_message ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-500 text-white p-4 mb-6 rounded-lg">
                    <?= $error_message ?>
                </div>
            <?php else: ?>
                <!-- Search Form -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Data PBB</h1>
                    <form action="" method="GET" class="w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                        <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" 
                               placeholder="Cari..." class="w-full sm:w-auto p-3 rounded-lg bg-gray-800 text-white border border-gray-600">
                        <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">Cari</button>
                    </form>
                </div>

                <!-- Data Belum Lunas -->
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg mb-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-2">
                        <h2 class="text-xl font-semibold text-red-400">Data Belum Lunas (<?= $total_belum ?>)</h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4">
                            <div class="text-sm text-gray-400">
                                Menampilkan <?= min($total_belum, $limit) ?> dari <?= $total_belum ?> data
                            </div>
                            <a href="?export=belum_lunas<?= $keyword ? '&search='.urlencode($keyword) : '' ?>" 
                               class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
                                Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <?= createTable($result_belum, $role, $page_belum, $offset_belum) ?>
                    </div>
                    <?= createPagination($page_belum, $total_pages_belum, 'page_belum', $keyword) ?>
                </div>

                <!-- Data Lunas -->
                <div class="bg-gray-800 p-4 sm:p-6 rounded-lg">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-2">
                        <h2 class="text-xl font-semibold text-green-400">Data Lunas (<?= $total_lunas ?>)</h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4">
                            <div class="text-sm text-gray-400">
                                Menampilkan <?= min($total_lunas, $limit) ?> dari <?= $total_lunas ?> data
                            </div>
                            <a href="?export=lunas<?= $keyword ? '&search='.urlencode($keyword) : '' ?>" 
                               class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
                                Export Excel
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <?= createTable($result_lunas, $role, $page_lunas, $offset_lunas) ?>
                    </div>
                    <?= createPagination($page_lunas, $total_pages_lunas, 'page_lunas', $keyword) ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

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