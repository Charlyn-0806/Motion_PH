<?php
include 'db_connect.php';

$message = "";
$message_type = ""; // To define the alert color (success or danger)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get values from form names
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $age      = $_POST['age_group']; 

    try {
        // 2. Map them to your database columns
        // We set role as 'user' by default for security
        $sql = "INSERT INTO users (username, email, password, role, age, membership_type) 
                VALUES (?, ?, ?, 'user', ?, 'Free')";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$username, $email, $password, $age])) {
            // Redirect to login page with success status
            header("Location: index.php?status=success");
            exit();
        }
    } catch (PDOException $e) {
        $message_type = "danger";
        // Check for duplicate entry error specifically
        if ($e->getCode() == 23000) {
            $message = "Error: Username or Email already exists.";
        } else {
            $message = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Parc Divas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f7f6; min-height: 100vh; display: flex; align-items: center; padding: 20px 0; }
        .signup-card { 
            background: white; border-radius: 15px; padding: 40px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 8px solid #2e7d32; 
        }
        .btn-success { background: #2e7d32; border: none; padding: 12px; font-weight: bold; }
        .btn-success:hover { background: #1b5e20; }
        .form-control, .form-select { border-radius: 8px; padding: 10px; margin-bottom: 15px; }
        .form-label { margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="signup-card">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-success">Sign Up</h2>
                    <p class="text-muted">Become a member of Parc Divas</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $message_type ?> py-2 text-center">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="sign_up.php">
                    <label class="form-label small fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required>

                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>

                    <label class="form-label small fw-bold">Age Group</label>
                    <select name="age_group" class="form-select" required>
                        <option value="" selected disabled>Select Age Group</option>
                        <option value="Child">Child</option>
                        <option value="Teen">Teen</option>
                        <option value="Adult">Adult</option>
                        <option value="Senior">Senior</option>
                    </select>

                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Create password" required>

                    <button type="submit" class="btn btn-success w-100 mt-2">Create Account</button>
                </form>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none text-muted small">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>