<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Gaya umum */
        body {
            font-family: Arial, sans-serif;
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
            position: relative; /* Menambahkan posisi relatif untuk mengontrol z-index */
            z-index: 1000; /* Menetapkan z-index tinggi untuk menampilkan header di atas konten */
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
            justify-content: flex-end; /* Menyusun pilihan navbar ke kanan pada mode desktop */
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px; /* Menambahkan padding agar pilihan lebih mudah diklik */
            display: block; /* Mengubah pilihan navbar menjadi blok untuk menghindari tumpukan pada mode mobile */
            transition: background-color 0.3s ease; /* Transisi saat hover */
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Warna latar belakang semi transparan saat hover */
            border-radius: 5px; /* Corner radius untuk tampilan yang lebih halus */
        }

        main {
            padding: 2rem;
            margin-top: 100px;
            position: relative; /* Menambahkan posisi relatif pada konten utama */
            z-index: 0; /* Mengatur z-index lebih rendah agar konten tidak menutupi navbar */
            margin-bottom: 80px; /* Tambahkan margin-bottom di sini */
        }

        .dashboard {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation-duration: 1s;
        }

        .dashboard h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .dashboard p {
            text-align: center;
            margin-bottom: 1rem;
        }

        .dashboard .card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard .card h2 {
            margin: 0;
        }

        .dashboard .card a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            width: 100%;
            height: 30px;
            position: fixed;
            bottom: 0;
        }

        /* Media Queries untuk responsivitas */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .logo-container {
                margin-bottom: 1rem;
            }

            nav ul {
                flex-direction: column; /* Menata pilihan navbar dalam satu kolom pada mode mobile */
                width: 100%;
                background-color: #4CAF50;
                position: absolute;
                top: 60px; /* Menyesuaikan posisi dari header */
                left: 0;
                right: 0;
                display: none; /* Menyembunyikan pilihan navbar secara default pada mode mobile */
                z-index: 999; /* Mengatur z-index tinggi agar navbar tetap di atas konten */
            }

            nav ul.show {
                display: flex; /* Menampilkan pilihan navbar ketika tombol toggle diklik */
                flex-direction: column;
            }

            nav ul li {
                margin-left: 0; /* Menghilangkan margin kiri untuk pilihan navbar di mode mobile */
                text-align: center; /* Menengahkan teks pilihan navbar di mode mobile */
            }

            nav ul li a {
                width: 100%; /* Mengisi lebar penuh untuk pilihan navbar di mode mobile */
                padding: 15px 0; /* Menyesuaikan padding untuk pilihan navbar di mode mobile */
            }

            main {
                padding-top: 10px; /* Menambahkan padding atas untuk konten utama agar tidak tertutupi oleh navbar */
                margin-bottom: 100px; /* Sesuaikan margin-bottom untuk layar kecil */
            }

            footer {
                padding: 1rem 0.5rem; /* Menyesuaikan padding footer pada mode mobile */
                font-size: 0.9rem; /* Mengurangi ukuran font pada mode mobile */
            }
        }

        /* Tambahan gaya untuk animasi header */
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

        /* Menyembunyikan tombol toggle pada mode desktop */
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
        <section class="dashboard animate__animated animate__fadeIn">
            <h1>Dashboard Siswa</h1>
            <p>Selamat datang di dashboard siswa. Berikut adalah informasi penting yang dapat Anda akses:</p>
            <div class="card">
                <h2>Materi Pelajaran</h2>
                <a href="subjects.php">Lihat Materi</a>
            </div>
            <div class="card">
                <h2>Tugas</h2>
                <a href="takes.php">Lihat Tugas</a>
            </div>
            <div class="card">
                <h2>Rekomendasi Pelajaran</h2>
                <a href="recommendations.php">Lihat Rekomendasi</a>
            </div>
            <div class="card">
                <h2>Profil</h2>
                <a href="siswa-profile.php">Lihat Profil</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
    </footer>

    <script>
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                // Redirect ke halaman logout atau lakukan tindakan logout di sini
                window.location.href = '../login.php'; // Ganti dengan halaman logout sesuai kebutuhan
            } else {
                // Do nothing
            }
        }

        function toggleMenu() {
            const navList = document.getElementById('nav-list');
            navList.classList.toggle('show');
        }
    </script>
</body>
</html>