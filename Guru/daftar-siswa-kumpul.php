<?php
session_start();
require_once '../koneksi.php';

// Cek jika teacher_id ada di session
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

// Ambil data dari tabel kumpul_tugas untuk tugas yang diajarkan oleh guru yang login
$sql = "SELECT 
    kt.task_id, 
    t.title AS tugas_title,
    kt.student_name, 
    kt.student_class, 
    kt.submission_date, 
    kt.file_path 
FROM 
    kumpul_tugas kt
JOIN 
    tugas t ON kt.task_id = t.id
JOIN 
    subjects s ON t.subject_id = s.id
WHERE 
    s.teacher_id = ?
ORDER BY 
    t.title ASC, kt.submission_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa yang Mengumpulkan Tugas</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        main {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .file-link {
            color: #4CAF50;
            text-decoration: none;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #45a049;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
    <header>
        <h1>Daftar Siswa yang Mengumpulkan Tugas</h1>
    </header>
    <main>
        <?php
        if ($result->num_rows > 0) {
            $current_task = '';
            while ($row = $result->fetch_assoc()) {
                if ($row['tugas_title'] !== $current_task) {
                    if ($current_task !== '') {
                        echo '</table>'; // Tutup tabel sebelumnya
                    }
                    $current_task = $row['tugas_title'];
                    echo '<h2>' . htmlspecialchars('Tugas: ' . $current_task) . '</h2>';
                    echo '<table>';
                    echo '<tr><th>Nama Siswa</th><th>Kelas</th><th>Tanggal Dikumpulkan</th><th>File</th></tr>';
                }
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['student_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['student_class']) . '</td>';
                echo '<td>' . htmlspecialchars($row['submission_date']) . '</td>';
                echo '<td><a href="' . htmlspecialchars($row['file_path']) . '" class="file-link" target="_blank">Lihat File</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>Tidak ada siswa yang telah mengumpulkan tugas.</p>';
        }
        ?>
        <a href="takes-guru.php" class="back-btn">Kembali</a>
    </main>
    <footer>
        <p>&copy; 2024 SMA Muhammadiyah Pangkep. All rights reserved.</p>
    </footer>
</body>
</html>
