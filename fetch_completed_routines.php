<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') exit('Unauthorized');
include '../db_connect.php';

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT genre_name, steps_completed, time_consumed, date_completed FROM user_progress WHERE user_id = ? ORDER BY date_completed DESC");
    $stmt->execute([$userId]);
    $progressData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    $progressData = [];
}

if(!$progressData){
    echo '<p class="text-muted">No routines completed yet.</p>';
} else {
    echo '<div class="list-group">';
    foreach($progressData as $routine){
        echo '<div class="list-group-item mb-2">';
        echo '<strong>Genre:</strong> '.htmlspecialchars($routine['genre_name']).'<br>';
        echo '<strong>Steps Completed:</strong> '.htmlspecialchars($routine['steps_completed']).'<br>';
        echo '<strong>Time Consumed:</strong> '.gmdate("H:i:s", $routine['time_consumed']).'<br>';
        echo '<strong>Date:</strong> '.htmlspecialchars($routine['date_completed']);
        echo '</div>';
    }
    echo '</div>';
}
?>
