<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil ID guru dari sesi
$sql = "SELECT id FROM teachers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo "Guru tidak ditemukan.";
    exit;
}

$teacher_id = $teacher['id'];

// Query untuk mendapatkan data siswa berdasarkan mata pelajaran yang sama dengan yang diajarkan oleh guru
$sql = "
SELECT 
    s.id AS student_id,
    s.name AS student_name,
    s.nis AS student_nis,
    s.alamat AS student_address,
    s.no_hp AS student_phone,
    s.date_of_birth AS student_dob,
    s.jenis_kelamin AS student_gender,
    s.gaya_belajar AS student_learning_style,
    sub.id AS subject_id,
    sub.name AS subject_name,
    s.class AS student_class
FROM 
    teachers t
JOIN 
    teacher_subjects ts ON t.id = ts.teacher_id
JOIN 
    subjects sub ON ts.subject_id = sub.id
JOIN 
    student_subjects ss ON sub.id = ss.subject_id
JOIN 
    students s ON ss.student_id = s.id
WHERE 
    t.id = ?
ORDER BY 
    t.name, s.name, sub.name
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id); // Bind teacher_id ke query
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome CDN -->
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
            height: 40px;
            position: fixed;
            width: 99%;
            z-index: 1000;
            top: 0;
            left: 0;
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
            padding-top: 260px;
            padding-bottom: 60px;
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
            overflow: auto;
        }

        .profile {
            max-width: 1200px; /* Ubah ini sesuai kebutuhan Anda */
            margin: 0 auto; /* Pastikan kotak tetap berada di tengah */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            padding: 15px;
            font-size: 0.9rem;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
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
            height: 30px;
            margin-top: 20px;
        }

        /* Gaya untuk menu toggle */
        .menu-toggle {
            display: none; /* Sembunyikan secara default */
            font-size: 24px;
            cursor: pointer;
            color: black;
        }

        nav ul {
            display: flex;
        }

        nav ul.show {
            display: flex; /* Tampilkan menu saat tombol toggle ditekan */
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

    @media (max-width: 768px) {
        header {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem; /* Mengurangi padding untuk lebih dekat dengan header */
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
        padding: 1rem; /* Mengurangi padding untuk lebih dekat dengan header */
        padding-top: 90px; /* Jarak dari header setelah mengurangi padding */
        padding-bottom: 80px; /* Jarak bawah untuk footer di tampilan mobile */
    }

    table th, table td {
        font-size: 0.8rem; /* Disesuaikan untuk tampilan mobile */
    }

    /* Menyembunyikan kolom nomor pada tampilan mobile */
    .hide-on-mobile {
        display: none;
    }

    /* Mengubah tampilan tabel menjadi format kartu */
    .profile table {
        display: block;
        width: 100%;
    }

    .profile table thead {
        display: none;
    }

    .profile table tbody {
        display: flex;
        flex-direction: column;
    }

    .profile table tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: white;
        padding: 1rem;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .profile table td {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        border: none;
        padding: 0.5rem 0;
    }

    .profile table td::before {
        content: attr(data-label);
        font-weight: bold;
        margin-right: 0.5rem;
    }

    footer {
        background-color: #4CAF50;
        color: white;
        text-align: center;
        padding: 1rem;
        width: 100%;
        margin-top: 2rem; /* Memberikan jarak antara konten dan footer */
        position: fixed;
        bottom: 0;
        height: 30px;
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
<main>
    <section class="profile">
        <h1>DATA SISWA YANG DIAJAR</h1>

        <table id="students-table">
            <thead>
                <tr>
                    <th class="hide-on-mobile">No</th>
                    <th>Nama Siswa</th>
                    <th>Nis</th>
                    <th>kelas</th>
                    <th>Alamat</th>
                    <th>No Hp</th>
                    <th>Tanggal lahir</th>
                    <th>jenis Kelamin</th>
                    <th>gaya belajar</th>
                    <th>Mata Pelajaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td class="hide-on-mobile"><?php echo htmlspecialchars($no++); ?></td>
                    <td data-label="Nama:"><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td data-label="NIS:"><?php echo htmlspecialchars($row['student_nis']); ?></td>
                    <td data-label="Kelas:"><?php echo htmlspecialchars($row['student_class']); ?></td>
                    <td data-label="Alamat:"><?php echo htmlspecialchars($row['student_address']); ?></td>
                    <td data-label="No Hp:"><?php echo htmlspecialchars($row['student_phone']); ?></td>
                    <td data-label="Tanggal Lahir:"><?php echo htmlspecialchars($row['student_dob']); ?></td>
                    <td data-label="Jenis Kelamin:"><?php echo htmlspecialchars($row['student_gender']); ?></td>
                    <td data-label="Gaya Belajar:"><?php echo htmlspecialchars($row['student_learning_style']); ?></td>
                    <td data-label="Mata Pelajaran:"><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td data-label="Aksi:">
                    <button onclick="deleteStudentSubject(<?php echo $row['student_id']; ?>, <?php echo $row['subject_id']; ?>)">Hapus</button>
                </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <form id="add-student-form">
    <label for="student_id">Pilih Siswa:</label>
    <select id="student_id" name="student_id" required>
        <!-- Opsi siswa akan dimuat di sini -->
    </select>

    <!-- Ini adalah input hidden yang akan menyimpan ID mata pelajaran -->
    <input type="hidden" id="subject_id" name="subject_id">

    <button type="submit">Tambah Siswa ke Mata Pelajaran</button>
</form>

    </section>
</main>

<footer>
    <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
</footer>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    loadSubjects(); // Memuat mata pelajaran saat halaman dimuat

    // Fungsi untuk memuat data siswa ke select option
    function loadStudents() {
    fetch('get_students.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data siswa:', data); // Debugging
            const studentSelect = document.getElementById('student_id');
            studentSelect.innerHTML = '';

            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }

            data.forEach(student => {
                let option = document.createElement('option');
                option.value = student.student_id;
                option.text = `${student.student_name} (NIS: ${student.student_nis})`;
                studentSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Fetch error:', error));
}



    // Fungsi untuk memuat data mata pelajaran yang diajarkan oleh guru yang sedang login
    function loadSubjects() {
        fetch('get_subjects.php')
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    // Ambil ID mata pelajaran pertama
                    const subjectId = data[0].id;

                    // Set nilai subject_id pada form
                    document.getElementById('subject_id').value = subjectId;
                } else {
                    alert('Tidak ada mata pelajaran yang diajarkan oleh guru ini.');
                }
            });
    }

    // Handle submit form tambah data siswa
    document.getElementById('add-student-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const studentId = document.getElementById('student_id').value;
        const subjectId = document.getElementById('subject_id').value;

        fetch('add_student_subject.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ student_id: studentId, subject_id: subjectId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Tampilkan pesan sukses
                location.reload(); // Reload halaman setelah menambah data
            } else {
                alert(data.message); // Tampilkan pesan error jika terjadi duplikasi atau kegagalan lainnya
            }
        });
    });

    // Fungsi untuk menghapus data siswa-mata pelajaran
    window.deleteStudentSubject = function(studentId, subjectId) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            fetch('delete_student_subject.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ student_id: studentId, subject_id: subjectId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data siswa berhasil dihapus.');
                    location.reload(); // Reload halaman setelah menghapus data
                } else {
                    alert('Gagal menghapus data siswa.');
                }
            });
        }
    }

    // Load data siswa saat halaman dimuat
    loadStudents();
});

function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = '../login.php'; // Ganti dengan halaman logout sesuai kebutuhan
    }
}

function toggleMenu() {
            var navList = document.getElementById('nav-list');
            navList.classList.toggle('show');
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
