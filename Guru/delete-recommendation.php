<?php
require_once '../koneksi.php';

// Ambil recommendation_id dari GET
$recommendation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk menghapus data rekomendasi
$sql = "DELETE FROM recommendations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recommendation_id);

if ($stmt->execute()) {
    echo "<script>
            alert('Rekomendasi berhasil dihapus.');
            window.location.href = 'recommendations-guru.php'; // Ganti dengan nama halaman yang sesuai
          </script>";
} else {
    echo "<script>
            alert('Terjadi kesalahan saat menghapus rekomendasi.');
            window.location.href = 'recommendations-guru.php'; // Ganti dengan nama halaman yang sesuai
          </script>";
}

$stmt->close();
$conn->close();
?>
