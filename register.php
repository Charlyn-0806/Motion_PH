<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $age = $_POST['age']; // Added age
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    try {
        // 1. We use 'user' as a string to match your database
        // 2. We added the 'age' column to match your phpMyAdmin
        $sql = "INSERT INTO users (username, email, password, role, age, membership_type) 
                VALUES (?, ?, ?, 'user', ?, 'Free')";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$username, $email, $password, $age])) {
            header("Location: login.php?success=account_created");
            exit();
        }
    } catch (PDOException $e) {
        // If the email is already taken, this catches it
        $error = "Registration failed. This email might already be in use.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Parc Divas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow p-4">
                <h3 class="text-center fw-bold text-success">Register</h3>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Create Account</button>
                </form>
                <div class="text-center mt-3">
                    <small>Already have an account? <a href="index.php">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>