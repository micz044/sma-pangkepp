<?php
// Koneksi ke database
require_once '../koneksi.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$subjectId = intval($_GET['id']);

// Tabel-tabel yang mungkin berisi data
$tables = ['agama', 'matematika', 'bahasa_indonesia', 'bahasa_inggris', 'pkn'];
$data = [];

// Loop untuk setiap tabel
foreach ($tables as $table) {
    $sql = "SELECT name, description FROM $table WHERE subject_id = $subjectId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data[$table] = $result->fetch_all(MYSQLI_ASSOC);
    }
}

$conn->close();

// Fungsi untuk mengubah nama tabel menjadi format yang benar
function formatTableName($table) {
    switch ($table) {
        case 'bahasa_indonesia':
            return 'BAHASA INDONESIA';
        case 'bahasa_inggris':
            return 'BAHASA INGGRIS';
        case 'pkn':
            return 'PKN';
        default:
            return ucfirst($table);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mata Pelajaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .subject-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            background-color: white;
            padding: 2rem;
            height: 100%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .subject-detail {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            text-align: center;
        }

        .subject-detail h3 {
            margin: 0;
        }

        .subject-detail p {
            margin: 10px 0;
        }

        .pdf-thumbnail {
            width: 70px;
            height: auto;
            display: block;
            margin: 10px auto;
            cursor: pointer;
        }

        .pdf-thumbnail:hover {
            opacity: 0.8;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            width: fit-content;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            background: white;
            position: relative;
            height: 80%;
        }

        .modal-content iframe {
            width: 100%;
            height: 100%;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #aaa;
            font-size: 35px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (!empty($data)): ?>
        <?php foreach ($data as $table => $rows): ?>
            <center><h1><?php echo htmlspecialchars(formatTableName($table)); ?></h1><br></center>
            <div class="subject-details">
                <?php foreach ($rows as $index => $row): //Menggunakan $index untuk menentukan nomor pertemuan ?>
                    <div class="subject-detail">
                        <h3>Pertemuan <?php echo $index + 1; ?></h3> <!-- Menampilkan nomor pertemuan -->
                        <?php foreach ($row as $key => $value): ?>
                            <?php if ($key === 'description'): ?>
                                <a href="view-pdf.php?file=<?php echo urlencode($value); ?>&subject=<?php echo urlencode($table); ?>" target="_blank">
                                    <img src="../gambar/pdf.png" alt="PDF Icon" class="pdf-thumbnail">
                                </a>
                            <p><strong><?php echo htmlspecialchars($row['name']); ?></strong></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Data tidak ditemukan.</p>
    <?php endif; ?>

    <a href="subjects.php" class="back-button">Kembali</a>
</div>

<!-- Modal -->
<div id="pdfModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <iframe id="pdfFrame" src=""></iframe>
    </div>
</div>

<script>
function openModal(pdfUrl) {
    // Debugging line
    console.log('Opening PDF: ' + pdfUrl);

    document.getElementById('pdfFrame').src = pdfUrl;
    document.getElementById('pdfModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('pdfModal').style.display = 'none';
    document.getElementById('pdfFrame').src = '';
}

function showSubjects() {
    // Menampilkan kembali daftar mata pelajaran dan menyembunyikan detail
    const subjectList = document.getElementById('subject-list');
    const subjectDetails = document.getElementById('subject-details');

    subjectDetails.style.display = 'none';
    subjectList.style.display = 'grid';
}
</script>
</body>
</html>