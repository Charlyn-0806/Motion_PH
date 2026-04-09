<?php
include 'db_connect.php';

$adminUser = 'yvette'; // This will be your username
$adminPass = 'parcdivas00'; // This will be your password
$hashedPass = password_hash($adminPass, PASSWORD_DEFAULT);

try {
    // 1. Clear out the old admin to start fresh
    $pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$adminUser]);

    // 2. Insert the new admin with the CORRECT hash and role
    $sql = "INSERT INTO users (username, email, password, role, age, membership_type) 
            VALUES ('yvette', 'admin@parcdivas.com', 'parcdivas00', 'admin', 25, 'Premium')";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$adminUser, $hashedPass])) {
        echo "<h3>Admin Reset Successful!</h3>";
        echo "Username: <b>$adminUser</b><br>";
        echo "Password: <b>$adminPass</b><br><br>";
        echo "<a href='login.php'>Click here to Login</a>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>