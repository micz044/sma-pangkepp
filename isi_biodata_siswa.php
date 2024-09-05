<?php
session_start();
require_once 'koneksi.php';

// Mendapatkan user_id dari parameter URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Flag untuk menunjukkan apakah data berhasil disimpan
$success = false;

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $nis = $_POST['nis'] ?? '';
    $class = $_POST['class'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $gaya_belajar = $_POST['gaya_belajar'] ?? '';

    // Cek apakah data sudah ada
    $sql_check = "SELECT id FROM students WHERE user_id=?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            // Data sudah ada, lakukan UPDATE
            $sql = "UPDATE students SET name=?, nis=?, class=?, alamat=?, no_hp=?, date_of_birth=?, jenis_kelamin=?, gaya_belajar=? WHERE user_id=?";
        } else {
            // Data belum ada, lakukan INSERT
            $sql = "INSERT INTO students (user_id, name, nis, class, alamat, no_hp, date_of_birth, jenis_kelamin, gaya_belajar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }
        $stmt_check->close();
    } else {
        echo "Terjadi kesalahan saat menyiapkan query: " . $conn->error;
    }

    if ($stmt = $conn->prepare($sql)) {
        if ($result_check->num_rows > 0) {
            // UPDATE
            $stmt->bind_param("sisssssss", $name, $nis, $class, $alamat, $no_hp, $date_of_birth, $jenis_kelamin, $gaya_belajar, $user_id);
        } else {
            // INSERT
            $stmt->bind_param("sisssssss", $user_id, $name, $nis, $class, $alamat, $no_hp, $date_of_birth, $jenis_kelamin, $gaya_belajar);
        }
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            echo "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Terjadi kesalahan saat menyiapkan query: " . $conn->error;
    }

    // Redirect ke halaman login jika berhasil
    if ($success) {
        echo "<script>
            alert('Biodata berhasil disimpan.');
            window.location.href = 'login.php'; // Ubah ke URL halaman login Anda
        </script>";
        exit;
    }
}

// Mendapatkan data siswa berdasarkan user_id
$sql = "SELECT * FROM students WHERE user_id=?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Terjadi kesalahan saat menyiapkan query: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Biodata Siswa</title>
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

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        .form-box {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            text-align: center;
            box-sizing: border-box;
            max-width: 600px;
        }

        .form-box img {
            width: 100px;
            margin-bottom: 1rem;
        }

        .form-box h2 {
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

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
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

        @media (max-width: 768px) {
            .form-box {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="form-container animate__animated animate__fadeIn">
        <div class="form-box">
            <center><img src="gambar/logo.png" alt="Logo Sekolah"></center>
            <h2>Isi Biodata Siswa</h2>
            <form method="post">
                <div class="form-group">
                    <label for="name">Nama :</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nis">NIS :</label>
                    <input type="number" id="nis" name="nis" value="<?php echo htmlspecialchars($student['nis'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="class">Kelas :</label>
                    <input type="text" id="class" name="class" value="<?php echo htmlspecialchars($student['class'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat :</label>
                    <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($student['alamat'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="no_hp">No HP :</label>
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($student['no_hp'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Tanggal Lahir :</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin :</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="laki-laki" <?php echo ($student['jenis_kelamin'] ?? '') == 'laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="perempuan" <?php echo ($student['jenis_kelamin'] ?? '') == 'perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gaya_belajar">Gaya Belajar :</label>
                    <select id="gaya_belajar" name="gaya_belajar" required>
                        <option value="video" <?php echo ($student['gaya_belajar'] ?? '') == 'video' ? 'selected' : ''; ?>>Video</option>
                        <option value="ppt" <?php echo ($student['gaya_belajar'] ?? '') == 'ppt' ? 'selected' : ''; ?>>PPT</option>
                        <option value="infografis" <?php echo ($student['gaya_belajar'] ?? '') == 'infografis' ? 'selected' : ''; ?>>Infografis</option>
                        <option value="modul" <?php echo ($student['gaya_belajar'] ?? '') == 'modul' ? 'selected' : ''; ?>>Modul</option>
                    </select>
                </div>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</body>
</html>
