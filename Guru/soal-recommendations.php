<?php
session_start();

require_once '../koneksi.php';

// Ambil rekomendasi_id dari parameter URL
$rekomendasi_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah rekomendasi_id valid
if ($rekomendasi_id <= 0) {
    die("Recommendation ID not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_soal'])) {
    $soal_baru = $_POST['soal'];
    $opsi1 = $_POST['opsi1'];
    $opsi2 = $_POST['opsi2'];
    $opsi3 = $_POST['opsi3'];
    $opsi4 = $_POST['opsi4'];
    $jawaban_benar = $_POST['jawaban_benar']; // Teks dari opsi yang dipilih

    $sql_insert = "INSERT INTO soal_rekomendasi (rekomendasi_id, soal, opsi1, opsi2, opsi3, opsi4, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssssss", $rekomendasi_id, $soal_baru, $opsi1, $opsi2, $opsi3, $opsi4, $jawaban_benar);

    if ($stmt_insert->execute()) {
        header("Location: soal-recommendations.php?id=" . $rekomendasi_id);
        exit;
    } else {
        echo "Error inserting record: " . $conn->error;
    }
}


// Ambil soal berdasarkan rekomendasi_id
$sql = "SELECT id, soal, opsi1, opsi2, opsi3, opsi4 FROM soal_rekomendasi WHERE rekomendasi_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rekomendasi_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal Rekomendasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Gaya umum */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        main {
            padding: 2rem;
            margin-top: 60px;
        }

        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .quiz-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #4CAF50;
        }

        .question {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .question h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            flex-grow: 1;
        }

        .question button {
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .question button:hover {
            background-color: #45a049;
        }

        .question .delete-btn {
            background-color: #f44336;
        }

        .question .delete-btn:hover {
            background-color: #e53935;
        }

        /* Gaya untuk form tambah soal */
        #tambahSoalForm {
            display: none;
            margin-top: 20px;
        }

        .form-container {
            margin-bottom: 2rem;
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
            color: #333;
        }

        .form-container label {
            font-size: 1rem;
            color: #333;
        }

        .form-container input[type="text"],
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container button[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-container .hide-form-btn {
            background-color: #f44336;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .form-container .hide-form-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
<main>
    <div class="quiz-container">
        <h1>Soal Rekomendasi</h1>

        <div style="margin-bottom: 20px;">
            <a href="recommendations-guru.php" style="text-decoration: none; color: #4CAF50;">
                ← Kembali ke halaman sebelumnya
            </a>
        </div>

        <!-- Menampilkan daftar soal yang ada -->
        <form action="update_soal.php" method="post">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="question">
                        <h2><?php echo htmlspecialchars($row['soal']); ?></h2>
                        <button type="button" onclick="window.location.href='edit_soal.php?id=<?php echo $row['id']; ?>'">Edit</button>
                        <button type="button" class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Hapus</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada soal untuk rekomendasi ini.</p>
            <?php endif; ?>
        </form>

        <!-- Tombol untuk menampilkan form tambah soal -->
        <button onclick="showForm()">Tambah Data</button>

    <!-- Form tambah soal yang tersembunyi -->
    <div id="tambahSoalForm" class="form-container">
        <h2>Tambah Soal Baru</h2>
        <form action="" method="post">
            <input type="hidden" name="add_soal" value="1">
            <input type="hidden" name="recommendation_id" value="<?php echo $rekomendasi_id; ?>">
            
            <label for="soal">Soal:</label>
            <textarea id="soal" name="soal" required></textarea>

            <label for="opsi1">Opsi 1:</label>
            <input type="text" id="opsi1" name="opsi1" required>

            <label for="opsi2">Opsi 2:</label>
            <input type="text" id="opsi2" name="opsi2" required>

            <label for="opsi3">Opsi 3:</label>
            <input type="text" id="opsi3" name="opsi3">

            <label for="opsi4">Opsi 4:</label>
            <input type="text" id="opsi4" name="opsi4">

            <label for="jawaban_benar">Jawaban Benar:</label>
            <select id="jawaban_benar" name="jawaban_benar" required>
                <!-- Options will be dynamically added here -->
            </select>

            <button type="submit">Tambah Soal</button>
            <button type="button" class="hide-form-btn" onclick="hideForm()">Batal</button>
        </form>
    </div>
    </div>
</main>

<script>
        function updateJawabanBenarOptions() {
            var opsi1 = document.getElementById('opsi1').value;
            var opsi2 = document.getElementById('opsi2').value;
            var opsi3 = document.getElementById('opsi3').value;
            var opsi4 = document.getElementById('opsi4').value;

            var jawabanBenarSelect = document.getElementById('jawaban_benar');

            // Kosongkan semua opsi yang ada
            jawabanBenarSelect.innerHTML = '';

            // Tambahkan opsi baru
            if (opsi1) {
                var option1 = document.createElement('option');
                option1.value = opsi1;
                option1.text = 'Opsi 1';
                jawabanBenarSelect.add(option1);
            }
            if (opsi2) {
                var option2 = document.createElement('option');
                option2.value = opsi2;
                option2.text = 'Opsi 2';
                jawabanBenarSelect.add(option2);
            }
            if (opsi3) {
                var option3 = document.createElement('option');
                option3.value = opsi3;
                option3.text = 'Opsi 3';
                jawabanBenarSelect.add(option3);
            }
            if (opsi4) {
                var option4 = document.createElement('option');
                option4.value = opsi4;
                option4.text = 'Opsi 4';
                jawabanBenarSelect.add(option4);
            }
        }

        // Tambahkan event listener untuk setiap input opsi
        document.getElementById('opsi1').addEventListener('input', updateJawabanBenarOptions);
        document.getElementById('opsi2').addEventListener('input', updateJawabanBenarOptions);
        document.getElementById('opsi3').addEventListener('input', updateJawabanBenarOptions);
        document.getElementById('opsi4').addEventListener('input', updateJawabanBenarOptions);

        // Panggil fungsi saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', updateJawabanBenarOptions);

    function showForm() {
        document.getElementById('tambahSoalForm').style.display = 'block';
    }

    function hideForm() {
        document.getElementById('tambahSoalForm').style.display = 'none';
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
            window.location.href = 'delete_soal.php?id=' + id;
        }
    }
</script>
</body>
</html>

<?php
$conn->close();
?>
