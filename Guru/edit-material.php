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

// Ambil ID materi dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID materi tidak tersedia.");
}
$material_id = $_GET['id'];

// Ambil mata pelajaran yang diajarkan oleh guru
$sql_subject = "SELECT s.name AS subject_name 
                 FROM subjects s 
                 JOIN agama a ON s.id = a.subject_id
                 WHERE a.id = ?
                 UNION ALL
                 SELECT s.name AS subject_name 
                 FROM subjects s 
                 JOIN bahasa_indonesia b ON s.id = b.subject_id
                 WHERE b.id = ?
                 UNION ALL
                 SELECT s.name AS subject_name 
                 FROM subjects s 
                 JOIN bahasa_inggris bi ON s.id = bi.subject_id
                 WHERE bi.id = ?
                 UNION ALL
                 SELECT s.name AS subject_name 
                 FROM subjects s 
                 JOIN matematika m ON s.id = m.subject_id
                 WHERE m.id = ?
                 UNION ALL
                 SELECT s.name AS subject_name 
                 FROM subjects s 
                 JOIN pkn p ON s.id = p.subject_id
                 WHERE p.id = ?";

$stmt_subject = $conn->prepare($sql_subject);
$stmt_subject->bind_param("iiiii", $material_id, $material_id, $material_id, $material_id, $material_id);
$stmt_subject->execute();
$result_subject = $stmt_subject->get_result();
$subject_name = $result_subject->fetch_assoc()['subject_name'];

// Tentukan tabel dan folder berdasarkan mata pelajaran
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
        die("Mata pelajaran tidak valid.");
}

// Ambil data materi dari tabel yang sesuai
$sql_material = "SELECT name, description FROM $table_name WHERE id = ?";
$stmt_material = $conn->prepare($sql_material);
$stmt_material->bind_param("i", $material_id);
$stmt_material->execute();
$result_material = $stmt_material->get_result();

if ($result_material->num_rows === 0) {
    die("Materi tidak ditemukan.");
}
$material = $result_material->fetch_assoc();

$material_name = $material['name'];
$material_description = $material['description'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Materi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }
        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        a{
            text-decoration: none;
            color: white;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="file"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="file"] {
            padding: 0;
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group:last-child {
            margin-bottom: 0;
        }
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Materi</h1>
    </header>
    <main>
        <form action="update-material.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="material_id" value="<?php echo htmlspecialchars($material_id); ?>">
            <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>">
            
            <div class="form-group">
                <label for="material_name">Nama Materi:</label>
                <input type="text" id="material_name" name="material_name" value="<?php echo htmlspecialchars($material_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="pdf_file">Unggah File PDF:</label>
                <?php if (!empty($material_description)): ?>
                    <p>File PDF saat ini: <a href="<?php echo htmlspecialchars($folder_name . '/' . $material_description); ?>" target="_blank"><?php echo htmlspecialchars($material_description); ?></a></p>
                <?php endif; ?>
                <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf">
            </div>
            
            <button type="submit">Simpan</button>
            <button><a href="subjects-guru.php">Kembali</a></button>
        </form>
    </main>
    <footer>
        &copy; <?php echo date("Y"); ?> Sistem Informasi Pembelajaran
    </footer>
</body>
</html>
