<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') exit('Unauthorized');
include '../db_connect.php';

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT DATE(date_completed) AS step_date, SUM(steps_completed) AS total_steps
        FROM user_progress
        WHERE user_id = ?
        GROUP BY DATE(date_completed)
        ORDER BY step_date ASC
    ");
    $stmt->execute([$userId]);
    $dailySteps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dailySteps);
} catch(PDOException $e){
    echo json_encode([]);
}
