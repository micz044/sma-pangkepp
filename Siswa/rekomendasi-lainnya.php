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
               GROUP BY student_id ) AS g 
        JOIN students s ON g.student_id = s.id 
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

// Cek apakah ada rekomendasi
if (empty($recommendations)) {
    echo "Tidak ada rekomendasi yang tersedia.";
    exit;
}

// Mengambil gaya belajar siswa
$gayaBelajar = $recommendations[0]['gaya_belajar'];

// Menghitung cosine similarity untuk semua rekomendasi
foreach ($recommendations as &$rec) {
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

// Temukan nilai recommendation_score tertinggi
$maxScore = max(array_column($recommendations, 'recommendation_score'));

// Filter rekomendasi untuk menghilangkan yang memiliki nilai tertinggi
$filtered_recommendations = array_filter($recommendations, function($rec) use ($maxScore) {
    return $rec['recommendation_score'] < $maxScore;
});

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Lainnya</title>
    <style>
        body {
            font-family: arial;
            background-image: url('../gambar/cekolah.jpg');
            background-size: cover;
        }
        .container {
            margin-top: 100px;
            padding: 20px;
            display: flex;
            gap: 20px;
            flex-direction: column-reverse;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            height: 40px;
            margin-left: -0.5%;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .school-name p {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }
        h1 {
            font-size: 2rem;
            margin-left: 1%;
            margin-bottom: 1rem;
        }
        .nilai{
            margin-left: 2%;
        }
        .btn-back {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin-left: 1.5%;
            margin-bottom: 1rem;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        .recommendations-list {
            list-style: none;
            padding: 0;
            height: 100%;
            background-color: #f9f9f9;
            flex: 1;
        }
        .recommendation-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .recommendation-item h2 {
            margin-top: 0;
        }
        .recommendation-item a {
            color: #007bff;
            text-decoration: none;
        }
        .recommendation-content-box {
            flex: 2;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 4px;
            background-color: #f9f9f9;
            max-width: 2000px;
            width: 100%;
        }
        .recommendation-content-box h2 {
            margin-top: 0;
        }
        iframe, object, img {
            max-width: 100%;
            height: 600px;
            border: none;
        }
        .btn-quiz {
            background-color: #4CAF50;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .btn-quiz:hover {
            background-color: #45a049;
        }
        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            width: 100%;
            height: 20px;
            position: fixed;
            margin-left: -0.5%;
            bottom: 0;
            z-index: 1000;
        }
        @media (min-width: 768px) {
            .container {
                flex-direction: row;
            }
        }
        .recommendation-content-box {
            order: -1;
            flex: 2;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 4px;
            background-color: #f9f9f9;
            max-width: 2000px;
            width: 100%;
            margin-bottom: 50px;
        }
        .recommendations-list {
            flex: 1;
            background-color: #F8F8FF;
            list-style: none;
            padding: 0;
        }
    </style>
    <script>
        function showContent(title, description, url, type, id) {
            // Mengatur judul dan deskripsi
            document.getElementById('content-title').innerText = title;
            document.getElementById('content-description').innerText = description;
            
            var contentDisplay = document.getElementById('content-display');
            contentDisplay.innerHTML = ''; // Kosongkan konten sebelumnya

            // Menampilkan konten berdasarkan tipe
            if (type === 'video') {
                if (url.includes('youtube.com/watch')) {
                    var videoId = new URL(url).searchParams.get('v');
                    url = `https://www.youtube.com/embed/${videoId}`;
                }
                contentDisplay.innerHTML = `<iframe src="${url}" frameborder="0" allowfullscreen style="width: 100%; height: 600px;"></iframe>`;
            } else if (type === 'ppt' || type === 'infografis' || type === 'modul') {
                contentDisplay.innerHTML = `<iframe src="${url}" frameborder="0" style="width: 100%; height: 600px;"></iframe>`;
            }

            // Menambahkan tombol "Kerjakan Soal" jika ID tersedia
            var quizButtonContainer = document.createElement('div');
            quizButtonContainer.style.textAlign = 'center';
            quizButtonContainer.style.marginTop = '20px';

            if (id) {
                var quizButton = document.createElement('a');
                quizButton.href = `soal.php?recommendation_id=${id}`;
                quizButton.className = 'btn-quiz';
                quizButton.innerText = 'Kerjakan Soal';
                quizButtonContainer.appendChild(quizButton);
            }

            contentDisplay.appendChild(quizButtonContainer);
        }
    </script>
</head>
<body>
<header>
    <div class="logo-container">
        <div class="logo">
            <img src="../gambar/logo.png" alt="Logo Sekolah">
        </div>
        <div class="school-name">
            <p>SMA Muhammadiyah Pangkep</p>
        </div>
    </div>
</header>
<div class="container">
    <div class="recommendations-list">
        <?php 
        // Temukan nilai recommendation_score tertinggi
        $maxScore = max(array_column($recommendations, 'recommendation_score'));
        
        // Filter rekomendasi untuk menghilangkan yang memiliki nilai tertinggi
        $filtered_recommendations = array_filter($recommendations, function($rec) use ($maxScore) {
            return $rec['recommendation_score'] < $maxScore;
        });

        $previous_score = isset($all_scores) && !empty($all_scores) ? max(array_column($all_scores, 'nilai')) : null;
        
        foreach ($filtered_recommendations as $recommendation): 
            $current_score = isset($recommendation['recommendation_score']) ? $recommendation['recommendation_score'] : null;
            $show_message = ($current_score !== null && $current_score !== '');
        
            // Menampilkan pesan
            $message = '';
            $color = '';
        
            if ($show_message) {
                if ($current_score < 60) {
                    $message = 'Rekomendasi ini tidak cocok dengan Anda. <br><br>Coba Lihat Rekomendasi Yang lain';
                    $color = 'red';
                } elseif ($current_score >= 60 && $current_score < 70) {
                    $message = 'Rekomendasi ini kurang cocok dengan Anda. <br><br>Coba Lihat Rekomendasi Yang lain';
                    $color = 'orange';
                } elseif ($current_score >= 70 && $current_score < 80) {
                    $message = 'Rekomendasi ini bagus untuk Anda.';
                    $color = 'blue';
                } elseif ($current_score >= 80) {
                    $message = 'Rekomendasi ini sangat cocok dengan Anda.';
                    $color = 'green';
                } else {
                    $message = 'Belum ada nilai';
                    $color = 'black';
                }
            } else {
                $message = 'Belum ada nilai';
                $color = 'black';
            }
        ?>
            <div class="recommendation-item" onclick="showContent('<?php echo htmlspecialchars($recommendation['recommendation_title']); ?>', '<?php echo htmlspecialchars($recommendation['recommendation_description']); ?>', '<?php echo htmlspecialchars($recommendation['recommendation_url']); ?>', '<?php echo htmlspecialchars($recommendation['recommendation_type']); ?>', '<?php echo htmlspecialchars($recommendation['recommendation_id']); ?>')">
                <h2><?php echo htmlspecialchars($recommendation['recommendation_title']); ?></h2>
                <p><strong>Nilai: <?php echo $current_score !== null ? htmlspecialchars($current_score) : 'Belum ada nilai untuk rekomendasi ini'; ?></strong></p>
                <?php if ($show_message): ?>
                    <p class="recommendation-feedback" style="color: <?php echo $color; ?>;">
                        <?php echo $message; ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        
        <a href="recommendations.php" class="btn-back">Kembali ke Rekomendasi</a>
    </div>

    <div class="recommendation-content-box">
        <h2 id="content-title"></h2>
        <p id="content-description"></p>
        <div id="content-display"></div>
    </div>
</div>


<footer>
    <p>Hak Cipta &copy; 2024 Nama Sekolah. Semua Hak Dilindungi Undang-undang.</p>
</footer>
</body>
</html>
