<?php
session_start();
require_once '../koneksi.php';

// Ambil ID siswa dari sesi
$user_id = $_SESSION['user_id']; // Pastikan user_id disimpan dalam sesi

// Query untuk mengambil ID siswa
$sql = "SELECT id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if ($student) {
    $student_id = $student['id'];
} else {
    die("ID siswa tidak ditemukan.");
}

// Ambil data dari form
$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : null;
$student_name = isset($_POST['student_name']) ? $_POST['student_name'] : '';
$student_class = isset($_POST['student_class']) ? $_POST['student_class'] : '';

// Proses upload file
$upload_dir = 'uploads/'; // Sesuaikan dengan direktori upload Anda
$file_path = $upload_dir . basename($_FILES['file']['name']);

if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
    // Query untuk menyimpan tugas
    $sql = "INSERT INTO kumpul_tugas (task_id, student_name, student_class, file_path, submission_date, siswa_id) VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssi', $task_id, $student_name, $student_class, $file_path, $student_id);

    if ($stmt->execute()) {
        echo "Tugas berhasil disubmit!";
    } else {
        echo "Gagal menyimpan tugas: " . $stmt->error;
    }
} else {
    echo "Gagal mengupload file.";
}

$stmt->close();
$conn->close();
?>
