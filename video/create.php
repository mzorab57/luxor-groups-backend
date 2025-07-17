<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    $sql = "INSERT INTO video (title, description, video) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $uploaded_video]);

    echo json_encode(['status' => 'created', 'video' => $uploaded_video]);
}
?>
