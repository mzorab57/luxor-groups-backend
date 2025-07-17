<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $imageToDelete = $_POST['image'];

    // هێنانی path folder
    $uploadDir = '../uploads/project/';

    // خوێندنی وێنەکانی هەنووکە
    $stmt = $pdo->prepare("SELECT images FROM project WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $images = json_decode($row['images'], true);

        if (($key = array_search($imageToDelete, $images)) !== false) {
            unset($images[$key]);
            $images = array_values($images); // re-index

            // DB نوێ بکە
            $images_json = json_encode($images);
            $stmt = $pdo->prepare("UPDATE project SET images = ? WHERE id = ?");
            $stmt->execute([$images_json, $id]);

            // فایلەکە لە فولدەرەکە بسڕەوە
            $filePath = $uploadDir . $imageToDelete;
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            echo json_encode(['status' => 'deleted', 'image' => $imageToDelete]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'وێنە نەدۆزرایەوە']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record نەدۆزرایەوە']);
    }
}
?>
