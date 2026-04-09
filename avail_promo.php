<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Avail Promo | MOTION_PH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .promo-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .gcash-btn { background-color: #007dfe; color: white; font-weight: bold; border-radius: 50px; padding: 12px 30px; transition: 0.3s; }
        .gcash-btn:hover { background-color: #0056b3; color: white; transform: scale(1.05); }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="promo-card bg-white p-5">
                <img src="images/gcash_logo.png" width="150" alt="GCash" class="mb-4">
                <h2 class="fw-bold mb-3">Unlock Advance Steps</h2>
                <p class="text-muted mb-4">Master complex dance moves with our Premium Package. One-time payment only.</p>
                
                <div class="display-4 fw-bold mb-2">₱299.00</div>
                <p class="badge bg-success mb-4">LIFETIME ACCESS</p>

                <hr>
                
                <form action="process_payment.php" method="POST">
                    <p class="small text-muted mb-3">Pay securely using your GCash number</p>
                    <div class="mb-3">
                        <input type="text" name="gcash_num" class="form-control form-control-lg text-center" placeholder="09XX-XXX-XXXX" required>
                    </div>
                    <button type="submit" name="pay_now" class="btn gcash-btn w-100">
                        PAY WITH GCASH
                    </button>
                </form>
            </div>
            <a href="user_dashboard.php" class="btn btn-link mt-3 text-secondary text-decoration-none">Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>