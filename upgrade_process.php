<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

try {
    // In a real app, you would verify payment here. 
    // For now, we just update the database.
    $stmt = $pdo->prepare("UPDATE users SET membership_type = 'Premium' WHERE id = ?");
    
    if ($stmt->execute([$_SESSION['user_id']])) {
        // Refresh the page with success
        header("Location: user_dashboard.php?msg=upgraded");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>