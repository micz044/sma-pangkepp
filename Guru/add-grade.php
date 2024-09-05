<?php
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $nilai_tugas = $_POST['nilai_tugas'];
    $nilai_ulangan_harian = $_POST['nilai_ulangan_harian'];
    $kehadiran = $_POST['kehadiran'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];

    // Hitung rata-rata
    $total_nilai = $nilai_tugas + $nilai_ulangan_harian + $kehadiran + $nilai_uts + $nilai_uas;
    $jumlah_kolom = 5; // Jumlah kolom nilai yang digunakan untuk menghitung rata-rata
    $rata_rata = $total_nilai / $jumlah_kolom;

    $sql = "INSERT INTO grades (student_id, subject_id, nilai_tugas, nilai_ulangan_harian, kehadiran, nilai_uts, nilai_uas, grade) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiddddd", $student_id, $subject_id, $nilai_tugas, $nilai_ulangan_harian, $kehadiran, $nilai_uts, $nilai_uas, $rata_rata);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
}

$conn->close();
?>
