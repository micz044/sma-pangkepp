<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil student_id berdasarkan user_id
$user_id = $_SESSION['user_id'];
$student_id_sql = "SELECT id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($student_id_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_id_result = $stmt->get_result();
$student_id = $student_id_result->fetch_assoc()['id'];

// Query untuk mendapatkan data nilai siswa
$sql = "
        SELECT s.name AS subject_name, 
            g.nilai_tugas,
            g.nilai_ulangan_harian,
            g.kehadiran,
            g.nilai_uts,
            g.nilai_uas,
            g.grade
        FROM grades g
        JOIN subjects s ON g.subject_id = s.id
        WHERE g.student_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
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
            position: fixed;
            top: 0;
            height: 40px;
            width: 100%;
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

        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
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
            padding-top: 260px; /* Jarak antara header dan konten */
            width: 100%;
            box-sizing: border-box;
        }

        .profile {
            max-width: 1200px; /* Menambah lebar maksimum konten */
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .profile h1 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #4CAF50;
        }

        .table-container {
            overflow-x: auto; /* Scroll horizontal untuk tabel jika diperlukan */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
            white-space: nowrap; /* Mencegah pemotongan teks */
        }

        table th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #ddd;
        }

        table tbody tr td:first-child {
            text-align: center;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            width: 100%;
            position: fixed;
            bottom: 0;
            height: 20px;
            margin-top: 20px;
        }

        /* Animasi */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Media Queries untuk responsivitas */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

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
        <h1>Nilai Kamu</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Nilai Tugas</th>
                        <th>Nilai Ulangan Harian</th>
                        <th>Kehadiran</th>
                        <th>Nilai UTS</th>
                        <th>Nilai UAS</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo !empty($row['nilai_tugas']) ? htmlspecialchars($row['nilai_tugas']) : 'Belum Ada'; ?></td>
                            <td><?php echo !empty($row['nilai_ulangan_harian']) ? htmlspecialchars($row['nilai_ulangan_harian']) : 'Belum Ada'; ?></td>
                            <td><?php echo !empty($row['kehadiran']) ? htmlspecialchars($row['kehadiran']) : 'Belum Ada'; ?></td>
                            <td><?php echo !empty($row['nilai_uts']) ? htmlspecialchars($row['nilai_uts']) : 'Belum Ada'; ?></td>
                            <td><?php echo !empty($row['nilai_uas']) ? htmlspecialchars($row['nilai_uas']) : 'Belum Ada'; ?></td>
                            <td><?php echo !empty($row['grade']) ? htmlspecialchars($row['grade']) : 'Belum Ada'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
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


