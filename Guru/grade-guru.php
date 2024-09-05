<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

// Query untuk mendapatkan data nilai berdasarkan mata pelajaran yang diajarkan guru
$sql = "
    SELECT g.id AS grade_id, s.name AS subject_name, st.name AS student_name,
           g.nilai_tugas, g.nilai_ulangan_harian, g.kehadiran, g.nilai_uts, g.nilai_uas, g.grade
    FROM grades g
    JOIN subjects s ON g.subject_id = s.id
    JOIN students st ON g.student_id = st.id
    WHERE s.teacher_id = ?
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data mata pelajaran dan siswa untuk dropdown
$subjectsSql = "SELECT id, name FROM subjects WHERE teacher_id = ?";
$subjectsStmt = $conn->prepare($subjectsSql);
$subjectsStmt->bind_param("i", $teacher_id);
$subjectsStmt->execute();
$subjectsResult = $subjectsStmt->get_result();

$studentsSql = "SELECT id, name FROM students";
$studentsResult = $conn->query($studentsSql);

?>

<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas - Rekomendasi Tugas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome CDN -->
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
            padding-top: 170px;
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
            overflow: auto;
        }

        label[for="subject"],
#subject {
    display: none;
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

        .form-container {
            margin-bottom: 2rem;
        }

        .form-container form {
    display: flex;
    flex-direction: row; /* Ubah ini dari column ke row */
    gap: 1rem;
    align-items: center; /* Untuk memastikan semua elemen sejajar secara vertikal */
}

.form-container div {
    display: flex;
    flex-direction: column; /* Tetap flex untuk menyusun label di atas input */
    margin-right: 10px; /* Tambahkan margin kanan antar elemen */
}

        .form-container label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-container input, .form-container select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container button {
            padding: 0.75rem;
            margin-top: 25px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            align-self: flex-start; /* Menjaga tombol berada di awal baris, sejajar dengan input */
            margin-left: 10px; /* Tambahkan margin kiri untuk tombol */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
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

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons button {
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-buttons .save-btn {
            background-color: #4CAF50;
        }

        .action-buttons .save-btn:hover {
            background-color: #45a049;
        }

        .action-buttons .delete-btn {
            background-color: #f44336;
        }

        .action-buttons .delete-btn:hover {
            background-color: #e53935;
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
    header {
        flex-direction: column;
        align-items: flex-start;
    }

    .logo-container {
        margin-bottom: 1rem;
    }

    .profile {
        padding: 1rem;
    }

.table-container {
    overflow-x: auto; /* Scroll horisontal jika diperlukan */
    max-width: 100%; /* Pastikan kontainer tidak melampaui lebar layar */
}

/* Tabel Responsif */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
    display: block;
    white-space: nowrap;
    overflow-y: auto; /* Scroll vertikal */
}

table th:nth-child(1), table td:nth-child(1) {
    min-width: 50px; /* Sesuaikan nilai ini dengan kebutuhan */
    width: 50px; /* Tambahkan ini untuk memastikan lebar kolom tetap konsisten */
    text-align: center; /* Agar teks dalam kolom No berada di tengah */
}


/* Header tabel */
table th {
    background-color: #4CAF50;
    color: white;
    position: sticky;
    top: 0; /* Menjaga header tetap di atas */
    border: 1px solid #ddd; /* Border header */
}

/* Sel tabel */
table th, table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd; /* Border sel */
    min-width: 100px; /* Menetapkan lebar minimum untuk kolom */
}

   .form-container {
        max-width: 100%;
        margin: 0 auto;
    }

    .form-container form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .form-container div {
        flex: 1 1 100%;
        margin-right: 0;
    }

    .form-container input, .form-container select {
        width: 100%;
    }

    .action-buttons {
        display: inline-block;
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
        <h1>DATA NILAI SISWA</h1>
        <div class="table-container">
        <table id="grades-table">
            <thead>
                <tr>
                    <th class="no-column">No</th>
                    <th>Nama Siswa</th>
                    <th>Mata Pelajaran</th>
                    <th>Nilai Tugas</th>
                    <th>Nilai Ulangan Harian</th>
                    <th>Kehadiran</th>
                    <th>Nilai UTS</th>
                    <th>Nilai UAS</th>
                    <th>Total Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr data-id="<?php echo htmlspecialchars($row['grade_id']); ?>">
                        <td class="no-column"><?php echo htmlspecialchars($no++); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><input type="number" class="homework-input" value="<?php echo htmlspecialchars($row['nilai_tugas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" step="0.01" min="0" max="100"></td>
                        <td><input type="number" class="daily-quiz-input" value="<?php echo htmlspecialchars($row['nilai_ulangan_harian'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" step="0.01" min="0" max="100"></td>
                        <td><input type="number" class="attendance-input" value="<?php echo htmlspecialchars($row['kehadiran'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" step="0.01" min="0" max="100"></td>
                        <td><input type="number" class="uts-input" value="<?php echo htmlspecialchars($row['nilai_uts'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" step="0.01" min="0" max="100"></td>
                        <td><input type="number" class="uas-input" value="<?php echo htmlspecialchars($row['nilai_uas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" step="0.01" min="0" max="100"></td>
                        <td><?php echo htmlspecialchars($row['grade'] ?? ''); ?></td>
                        <td class="action-buttons">
                            <button class="save-btn"><i class="fas fa-save"></i> Simpan</button>
                            <button class="delete-btn"><i class="fas fa-trash"></i> Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <div class="form-container">
            <form id="add-grade-form">
                <div>
                    <label for="student">Siswa:</label>
                    <select id="student" name="student_id" required>
                        <?php while ($student = $studentsResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($student['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="subject">Mata Pelajaran:</label>
                    <select id="subject" name="subject_id" required>
                        <?php while ($subject = $subjectsResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($subject['id']); ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="homework_grade">Nilai Tugas:</label>
                    <input type="number" id="homework_grade" name="nilai_tugas" step="0.01" min="0" max="100" required>
                </div>
                <div>
                    <label for="daily_quiz_grade">Nilai Ulangan Harian:</label>
                    <input type="number" id="daily_quiz_grade" name="nilai_ulangan_harian" step="0.01" min="0" max="100" required>
                </div>
                <div>
                    <label for="attendance">Kehadiran:</label>
                    <input type="number" id="attendance" name="kehadiran" step="0.01" min="0" max="100" required>
                </div>
                <div>
                    <label for="uts_grade">Nilai UTS:</label>
                    <input type="number" id="uts_grade" name="nilai_uts" step="0.01" min="0" max="100" required>
                </div>
                <div>
                    <label for="uas_grade">Nilai UAS:</label>
                    <input type="number" id="uas_grade" name="nilai_uas" step="0.01" min="0" max="100" required>
                </div>
                <button type="submit">Tambah Nilai</button>
            </form>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2024 Rekomendasi Bahan Pelajaran. All rights reserved.</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('grades-table');
        const addGradeForm = document.getElementById('add-grade-form');

        table.addEventListener('click', function (event) {
            if (event.target.classList.contains('save-btn')) {
                const row = event.target.closest('tr');
                const gradeId = row.dataset.id;
                const homeworkGrade = row.querySelector('.homework-input').value;
                const dailyQuizGrade = row.querySelector('.daily-quiz-input').value;
                const attendance = row.querySelector('.attendance-input').value;
                const utsGrade = row.querySelector('.uts-input').value;
                const uasGrade = row.querySelector('.uas-input').value;

                // Update the grades via AJAX
                fetch('update-grade.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${gradeId}&nilai_tugas=${homeworkGrade}&nilai_ulangan_harian=${dailyQuizGrade}&kehadiran=${attendance}&nilai_uts=${utsGrade}&nilai_uas=${uasGrade}`
            })
            .then(response => response.text())
            .then(data => {
                alert('Nilai berhasil diperbarui!');
                window.location.href = 'grade-guru.php'; // Arahkan ke halaman grade-guru.php
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

            if (event.target.classList.contains('delete-btn')) {
                const row = event.target.closest('tr');
                const gradeId = row.dataset.id;

                // Delete the grade via AJAX
                fetch('delete-grade.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${gradeId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        row.remove();
                        alert('Nilai berhasil dihapus!');
                    } else {
                        alert('Terjadi kesalahan saat menghapus nilai.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });

        addGradeForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(addGradeForm);
            fetch('add-grade.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert('Nilai berhasil ditambahkan!');
                    window.location.href = 'grade-guru.php';
                } else {
                    alert('Terjadi kesalahan saat menambahkan nilai.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

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

<?php
$conn->close();
?>
