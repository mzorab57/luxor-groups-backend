<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    $images_json = json_encode($uploaded_images);

    $sql = "INSERT INTO gallery (title, description, category, size, price, sku, orientation, artist_name, images) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $category, $size, $price, $sku, $orientation, $artist_name, $images_json]);

    echo json_encode(['status' => 'created', 'images' => $uploaded_images]);
}
?>
