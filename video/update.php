<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $uploadDir = '../uploads/video/';
    $uploaded_video = '';

    if (!empty($_FILES['video']['name'])) {
        $videoName = uniqid() . '_' . basename($_FILES['video']['name']);
        $uploadPath = $uploadDir . $videoName;

        if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadPath)) {
            $uploaded_video = $videoName;
        }
    }

    if (!empty($uploaded_video)) {
        // ڕەشکردنەوەی ڤیدیۆ کۆن
        $stmt = $pdo->prepare("SELECT video FROM video WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['video']) {
            $oldVideoPath = $uploadDir . $row['video'];
            if (file_exists($oldVideoPath)) {
                unlink($oldVideoPath);
            }
        }

        $sql = "UPDATE video SET title = ?, description = ?, video = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $uploaded_video, $id]);

    } else {
        $sql = "UPDATE video SET title = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $id]);
    }

    echo json_encode(['status' => 'updated', 'id' => $id, 'video' => $uploaded_video]);
}
?>
