<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];


    $stmt = $pdo->prepare("SELECT video FROM video WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row && $row['video']) {
        $videoPath = '../uploads/video/' . $row['video'];
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM video WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['status' => 'deleted']);
}
?>
