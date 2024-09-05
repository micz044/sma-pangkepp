<?php
session_start();
require_once '../koneksi.php';

// Ambil ID siswa dari sesi
$student_id = $_SESSION['student_id']; // Pastikan student_id disimpan dalam sesi

// Query untuk mengambil tugas yang belum melewati tanggal pengumpulan dan belum dikerjakan
$sql = "SELECT 
    s.name AS subject_name, 
    tg.id AS task_id, 
    tg.title AS tugas_title, 
    tg.description AS tugas_description, 
    tg.due_date AS tugas_due_date,
    CASE 
        WHEN kt.id IS NOT NULL THEN 'dikerjakan'
        ELSE 'belum_dikerjakan'
    END AS status
FROM 
    tugas tg 
JOIN 
    subjects s ON tg.subject_id = s.id 
LEFT JOIN 
    kumpul_tugas kt ON tg.id = kt.task_id AND kt.siswa_id = ?
WHERE 
    tg.due_date >= CURDATE() 
ORDER BY 
    s.name, tg.due_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas - Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(../gambar/cekolah.jpg);
            background-size: cover;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f0f0f0;
            overflow-x: hidden;
        }

        a{
            text-decoration: none;
            color: #000;
        }

        body::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        body {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
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
            top: 0;
            left: 0;
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
            padding-top: 120px;
            margin-bottom: 80px;
        }

        .task-card.completed {
            background-color: #e0e0e0;
            color: #666;
            cursor: not-allowed;
        }

        .task-card.completed a {
            pointer-events: none;
        }

        .task-card.completed::after {
            content: "Tugas telah dikerjakan";
            display: block;
            margin-top: 10px;
            color: #888;
            font-style: italic;
        }

        .tasks {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation-duration: 1s;
        }

        .tasks h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .task-card {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            position: fixed;
            height: 20px;
            width: 100%;
            bottom: 0;
            z-index: 1000;
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
                flex-direction: column;
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
                padding-top: 140px;
                margin-bottom: 120px;
            }

            footer {
                padding: 1rem 0.5rem;
                font-size: 0.9rem;
            }
        }

        .animate__animated {
            animation-duration: 1s;
        }

        .animate__fadeInDown {
            animation-name: fadeInDown;
        }

        .animate__fadeInUp {
            animation-name: fadeInUp;
        }

        .animate__fadeIn {
            animation-name: fadeIn;
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
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
                <li><a href="student-dashboard.php" onclick="toggleMenu()">Dashboard</a></li>
                <li><a href="subjects.php" onclick="toggleMenu()">Materi Pelajaran</a></li>
                <li><a href="takes.php" onclick="toggleMenu()">Tugas</a></li>
                <li><a href="nilai.php" onclick="toggleMenu()">Nilai</a></li>
                <li><a href="recommendations.php" onclick="toggleMenu()">Rekomendasi Pelajaran</a></li>
                <li><a href="siswa-profile.php" onclick="toggleMenu()">Profil</a></li>
                <li><a href="#" onclick="confirmLogout()">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="animate__animated animate__fadeInUp">
        <section class="tasks animate__animated animate__fadeIn">
            <h1>DAFTAR TUGAS</h1>
            <div class="task-list" id="task-list">
            <?php
                if ($result->num_rows > 0) {
                    $current_subject = '';
                    $task_counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        if ($row['subject_name'] !== $current_subject) {
                            if ($current_subject !== '') {
                                echo '</div>'; // Tutup div sebelumnya
                            }
                            $current_subject = $row['subject_name'];
                            $task_counter = 1; // Reset counter saat subjek berubah
                            echo '<h2>' . htmlspecialchars($current_subject) . '</h2>';
                            echo '<div class="task-list">';
                        }
                        
                        $task_class = $row['status'] === 'dikerjakan' ? 'task-card completed' : 'task-card';

                        echo '<div class="' . htmlspecialchars($task_class) . '">';
                        echo '<a href="task-detail.php?id=' . htmlspecialchars($row['task_id']) . '" class="task-link">';
                        echo '<h3>Tugas ' . $task_counter++ . '</h3>'; // Tambahkan penomoran tugas

                        // Menampilkan judul tugas
                        echo '<p><strong>Judul:</strong> ' . htmlspecialchars($row['tugas_title']) . '</p>';
                        echo '<p><strong>Batas Waktu:</strong> ' . htmlspecialchars($row['tugas_due_date']) . '</p>';
                        echo '</a>'; // Akhir dari link
                        echo '</div>';
                    }
                    echo '</div>'; // Tutup div terakhir
                } else {
                    echo "<p>Tidak ada tugas yang tersedia.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Rekomendasi Bahan Pelajaran. Semua hak dilindungi.</p>
    </footer>

    <script>
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = '../login.php'; // Ganti dengan halaman logout sesuai kebutuhan
            }
        }

        function toggleMenu() {
            const navList = document.getElementById('nav-list');
            navList.classList.toggle('show');
        }
    </script>
</body>
</html>