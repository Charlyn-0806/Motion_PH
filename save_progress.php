<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $steps = intval($_POST['steps']);
    $routine = $_POST['routine'];
    
    // Fetch age for proper calorie calculation
    $stmtUser = $pdo->prepare("SELECT age FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch();
    $age = $user['age'] ?? 60;

    // Analytics Calculation
    $age_factor = ($age > 60) ? 0.85 : 1.0; 
    $calories = round(($steps * 0.04) * $age_factor, 2);

    // UPDATED INSERT (No log_date column)
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, steps_completed, calories_burned, routine_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $steps, $calories, $routine]);
        
        echo json_encode(['status' => 'success', 'calories' => $calories]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>