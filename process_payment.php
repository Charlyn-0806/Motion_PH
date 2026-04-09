<?php
session_start();
include 'db_connect.php';

if (isset($_POST['pay_now'])) {
    $user_id = $_SESSION['user_id'];
    $gcash_num = $_POST['gcash_num'];

    // In a real app, you'd connect to an API here. 
    // For this project, we simulate success:
    
    $query = "UPDATE users SET membership_type = 'Premium' WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute(['id' => $user_id])) {
        // Redirect to a success page
        $_SESSION['status'] = "Payment Successful! You are now a Premium member.";
        header("Location: user_dashboard.php?payment=success");
    } else {
        header("Location: avail_promo.php?error=failed");
    }
}
?>