<?php
session_start();
require_once 'koneksi.php';

// Proses login ketika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Cek apakah role valid
    if (!in_array($role, ['student', 'teacher'])) {
        $error_message = "Peran tidak valid.";
    } else {
        $sql = "SELECT * FROM users WHERE username = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id']; // Simpan ID pengguna di sesi
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] == 'student') {
                    // Ambil ID siswa dari tabel students
                    $student_sql = "SELECT id FROM students WHERE user_id = ?";
                    $student_stmt = $conn->prepare($student_sql);
                    $student_stmt->bind_param("i", $row['id']);
                    $student_stmt->execute();
                    $student_result = $student_stmt->get_result();
                    
                    if ($student_result->num_rows > 0) {
                        $student_row = $student_result->fetch_assoc();
                        $_SESSION['student_id'] = $student_row['id']; // Simpan ID siswa di sesi
                        header("Location: Siswa/student-dashboard.php"); // Redirect ke dashboard siswa
                    } else {
                        $error_message = "Data siswa tidak ditemukan.";
                        $_SESSION = array(); // Hapus sesi jika terjadi kesalahan
                        session_destroy();
                    }
                } elseif ($row['role'] == 'teacher') {
                    // Ambil ID guru dari tabel teachers
                    $teacher_sql = "SELECT id FROM teachers WHERE user_id = ?";
                    $teacher_stmt = $conn->prepare($teacher_sql);
                    $teacher_stmt->bind_param("i", $row['id']);
                    $teacher_stmt->execute();
                    $teacher_result = $teacher_stmt->get_result();
                    
                    if ($teacher_result->num_rows > 0) {
                        $teacher_row = $teacher_result->fetch_assoc();
                        $_SESSION['teacher_id'] = $teacher_row['id']; // Simpan ID guru di sesi
                        header("Location: Guru/teacher-dashboard.php"); // Redirect ke dashboard guru
                    } else {
                        $error_message = "Data guru tidak ditemukan.";
                        $_SESSION = array(); // Hapus sesi jika terjadi kesalahan
                        session_destroy();
                    }
                }
                exit;
            } else {
                $error_message = "Password salah.";
            }
        } else {
            $error_message = "Pengguna tidak ditemukan.";
        }
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rekomendasi Bahan Pelajaran</title>
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

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        .login-form {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            text-align: center;
            box-sizing: border-box;
            max-width: 400px;
        }

        .login-form img {
            width: 100px;
            margin-bottom: 1rem;
        }

        .login-form h2 {
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
            .login-form {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container animate__animated animate__fadeIn">
        <div class="login-form">
            <center><img src="gambar/logo.png" alt="Logo Sekolah"></center>
            <h2>LOGIN</h2>
            <form id="loginForm" method="post" action="">
                <div class="form-group">
                    <label for="username">Nama :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>
                    <button type="button" id="togglePassword" style="margin-top: 10px;">Tampilkan Password</button>
                </div>
                <div class="form-group">
                    <label for="role">Login sebagai</label>
                    <select id="role" name="role" required>
                        <option value="">Pilih peran</option>
                        <option value="student">Siswa</option>
                        <option value="teacher">Guru</option>
                    </select>
                </div>
                <button type="submit">Login</button><br><br>
                <a href="register.php">Buat Akun</a>
            </form>
            <p id="error-message"><?php if (isset($error_message)) echo $error_message; ?></p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const role = document.getElementById('role').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');

            if (role && username && password) {
                loginForm.submit();
            } else {
                errorMessage.textContent = 'Semua field harus diisi.';
            }
        });

        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Tampilkan Password' : 'Sembunyikan Password';
        });
    });
    </script>
</body>
</html>