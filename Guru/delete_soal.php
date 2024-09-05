<?php
session_start();

// Koneksi ke database
require_once '../koneksi.php';

// Ambil soal_id dari parameter URL
$soal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah soal_id valid
if ($soal_id <= 0) {
    die("Soal ID not found.");
}

// Hapus soal berdasarkan soal_id
$sql = "DELETE FROM soal_rekomendasi WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $soal_id);

if ($stmt->execute()) {
    header("Location: recommendations-guru.php");
    exit;
} else {
    echo "Error deleting record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
