<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

   
    $stmt = $pdo->prepare("SELECT images FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $images = json_decode($row['images'], true);
        $uploadDir = '../uploads/gallery/';
        foreach ($images as $img) {
            $filePath = $uploadDir . $img;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // DELETE record
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record نەدۆزرایەوە']);
    }
}
?>
