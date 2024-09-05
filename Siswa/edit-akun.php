<?php
session_start();

require_once '../koneksi.php';

// Cek apakah user sudah login, jika tidak, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID pengguna dari sesi atau URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
} else {
    echo "ID pengguna tidak ditemukan.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input dan cek apakah password sesuai dengan konfirmasi password
    if (!empty($password) && $password != $confirm_password) {
        $error_message = "Password dan konfirmasi password tidak cocok.";
    } else {
        if (!empty($password)) {
            // Hash password jika diisi
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update data user termasuk password
            $sql = "UPDATE users SET username=?, email=?, password=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $id);
        } else {
            // Update data user tanpa password
            $sql = "UPDATE users SET username=?, email=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $email, $id);
        }

        if ($stmt->execute()) {
            $success_message = "Data user berhasil diupdate.";
        } else {
            $error_message = "Terjadi kesalahan: " . $stmt->error;
        }

        $stmt->close();
    }
}


// Ambil data user dari database untuk ditampilkan di form
$sql = "SELECT username, email FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .edit-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .edit-container h1 {
            margin-bottom: 1rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #4CAF50;
        }

        .password-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .password-toggle label {
            margin-right: 10px;
            cursor: pointer;
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
            margin-top: 1rem;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-link {
            display: block;
            margin-top: 1rem;
            color: #4CAF50;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        #error-message {
            color: red;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h1>Edit Akun</h1>
        <form method="POST" action="edit-akun.php?id=<?php echo htmlspecialchars($id); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="password-toggle">
                    <input type="password" id="password" name="password">
                    <label>
                        <input type="checkbox" id="togglePassword">
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <div class="password-toggle">
                    <input type="password" id="confirm_password" name="confirm_password">
                    <label>
                        <input type="checkbox" id="toggleConfirmPassword">
                    </label>
                </div>
            </div>
            <button type="submit">Update</button>
            <a href="siswa-profile.php" class="back-link">Kembali ke Profil</a>
        </form>
        <p id="error-message"><?php if (isset($error_message)) echo $error_message; ?></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordField.setAttribute('type', type);
        });

        <?php if (isset($error_message)) { ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "<?php echo $error_message; ?>",
            });
        <?php } elseif (isset($success_message)) { ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "<?php echo $success_message; ?>",
            }).then(function() {
                window.location.href = 'siswa-profile.php'; // Redirect after success
            });
        <?php } ?>
    });
    </script>
</body>
</html>

