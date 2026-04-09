<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    
    // Change your query line to this:
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
    // trim() removes any accidental spaces the user might have typed
    $typed_password = trim($_POST['password']); 
    $hashed_password_in_db = trim($user['password']);

    // ... inside your if (password_verify(...)) block ...
    if (password_verify($typed_password, $hashed_password_in_db)) {
        $_SESSION['id'] = $user['id'];           // Matches your 'users' table id
        $_SESSION['user_id'] = $user['id'];      // FIX: Matches what dashboard.php expects
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower(trim($user['role']));

        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        // If it fails again, let's look for a mismatch in the lengths
        die("Password mismatch. Form length: " . strlen($typed_password) . " | DB Hash length: " . strlen($hashed_password_in_db));
    }
}       
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Parc Divas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 350px; padding: 30px; background: white; border-radius: 10px; border-top: 5px solid #2e7d32; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-card">
        <h4 class="text-center fw-bold text-success">PARC DIVAS</h4>
        <p class="text-center text-muted small">Member Access</p>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger py-1 text-center small">Invalid Credentials</div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
    </div>
</body>
</html>