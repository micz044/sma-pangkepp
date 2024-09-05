<?php
session_start();
header('Content-Type: application/json');

require_once '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, name FROM subjects WHERE id IN (SELECT subject_id FROM teacher_subjects WHERE teacher_id = (SELECT id FROM teachers WHERE user_id = ?))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode($subjects);
$conn->close();
?>
