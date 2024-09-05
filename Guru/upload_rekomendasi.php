<?php
if (isset($_POST['submit'])) {
    $subject_id = $_POST['subject_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['file'];
    $url = $_POST['url'];
    $tipe = $_POST['tipe'];

    // Tentukan direktori penyimpanan berdasarkan subject_id
    switch ($subject_id) {
        case 1:
            $targetDir = '../rekomendasi/matematika/';
            break;
        case 2:
            $targetDir = '../rekomendasi/bahasa_indonesia/';
            break;
        case 3:
            $targetDir = '../rekomendasi/bahasa_inggris/';
            break;
        case 4:
            $targetDir = '../rekomendasi/pkn/';
            break;
        case 5:
            $targetDir = '../rekomendasi/agama/';
            break;
        default:
            $targetDir = '../rekomendasi/umum/'; // Folder default jika subject_id tidak dikenali
    }

    $target_file = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Periksa apakah file atau URL yang diunggah
    if (empty($url)) {
        // Periksa tipe file
        $allowedTypes = ['pdf', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Maaf, hanya file PDF, PPT, PPTX, JPG, JPEG, PNG & GIF yang diizinkan.');</script>";
            $uploadOk = 0;
        }
        
        // Jika file tidak valid, tampilkan pesan kesalahan
        if ($uploadOk == 0) {
            echo "<script>alert('Maaf, file Anda tidak diunggah.');</script>";
        } else {
            // Buat direktori jika belum ada
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Pindahkan file ke direktori target
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $url = $target_file; // Update URL dengan path file yang diunggah
            } else {
                echo "<script>alert('Maaf, terjadi kesalahan saat mengunggah file Anda.');</script>";
            }
        }
    }

    require_once '../koneksi.php';
    
    // Simpan data ke database
    $query = $koneksi->prepare("INSERT INTO recommendations (subject_id, title, description, url, tipe) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param("issss", $subject_id, $title, $description, $url, $tipe);
    $query->execute();
    
    if ($query->affected_rows > 0) {
        echo "<script>
                alert('Rekomendasi berhasil ditambahkan.');
                window.location.href = 'recommendations-guru.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menambahkan rekomendasi.');</script>";
    }
    
    $query->close();
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Gaya umum */
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url(../gambar/cekolah.jpg);
            background-size: cover;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f0f0f0;
            overflow-x: auto;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
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

        h3{
            text-align: center;
            font-size: 24px;
        }

        a{
            text-align: center;
            font-size: 18px;
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
            padding-top: 10px;
            position: relative;
            z-index: 0;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation-duration: 1s;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .form-group textarea {
            resize: vertical;
            height: 100px;
        }

        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            transition: background-color 0.3s ease;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
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
                width: 100%;
            }

            nav ul li a {
                width: 100%;
                padding: 15px 0;
            }

            main {
                padding-top: 10px;
                padding-bottom: 60px;
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
                <li><a href="recommendations-guru.php" onclick="toggleMenu()">Rekomendasi</a></li>
                <li><a href="guru-profile.php" onclick="toggleMenu()">Profil</a></li>
                <li><a href="#" onclick="confirmLogout()">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="animate__animated animate__fadeInUp">
        <div class="form-container animate__animated animate__fadeIn">
            <h1>Tambah Rekomendasi Bahan Pelajaran</h1>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="subject_id">Subject ID:</label>
                    <input type="number" id="subject_id" name="subject_id" required>
                </div>
                <div class="form-group">
                    <label for="tipe">Tipe:</label>
                    <select id="tipe" name="tipe" required>
                    <option value="video">Video</option>
                    <option value="modul">Modul</option>
                    <option value="ppt">PPT</option>
                    <option value="infografis">Infografis</option>
                    <!-- Tambahkan opsi lain jika diperlukan -->
                </select>
                </div>
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="file">File:</label>
                    <input type="file" id="file" name="file">
                </div>
                <div class="form-group">
                    <label for="url">atau URL:</label>
                    <input type="url" id="url" name="url">
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="Tambah">
                </div>
                <div class="form-group">
                    <a href="recommendations-guru.php">Kembali</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 SMA Muhammadiyah Pangkep. All rights reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            var navList = document.getElementById("nav-list");
            navList.classList.toggle("show");
        }

        function confirmLogout() {
            var result = confirm("Apakah Anda yakin ingin logout?");
            if (result) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>