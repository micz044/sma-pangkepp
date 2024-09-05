<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa - Rekomendasi Bahan Pelajaran</title>
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
           margin-top: 200px;
            margin-bottom: 80px;
        }

        .profile {
            max-width: 800px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 1rem;
            align-items: center;
            animation-duration: 1s;
        }

        .profile h1 {
            grid-column: 1 / -1;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: #333;
            text-align: center;
        }

        .profile .avatar {
            grid-row: span 3;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid #4CAF50;
            background-color: white;
            justify-self: center;
        }

        .profile .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile .info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .profile .info p {
            margin: 0;
            font-size: 1rem;
        }

        .profile .info span {
            font-weight: bold;
            color: #4CAF50;
        }

        .profile .edit-buttons {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }

        .profile .edit-buttons a {
            background-color: #1E90FF;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .profile .edit-buttons a:hover {
            background-color: #0000CD;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            width: 100%;
            position: fixed;
            bottom: 0;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .profile {
                grid-template-columns: 1fr;
            }

            .profile .avatar {
                grid-row: auto;
                margin: 0 auto 1rem;
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

            main{
                margin-top: 80px;
            }

            .profile .edit-buttons {
                justify-content: center;
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
        <section class="profile animate__animated animate__fadeIn">
            <h1>Profil Siswa</h1>
            <div class="avatar">
                <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Avatar">
            </div>
            <div class="info">
                <p><span>Nama:</span> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><span>NIS:</span> <?php echo htmlspecialchars($user['nis']); ?></p>
                <p><span>Jenis Kelamin:</span> <?php echo htmlspecialchars($user['jenis_kelamin']); ?></p>
                <p><span>Gaya Belajar:</span> suka menggunakan <?php echo htmlspecialchars($user['gaya_belajar']); ?></p>
                <p><span>Kelas:</span> <?php echo htmlspecialchars($user['class']); ?></p>
                <p><span>Alamat:</span> <?php echo htmlspecialchars($user['alamat']); ?></p>
                <p><span>No HP:</span> <?php echo htmlspecialchars($user['no_hp']); ?></p>
                <p><span>Tanggal Lahir:</span> <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
            </div>
            <div class="edit-buttons">
                <a href="edit-profil.php">Edit Profil</a>
                <a href="edit-akun.php">Edit Akun</a>
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
