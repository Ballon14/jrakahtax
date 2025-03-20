<?php
include "database/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $query = "SELECT status_pembayaran FROM tb_datas WHERE id = $id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $statusSekarang = $data['status_pembayaran'];

        // Ubah status ke sebaliknya
        $statusBaru = ($statusSekarang == "Lunas") ? "Belum Lunas" : "Lunas";

        $updateQuery = "UPDATE tb_datas SET status_pembayaran = '$statusBaru' WHERE id = $id";
        if ($conn->query($updateQuery) === TRUE) {
            header("Location: detail.php?id=" . $id); // Kembali ke halaman detail
            exit();
        } else {
            echo "Gagal mengubah status: " . $conn->error;
        }
    } else {
        echo "Data tidak ditemukan.";
    }
} else {
    echo "Permintaan tidak valid.";
}

$conn->close();
?>
