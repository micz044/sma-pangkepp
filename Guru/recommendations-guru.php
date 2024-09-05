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

// Query untuk mendapatkan subject_id berdasarkan teacher_id
$sql_subjects = "SELECT subject_id FROM teacher_subjects WHERE teacher_id = ?";
$stmt_subjects = $conn->prepare($sql_subjects);
$stmt_subjects->bind_param("i", $teacher_id);
$stmt_subjects->execute();
$result_subjects = $stmt_subjects->get_result();
$subject_ids = [];
while ($row = $result_subjects->fetch_assoc()) {
    $subject_ids[] = $row['subject_id'];
}

// Query untuk mendapatkan data rekomendasi berdasarkan subject_id
$placeholders = implode(',', array_fill(0, count($subject_ids), '?'));
$sql_recommendations = "SELECT 
                            r.id AS recommendation_id,
                            s.name AS subject_name, 
                            r.title AS material_name, 
                            r.tipe AS material_type, 
                            r.description AS material_description,
                            r.url AS material_url
                        FROM 
                            recommendations r
                        JOIN 
                            subjects s ON r.subject_id = s.id
                        WHERE 
                            r.subject_id IN ($placeholders)";
$stmt_recommendations = $conn->prepare($sql_recommendations);
$stmt_recommendations->bind_param(str_repeat('i', count($subject_ids)), ...$subject_ids);
$stmt_recommendations->execute();
$result_recommendations = $stmt_recommendations->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url(../gambar/cekolah.jpg);
            background-size: cover;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f0f0f0;
            overflow-x: hidden;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            height: 40px;
            justify-content: space-between;
            align-items: center;
            position: relative;
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

        h1 {
            text-align: center;
            font-size: 24px;
        }

        h4 {
           margin-left: 1.3%;
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
            padding-top: 30px;
            position: relative;
            z-index: 0;
        }

        #back-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: none;
        }

        #back-button.hidden {
            display: none;
        }

        .recommendations {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .recommendations h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .recommendation-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #e9f5e9;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .recommendation-item:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .recommendation-item img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .recommendation-details {
            flex-grow: 1;
        }

        .recommendation-item h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .recommendation-item p {
            margin: 0;
            margin-left: 10px;
        }

        .recommendation-item button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-left: 10px;
            transition: background-color 0.2s, transform 0.2s;
        }

        .recommendation-item button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .add-recommendation-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .add-recommendation-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .add-recommendation-btn:active {
            background-color: #1e7e34;
            transform: scale(0.95);
        }

        /* Menu Toggle Button (Mobile) */
        .menu-toggle {
            display: none; /* Tampilkan hanya pada perangkat mobile */
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        #recommendation-content iframe {
            width: 50%;
            margin-top: 25px;
            margin-bottom: 45px;
            height: 700px;
            border: none;
        }

        #recommendation-content {
            text-align: center;
        }

        a{
            text-decoration: none;
            color: blue;
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

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
            }

            .logo-container {
                margin-bottom: 1rem;
            }

    nav ul {
        display: none; /* Sembunyikan menu secara default pada mode mobile */
        flex-direction: column;
        width: 100%;
        background-color: #4CAF50;
        position: absolute;
        left: 0;
        right: 0;
        top: 50px; /* Jarak dari header */
        padding: 0;
    }

    nav ul.show {
        display: flex; /* Tampilkan menu saat tombol toggle ditekan */
    }

    .menu-toggle {
        display: block; /* Tampilkan pada mode mobile */
    }

    nav ul li {
        margin: 0;
    }

    nav ul li a {
        padding: 15px;
        color: white;
        text-decoration: none;
        display: block;
    }

    main {
        padding-top: 10px;
        padding-bottom: 60px;
    }

    #recommendation-content iframe {
            width: 98%;
            margin-top: 25px;
            margin-bottom: 45px;
            height: 600px;
            border: none;
        }

        #recommendation-content {
            text-align: center;
        }

    footer {
        padding: 1rem 0.5rem;
        font-size: 0.9rem;
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

    <main>
    <h1>DAFTAR REKOMENDASI PELAJARAN</h1>
    <button id="back-button" class="hidden" onclick="window.location.href='takes-guru.php'">Kembali</button>

    <div class="recommendations">
        <?php if ($result_recommendations->num_rows > 0): ?>
            <?php while($row = $result_recommendations->fetch_assoc()): ?>
                <div class="recommendation-item animate__animated animate__fadeIn">
                    <div class="recommendation-details">
                        <h4><?php echo htmlspecialchars($row['material_name']); ?></h4>
                        <p>Deskripsi : <?php echo htmlspecialchars($row['material_description']); ?></p><br>
                        <p>Tampilan : <a href="#" class="view-recommendation" data-url="<?php echo htmlspecialchars($row['material_url']); ?>"><?php echo htmlspecialchars($row['material_url']); ?></a></p><br>
                        <button onclick="window.location.href='soal-recommendations.php?id=<?php echo $row['recommendation_id']; ?>'">Lihat Soal</button>
                    </div>
                    <button onclick="window.location.href='edit-recommendation.php?id=<?php echo $row['recommendation_id']; ?>'">Edit</button>
                    <button class="delete-button" onclick="confirmDelete(<?php echo $row['recommendation_id']; ?>)">Hapus</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada rekomendasi.</p>
        <?php endif; ?>
        <button onclick="location.href='add-recommendation.php'">Tambah Rekomendasi</button>
        <button onclick="location.href='recommendations-guru.php'">Refresh</button>
    </div>
    
    <!-- Container for AJAX content -->
    <div id="recommendation-content"></div>
    <button id="refresh-button" style="display:none;">Refresh Halaman</button>
    </main>


    <footer>
        <p>&copy; 2024 SMA Muhammadiyah</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
    // Event listener hanya untuk tombol dengan atribut data-delete="true"
    document.querySelectorAll('.recommendation-item button[data-delete="true"]').forEach(button => {
        button.removeEventListener('click', handleDeleteClick);
        button.addEventListener('click', handleDeleteClick);
    });

    document.querySelectorAll('.view-recommendation').forEach(link => {
        link.removeEventListener('click', handleRecommendationClick);
        link.addEventListener('click', handleRecommendationClick);
        link.textContent = 'Tekan Untuk Melihat Preview';
    });

    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.removeEventListener('click', handleMenuToggle);
        menuToggle.addEventListener('click', handleMenuToggle);
    }
});

function handleDeleteClick(e) {
    const recommendationId = e.target.dataset.id;
    confirmDelete(recommendationId);
}

function handleRecommendationClick(e) {
    e.preventDefault();
    const url = e.target.dataset.url;
    loadContentFromURL(url);
}

function handleMenuToggle() {
    const navList = document.getElementById('nav-list');
    navList.classList.toggle('show');
}

function confirmDelete(recommendationId) {
    const isConfirmed = confirm("Apakah Anda yakin ingin menghapus rekomendasi ini?");
    if (isConfirmed) {
        window.location.href = 'delete-recommendation.php?id=' + recommendationId;
    }
}

function loadContentFromURL(url) {
    const contentContainer = document.getElementById('recommendation-content');
    const descriptionContainer = document.getElementById('preview-description');
    const refreshButton = document.getElementById('refresh-button');
    
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        const videoId = extractYouTubeVideoId(url);
        const iframe = `<iframe width="100%" height="650px" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
        contentContainer.innerHTML = iframe;
        descriptionContainer.innerHTML = 'Preview video dari YouTube:';
    } else if (url.endsWith('.pdf')) {
        contentContainer.innerHTML = `<iframe src="${url}" width="100%" height="600px"></iframe>`;
        descriptionContainer.innerHTML = 'Preview dokumen PDF:';
    } else {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                contentContainer.innerHTML = data;
                descriptionContainer.innerHTML = 'Preview konten lainnya:';
            })
            .catch(error => {
                console.error('Error fetching content:', error);
                contentContainer.innerHTML = '<p>Gagal memuat konten.</p>';
                descriptionContainer.innerHTML = 'Terjadi kesalahan saat memuat konten.';
            });
    }

    contentContainer.style.display = 'block';
    refreshButton.style.display = 'block';
}

function extractYouTubeVideoId(url) {
    let videoId = '';
    if (url.includes('youtube.com')) {
        const urlParams = new URLSearchParams(new URL(url).search);
        videoId = urlParams.get('v');
    } else if (url.includes('youtu.be')) {
        videoId = url.split('youtu.be/')[1];
    }
    return videoId;
}

function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = '../login.php'; // Ganti dengan halaman logout sesuai kebutuhan
    }
}

    </script>
</body>
</html>
