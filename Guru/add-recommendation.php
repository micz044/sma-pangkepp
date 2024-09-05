<?php
session_start(); // Memulai session

require_once '../koneksi.php';

// Ambil ID guru dari session
$teacher_id = $_SESSION['teacher_id']; // Pastikan ini di-set saat login

// Ambil mata pelajaran yang diajarkan oleh guru ini
$subjects = [];
if ($teacher_id) {
    $sql = "SELECT id, name FROM subjects WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }

    $stmt->close();
}

// Proses form jika di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = $_POST['subject_id'];
    $title = $_POST['title'];
    $tipe = $_POST['tipe'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $file = $_FILES['file'];

    // Tentukan folder penyimpanan berdasarkan subject_id
    $subject_folder = [
        1 => 'matematika',
        2 => 'bahasa_indonesia',
        3 => 'bahasa_inggris',
        4 => 'agama',
        5 => 'pkn'
    ];
    $folder = isset($subject_folder[$subject_id]) ? $subject_folder[$subject_id] : '';

    // Cek apakah tipe rekomendasi sudah ada untuk mata pelajaran ini
    $sql = "SELECT id FROM recommendations WHERE subject_id = ? AND tipe = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $subject_id, $tipe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = 'Rekomendasi dengan tipe ini sudah ada untuk mata pelajaran ini.';
    } else {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $file_tmp_name = $file['tmp_name'];
            $file_name = basename($file['name']);
            $upload_dir = "../rekomendasi/$folder/";
            $file_path = $upload_dir . $file_name;

            // Buat folder jika belum ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Pindahkan file ke folder yang ditentukan
            if (move_uploaded_file($file_tmp_name, $file_path)) {
                // Simpan URL file ke kolom url
                $url = $file_path;
            } else {
                $message = 'Gagal meng-upload file.';
                exit;
            }
        }

        // Query untuk menambah data rekomendasi
        $sql = "INSERT INTO recommendations (subject_id, title, tipe, description, url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $subject_id, $title, $tipe, $description, $url);

        if ($stmt->execute()) {
            $message = 'Rekomendasi berhasil ditambahkan.';
            $redirect = 'recommendations-guru.php';
        } else {
            $message = 'Terjadi kesalahan: ' . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rekomendasi</title>
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
        }
        form {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="file"], select, textarea {
            width: 99%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background: #5cb85c;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #4cae4c;
        }
        .message {
            margin: 20px 0;
            padding: 10px;
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
        }
        .message.error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .btn-back {
            background: #d9534f;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back:hover {
            background: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Rekomendasi</h2>
        <?php if (isset($message)): ?>
            <div class="message <?php echo isset($redirect) ? '' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php if (isset($redirect)): ?>
                <script>
                    setTimeout(function() {
                        window.location.href = "<?php echo $redirect; ?>";
                    }, 2000);
                </script>
            <?php endif; ?>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="subject_id">Mata Pelajaran:</label>
            <select id="subject_id" name="subject_id" required>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id']; ?>"><?= htmlspecialchars($subject['name']); ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="title">Judul:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="tipe">Tipe:</label>
            <select id="tipe" name="tipe" required>
                <option value="video">Video</option>
                <option value="modul">Modul</option>
                <option value="ppt">PPT</option>
                <option value="infografis">Infografis</option>
            </select><br>

            <label for="description">Deskripsi:</label>
            <textarea id="description" name="description" rows="4" cols="50"></textarea><br>

            <label for="url">URL:</label>
            <input type="text" id="url" name="url"><br>

            <label for="file">File:</label>
            <input type="file" id="file" name="file"><br>

            <input type="submit" value="Tambah Rekomendasi">
            <a href="recommendations-guru.php" class="btn-back">Kembali</a>
        </form>
    </div>
</body>
</html>