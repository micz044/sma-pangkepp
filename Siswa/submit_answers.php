<?php
session_start();

require_once '../koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil student_id berdasarkan user_id
$user_id = $_SESSION['user_id'];
$student_id_sql = "SELECT id FROM students WHERE user_id = ?";
$stmt = $conn->prepare($student_id_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_id_result = $stmt->get_result();
$student_id = $student_id_result->fetch_assoc()['id'];

// Inisialisasi variabel untuk total nilai dan total soal
$total_nilai = 0;
$total_soal = 0;

// Proses setiap jawaban
foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0) {
        $soal_id = str_replace('question_', '', $key);

        // Ambil jawaban benar dan rekomendasi_id dari tabel soal_rekomendasi
        $sql = "SELECT jawaban_benar, rekomendasi_id FROM soal_rekomendasi WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $soal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Hitung nilai
        $nilai = ($value === $row['jawaban_benar']) ? 100.00 : 0.00;
        $total_nilai += $nilai;
        $total_soal++;

        // Simpan jawaban, nilai, dan rekomendasi_id ke dalam tabel nilai_siswa
        $sql = "
            INSERT INTO nilai_siswa (siswa_id, soal_id, jawaban_siswa, nilai, tanggal, rekomendasi_id)
            VALUES (?, ?, ?, ?, CURDATE(), ?)
            ON DUPLICATE KEY UPDATE jawaban_siswa = VALUES(jawaban_siswa), nilai = VALUES(nilai)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $student_id, $soal_id, $value, $nilai, $row['rekomendasi_id']);
        $stmt->execute();
    }
}

// Hitung nilai akhir dalam rentang 0 hingga 100
$nilai_akhir = ($total_soal > 0) ? ($total_nilai / $total_soal) : 0;
$nilai_akhir = min($nilai_akhir, 100); // Batasi nilai maksimal hingga 100

// Perbarui nilai akhir untuk setiap rekomendasi di tabel nilai_siswa
$sql = "UPDATE nilai_siswa SET recommendation_score = ? WHERE siswa_id = ? AND rekomendasi_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dii", $nilai_akhir, $student_id, $row['rekomendasi_id']);
$stmt->execute();

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: 'Roboto', sans-serif;
        }
        .message-container {
            text-align: center;
        }
        .message-container h1 {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h1>Terima kasih telah mengisi jawaban!</h1>
        <p>Jawaban Anda telah berhasil dikirim.</p>
        <p>Nilai keseluruhan Anda adalah: <?php echo number_format($nilai_akhir, 2); ?> / 100</p>
        <button onclick="redirectToRecommendations()">Kembali ke Halaman Rekomendasi</button>
    </div>
    <script>
        function redirectToRecommendations() {
            window.location.href = 'recommendations.php'; // Ganti dengan URL halaman rekomendasi Anda
        }
    </script>
</body>
</html>