<?php
session_start();
require_once '../koneksi.php';

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil rekomendasi_id dari parameter URL atau POST
$rekomendasi_id = isset($_GET['recommendation_id']) ? intval($_GET['recommendation_id']) : 0;
if ($rekomendasi_id === 0 && isset($_POST['recommendation_id'])) {
    $rekomendasi_id = intval($_POST['recommendation_id']);
}

// Cek apakah rekomendasi_id valid
if ($rekomendasi_id <= 0) {
    die("Recommendation ID not found.");
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
    <title>Soal Kuis</title>
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

        header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            height: 40px;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 99%;
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
            padding-top: 100px; /* Jarak antara header dan konten */
        }

        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 60px;
        }

        .quiz-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #4CAF50;
        }

        .question {
            margin-bottom: 1.5rem;
        }

        .question h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .question label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .question input[type="radio"] {
            margin-right: 0.5rem;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem;
            height: 30px;
            width: 100%;
            position: fixed;
            bottom: 0;
            box-shadow: 0 -1px 10px rgba(0, 0, 0, 0.1);
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

        #timer {
            font-size: 1.2rem;
            color: #e74c3c;
            text-align: center;
            margin-bottom: 1rem;
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
    <div class="quiz-container">
        <h1>Soal Kuis</h1>
        <div id="timer">Waktu tersisa: 10:00</div>
        <form action="submit_answers.php" method="post">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="question">
                        <h2><?php echo htmlspecialchars($row['soal']); ?></h2>
                        <?php if (!is_null($row['opsi1'])): ?>
                            <label><input type="radio" name="question_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['opsi1']); ?>"> <?php echo htmlspecialchars($row['opsi1']); ?></label>
                        <?php endif; ?>
                        <?php if (!is_null($row['opsi2'])): ?>
                            <label><input type="radio" name="question_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['opsi2']); ?>"> <?php echo htmlspecialchars($row['opsi2']); ?></label>
                        <?php endif; ?>
                        <?php if (!is_null($row['opsi3'])): ?>
                            <label><input type="radio" name="question_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['opsi3']); ?>"> <?php echo htmlspecialchars($row['opsi3']); ?></label>
                        <?php endif; ?>
                        <?php if (!is_null($row['opsi4'])): ?>
                            <label><input type="radio" name="question_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['opsi4']); ?>"> <?php echo htmlspecialchars($row['opsi4']); ?></label>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                <button type="submit" class="submit-btn">Kirim Jawaban</button>
            <?php else: ?>
                <p>Tidak ada soal untuk rekomendasi ini.</p>
            <?php endif; ?>
        </form>
        <div class="back-link">
            <a href="recommendations.php">← Kembali ke halaman rekomendasi</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 SMA Muhammadiyah Pangkep. All rights reserved.</p>
</footer>

<script>
    // Timer 10 menit
    let time = 600; // 10 menit dalam detik
    const timerElement = document.getElementById('timer');
    const interval = setInterval(function() {
        let minutes = Math.floor(time / 60);
        let seconds = time % 60;
        if (seconds < 10) {
            seconds = '0' + seconds;
        }
        timerElement.textContent = 'Waktu tersisa: ' + minutes + ':' + seconds;
        time--;
        if (time < 0) {
            clearInterval(interval);
            document.querySelector('form').submit(); // Kirim formulir ketika waktu habis
        }
    }, 1000);
</script>

</body>
</html>

<?php
$conn->close();
?>
