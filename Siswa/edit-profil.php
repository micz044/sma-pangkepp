<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Tangani form jika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $nis = $_POST['nis'];
    $class = $_POST['class'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $gaya_belajar = $_POST['gaya_belajar']; // Ambil gaya belajar
    $date_of_birth = $_POST['date_of_birth'];
    $jenis_kelamin = $_POST['jenis_kelamin']; // Ambil jenis kelamin
    $profile_photo = $_FILES['profile_photo'];

    $profile_photo_path = '';

    // Ambil foto profil lama dari database
    $sql = "SELECT profile_photo FROM students WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_photo = '';
    if ($row = $result->fetch_assoc()) {
        $old_photo = $row['profile_photo'];
    }
    $stmt->close();

    if ($profile_photo['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $profile_photo['tmp_name'];
        $file_name = basename($profile_photo['name']);
        $profile_photo_path = 'uploads/' . $file_name;

        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($file_tmp_name, $profile_photo_path)) {
            // Hapus foto profil lama jika ada
            if ($old_photo && file_exists($old_photo)) {
                unlink($old_photo);
            }
        } else {
            $profile_photo_path = '';
        }
    } else {
        // Jika tidak ada foto baru, gunakan foto lama
        $profile_photo_path = $old_photo;
    }

    // Query untuk memperbarui profil
    $sql = "UPDATE students SET name = ?, nis = ?, class = ?, alamat = ?, no_hp = ?, gaya_belajar = ?, date_of_birth = ?, jenis_kelamin = ?, profile_photo = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $name, $nis, $class, $alamat, $no_hp, $gaya_belajar, $date_of_birth, $jenis_kelamin, $profile_photo_path, $user_id);

    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui.";
        $redirect_url = 'siswa-profile.php';
    } else {
        $message = "Gagal memperbarui profil: " . $stmt->error;
        $redirect_url = 'edit-profil.php';
    }

    $stmt->close();
    $conn->close();

    // Redirect ke halaman yang sesuai
    header("Location: $redirect_url?message=" . urlencode($message));
    exit;
}

// Ambil data pengguna untuk ditampilkan di formulir
$sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
            width: 100%;
            top: 0;
            left: 0;
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
            padding-top: 120px;
            margin-bottom: 80px;
            position: relative;
            z-index: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile {
            max-width: 600px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .profile h2 {
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: #333;
            text-align: center;
        }

        .profile form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .profile label {
            font-weight: bold;
        }

        .profile input[type="text"],
        .profile input[type="date"],
        .profile input[type="file"] {
            padding: 0.75rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
        }

        .profile input[type="text"]:focus,
        .profile input[type="date"]:focus,
        .profile input[type="file"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .profile button {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .profile button:hover {
            background-color: #45a049;
        }

        .profile p {
            text-align: center;
            color: #4CAF50;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            height: 20px;
            width: 100%;
            position: fixed;
            bottom: 0;
            z-index: 1000;
        }
        select{
            height: 30px;
        }

        .back-link {
            text-align: center;
            margin-top: 1rem;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
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
    </header>

    <main>
        <section class="profile animate__animated animate__fadeIn">
            <h2>Edit Profil</h2>
            <form id="editProfileForm" action="edit-profil.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                
                <label for="name">Nama:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                
                <label for="nis">NIS:</label>
                <input type="text" id="nis" name="nis" value="<?php echo htmlspecialchars($user['nis']); ?>" required>

                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="laki-laki" <?php echo $user['jenis_kelamin'] == 'laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="perempuan" <?php echo $user['jenis_kelamin'] == 'perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                </select>

                <label for="class">Kelas:</label>
                <input type="text" id="class" name="class" value="<?php echo htmlspecialchars($user['class']); ?>">
                
                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($user['alamat']); ?>" required>
                
                <label for="no_hp">No Hp:</label>
                <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
                
                <label for="date_of_birth">Tanggal Lahir:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>">

                <label for="gaya_belajar">Gaya Belajar:</label>
                <select id="gaya_belajar" name="gaya_belajar" required>
                    <option value="video" <?php echo $user['gaya_belajar'] == 'video' ? 'selected' : ''; ?>>Video</option>
                    <option value="ppt" <?php echo $user['gaya_belajar'] == 'ppt' ? 'selected' : ''; ?>>PPT</option>
                    <option value="modul" <?php echo $user['gaya_belajar'] == 'modul' ? 'selected' : ''; ?>>Modul</option>
                    <option value="infografis" <?php echo $user['gaya_belajar'] == 'infografis' ? 'selected' : ''; ?>>Infografis</option>
                </select>
                            
                <label for="profile_photo">Foto Profil:</label>
                <input type="file" id="profile_photo" name="profile_photo">
                
                <button type="submit">Simpan Perubahan</button>
            </form>
            <?php if (isset($_GET['message'])) { ?>
                <p><?php echo htmlspecialchars($_GET['message']); ?></p>
            <?php } ?>
            <div class="back-link">
            <a href="siswa-profile.php">← Kembali ke halaman Profil</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 SMA Muhammadiyah Pangkep. All rights reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            const navList = document.getElementById('nav-list');
            navList.classList.toggle('show');
        }

        function confirmLogout() {
            if (confirm("Apakah Anda yakin ingin keluar?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>
