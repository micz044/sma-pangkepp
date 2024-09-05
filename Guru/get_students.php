<?php
session_start();
header('Content-Type: application/json');

require_once '../koneksi.php';

$user_id = $_SESSION['user_id'];

// Ambil ID guru
$sql = "SELECT id FROM teachers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo json_encode([]);
    exit;
}

$teacher_id = $teacher['id'];

// Ambil mata pelajaran yang diajarkan oleh guru
$sql = "
SELECT 
    sub.id AS subject_id
FROM 
    teachers t
JOIN 
    teacher_subjects ts ON t.id = ts.teacher_id
JOIN 
    subjects sub ON ts.subject_id = sub.id
WHERE 
    t.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($subjects)) {
    echo json_encode([]);
    exit;
}

$subject_ids = array_column($subjects, 'subject_id');

// Ambil siswa yang belum terdaftar di mata pelajaran
$sql = "
SELECT DISTINCT 
    s.id AS student_id,
    s.name AS student_name,
    s.nis AS student_nis
FROM 
    students s
LEFT JOIN 
    student_subjects ss ON s.id = ss.student_id AND ss.subject_id IN (" . implode(',', array_map('intval', $subject_ids)) . ")
WHERE 
    ss.student_id IS NULL
";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$conn->close();
?>
