<?php
if (!isset($_GET['file']) || empty($_GET['file']) || !isset($_GET['subject']) || empty($_GET['subject'])) {
    die("File atau mata pelajaran tidak ditemukan.");
}

$subject = $_GET['subject'];
$file = $_GET['file'];
$allowedSubjects = ['bahasa_indonesia', 'bahasa_inggris', 'pkn', 'matematika', 'agama'];

// Validasi mata pelajaran
if (!in_array($subject, $allowedSubjects)) {
    die("Mata pelajaran tidak valid.");
}

// Path ke file
$filePath = "../Guru/materi/" . $subject . "/" . $file;

// Debugging: Tampilkan path untuk memeriksa apakah path benar
// echo $filePath; exit;

if (!file_exists($filePath)) {
    die("File tidak ditemukan di path: " . $filePath);
}

// Set header untuk menampilkan file PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
readfile($filePath);
exit;
?>
