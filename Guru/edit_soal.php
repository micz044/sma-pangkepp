<?php
session_start();

require_once '../koneksi.php';

// Ambil soal_id dari parameter URL
$soal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah soal_id valid
if ($soal_id <= 0) {
    die("Soal ID not found.");
}

// Ambil data soal berdasarkan soal_id
$sql = "SELECT soal, opsi1, opsi2, opsi3, opsi4, jawaban_benar FROM soal_rekomendasi WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $soal_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika soal ditemukan
if ($result->num_rows > 0) {
    $soal_data = $result->fetch_assoc();
} else {
    die("Soal not found.");
}

// Proses update soal jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_soal'])) {
    $soal = $_POST['soal'];
    $opsi1 = $_POST['opsi1'];
    $opsi2 = $_POST['opsi2'];
    $opsi3 = $_POST['opsi3'];
    $opsi4 = $_POST['opsi4'];
    $jawaban_benar = $_POST['jawaban_benar'];

    $sql_update = "UPDATE soal_rekomendasi SET soal = ?, opsi1 = ?, opsi2 = ?, opsi3 = ?, opsi4 = ?, jawaban_benar = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssssi", $soal, $opsi1, $opsi2, $opsi3, $opsi4, $jawaban_benar, $soal_id);

    if ($stmt_update->execute()) {
        header("Location: recommendations-guru.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: calc(100% - 22px); /* Adjust width to account for padding and border */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        textarea {
            resize: vertical;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            box-sizing: border-box;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Soal</h1>
        <form action="" method="post">
            <label for="soal">Soal:</label>
            <textarea name="soal" id="soal" rows="4" required><?php echo htmlspecialchars($soal_data['soal']); ?></textarea>

            <label for="opsi1">Opsi 1:</label>
            <input type="text" name="opsi1" id="opsi1" value="<?php echo htmlspecialchars($soal_data['opsi1']); ?>" required>

            <label for="opsi2">Opsi 2:</label>
            <input type="text" name="opsi2" id="opsi2" value="<?php echo htmlspecialchars($soal_data['opsi2']); ?>" required>

            <label for="opsi3">Opsi 3:</label>
            <input type="text" name="opsi3" id="opsi3" value="<?php echo htmlspecialchars($soal_data['opsi3']); ?>">

            <label for="opsi4">Opsi 4:</label>
            <input type="text" name="opsi4" id="opsi4" value="<?php echo htmlspecialchars($soal_data['opsi4']); ?>">

            <label for="jawaban_benar">Jawaban Benar:</label>
            <p>-pastikan ketik sesuai dengan opsi yang diatas-</p>
            <input type="text" name="jawaban_benar" id="jawaban_benar" value="<?php echo htmlspecialchars($soal_data['jawaban_benar']); ?>" required>

            <button type="submit" name="update_soal">Update Soal</button>
        </form>
        <div class="back-link">
            <a href="recommendations-guru.php">← Kembali ke halaman rekomendasi</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
