<?php
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nilai_tugas = $_POST['nilai_tugas'];
    $nilai_ulangan_harian = $_POST['nilai_ulangan_harian'];
    $kehadiran = $_POST['kehadiran'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];

    // Hitung rata-rata
    $total_nilai = $nilai_tugas + $nilai_ulangan_harian + $kehadiran + $nilai_uts + $nilai_uas;
    $jumlah_kolom = 5; // Jumlah kolom nilai yang digunakan untuk menghitung rata-rata
    $rata_rata = $total_nilai / $jumlah_kolom;

    $sql = "UPDATE grades 
            SET nilai_tugas = ?, nilai_ulangan_harian = ?, kehadiran = ?, nilai_uts = ?, nilai_uas = ?, grade = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddddddi", $nilai_tugas, $nilai_ulangan_harian, $kehadiran, $nilai_uts, $nilai_uas, $rata_rata, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
}

$conn->close();
?>
