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


$sql = "SELECT 
u.id AS user_id,
t.id AS teacher_id,
s.name AS subject_name, 
m.id AS material_id,
m.name AS material_name, 
m.description AS material_description
FROM 
subjects s
JOIN 
teachers t ON s.teacher_id = t.id
JOIN 
users u ON t.user_id = u.id
LEFT JOIN 
matematika m ON s.id = m.subject_id
WHERE 
t.id = ?
AND m.name IS NOT NULL 
AND m.description IS NOT NULL

UNION ALL

SELECT 
u.id AS user_id,
t.id AS teacher_id,
s.name AS subject_name, 
b.id AS material_id,
b.name AS material_name, 
b.description AS material_description
FROM 
subjects s
JOIN 
teachers t ON s.teacher_id = t.id
JOIN 
users u ON t.user_id = u.id
LEFT JOIN 
bahasa_indonesia b ON s.id = b.subject_id
WHERE 
t.id = ?
AND b.name IS NOT NULL 
AND b.description IS NOT NULL

UNION ALL

SELECT 
u.id AS user_id,
t.id AS teacher_id,
s.name AS subject_name, 
bi.id AS material_id,
bi.name AS material_name, 
bi.description AS material_description
FROM 
subjects s
JOIN 
teachers t ON s.teacher_id = t.id
JOIN 
users u ON t.user_id = u.id
LEFT JOIN 
bahasa_inggris bi ON s.id = bi.subject_id
WHERE 
t.id = ?
AND bi.name IS NOT NULL 
AND bi.description IS NOT NULL

UNION ALL

SELECT 
u.id AS user_id,
t.id AS teacher_id,
s.name AS subject_name, 
a.id AS material_id,
a.name AS material_name, 
a.description AS material_description
FROM 
subjects s
JOIN 
teachers t ON s.teacher_id = t.id
JOIN 
users u ON t.user_id = u.id
LEFT JOIN 
agama a ON s.id = a.subject_id
WHERE 
t.id = ?
AND a.name IS NOT NULL 
AND a.description IS NOT NULL

UNION ALL

SELECT 
u.id AS user_id,
t.id AS teacher_id,
s.name AS subject_name, 
p.id AS material_id,
p.name AS material_name, 
p.description AS material_description
FROM 
subjects s
JOIN 
teachers t ON s.teacher_id = t.id
JOIN 
users u ON t.user_id = u.id
LEFT JOIN 
pkn p ON s.id = p.subject_id
WHERE 
t.id = ?
AND p.name IS NOT NULL 
AND p.description IS NOT NULL

ORDER BY 
subject_name, material_name DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $teacher_id, $teacher_id, $teacher_id, $teacher_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Pelajaran - Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            width: 99%; /* Menambahkan posisi relatif untuk mengontrol z-index */
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
    padding-top: 120px;
    position: relative;
    z-index: 0;
    width: 100%;
    height: 100%;
    height: calc(100vh - 120px); /* Mengurangi tinggi header */
    box-sizing: border-box;
    overflow: auto; /* Menambahkan overflow auto untuk scrollbar */
}

#pdf-viewer {
    width: 60%;
    height: 90%; /* Sesuaikan tinggi sesuai dengan tinggi viewport */
    display: none;
    margin-top: 20px;
    margin-left: auto; /* Menambahkan auto margin kiri */
    margin-right: auto; /* Menambahkan auto margin kanan */
}

#pdf-frame {
    width: 100%;
    height: 100%;
    border: none;
}

.add-meeting-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    margin: 10px auto; /* Mengatur margin otomatis untuk membuat tombol berada di tengah */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display: block; /* Ubah display menjadi block untuk pusat otomatis */
    transition: background-color 0.3s ease;
}

.add-meeting-btn:hover {
    background-color: #45a049;
}

#back-btn-container {
    text-align: center;
    margin-top: 20px;
}

#back-btn {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    display: inline-block;
}

#back-btn:hover {
    background-color: #45a049;
}

        .recommendations {
            margin: 0 auto;
            max-width: 1200px;
        }

        .meeting-card {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .meeting-card h3 {
        margin: 0;
    }

    .meeting-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .upload-assignment {
        margin-top: 20px;
        background-color: #f7f7f7;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    .upload-assignment label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .upload-assignment input[type="file"] {
        display: block;
        margin-top: 10px;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

        .meetings {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

.meeting-box {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    transition: background-color 0.3s, transform 0.3s;
    width: calc(50% - 20px); /* Two columns layout */
    box-sizing: border-box;
    font-size: 16px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.meeting-box .button-container {
    display: flex;
    gap: 5px; /* Jarak antar tombol lebih kecil */
    margin-top: 10px;
}

.meeting-box button {
    padding: 5px 10px; /* Padding kecil untuk tombol */
    font-size: 12px; /* Ukuran font kecil */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: inline-block; /* Menjadikan tombol berada dalam satu baris */
}

.meeting-box .view-btn {
    background-color: #4CAF50;
    color: white;
}

.meeting-box .view-btn:hover {
    background-color: #45a049;
}

.meeting-box .edit-btn {
    background-color: #2196F3;
    color: white;
}

.meeting-box .edit-btn:hover {
    background-color: #1976D2;
}

.meeting-box .delete-btn {
    background-color: #f44336;
    color: white;
}

.meeting-box .delete-btn:hover {
    background-color: #d32f2f;
}

        .meeting-box img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .recommendations h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .meetings h2 {
            text-align: center;
            margin-top: 20px;
            font-size: 1.5rem;
        }

                /* Gaya untuk menu toggle */
                .menu-toggle {
                display: none; /* Sembunyikan secara default */
                font-size: 24px;
                cursor: pointer;
                color: black;
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
                padding-top: 100px;
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
<main class="animate__animated animate__fadeInUp">
    <section class="recommendations animate__animated animate__fadeIn">
        <h1>Materi Pelajaran</h1>
        <?php 
if ($result->num_rows > 0) {
    $current_subject = '';
    $meeting_counter = 1; // Inisialisasi counter untuk pertemuan
    while ($row = $result->fetch_assoc()) {
        if ($row['subject_name'] !== $current_subject) {
            if ($current_subject !== '') {
                echo '</div>'; // Tutup div sebelumnya
            }
            $current_subject = $row['subject_name'];
            $meeting_counter = 1; // Reset counter saat subjek berubah
            echo '<h2>' . htmlspecialchars($current_subject) . '</h2>';
            echo '<div class="meetings" id="meetings-container">';
        }
                // Tentukan folder berdasarkan subject_name
                $subject_folder = strtolower(str_replace(' ', '_', $row['subject_name']));
                $pdf_path = 'materi/' . $subject_folder . '/' . htmlspecialchars($row['material_description']);
                
                echo '<div class="meeting-box">';
                echo '<h3>Pertemuan ' . $meeting_counter++ . '</h3>'; // Tambahkan penomoran pertemuan
                echo '<img src="../gambar/pdf.png" alt="PDF">';
                echo '<p>' . htmlspecialchars($row['material_name']) . '</p>';
                echo '<div class="button-container">'; // Kontainer untuk tombol
                echo '<button class="view-btn" onclick="viewPDF(\'' . htmlspecialchars($pdf_path) . '\')">Lihat PDF</button>';
                echo '<button class="edit-btn" onclick="openEditModal(' . $row['material_id'] . ')"><i class="fa fa-edit"></i> Edit</button>';
                echo '<button class="delete-btn" onclick="confirmDelete(' . $row['material_id'] . ')"><i class="fa fa-trash"></i> Hapus</button>';
                echo '</div>'; // Akhiri kontainer tombol
                echo '</div>';  
    }    
    echo '</div>';
} else {
    echo "<p>Tidak ada data untuk ditampilkan.</p>";
}
?>

<div style="text-align: center; margin-top: 20px;">
    <button id="add-meeting-btn" class="add-meeting-btn" onclick="openAddMeetingModal()">Tambah Pertemuan</button>
</div>

</section>
<section id="pdf-viewer">
    <iframe id="pdf-frame" src=""></iframe>
    <div style="text-align: center; margin-top: 20px;">
        <button id="back-btn" onclick="hidePDF()" style="display: none;">Kembali ke Daftar Pertemuan</button>
    </div>
</section>
</main>
<footer>
    <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
</footer>

<script>
function openAddMeetingModal() {
    // Arahkan ke halaman lain atau tampilkan modal untuk menambahkan pertemuan
    window.location.href = 'add-meeting.php';
}

function openEditModal(id) {
    // Arahkan ke halaman edit dengan ID materi
    window.location.href = 'edit-material.php?id=' + id;
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus materi ini?')) {
        window.location.href = 'delete-material.php?id=' + id;
    }
}

function viewPDF(url) {
    console.log("Viewing PDF: " + url); // Debug log
    const iframe = document.getElementById('pdf-frame');
    const meetingsContainer = document.getElementById('meetings-container');
    const pdfViewer = document.getElementById('pdf-viewer');
    const backBtn = document.getElementById('back-btn');
    const addMeetingBtn = document.getElementById('add-meeting-btn'); // Tambahkan referensi ke tombol tambah pertemuan

    // Menyembunyikan kotak pertemuan, tombol tambah pertemuan, dan menampilkan PDF
    meetingsContainer.style.display = 'none';
    pdfViewer.style.display = 'block';
    backBtn.style.display = 'block';
    addMeetingBtn.style.display = 'none'; // Sembunyikan tombol tambah pertemuan

    iframe.src = url;
}

function hidePDF() {
    const meetingsContainer = document.getElementById('meetings-container');
    const pdfViewer = document.getElementById('pdf-viewer');
    const backBtn = document.getElementById('back-btn');
    const addMeetingBtn = document.getElementById('add-meeting-btn'); // Tambahkan referensi ke tombol tambah pertemuan
    window.location.href = 'subjects-guru.php';

    // Menampilkan kembali kotak pertemuan, tombol tambah pertemuan, dan menyembunyikan PDF
    meetingsContainer.style.display = 'block';
    pdfViewer.style.display = 'none';
    backBtn.style.display = 'none';
    addMeetingBtn.style.display = 'block'; // Tampilkan kembali tombol tambah pertemuan
}

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