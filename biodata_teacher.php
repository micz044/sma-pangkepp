<?php
session_start();
require_once 'koneksi.php';

// Mendapatkan user_id dari parameter URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Flag untuk menunjukkan apakah data berhasil disimpan
$success = false;

// Mendapatkan daftar mata pelajaran
$subjects = [];
$sql_subjects = "SELECT id, name FROM subjects";
if ($result_subjects = $conn->query($sql_subjects)) {
    while ($row = $result_subjects->fetch_assoc()) {
        $subjects[] = $row;
    }
    $result_subjects->free();
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $nip = $_POST['nip'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $department = $_POST['department'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $subject_id = $_POST['subject_id'] ?? '';

    // Cek apakah data sudah ada
    $sql_check = "SELECT id FROM teachers WHERE user_id=?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            // Data sudah ada, lakukan UPDATE
            $sql = "UPDATE teachers SET nip=?, name=?, alamat=?, department=?, no_hp=?, tanggal_lahir=?, jenis_kelamin=? WHERE user_id=?";
            if ($stmt = $conn->prepare($sql)) {
                // UPDATE
                $stmt->bind_param("issssssi", $nip, $name, $alamat, $department, $no_hp, $tanggal_lahir, $jenis_kelamin, $user_id);
            } else {
                echo "Terjadi kesalahan saat menyiapkan query: " . $conn->error;
                exit;
            }
        } else {
            // Data belum ada, lakukan INSERT
            $sql = "INSERT INTO teachers (user_id, nip, name, alamat, department, no_hp, tanggal_lahir, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                // INSERT
                $stmt->bind_param("isssssss", $user_id, $nip, $name, $alamat, $department, $no_hp, $tanggal_lahir, $jenis_kelamin);
            } else {
                echo "Terjadi kesalahan saat menyiapkan query: " . $conn->error;
                exit;
            }
        }

        if ($stmt->execute()) {
            // Cek apakah mata pelajaran sudah diajarkan oleh guru lain
            $sql_check_subject = "SELECT 1 FROM teacher_subjects WHERE subject_id=? AND teacher_id != (SELECT id FROM teachers WHERE user_id=?)";
            if ($stmt_check_subject = $conn->prepare($sql_check_subject)) {
                $stmt_check_subject->bind_param("ii", $subject_id, $user_id);
                $stmt_check_subject->execute();
                $result_check_subject = $stmt_check_subject->get_result();
                if ($result_check_subject->num_rows > 0) {
                    echo "<script>
                        alert('Mata pelajaran sudah diisi oleh guru lain.');
                        window.location.href = 'biodata_teacher.php?user_id=$user_id'; // Ubah ke URL halaman biodata guru Anda
                    </script>";
                } else {
                    // Insert mata pelajaran
                    $sql_insert_subject = "INSERT INTO teachers_subjects (teacher_id, subject_id) VALUES ((SELECT id FROM teachers WHERE user_id=?), ?)";
                    if ($stmt_insert_subject = $conn->prepare($sql_insert_subject)) {
                        $stmt_insert_subject->bind_param("ii", $user_id, $subject_id);
                        if ($stmt_insert_subject->execute()) {
                            $success = true;
                        } else {
                            echo "Terjadi kesalahan saat menyimpan mata pelajaran: " . $stmt_insert_subject->error;
                        }
                        $stmt_insert_subject->close();
                    } else {
                        echo "Terjadi kesalahan saat menyiapkan query mata pelajaran: " . $conn->error;
                    }
                }
                $stmt_check_subject->close();
            } else {
                echo "Terjadi kesalahan saat menyiapkan query mata pelajaran: " . $conn->error;
            }
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

// Mendapatkan data guru berdasarkan user_id
$sql = "SELECT * FROM teachers WHERE user_id=?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
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
    <title>Biodata Teacher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="number"], input[type="date"], select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Biodata Guru</h1>
        <form action="biodata_teacher.php?user_id=<?php echo $user_id; ?>" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($teacher['name'] ?? '', ENT_QUOTES); ?>" required><br>

            <label for="nip">NIP:</label>
            <input type="number" id="nip" name="nip" value="<?php echo htmlspecialchars($teacher['nip'] ?? '', ENT_QUOTES); ?>" required><br>

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($teacher['alamat'] ?? '', ENT_QUOTES); ?>" required><br>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($teacher['department'] ?? '', ENT_QUOTES); ?>"><br>

            <label for="no_hp">No HP:</label>
            <input type="number" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($teacher['no_hp'] ?? '', ENT_QUOTES); ?>" required><br>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($teacher['tanggal_lahir'] ?? '', ENT_QUOTES); ?>"><br>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="laki-laki" <?php echo (isset($teacher['jenis_kelamin']) && $teacher['jenis_kelamin'] == 'laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="perempuan" <?php echo (isset($teacher['jenis_kelamin']) && $teacher['jenis_kelamin'] == 'perempuan') ? 'selected' : ''; ?>>Perempuan</option>
            </select><br>

            <label for="subject_id">Mata Pelajaran:</label>
            <select id="subject_id" name="subject_id" required>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name'], ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select><br>

            <input type="submit" value="Simpan">
        </form>

        <div class="link">
            <a href="login.php">Kembali ke Halaman Login</a>
        </div>
    </div>
</body>
</html>
