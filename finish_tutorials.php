<?php
session_start();
include 'db_connect.php';

// Get the 'id' from the session
$current_id = $_SESSION['id'] ?? null;

if (!$current_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$steps = $_POST['steps'] ?? 0;
$routine = $_POST['routine'] ?? 'Zumba';
$kcal = $steps * 0.04;

try {
    // Map the session 'id' to the 'user_id' column in activity_log
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, steps_completed, calories_burned, routine_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$current_id, $steps, $kcal, $routine]);
    
    echo json_encode(['status' => 'success', 'calories' => $kcal]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}