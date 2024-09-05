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

// Query data yang relevan
$sql = "SELECT 
s.id AS subject_id, 
s.name AS subject_name, 
tg.id AS tugas_id, -- Pastikan kolom ini ada
tg.title AS tugas_title, 
tg.description AS tugas_description, 
tg.due_date AS tugas_due_date
FROM 
tugas tg
JOIN 
subjects s ON tg.subject_id = s.id
WHERE 
tg.teacher_id = ?
ORDER BY 
s.name, tg.due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

// Cek hasil query
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas - Rekomendasi Tugas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('../gambar/cekolah.jpg');
            background-size: cover;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f0f0f0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            height: 40px;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 99%;
            z-index: 1000;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 50px;
            margin-right: 10px;
        }

        .school-name p {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }

        .overdue {
            color: red;
            font-weight: bold;  
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px;
            display: block;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        main {
            padding: 2rem;
            padding-top: 200px;
            position: relative;
            z-index: 0;
            width: 100%;
            height: calc(100vh - 120px);
            box-sizing: border-box;
            overflow: auto;
        }

        .recommendations {
            margin: 0 auto;
            max-width: 1200px;
        }

        .tasks {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .task-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            width: calc(50% - 20px);
            box-sizing: border-box;
            font-size: 16px;
            position: relative;
        }

        .task-box:hover {
            background-color: #f0f0f0;
            transform: scale(1.02);
        }

        .task-box .pdf-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: #4CAF50;
        }

        .recommendations h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .tasks h2 {
            text-align: center;
            margin-top: 20px;
            font-size: 1.5rem;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            width: 100%;
            position: fixed;
            bottom: 0;
            height: 30px;
            margin-top: 20px;
        }

        .button-container {
            display: flex;
            gap: 35px;
            margin-top: 20px;
        }

        .lihat-tugas-btn{
            display: block;
            text-align: center;
            width: 150px;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-left: -20px; 
        }

        .tambah-tugas-btn {
            display: block;
            text-align: center;
            width: 120px;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .tambah-tugas-btn:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .logo-container {
                margin-bottom: 1rem;
            }

            nav ul {
                flex-direction: column;
                width: 100%;
                background-color: #4CAF50;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                display: none;
                z-index: 999;
            }

            nav ul.show {
                display: flex;
            }

            nav ul li {
                margin-left: 0;
                text-align: center;
            }

            nav ul li a {
                width: 100%;
                padding: 15px 0;
            }

            main {
                padding-top: 150px;
                padding-bottom: 60px;
            }

            footer {
                padding: 1rem 0.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<header class="animate__animated animate__fadeInDown">
    <div class="logo-container">
        <div class="logo">
            <img src="../gambar/logo.png" alt="Logo">
        </div>
        <div class="school-name">
            <p>SMA Muhammadiyah Pangkep</p>
        </div>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav>
        <ul id="nav-list">
            <li><a href="teacher-dashboard.php" onclick="toggleMenu()">Dashboard</a></li>
            <li><a href="subjects-guru.php" onclick="toggleMenu()">Materi Pelajaran</a></li>
            <li><a href="takes-guru.php" onclick="toggleMenu()">Tugas</a></li>
            <li><a href="grade-guru.php" onclick="toggleMenu()">Nilai Siswa</a></li>
            <li><a href="recommendations-guru.php" onclick="toggleMenu()">Input Rekomendasi</a></li>
            <li><a href="data-siswa.php" onclick="toggleMenu()">Data Siswa</a></li>
            <li><a href="guru-profile.php" onclick="toggleMenu()">Profil</a></li>
            <li><a href="#" onclick="confirmLogout()">Logout</a></li>
        </ul>
    </nav>
</header>
<main class="animate__animated animate__fadeInUp">
    <section class="recommendations animate__animated animate__fadeIn">
        <h1>TUGAS</h1><br>
        <?php 
if ($result->num_rows > 0) {
    $current_subject = ''; // Definisikan variabel sebelum digunakan
    while ($row = $result->fetch_assoc()) {
        if (isset($row['subject_name']) && $row['subject_name'] !== $current_subject) {
            if ($current_subject !== '') {
                echo '</div>'; // Tutup div sebelumnya
            }
            $current_subject = $row['subject_name'];
            echo '<h2>' . htmlspecialchars($current_subject) . '</h2>';
            echo '<div class="tasks">';
        }

        $due_date = new DateTime($row['tugas_due_date']);
        $current_date = new DateTime();
        $overdue = $due_date < $current_date;

        if (isset($row['tugas_title'], $row['tugas_description'], $row['tugas_due_date'], $row['subject_id'])) {
            echo '<div class="task-box">';
            echo '<i class="fas fa-file-pdf pdf-icon"></i>'; // Ikon PDF

            // Menampilkan judul tugas
            echo '<h3>' . htmlspecialchars($row['tugas_title']) . '</h3>';

            // Memeriksa apakah deskripsi adalah nama file
            $description = htmlspecialchars($row['tugas_description']);
            $file_extension = pathinfo($description, PATHINFO_EXTENSION);

            // Mengecek apakah deskripsi berupa file
            if (in_array($file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])) {
                echo '<p>Tugas ini berupa file</p>';
            } else {
                echo '<p>' . $description . '</p>';
            }

            if ($overdue) { // Jika melewati deadline, tampilkan pesan
                echo '<p class="overdue">Tugas telah melewati deadline</p>';
            } else { // Jika belum melewati deadline, tampilkan batas waktu
                echo '<p><strong>Batas Waktu:</strong> ' . htmlspecialchars($row['tugas_due_date']) . '</p>';
            }

            echo '<form method="post" action="delete-task.php" style="display:inline;">
                <input type="hidden" name="tugas_id" value="' . htmlspecialchars($row['tugas_id']) . '">
                <button type="submit" onclick="return confirm(\'Apakah Anda yakin ingin menghapus tugas ini?\');" style="border:none; background:none; color:red; cursor:pointer;">
                <i class="fas fa-trash-alt"></i> Hapus
                </button>
                </form>';
            echo '</div>';
        }
    }
    echo '</div>'; // Tutup div terakhir
} else {
    echo '<p>Belum Ada Tugas Yang DIberikan</p>';
}
?>

<div class="button-container">
    <a href="tambah-tugas.php" class="tambah-tugas-btn">Tambah Tugas</a>
    <a href="daftar-siswa-kumpul.php" class="lihat-tugas-btn">Lihat Tugas Siswa</a>
</div>
    </section>
</main>
<footer>
    <p>&copy; 2024 SMA Muhammadiyah Pangkep. All rights reserved.</p>
</footer>

<script>
function toggleMenu() {
    var navList = document.getElementById('nav-list');
    navList.classList.toggle('show');
}

function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = '../login.php';
    }
}
</script>
</body>
</html>
