<?php
require_once('../db.php');

$stmt = $pdo->query("SELECT * FROM video ORDER BY id DESC");
$data = $stmt->fetchAll();

echo json_encode($data);
?>
