<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $sku = $_POST['sku'];
    $orientation = $_POST['orientation'];
    $artist_name = $_POST['artist_name'];

    $uploadDir = '../uploads/gallery/';
    $uploaded_images = [];

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
        $stmt = $pdo->prepare("SELECT images FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $old_images = $row ? json_decode($row['images'], true) : [];
        $all_images = array_merge($old_images, $uploaded_images);
        $images_json = json_encode($all_images);

        $sql = "UPDATE gallery SET title = ?, description = ?, category = ?, size = ?, price = ?, sku = ?, orientation = ?, artist_name = ?, images = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $category, $size, $price, $sku, $orientation, $artist_name, $images_json, $id]);

    } else {
        $sql = "UPDATE gallery SET title = ?, description = ?, category = ?, size = ?, price = ?, sku = ?, orientation = ?, artist_name = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $category, $size, $price, $sku, $orientation, $artist_name, $id]);
    }

    echo json_encode(['status' => 'updated', 'id' => $id, 'new_images' => $uploaded_images]);
}
?>
