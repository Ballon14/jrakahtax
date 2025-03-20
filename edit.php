<?php 
include "database/db.php";
session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id || !is_numeric($id)) {
    echo "<script>alert('ID tidak valid!'); window.location.href='dashboard.php';</script>";
    exit();
}

// Ambil data dari database
$sql = "SELECT * FROM tb_datas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='dashboard.php';</script>";
    exit();
}

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $luas_tanah = $_POST['luas_tanah_m2'];
    $luas_bangunan = $_POST['luas_bangunan_m2'];
    $njop_per_m2 = $_POST['njop_per_m2'];
    $njkp = $_POST['njkp'];
    $pbb_terutang = $_POST['pbb_terutang'];
    $status_pembayaran = $_POST['status_pembayaran'];

    $update_sql = "UPDATE tb_datas SET nama=?, alamat=?, luas_tanah_m2=?, luas_bangunan_m2=?, njop_per_m2=?, njkp=?, pbb_terutang=?, status_pembayaran=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssiiiissi", $nama, $alamat, $luas_tanah, $luas_bangunan, $njop_per_m2, $njkp, $pbb_terutang, $status_pembayaran, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='data_pbb.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data! Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data PBB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-300 p-6">
    <div class="container mx-auto bg-gray-800 p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-3xl font-bold text-white mb-4">Edit Data Wajib Pajak</h2>
        <form method="POST" class="space-y-4">
            <label class="block">Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">Alamat</label>
            <input type="text" name="alamat" value="<?= htmlspecialchars($data['alamat']) ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">Luas Tanah (m²)</label>
            <input type="number" name="luas_tanah_m2" value="<?= $data['luas_tanah_m2'] ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">Luas Bangunan (m²)</label>
            <input type="number" name="luas_bangunan_m2" value="<?= $data['luas_bangunan_m2'] ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">NJOP per m²</label>
            <input type="number" name="njop_per_m2" value="<?= $data['njop_per_m2'] ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">NJKP</label>
            <input type="number" name="njkp" value="<?= $data['njkp'] ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">PBB Terutang</label>
            <input type="number" name="pbb_terutang" value="<?= $data['pbb_terutang'] ?>" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">

            <label class="block">Status Pembayaran</label>
            <select name="status_pembayaran" required class="p-2 w-full bg-gray-700 text-white rounded border border-gray-600">
                <option value="Lunas" <?= $data['status_pembayaran'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                <option value="Belum Lunas" <?= $data['status_pembayaran'] == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
            </select>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">Simpan Perubahan</button>
        </form>

        <div class="mt-4">
            <a href="data_pbb.php" class="text-blue-400 hover:underline">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
