<?php
session_start();
require_once '../koneksi.php';

// Ambil ID tugas dari query string
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil detail tugas
$sql = "SELECT 
s.name AS subject_name, 
tg.title AS tugas_title, 
tg.jelas AS tugas_jelas, 
tg.description AS tugas_description, 
tg.due_date AS tugas_due_date 
FROM 
tugas tg 
JOIN 
subjects s ON tg.subject_id = s.id 
WHERE 
tg.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $task_id);
$stmt->execute();
$result = $stmt->get_result();

// Inisialisasi variabel $task
$task = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas - Rekomendasi Bahan Pelajaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        header, footer {
            background-color: #1f7a8c;
            color: #fff;
            text-align: center;
            padding: 1em 0;
            height: 40px;
            font-size: 16pxs;
        }

        header h1{
            font-size: 18px;
        }

        main {
            max-width: 800px;
            margin: 2em auto;
            padding: 0 1em;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .task-detail h1 {
            color: #1f7a8c;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 0.5em;
            text-align: center;
        }

        .task-info {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f1f8fc;
        }

        .task-info h2 {
            font-size: 1.8em;
            margin-bottom: 10px;
            text-align: center;
            color: #1f7a8c;
        }

        .task-info p {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .countdown {
            font-size: 1.2em;
            font-weight: bold;
            color: red;
        }

        iframe {
            width: 100%;
            height: 600px;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .submission-form {
            padding: 20px;
            border-radius: 8px;
            background-color: #eef7fb;
            margin-bottom: 20px;
        }

        .submission-form h2 {
            font-size: 1.6em;
            margin-bottom: 15px;
            color: #1f7a8c;
        }

        .submission-form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .submission-form input[type="text"],
        .submission-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submission-form button {
            padding: 10px 20px;
            background-color: #1f7a8c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submission-form button:hover {
            background-color: #155e73;
        }

        .back-button {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .back-button button {
            padding: 10px 20px;
            background-color: #ccc;
            margin-bottom: 20px;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button button:hover {
            background-color: #bbb;
        }
    </style>
    <script>
        // JavaScript untuk hitungan mundur
        function countdownTimer(dueDate) {
            const countDownDate = new Date(dueDate).getTime();
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML = days + "d " + hours + "h " +
                    minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "Waktu Pengumpulan Telah Berakhir";
                }
            }, 1000);
        }
    </script>
</head>
<body onload="countdownTimer('<?php echo $task['tugas_due_date']; ?>')">
    <header class="animate__animated animate__fadeInDown">
        <h1>Halaman Pengumpulan Tugas</h1>
    </header>

    <main class="animate__animated animate__fadeInUp">
        <section class="task-detail animate__animated animate__fadeIn">
            <h1>Detail Tugas</h1>
            <?php if ($task): ?>
                <div class="task-info">
                    <h2><?php echo htmlspecialchars($task['tugas_title']); ?></h2>
                    <p><strong>Penjelasan:</strong> 
                        <?php 
                        if (isset($task['tugas_jelas']) && !empty($task['tugas_jelas'])) {
                            echo htmlspecialchars($task['tugas_jelas']);
                        } else {
                            echo "Tidak ada penjelasan tersedia.";
                        }
                        ?>
                    </p>
                    <?php if (isset($task['tugas_description']) && !empty($task['tugas_description'])): ?>
                        <iframe src="../Guru/uploads/<?php echo htmlspecialchars($task['tugas_description']); ?>" frameborder="1"></iframe>
                    <?php endif; ?>
                    <p><strong>Batas Waktu:</strong> <span id="countdown"></span></p>
                    <p><strong>Mata Pelajaran:</strong> <?php echo htmlspecialchars($task['subject_name']); ?></p>
                </div>

                <div class="submission-form">
                    <h2>Form Pengumpulan Tugas</h2>
                    <form method="post" action="submit-task.php" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id); ?>">
                    <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($task['subject_name']); ?>">
    
                    <label for="student_name">Nama:</label>
                    <input type="text" id="student_name" name="student_name" required>
    
                    <label for="student_class">Kelas:</label>
                    <input type="text" id="student_class" name="student_class" required>
    
                    <label for="subject">Mata Pelajaran:</label>
                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($task['subject_name']); ?>" readonly>
    
                    <label for="file">File Pengumpulan:</label>
                    <input type="file" id="file" name="file" required>
    
                    <button type="submit">Kumpulkan Tugas</button>
                </form>
                </div>
            <?php else: ?>
                <p>Tugas tidak ditemukan.</p>
            <?php endif; ?>

            <!-- Tombol Kembali -->
            <div class="back-button">
                <button onclick="window.history.back();">Kembali</button>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Rekomendasi Bahan Pelajaran. Semua hak dilindungi.</p>
    </footer>
</body>
</html>
