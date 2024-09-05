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

// Ambil ID materi dari permintaan GET
$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($material_id > 0) {

    // Cari nama file berdasarkan ID materi
    $sql = "SELECT subject_name, description FROM (
                SELECT 'matematika' AS subject_name, description FROM matematika WHERE id = ?
                UNION ALL
                SELECT 'bahasa_indonesia' AS subject_name, description FROM bahasa_indonesia WHERE id = ?
                UNION ALL
                SELECT 'bahasa_inggris' AS subject_name, description FROM bahasa_inggris WHERE id = ?
                UNION ALL
                SELECT 'agama' AS subject_name, description FROM agama WHERE id = ?
                UNION ALL
                SELECT 'pkn' AS subject_name, description FROM pkn WHERE id = ?
            ) AS materials";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $material_id, $material_id, $material_id, $material_id, $material_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $subject_name = $row['subject_name'];
        $description = $row['description'];
        
        // Hapus materi dari tabel yang relevan
        $delete_sql = "DELETE FROM $subject_name WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $material_id);
        $delete_stmt->execute();
        
        // Hapus file PDF terkait
        $file_path = "../materi/$subject_name/" . basename($description);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        echo "<script>alert('File berhasil dihapus'); window.location.href = 'subjects-guru.php';</script>";
    } else {
        echo "<script>alert('Materi tidak ditemukan'); window.location.href = 'subjects-guru.php';</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('ID materi tidak valid'); window.location.href = 'subjects-guru.php';</script>";
}
?>
