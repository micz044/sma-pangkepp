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

// Ambil data dari form
$material_id = $_POST['material_id'];
$subject_name = $_POST['subject_name'];
$material_name = $_POST['material_name'];

// Tentukan tabel dan folder berdasarkan nama mata pelajaran
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
        echo "<p>Mata pelajaran tidak valid.</p>";
        exit;
}

// Proses unggahan file PDF
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
    $pdf_file = $_FILES['pdf_file'];
    $file_name = $pdf_file['name'];
    $file_tmp = $pdf_file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = array("pdf");

    if (in_array($file_ext, $allowed_ext)) {
        $file_new_name = uniqid() . "." . $file_ext;
        $upload_path = $folder_name . "/" . $file_new_name;

        // Buat folder jika belum ada
        if (!is_dir($folder_name)) {
            mkdir($folder_name, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Update data materi di database
            $sql = "UPDATE $table_name SET name = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $material_name, $file_new_name, $material_id);

            if ($stmt->execute()) {
                echo "<script>alert('Materi berhasil diperbarui!'); window.location.href='subjects-guru.php';</script>";
            } else {
                echo "<p>Gagal memperbarui materi: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Gagal mengupload file.</p>";
        }
    } else {
        echo "<p>Hanya file PDF yang diperbolehkan.</p>";
    }
} else {
    // Update data tanpa mengganti file PDF jika tidak ada file baru
    $sql = "UPDATE $table_name SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $material_name, $material_id);

    if ($stmt->execute()) {
        echo "<script>alert('Materi berhasil diperbarui!'); window.location.href='subjects-guru.php';</script>";
    } else {
        echo "<p>Gagal memperbarui materi: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

$conn->close();
?>
