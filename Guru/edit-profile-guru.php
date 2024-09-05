<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teachers WHERE user_id = ?";
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $nip = $_POST['nip'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    
    // Cek apakah ada file yang diupload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $profile_photo = $_FILES['profile_photo']['name'];
        $target_dir = "uploads/"; // Pastikan folder ini ada
        $target_file = $target_dir . basename($profile_photo);
        
        // Pindahkan file ke folder tujuan
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
            $update_sql = "UPDATE teachers SET name = ?, nip = ?, jenis_kelamin = ?, alamat = ?, no_hp = ?, tanggal_lahir = ?, profile_photo = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssssssi", $name, $nip, $jenis_kelamin, $alamat, $no_hp, $tanggal_lahir, $profile_photo, $user_id);
        } else {
            echo "Error uploading the file.";
        }
    } else {
        $update_sql = "UPDATE teachers SET name = ?, nip = ?, jenis_kelamin = ?, alamat = ?, no_hp = ?, tanggal_lahir = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssi", $name, $nip, $jenis_kelamin, $alamat, $no_hp, $tanggal_lahir, $user_id);
    }

    if ($update_stmt->execute()) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='guru-profile.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Guru</title>
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

        .edit-profile {
            max-width: 600px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            animation-duration: 1s;
            text-align: center;
        }

        .edit-profile h1 {
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #333;
        }

        .edit-profile form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .edit-profile form input[type="text"],
        .edit-profile form input[type="date"],
        .edit-profile form select {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-profile form button {
            padding: 0.75rem 1.5rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .edit-profile form button:hover {
            background-color: #45a049;
        }

        .edit-profile a {
            display: inline-block;
            margin-top: 1rem;
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="edit-profile animate__animated animate__fadeIn">
        <h1>Edit Profil Guru</h1>
        <form action="edit-profile-guru.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
    <input type="text" name="nip" value="<?php echo $user['nip']; ?>" required>
    <select name="jenis_kelamin" required>
        <option value="Laki-laki" <?php echo ($user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
        <option value="Perempuan" <?php echo ($user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
    </select>
    <input type="text" name="alamat" value="<?php echo $user['alamat']; ?>" required>
    <input type="text" name="no_hp" value="<?php echo $user['no_hp']; ?>" maxlength="15" required>
    <input type="date" name="tanggal_lahir" value="<?php echo $user['tanggal_lahir']; ?>" required>
    
    <label for="">Foto Anda :</label><br>
    <!-- Tambahan input untuk file foto profil -->
    <input type="file" name="profile_photo"><br>
    
    <button type="submit">Simpan Perubahan</button>
</form>
<a href="guru-profile.php">Kembali ke Profil</a>

        <a href="guru-profile.php">Kembali ke Profil</a>
    </div>
</body>
</html>
