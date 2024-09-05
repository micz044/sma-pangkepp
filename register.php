<?php
session_start();
require_once 'koneksi.php';

$error_message = ""; // Variable untuk menyimpan pesan error

// Proses registrasi ketika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $subjects = isset($_POST['subjects']) ? $_POST['subjects'] : []; // Mengambil mata pelajaran yang dipilih jika ada

    // Cek apakah username atau email sudah terdaftar
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_message = "Username atau email telah terdaftar.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user ke tabel users
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
            if ($stmt->execute()) {
                // Mendapatkan ID pengguna yang baru dibuat
                $userId = $conn->insert_id;

                // Jika peran adalah guru, simpan mata pelajaran ke tabel teacher_subjects
                if ($role === 'teacher' && !empty($subjects)) {
                    foreach ($subjects as $subjectId) {
                        $sql = "INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $userId, $subjectId);
                        $stmt->execute();
                    }
                }

                // Redirect berdasarkan peran
                if ($role === 'student') {
                    header("Location: isi_biodata_siswa.php?user_id=" . $userId);
                } elseif ($role === 'teacher') {
                    header("Location: biodata_teacher.php?user_id=" . $userId);
                } else {
                    header("Location: login.php");
                }
                exit;
            } else {
                $error_message = "Terjadi kesalahan: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $error_message = "Terjadi kesalahan saat menyiapkan query: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Rekomendasi Bahan Pelajaran</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('gambar/Sekolah.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        .register-form {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            text-align: center;
            box-sizing: border-box;
            max-width: 400px;
        }

        .register-form img {
            width: 100px;
            margin-bottom: 1rem;
        }

        .register-form h2 {
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #4CAF50;
        }

        button {
            width: 100%;
            padding: 0.5rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            box-sizing: border-box;
        }

        button:hover {
            background-color: #45a049;
        }

        #error-message {
            color: red;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .register-form {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="register-container animate__animated animate__fadeIn">
        <div class="register-form">
            <center><img src="gambar/logo.png" alt="Logo Sekolah"></center>
            <h2>REGISTER</h2>
            <form id="registerForm" method="post" action="">
                <div class="form-group">
                    <label for="username">Username :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>
                    <input type="checkbox" id="showPassword" onclick="togglePassword()">Tampilkan
                </div>
                <div class="form-group">
                    <label for="role">Login sebagai</label>
                    <select id="role" name="role" required>
                        <option value="">Pilih peran</option>
                        <option value="student">Siswa</option>
                        <option value="teacher">Guru</option>
                    </select>
                </div>
                <?php if ($error_message): ?>
                <div id="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <button type="submit">Register</button><br>
                <center><a href="login.php">Kembali ke halaman login</a></center>
            </form>
        </div>
    </div>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var showPasswordCheckbox = document.getElementById('showPassword');
            if (showPasswordCheckbox.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
