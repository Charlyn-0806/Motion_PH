<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit(json_encode([]));
}
include '../db_connect.php';

$genre_id = $_GET['genre_id'] ?? null;
$step_type = $_GET['step_type'] ?? null;

if (!$genre_id || !$step_type) {
    exit(json_encode([]));
}

try {
    $stmt = $pdo->prepare("SELECT * FROM dance_steps WHERE genre_id = ? AND step_type = ? ORDER BY created_at ASC");
    $stmt->execute([$genre_id, $step_type]);
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($steps);
} catch (PDOException $e) {
    echo json_encode([]);
}
