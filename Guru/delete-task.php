<?php
session_start();
require_once '../koneksi.php';

// Cek jika teacher_id ada di session
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil teacher_id dari session
$teacher_id = $_SESSION['teacher_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tugas_id = $_POST['tugas_id'];

    // Periksa apakah ada entri terkait di kumpul_tugas
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM kumpul_tugas WHERE task_id = ?");
    $stmt_check->bind_param("i", $tugas_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        // Jika ada entri terkait, tampilkan pesan kesalahan
        echo "<script>alert('Tidak dapat menghapus tugas karena masih ada tugas yang dikumpulkan.'); window.location.href = 'takes-guru.php';</script>";
    } else {
        // Hapus tugas dari database jika tidak ada entri terkait
        $stmt = $conn->prepare("DELETE FROM tugas WHERE id = ? AND teacher_id = ?");
        $stmt->bind_param("ii", $tugas_id, $teacher_id);
        if ($stmt->execute()) {
            echo "<script>alert('Tugas berhasil dihapus'); window.location.href = 'takes-guru.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus tugas'); window.location.href = 'takes-guru.php';</script>";
        }
        $stmt->close();
    }
}

$conn->close();
?>
