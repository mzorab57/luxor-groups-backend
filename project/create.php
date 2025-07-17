<?php
require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploaded_images = [];
    
    // وەرگرتنی JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // دەستنیشانکردنی جۆری داتا (JSON یان form-data)
    if ($data && is_array($data)) {
        // JSON data
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        
        // ئەگەر images لە JSON هەبوو
        if (isset($data['images'])) {
            $images = $data['images'];
            if (!is_array($images)) {
                $images = [$images];
            }
            $uploaded_images = $images;
        }
    } else {
        // Form-data
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
    }
    
    // فولدەرەکەی upload
    $uploadDir = '../uploads/project/';
    
    // دڵنیابە لەوەی folder-ەکە هەیە
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // دڵنیابوون لە ئەوەی فۆڵدەرەکە نووسراو بێت
    if (!is_writable($uploadDir)) {
        echo json_encode(['status' => 'error', 'message' => 'فۆڵدەرەکە نووسراو نییە']);
        exit;
    }
    
    // پرۆسێسکردنی وێنەکان (ئەگەر form-data هەبوو)
    if (isset($_FILES['images']) && !empty($_FILES['images']['tmp_name'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === 0) {
                // تاقیکردنەوەی جۆری فایل
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['images']['type'][$key];
                
                if (!in_array($fileType, $allowedTypes)) {
                    continue; // فایلی نادروست پشتگوێ بخە
                }
                
                // دروستکردنی ناوی یونیک
                $extension = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $imageName = uniqid() . '_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $imageName;
                
                // گواستنەوەی فایل
                if (move_uploaded_file($tmp_name, $uploadPath)) {
                    $uploaded_images[] = $imageName;
                }
            }
        }
    }
    
    // تاقیکردنەوەی ئەوەی بەلایەنی کەمەوە یەک وێنە هەیە
    if (empty($uploaded_images)) {
        echo json_encode(['status' => 'error', 'message' => 'هیچ وێنەیەک نەدۆزراوە']);
        exit;
    }
    
    // ناوی وێنەکان بە شێوەی JSON داخڵ بکە
    $images_json = json_encode($uploaded_images);
    
    try {
        $sql = "INSERT INTO project (title, description, images) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $images_json]);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'پڕۆژەکە بە سەرکەوتوویی زیاد کرا',
            'title' => $title,
            'description' => $description,
            'images' => $uploaded_images
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'هەڵەی دەیتابەیس: ' . $e->getMessage()]);
    }
}
?>