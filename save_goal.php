<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') exit('Unauthorized');

include '../db_connect.php';

$userId = $_SESSION['user_id'];
$goalValue = isset($_POST['goal_value']) ? (int)$_POST['goal_value'] : 0;

if ($goalValue <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid goal value.']);
    exit;
}

// Save the goal to the database
try {
    $stmt = $pdo->prepare("INSERT INTO user_goals (user_id, goal_value) VALUES (?, ?)");
    $stmt->execute([$userId, $goalValue]);

    echo json_encode(['success' => true, 'message' => 'Goal set successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
