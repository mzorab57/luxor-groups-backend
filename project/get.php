<?php
require_once('../db.php');

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM project ORDER BY id DESC");
    $data = $stmt->fetchAll();
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
