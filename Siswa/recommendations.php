<?php
// Koneksi ke database
require_once '../koneksi.php';

// Mulai sesi
session_start();

$student_id = $_SESSION['student_id']; // Ambil student_id dari sesi

// Fungsi untuk menghitung cosine similarity
function cosineSimilarity($vectorA, $vectorB) {
    if (empty($vectorA) || empty($vectorB)) {
        return 0;
    }

    $dotProduct = array_sum(array_map(function($a, $b) { return $a * $b; }, $vectorA, $vectorB));
    $magnitudeA = sqrt(array_sum(array_map(function($a) { return $a * $a; }, $vectorA)));
    $magnitudeB = sqrt(array_sum(array_map(function($b) { return $b * $b; }, $vectorB)));
    
    if ($magnitudeA == 0 || $magnitudeB == 0) {
        return 0;
    }

    return $dotProduct / ($magnitudeA * $magnitudeB);
}

// Query untuk mendapatkan nilai terendah dan data rekomendasi
$sql = "SELECT s.name AS student_name, 
               sub.name AS subject_name, 
               g.min_grade, 
               s.gaya_belajar, 
               r.id AS recommendation_id,       
               r.title AS recommendation_title, 
               r.description AS recommendation_description, 
               r.url AS recommendation_url,
               r.tipe AS recommendation_type,
               MAX(ns.recommendation_score) AS recommendation_score 
        FROM ( SELECT student_id, MIN(grade) AS min_grade
               FROM grades 
               GROUP BY student_id ) AS g JOIN students s ON g.student_id = s.id 
        JOIN grades gr ON g.student_id = gr.student_id AND g.min_grade = gr.grade 
        JOIN subjects sub ON gr.subject_id = sub.id 
        LEFT JOIN recommendations r ON r.subject_id = sub.id 
        LEFT JOIN nilai_siswa ns ON ns.siswa_id = s.id AND ns.rekomendasi_id = r.id
        WHERE s.id = ? 
        GROUP BY s.name, sub.name, g.min_grade, s.gaya_belajar, r.id, r.title, r.description, r.url, r.tipe
        ORDER BY sub.name";

// Menyiapkan statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id); // Mengikat parameter student_id
$stmt->execute();

// Mendapatkan hasil query
$result = $stmt->get_result();
$recommendations = $result->fetch_all(MYSQLI_ASSOC);

// Menutup koneksi
$stmt->close();
$conn->close();

// Mengambil gaya belajar siswa
$gayaBelajar = $recommendations[0]['gaya_belajar'];

// Filter rekomendasi berdasarkan gaya belajar siswa
$filtered_recommendations = array_filter($recommendations, function($rec) use ($gayaBelajar) {
    return $rec['recommendation_type'] === $gayaBelajar;
});

// Jika tidak ada rekomendasi yang cocok dengan gaya belajar, ambil semua rekomendasi
if (empty($filtered_recommendations)) {
    $filtered_recommendations = $recommendations;
}

// Menemukan rekomendasi dengan recommendation_score tertinggi
$highest_score_recommendation = null;
foreach ($recommendations as $rec) {
    if ($highest_score_recommendation === null || $rec['recommendation_score'] > $highest_score_recommendation['recommendation_score']) {
        $highest_score_recommendation = $rec;
    }
}

// Menghitung cosine similarity untuk rekomendasi yang difilter
foreach ($filtered_recommendations as &$rec) {
    // Buat vektor biner untuk gaya belajar rekomendasi
    $recommendationVector = [
        'video' => $rec['recommendation_type'] == 'video' ? 1 : 0,
        'ppt' => $rec['recommendation_type'] == 'ppt' ? 1 : 0,
        'infografis' => $rec['recommendation_type'] == 'infografis' ? 1 : 0,
        'modul' => $rec['recommendation_type'] == 'modul' ? 1 : 0,
    ];

    // Buat vektor biner untuk gaya belajar siswa
    $siswaVector = [
        'video' => $gayaBelajar == 'video' ? 1 : 0,
        'ppt' => $gayaBelajar == 'ppt' ? 1 : 0,
        'infografis' => $gayaBelajar == 'infografis' ? 1 : 0,
        'modul' => $gayaBelajar == 'modul' ? 1 : 0,
    ];

    // Hitung cosine similarity
    $rec['similarity'] = cosineSimilarity($siswaVector, $recommendationVector);
}

// Urutkan rekomendasi berdasarkan similarity
usort($filtered_recommendations, function($a, $b) {
    return $b['similarity'] <=> $a['similarity'];
});

// Ambil rekomendasi terbaik berdasarkan similarity
$best_recommendation = $filtered_recommendations[0];

// Jika rekomendasi dengan score tertinggi memiliki score lebih tinggi dari rekomendasi terbaik berdasarkan similarity, tampilkan rekomendasi dengan score tertinggi
if ($highest_score_recommendation['recommendation_score'] > $best_recommendation['recommendation_score']) {
    $best_recommendation = $highest_score_recommendation;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Bahan Pelajaran - SMA Muhammadiyah Pangkep</title>
    <style>
        /* Gaya umum */
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

        body::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
        .btn-disabled {
            pointer-events: none; /* Mencegah klik pada tombol */
            opacity: 0.5; /* Membuat tombol terlihat dinonaktifkan */
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
            justify-content: space-between;
            align-items: center;
            position: fixed;
            height: 40px;
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
            padding: 2rem 1rem;
            padding-top: 100px;
            margin-bottom: 40px;
        }

        .recommendations {
            max-width: 1600px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: row;
            gap: 2rem;
        }

        .content {
            width: 100%;
        }

        iframe {
            width: 100%;
        }

        .content h1, .content h3, .content p {
            text-align: center;
        }

        .sidebar {
            flex: 1;
            margin-top: 130px;
            max-width: 250px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 8px;
        }

        .sidebar h2, .sidebar .nilai-siswa h2 {
            text-align: center;
        }

        .sidebar .nilai-siswa {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            width: 200px;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            margin-top: 1rem;
        }

        .recommendation-boxes {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .recommendation-box {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .recommendation-box img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .recommendation-box h3 {
            margin: 0;
            text-align: center;
            font-size: 1.2rem;
        }

        .card iframe {
            height: 450px;
            border: none;
            border-radius: 8px;
        }

        .question-section {
            margin: 2rem auto;
            text-align: center;
            padding: 1rem;
            margin-bottom: -30px;
            max-width: 1400px;
            margin-bottom: 1px;
            height: 100px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        
        .question-section p {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        /* Gaya untuk menu toggle */
        .menu-toggle {
            display: none; /* Sembunyikan secara default */
            font-size: 24px;
            cursor: pointer;
            color: black;
        }

        .question-button {
            background-color: #4CAF50;
            color: white;
            margin-bottom: 120px;
            text-decoration: none;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .question-button:hover {
            background-color: #45a049;
        }
        .btn-recommendations {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin-top: 1rem;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
        }

        .btn-recommendations:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            width: 100%;
            height: 20px;
            position: fixed;
            bottom: 0;
        }

@media (max-width: 768px) {
    header {
        flex-direction: column;
        align-items: flex-start;
        padding: 0.5rem;
    }

    .recommendations {
        flex-direction: column;
        padding: 1rem;
    }

    .recommendations .content,
    .recommendations .sidebar {
        flex: none;
        width: 100%;
    }

    .sidebar {
        margin-top: 1rem;
        max-width: 440px;
    }

    .sidebar .nilai-siswa {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            width: 400px;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            margin-top: 1rem;
        }

    nav ul {
        flex-direction: column;
        width: 100%;
        background-color: #4CAF50;
        position: absolute;
        top: 60px;
        left: 0;
        display: none;
        z-index: 999;
    }

    .menu-toggle {
        display: block; /* Tampilkan pada mode mobile */
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

    footer {
        padding: 1rem 0.5rem;
        font-size: 0.9rem;
    }
}

    </style>
</head>
<body>
<header>
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
    <main>
    <section class="recommendations">
    <div class="content">
        <h1>REKOMENDASI YANG COCOK UNTUK ANDA</h1>
        <?php if (!empty($best_recommendation)) : ?>
            <div class="recommendation-content">
                <h3><?php echo htmlspecialchars($best_recommendation['recommendation_title']); ?></h3>
                <?php if ($best_recommendation['recommendation_url']) : ?>
                    <?php
                    $file_ext = pathinfo($best_recommendation['recommendation_url'], PATHINFO_EXTENSION);
                    $file_url = htmlspecialchars($best_recommendation['recommendation_url']);

                    // Handle YouTube Links
                    if (strpos($file_url, 'youtube.com') !== false || strpos($file_url, 'youtu.be') !== false) {
                        $embed_url = strpos($file_url, 'watch?v=') !== false ? str_replace('watch?v=', 'embed/', $file_url) : str_replace('youtu.be/', 'youtube.com/embed/', $file_url);
                        echo "<iframe style='width: 100%; height: 650px;' src='$embed_url' frameborder='0' allowfullscreen></iframe>";
                    } elseif ($file_ext === 'pdf') {
                        echo "<iframe src='$file_url' style='width: 100%; height: 695px;' frameborder='0'></iframe>";
                    } elseif ($file_ext === 'pptx' || $file_ext === 'ppt') {
                        echo "<iframe src='$file_url' style='width: 100%; height: 695px;' frameborder='0'></iframe>";
                    } elseif ($file_ext === 'mp4' || $file_ext === 'mkv') {
                        echo "<video controls src='$file_url' style='width: 100%; max-height: 500px;'></video>";
                    } elseif ($file_ext === 'jpg' || $file_ext === 'jpeg' || $file_ext === 'png') {
                        echo "<img src='$file_url' style='max-width: 100%; height: auto;'>";
                    } else {
                        echo "<p>Format tidak didukung untuk tampilan langsung.</p>";
                    }
                    ?>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <p>Tidak ada rekomendasi bahan pelajaran.</p>
        <?php endif; ?>
        <div class="question-section">
            <p>Apakah rekomendasi ini membantu Anda?</p>
            <a href="soal.php?recommendation_id=<?php echo htmlspecialchars($best_recommendation['recommendation_id']); ?>" class="question-button">Jawab Pertanyaan</a>
        </div>
    </div>
    <div class="sidebar">
            <div class="nilai-siswa">
            <?php if (!empty($best_recommendation)) : ?>
                <h4>Penilaian Rekomendasi</h4>
                <p><?php echo htmlspecialchars($best_recommendation['recommendation_score'] ?? 'Belum ada nilai'); ?></p>

                <!-- Logika untuk menampilkan pesan berdasarkan nilai -->
                <?php 
                $score = isset($best_recommendation['recommendation_score']) ? $best_recommendation['recommendation_score'] : null;

                if ($score !== null) {
                    if ($score < 60) {
                        $message = 'Rekomendasi ini tidak cocok dengan Anda. <br><br>Coba Lihat Rekomendasi Yang lain';
                        $color = 'red';
                    } elseif ($score >= 60 && $score < 70) {
                        $message = 'Rekomendasi ini kurang cocok dengan Anda. <br><br>Coba Lihat Rekomendasi Yang lain';
                        $color = 'orange'; // Warna untuk kurang cocok
                    } elseif ($score >= 70 && $score < 80) {
                        $message = 'Rekomendasi ini bagus untuk Anda.';
                        $color = 'blue'; // Warna untuk bagus
                    } elseif ($score >= 80) {
                        $message = 'Rekomendasi ini sangat cocok dengan Anda.';
                        $color = 'green';
                    }
                } else {
                    $message = '';
                }
                ?>
                <p style="color: <?php echo $color; ?>;"><?php echo $message; ?></p>

            <?php else : ?>
                <p>Belum ada nilai</p>
            <?php endif; ?>
        </div>
        <br>
        <a href="rekomendasi-lainnya.php?student_id=<?php echo $_SESSION['student_id']; ?>" 
        class="btn-recommendations" 
        <?php echo $score === null ? 'class="btn-disabled" onclick="return false;"' : ''; ?>>
        Lihat Rekomendasi Lainnya
        </a>
    </div>
    </section>
    </main>
    <footer>
        <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
    </footer>

    <script>
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = '../login.php';
            }
        }

        function toggleMenu() {
            const navList = document.getElementById('nav-list');
            navList.classList.toggle('show');
        }
    </script>
</body>
</html>