<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') exit('Unauthorized');
include '../db_connect.php';

$userId = $_SESSION['user_id'];

// Calculate calories burned today
try {
    $stmt = $pdo->prepare("
        SELECT SUM(steps_completed * 0.05) AS calories_today
        FROM user_progress
        WHERE user_id = ? AND DATE(date_completed) = CURDATE()
    ");
    $stmt->execute([$userId]);
    $caloriesToday = (float)$stmt->fetchColumn();

    // Total calories
    $stmt = $pdo->prepare("
        SELECT SUM(steps_completed * 0.05) AS total_calories
        FROM user_progress
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $totalCalories = (float)$stmt->fetchColumn();

} catch(PDOException $e){
    $caloriesToday = $totalCalories = 0;
}

// Healthy tips based on calories burned today
if ($caloriesToday >= 500) {
    $tips = "Excellent work! You burned a lot today. Stay hydrated and stretch well.";
} elseif ($caloriesToday >= 200) {
    $tips = "Good job! Keep moving to reach your daily goal.";
} else {
    $tips = "Keep going! Every step counts.";
}
?>

<div class="card p-3">
    <h5>Calories Burned</h5>
    <p><strong>Today:</strong> <?= round($caloriesToday,2) ?> kcal</p>
    <p><strong>Total:</strong> <?= round($totalCalories,2) ?> kcal</p>
    <div class="alert alert-info mt-2"><?= $tips ?></div>
</div>
