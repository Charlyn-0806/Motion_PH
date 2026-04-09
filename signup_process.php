<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $age_category = $_POST['age_group']; // This captures Child, Adult, or Senior

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    try {
        // Check if exists
        $check = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $check->execute([$user, $email]);

        if ($check->rowCount() > 0) {
            header("Location: index.php?error=exists");
            exit();
        }

        // Insert new user with the selected age group
        // If your column in DB is 'age', make sure it is a VARCHAR/TEXT type
        $sql = "INSERT INTO users (username, email, password, role, age, membership_type) 
                VALUES (?, ?, ?, 'user', ?, 'Free')";
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$user, $email, $hashed_pass, $age_category])) {
            header("Location: index.php?signup=success");
            exit();
        }

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
?>