<?php
header('Content-Type: application/json');

require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);

$student_id = $data['student_id'];
$subject_id = $data['subject_id'];

// Cek apakah data sudah ada
$sql = "SELECT * FROM student_subjects WHERE student_id = ? AND subject_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Siswa sudah terdaftar dalam mata pelajaran ini."]);
    $conn->close();
    exit;
}

// Tambah data siswa ke mata pelajaran
$sql = "INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $subject_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Siswa berhasil ditambahkan ke mata pelajaran."]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menambahkan siswa."]);
}

$conn->close();
?>
