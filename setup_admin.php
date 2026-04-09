<?php
include 'db_connect.php';

// 1. DEFINE THE VARIABLES FIRST (The names must match the ones in your SQL)
$admin_user  = 'yvette';
$admin_pass  = 'parcdivas00';
$admin_email = 'admin@parcdivas.com';

// 2. HASH THE PASSWORD
$hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

try {
    // 3. FIRST, DELETE ANY OLD 'yvette' TO AVOID "DUPLICATE" ERRORS
    $delete = $pdo->prepare("DELETE FROM users WHERE username = ?");
    $delete->execute([$admin_user]);

    // 4. INSERT THE NEW ACCOUNT
    $sql = "INSERT INTO users (username, email, password, role, age, membership_type) 
            VALUES (?, ?, ?, 'admin', 25, 'Premium')";
    
    $stmt = $pdo->prepare($sql);
    
    // Make sure these match the order of the question marks (?) above
    if ($stmt->execute([$admin_user, $admin_email, $hashed_pass])) {
        echo "<h3>Success! Admin account created.</h3>";
        echo "Username: <b>$admin_user</b><br>";
        echo "Password: <b>$admin_pass</b><br><br>";
        echo "<a href='login.php'>Go to Login Page</a>";
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>