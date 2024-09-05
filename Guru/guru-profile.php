<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teachers WHERE user_id = ?";
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
    <title>Profil Guru - Rekomendasi Bahan Pelajaran</title>
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

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: white;
        }

        main {
            margin-top: 120px;
            padding: 2rem;
            position: relative;
            z-index: 0;
        }

        .profile {
            max-width: 800px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            animation-duration: 1s;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .profile .avatar {
            margin-top: 30px;
            width: 250px;
            height: 350px;
            overflow: hidden;
            border: 3px solid #4CAF50;
            margin-right: 2rem;
        }

        .profile .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile .info {
            flex: 1;
            text-align: left;
        }

        .profile .info h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .profile .info p {
            margin: 0.5rem 0;
            font-size: 1.2rem;
        }

        .profile .info .highlight {
            font-weight: bold;
            color: #4CAF50;
        }
        

        .edit-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            margin-top: 1rem;
        }

        .edit-buttons a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .edit-buttons a:hover {
            background-color: #45a049;
        }

        .edit_akun button {
            background-color: #1E90FF;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit_akun button:hover {
            background-color: #0000CD;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile {
                flex-direction: column;
                align-items: center;
            }

            main{
                margin-top: 15px;
                margin-bottom: 25px;
            }

            .profile .avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .edit-buttons {
                width: 100%;
                align-items: center;
            }

            .menu-toggle {
                display: block;
            }

            nav ul {
                display: none;
                flex-direction: column;
                width: 100%;
                background-color: #4CAF50;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                padding: 0;
            }

            nav ul.show {
                display: flex;
            }

            nav ul li {
                margin: 0;
            }

            nav ul li a {
                padding: 15px;
            }

            footer {
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
                <li><a href="#" onclick="toggleMenu()">Profil</a></li>
                <li><a href="#" onclick="confirmLogout()">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="animate__animated animate__fadeInUp">
        <section class="profile animate__animated animate__fadeIn">
            <div class="avatar">
                <img src="uploads/<?php echo $user['profile_photo']; ?>" alt="Profile Photo">
            </div>
            <div class="info">
                <h1>PROFIL GURU</h1>
                <p><span class="highlight">Nama:</span> <?php echo $user['name']; ?></p>
                <p><span class="highlight">NIP:</span> <?php echo $user['nip']; ?></p>
                <p><span class="highlight">Jenis Kelamin:</span> <?php echo $user['jenis_kelamin']; ?></p>
                <p><span class="highlight">Alamat:</span> <?php echo $user['alamat']; ?></p>
                <p><span class="highlight">Nomor HP:</span> <?php echo $user['no_hp']; ?></p>
                <p><span class="highlight">Tanggal Lahir:</span> <?php echo $user['tanggal_lahir']; ?></p>
                <div class="edit-buttons">
                    <a href="edit-profile-guru.php?id=<?php echo $user['id']; ?>">Edit Profil</a>
                    <a href="edit-akun.php?id=<?php echo $user['user_id']; ?>">Edit Akun</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        &copy; 2024 SMA Muhammadiyah Pangkep
    </footer>

    <script>
        function confirmLogout() {
            if (confirm('Anda yakin ingin logout?')) {
                window.location.href = '../login.php';
            }
        }

        function toggleMenu() {
            var navList = document.getElementById('nav-list');
            navList.classList.toggle('show');
        }
    </script>
</body>
</html>
