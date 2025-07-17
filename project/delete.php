<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // خوێندنەوەی وێنەکان بۆ سڕینەوە لە فولدەر
    $stmt = $pdo->prepare("SELECT images FROM project WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $images = json_decode($row['images'], true);
        $uploadDir = '../uploads/project/';
        foreach ($images as $img) {
            $filePath = $uploadDir . $img;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // DELETE record
        $stmt = $pdo->prepare("DELETE FROM project WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record نەدۆزرایەوە']);
    }
}
?>
