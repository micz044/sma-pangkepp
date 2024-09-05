<?php
require_once '../koneksi.php';

$data = json_decode(file_get_contents('php://input'), true);

$student_id = $data['student_id'];
$subject_id = $data['subject_id'];

$sql = "DELETE FROM student_subjects WHERE student_id = ? AND subject_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $subject_id);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

echo json_encode($response);
?>
