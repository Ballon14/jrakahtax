<?php 
include "database/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = intval($_GET['id']);

try {
    // Menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM tb_datas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Data tidak ditemukan.");
    }

    $data = $result->fetch_assoc();
} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}

// Fungsi format Rupiah
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Wajib Pajak</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-300 p-6 flex justify-center items-center min-h-screen">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-2xl font-bold mb-4 text-white text-center">Detail Wajib Pajak</h2>
        
        <div class="space-y-2 text-sm">
            <p><strong>Nama:</strong> <?= htmlspecialchars($data['nama']) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat']) ?></p>
            <p><strong>Luas Tanah:</strong> <?= htmlspecialchars($data['luas_tanah_m2']) ?> m²</p>
            <p><strong>Luas Bangunan:</strong> <?= htmlspecialchars($data['luas_bangunan_m2']) ?> m²</p>
            <p><strong>NJOP per m²:</strong> <?= formatRupiah($data['njop_per_m2']) ?></p>
            <p><strong>Total NJOP:</strong> <?= formatRupiah($data['total_njop']) ?></p>
            <p><strong>NJKP:</strong> <?= formatRupiah($data['njkp']) ?></p>
            <p><strong>PBB Terutang:</strong> <?= formatRupiah($data['pbb_terutang']) ?></p>
            <p><strong>Status Pembayaran:</strong> 
                <?php if ($data['status_pembayaran'] == "Lunas"): ?>
                    <span class="text-green-400">Lunas</span>
                <?php else: ?>
                    <span class="text-red-400">Belum Lunas</span>
                <?php endif; ?>
            </p>
        </div>

        <div class="mt-4 text-center">
            <a href="data_pbb.php" class="text-blue-400 hover:underline">Kembali ke Daftar</a>
        </div>
    </div>

    <?php 
    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close(); 
    ?>
</body>
</html>
