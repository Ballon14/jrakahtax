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

if (!$id) {
    echo "<script>alert('ID tidak valid!'); window.location.href='dashboard.php';</script>";
    exit();
}

// Hapus data dari database
$sql = "DELETE FROM tb_datas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil dihapus!'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data!');</script>";
}

$stmt->close();
$conn->close();
?>
