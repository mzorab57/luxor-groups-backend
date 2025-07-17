<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $uploadDir = '../uploads/project/';
    $uploaded_images = [];

    // ئەگەر وێنە نوێ هەن
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === 0) {
                $imageName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                $uploadPath = $uploadDir . $imageName;

                if (move_uploaded_file($tmp_name, $uploadPath)) {
                    $uploaded_images[] = $imageName;
                }
            }
        }
    }

    if (!empty($uploaded_images)) {
        // DB → خوێندنی وێنە کۆنەکان
        $stmt = $pdo->prepare("SELECT images FROM project WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        $old_images = [];
        if ($row && $row['images']) {
            $old_images = json_decode($row['images'], true);
        }

        // merge بکە کۆن + نوێ
        $all_images = array_merge($old_images, $uploaded_images);
        $images_json = json_encode($all_images);

        // Update title + description + images
        $sql = "UPDATE project SET title = ?, description = ?, images = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $images_json, $id]);

    } else {
        // تەنها title و description
        $sql = "UPDATE project SET title = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $id]);
    }

    echo json_encode([
        'status' => 'updated',
        'id' => $id,
        'new_images' => $uploaded_images
    ]);
}
?>
