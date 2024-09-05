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

// Ambil mata pelajaran yang diajarkan oleh guru
$sql = "SELECT name FROM subjects WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$subject_name = $result->fetch_assoc()['name'];

// Tentukan tabel mana yang akan diisi dan direktori penyimpanan file berdasarkan nama mata pelajaran
$table_name = "";
$folder_name = "";
switch ($subject_name) {
    case "Agama":
        $table_name = "agama";
        $folder_name = "materi/agama";
        break;
    case "PKN":
        $table_name = "pkn";
        $folder_name = "materi/pkn";
        break;
    case "Matematika":
        $table_name = "matematika";
        $folder_name = "materi/matematika";
        break;
    case "Bahasa Indonesia":
        $table_name = "bahasa_indonesia";
        $folder_name = "materi/bahasa_indonesia";
        break;
    case "Bahasa Inggris":
        $table_name = "bahasa_inggris";
        $folder_name = "materi/bahasa_inggris";
        break;
    default:
        echo "<p>Guru tidak mengajar mata pelajaran yang valid.</p>";
        exit;
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = array("pdf");

    // Validasi file upload (hanya menerima file PDF)
    if (in_array($file_ext, $allowed_ext)) {
        $file_new_name = uniqid() . "." . $file_ext;
        $upload_path = $folder_name . "/" . $file_new_name;

        // Buat folder jika belum ada
        if (!is_dir($folder_name)) {
            mkdir($folder_name, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Insert data ke tabel yang sesuai
            $sql = "INSERT INTO $table_name (name, description, teacher_id, subject_id) VALUES (?, ?, ?, (SELECT id FROM subjects WHERE name = ?))";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssis", $name, $file_new_name, $teacher_id, $subject_name);

            if ($stmt->execute()) {
                echo "<script>alert('Materi berhasil ditambahkan!'); window.location.href='subjects-guru.php';</script>";
            } else {
                echo "<p>Gagal menambahkan pertemuan: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Gagal mengupload file.</p>";
        }
    } else {
        echo "<p>Hanya file PDF yang diperbolehkan.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pertemuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <center><h1>Tambah Pertemuan</h1></center>
    <form action="add-meeting.php" method="post" enctype="multipart/form-data">
        <label for="name">Nama Pertemuan:</label>
        <input type="text" name="name" id="name" required>

        <label for="file">Upload Materi (PDF):</label>
        <input type="file" name="file" id="file" accept=".pdf" required>

        <center><button type="submit">Tambah Pertemuan</button></center>
    </form>
    <center><button type="button" onclick="window.location.href='subjects-guru.php'">Kembali</button></center>
</body>
</html>
