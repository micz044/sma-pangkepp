<?php
// Koneksi ke database
require_once '../koneksi.php';

// Query untuk mendapatkan mata pelajaran dan nama guru
$sql = "
SELECT s.id, s.name as subject_name, s.description, t.name as teacher_name
FROM subjects s
JOIN teacher_subjects ts ON s.id = ts.subject_id
JOIN teachers t ON ts.teacher_id = t.id
";

$result = $conn->query($sql);

$subjects = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Pelajaran - Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
    }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 40px;
            position: fixed; /* Menambahkan posisi tetap */
            width: 99%; /* Lebar penuh */
            top: 0; /* Posisi di atas */
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
        padding-top: 200px; /* Menambahkan padding atas untuk memberi ruang bagi navbar */
        margin-bottom: 80px; /* Tambahkan margin bawah di sini */
        max-width: 1200px; /* Menambahkan lebar maksimum */
        margin: 0 auto; /* Memusatkan konten */
    }


        .subjects {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation-duration: 1s;
        }

        .subjects h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .subject-list, .meetings-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .meetings-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 1rem;
            margin-top: 20px;
        }

        #subject-details {
            display: none;
        }

        .subject-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 1rem;
        }

        .subject-card, .meeting-card {
            background-color: #f9f9f9;
            padding: 1rem;
            height: 140px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .subject-card:hover, .meeting-card:hover {
            transform: translateY(-5px);
        }

        .meetings-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 1rem;
            margin-top: 20px; /* Optional, can be adjusted */
        }

        #subject-details {
            display: none; /* Awalnya tersembunyi */
        }

        .subject-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 1rem;
        }

        /* Tambahkan CSS tambahan jika diperlukan */
        .subject-card, .meeting-card {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .subject-card:hover, .meeting-card:hover {
            transform: translateY(-5px);
        }

        .upload-assignment {
    display: none;
}


        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            height: 20px;
            position: fixed;
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
                padding-top: 140px; /* Menambahkan padding atas untuk memberi ruang bagi navbar */
                margin-bottom: 120px; /* Sesuaikan margin bawah untuk layar kecil */
            }

            footer {
                padding: 1rem 0.5rem; /* Menyesuaikan padding footer pada mode mobile */
                font-size: 0.9rem; /* Mengurangi ukuran font pada mode mobile */
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

        .back-button {
    display: block;
    margin: 20px auto; /* Center the button horizontally */
    width: fit-content; /* Adjust the width to fit the content */
}


        .upload-assignment {
            margin-top: 10px;
        }

        .upload-assignment input[type="file"] {
            display: block;
            margin-top: 10px;
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
    <section class="subjects animate__animated animate__fadeIn">
        <h1 class="judul">Mata Pelajaran</h1>
        <div class="subject-list" id="subject-list">
            <!-- Mulai Perulangan PHP untuk menampilkan data -->
            <?php foreach ($subjects as $subject): ?>
                <div class="subject-card" id="subject-<?php echo $subject['id']; ?>" onclick="showDetails(<?php echo $subject['id']; ?>)">
                    <h2><?php echo $subject['subject_name']; ?></h2>
                    <p><strong>Guru: <?php echo $subject['teacher_name']; ?></strong></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="subject-details" style="display:none;">
        <!-- Detail akan ditampilkan di sini -->
    </section>
</main>

    <footer>
        <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
    </footer>

    <script>
        function showDetails(subjectId) {
            const subjectSection = document.querySelector('.subjects');
            const subjectDetails = document.getElementById('subject-details');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `get_subject_details.php?id=${subjectId}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    subjectDetails.innerHTML = xhr.responseText;

                    // Menyembunyikan section subjects dan menampilkan detail
                    subjectSection.style.display = 'none';
                    subjectDetails.style.display = 'block';
                }
            };
            xhr.send();
        }

        function showSubjects() {
            const subjectList = document.getElementById('subject-list');
            const subjectDetails = document.getElementById('subject-details');

            subjectDetails.style.display = 'none';
            subjectList.style.display = 'grid';
        }

        function openPDF(link) {
            window.open(link, '_blank');
        }

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