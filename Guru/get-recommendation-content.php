<?php
require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $recommendation_id = intval($_GET['id']);

    $sql = "SELECT title, tipe, description, url FROM recommendations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recommendation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
        echo '<p><strong>Type:</strong> ' . htmlspecialchars($row['tipe']) . '</p>';
        echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>';
        echo '<p><strong>URL:</strong> <a href="' . htmlspecialchars($row['url']) . '" target="_blank">' . htmlspecialchars($row['url']) . '</a></p>';
    } else {
        echo 'Rekomendasi tidak ditemukan.';
    }

    $stmt->close();
}

$conn->close();
?>
