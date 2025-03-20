<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1F2937">
    <title>Tentang Kami</title>
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
                    <a href="tambah.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Tambah Data</a>
                    <a href="laporan.php" class="block py-2 px-3 rounded-lg hover:bg-gray-700 transition">Laporan</a>
                    <a href="tentang.php" class="block py-2 px-3 rounded-lg bg-gray-700">Tentang Kami</a>
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
        <main class="flex-1 p-4 sm:p-6 sm:ml-64">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-6">Tentang Kami</h1>
            
            <!-- About Section -->
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="flex flex-col sm:flex-row">
                    <div class="w-full sm:w-1/3 h-48 sm:h-auto">
                        <img src="assets/images/logo.png" alt="logo" class="w-full h-full object-contain p-4">
                    </div>
                    <div class="p-4 sm:p-6">
                        <h2 class="text-2xl sm:text-4xl font-bold text-white mb-4">Jrakah Tax</h2>
                        <div class="space-y-4 text-gray-300">
                            <p>Di era modern ini, kepatuhan pajak bukan hanya kewajiban, tetapi juga kunci kelancaran usaha dan ketenangan finansial. Jrakah Tax hadir sebagai mitra terpercaya dalam membantu masyarakat dan pelaku usaha mengelola pajak dengan lebih mudah, akurat, dan efisien.</p>
                            <p>Kami menyediakan layanan rekapitulasi Pajak Bumi dan Bangunan (PBB), pengecekan status pembayaran, hingga konsultasi pajak untuk memastikan setiap klien memahami kewajibannya dengan baik. Dengan sistem berbasis digital dan tampilan yang user-friendly, kami berkomitmen memberikan layanan yang transparan dan dapat diandalkan.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Team Section -->
            <section class="bg-gray-800 mt-6 rounded-lg p-4 sm:p-6">
                <div class="text-center">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">Meet Our Team</h2>
                    <p class="text-gray-400 mb-6">Kami adalah tim profesional yang siap membantu Anda dalam urusan pajak.</p>
                
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Team Member 1 -->
                        <div class="bg-gray-700 p-4 sm:p-6 rounded-lg shadow-lg">
                            <img src="assets/person/man.png" alt="Team Member" 
                                 class="w-24 h-24 sm:w-32 sm:h-32 mx-auto rounded-full mb-4 border-4 border-gray-600">
                            <h3 class="text-lg sm:text-xl font-semibold text-white">Prayitno</h3>
                            <p class="text-gray-400">Owner Jrakah Tax</p>
                        </div>

                        <!-- Team Member 2 -->
                        <div class="bg-gray-700 p-4 sm:p-6 rounded-lg shadow-lg">
                            <img src="assets/person/man (1).png" alt="Team Member" 
                                 class="w-24 h-24 sm:w-32 sm:h-32 mx-auto rounded-full mb-4 border-4 border-gray-600">
                            <h3 class="text-lg sm:text-xl font-semibold text-white">Iqbal</h3>
                            <p class="text-gray-400">Team Developer Jrakah Tax</p>
                        </div>
                    </div>
                </div>
            </section>
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