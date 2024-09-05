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

// Variabel untuk menampung data rekomendasi
$recommendation = null;
$errors = [];
$isYouTube = false;
$isFile = false;

// Proses pembaruan data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $file = $_FILES['file'];

    // Validasi data
    if (empty($title) || empty($description)) {
        $errors[] = "Judul dan deskripsi harus diisi.";
    }

    if (empty($errors)) {
        // Tentukan folder penyimpanan berdasarkan subject_id
        $subject_folder = [
            1 => 'matematika',
            2 => 'bahasa_indonesia',
            3 => 'bahasa_inggris',
            4 => 'agama',
            5 => 'pkn'
        ];

        // Ambil subject_id dari rekomendasi saat ini
        $sql_subject = "SELECT subject_id, url FROM recommendations WHERE id = ?";
        $stmt_subject = $conn->prepare($sql_subject);
        $stmt_subject->bind_param("i", $id);
        $stmt_subject->execute();
        $result_subject = $stmt_subject->get_result();

        if ($result_subject->num_rows > 0) {
            $row_subject = $result_subject->fetch_assoc();
            $subject_id = $row_subject['subject_id'];
            $current_url = $row_subject['url'];
            $folder = isset($subject_folder[$subject_id]) ? $subject_folder[$subject_id] : '';

            // Proses file jika ada
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
                    $errors[] = 'Gagal meng-upload file.';
                }
            } else {
                // Jika tidak ada file baru, gunakan URL yang sudah ada
                if (empty($url)) {
                    $url = $current_url;
                }
            }

            if (empty($errors)) {
                $sql_update = "UPDATE recommendations SET title = ?, description = ?, url = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sssi", $title, $description, $url, $id);

                if ($stmt_update->execute()) {
                    header("Location: recommendations-guru.php");
                    exit;
                } else {
                    $errors[] = "Terjadi kesalahan saat memperbarui data.";
                }
            }
        } else {
            $errors[] = "Subject ID tidak ditemukan.";
        }
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mendapatkan data rekomendasi
    $sql = "SELECT id, subject_id, title, tipe, description, url FROM recommendations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recommendation = $result->fetch_assoc();
        $isYouTube = strpos($recommendation['url'], 'youtube.com') !== false || strpos($recommendation['url'], 'youtu.be') !== false;
        $isFile = !$isYouTube && !empty($recommendation['url']);
    } else {
        header("Location: recommendations-guru.php");
        exit;
    }
} else {
    header("Location: recommendations-guru.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rekomendasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            position: relative;
        }
        .logo-container {
            display: flex;
            align-items: center;
            flex: 1;
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
        nav {
            flex: 2;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
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
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        #file-upload {
            display: flex;
            align-items: center;
        }
        #file-name {
            margin-left: 10px;
            color: #333;
            font-size: 0.9rem;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            margin-bottom: 1rem;
        }
        .button {
            background-color: #f44336;
            color: white;
            text-align: center;
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #d32f2f;
        }
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 65px;
                padding: 1rem;
            }
            .menu-toggle {
                display: block;
                margin-bottom: 1rem;
            }
            nav {
                display: none;
                width: 100%;
            }
            nav.show {
                display: block;
            }
            nav ul {
                flex-direction: column;
                width: 100%;
            }
            nav ul li {
                margin: 0;
            }
            form insu {
                width: 98%; /* Atur lebar input form untuk tampilan mobile */
            }
            .form-group input, .form-group textarea {
                width: 98%; /* Memastikan input form lebih kecil pada mobile */
                padding: 0.3rem; /* Menyesuaikan padding untuk input form */
                font-size: 0.9rem; /* Mengurangi ukuran font input */
            }
            .form-group button {
                padding: 0.5rem 1rem; /* Menyesuaikan padding tombol */
                font-size: 0.9rem; /* Mengurangi ukuran font tombol */
            }
            main {
                padding: 1rem;
                width: 90%; /* Memastikan area utama lebih sesuai dengan tampilan mobile */
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <div class="logo">
                <img src="../gambar/logo.png" alt="Logo">
            </div>
            <div class="school-name">
                <p>SMA Muhammadiyah Pangkep</p>
            </div>
        </div>
        <nav id="nav-menu">
            <ul>
                <li><a href="recommendations-guru.php">Kembali Ke Rekomendasi</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Edit Rekomendasi</h1>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($recommendation): ?>
            <form method="POST" action="edit-recommendation.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($recommendation['id']); ?>">

        <div class="form-group">
            <label for="title">Judul Rekomendasi</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($recommendation['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($recommendation['description']); ?></textarea>
        </div>

        <div class="form-group" id="url-upload">
            <label for="url">Link URL</label>
            <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($recommendation['url']); ?>" required>
        </div>

        <div class="form-group" id="file-upload">
            <label for="file">Unggah File (jika ada)</label>
            <input type="file" id="file" name="file">
            <span id="file-name"><?php echo htmlspecialchars($recommendation['url']); ?></span>
        </div>

        <div class="form-group">
            <button type="submit" name="update">Perbarui</button>
            <a href="recommendations-guru.php" class="button">Kembali</a>
        </div>
        </form>
        <?php else: ?>
            <p>Rekomendasi tidak ditemukan.</p>
        <?php endif; ?>
    </main>

    <script>    
    document.addEventListener("DOMContentLoaded", function() {
        const urlInput = document.getElementById('url');
        const urlInputContainer = document.querySelector('.form-group#url-upload');
        const fileInputContainer = document.querySelector('.form-group#file-upload');
        const fileNameSpan = document.getElementById('file-name');

        function updateFormDisplay() {
            const urlValue = urlInput.value.trim();

            if (urlValue.startsWith("https://www.youtube.com") || urlValue.startsWith("https://youtu.be")) {
                urlInputContainer.style.display = 'block';
                fileInputContainer.style.display = 'none';
            } else if (urlValue) {
                urlInputContainer.style.display = 'none';
                fileInputContainer.style.display = 'block';
                fileNameSpan.textContent = urlValue;  // Set the file name
            } else {
                urlInputContainer.style.display = 'block';
                fileInputContainer.style.display = 'none';
                fileNameSpan.textContent = '';  // Clear the file name
            }
        }

        // Initial check
        updateFormDisplay();

        // Add event listener to URL input
        urlInput.addEventListener('input', updateFormDisplay);
    });
    </script>
</body>
</html>