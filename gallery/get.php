<?php
require_once('../db.php');

$stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
$data = $stmt->fetchAll();

echo json_encode($data);
?>
