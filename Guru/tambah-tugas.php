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

// Ambil mata pelajaran yang diajarkan oleh guru yang login
$sql = "SELECT s.id, s.name 
        FROM subjects s 
        JOIN teacher_subjects ts ON s.id = ts.subject_id 
        WHERE ts.teacher_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

// Ambil mata pelajaran untuk diisi otomatis
$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// Cek jika ada data mata pelajaran
if (count($subjects) === 0) {
    echo "Tidak ada mata pelajaran yang ditemukan untuk guru ini.";
    exit;
}

// Ambil mata pelajaran pertama untuk diinput otomatis
$subject_id = $subjects[0]['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $file = $_FILES['file']['name'];

    // Validasi tanggal batas waktu
    if (strtotime($due_date) < strtotime(date('Y-m-d'))) {
        echo "Tanggal batas waktu tidak bisa sebelum hari ini.";
        exit;
    }

    // Proses upload file jika ada
    if ($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_name = basename($file); // Simpan nama file
        } else {
            echo "Gagal mengupload file.";
            exit;
        }
    } else {
        $file_name = ""; // Tidak ada file diupload
    }

    // Insert tugas baru
    $stmt = $conn->prepare("INSERT INTO tugas (title, jelas, description, due_date, teacher_id, subject_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $title, $description, $file_name, $due_date, $teacher_id, $subject_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: takes-guru.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #343a40;
        }
        form {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin: 15px 0 5px;
            color: #495057;
            font-weight: 600;
        }
        input[type="text"], textarea, input[type="date"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
            color: #495057;
            background-color: #fff;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #28a745;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .back-link a:hover {
            color: #218838;
        }
    </style>
</head>
<body>

<h2>Tambah Tugas</h2>
<form method="post" enctype="multipart/form-data">
    <label for="title">Judul Tugas:</label>
    <input type="text" id="title" name="title" required>

    <label for="description">Deskripsi:</label>
    <textarea id="description" name="description" rows="4" required></textarea>

    <label for="file">Atau Upload File:</label>
    <input type="file" id="file" name="file">

    <label for="due_date">Tanggal Batas Waktu:</label>
    <input type="date" id="due_date" name="due_date" min="<?php echo date('Y-m-d'); ?>" required>

    <!-- Mata Pelajaran disembunyikan -->
    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject_id); ?>">

    <input type="submit" value="Tambah Tugas">
</form>

<div class="back-link">
    <a href="takes-guru.php">Kembali ke Daftar Tugas</a>
</div>

</body>
</html>
