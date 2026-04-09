<?php
session_start();
// Redirect if not logged in or wrong role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php?error=unauthorized"); exit(); 
}
include 'db_connect.php';

// Replace line 9 with this:
$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}
$current_page = 'dashboard';

// 1. FETCH FULL USER DATA (To get Membership Type)
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();
$user_name = $user['username'] ?? 'Dancer';
$membership = $user['membership_type'] ?? 'Free';

// 2. FETCH PROGRESS LOGS
$logStmt = $pdo->prepare("SELECT steps_completed, time_consumed, genre_name, date_completed 
                          FROM user_progress WHERE user_id = ? 
                          ORDER BY date_completed DESC LIMIT 10");
$logStmt->execute([$user_id]);
$logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. GET AGGREGATE TOTALS
$stmt = $pdo->prepare("SELECT SUM(steps_completed) as total_steps, SUM(time_consumed) as total_time 
                       FROM user_progress WHERE user_id = ?");
$stmt->execute([$user_id]);
$dashStats = $stmt->fetch();

$total_steps = $dashStats['total_steps'] ?? 0;
$total_calories = $total_steps * 0.04;

// 4. PREDICTION LOGIC
function getPrediction($logs) {
    if (empty($logs)) return 0;
    $y_values = array_column(array_reverse($logs), 'steps_completed');
    $n = count($y_values);
    if ($n < 2) return $y_values[0] ?? 0;
    $x_values = range(1, $n);
    $sumX = array_sum($x_values); $sumY = array_sum($y_values);
    $sumXX = 0; $sumXY = 0;
    foreach ($x_values as $i => $x) {
        $sumXX += $x * $x;
        $sumXY += $x * $y_values[$i];
    }
    $denominator = ($n * $sumXX - $sumX * $sumX);
    if ($denominator == 0) return round($sumY / $n);
    $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
    $intercept = ($sumY - $slope * $sumX) / $n;
    return round(max(0, ($slope * ($n + 1)) + $intercept));
}
$predicted_steps = getPrediction($logs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Parc Divas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .welcome-banner { 
            background: linear-gradient(90deg, #1b3022, #2e7d32); 
            color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; 
        }
        .stat-card { 
            background: white; border-radius: 15px; padding: 20px; 
            border-left: 5px solid #2e7d32; box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            color: #121212;
        }
        .membership-badge {
            font-size: 0.7rem; letter-spacing: 1px; padding: 5px 12px; border-radius: 50px;
        }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-header text-center">
        <h4 class="mb-0">PARC DIVAS</h4>
        <small class="text-white-50">AI Dance System</small>
    </div>
    <nav class="nav flex-column mt-4">
    <a class="nav-link active" href="user_dashboard.php"><i class="fas fa-th-large me-2"></i> Dashboard</a>
    <a class="nav-link" href="tutorials.php"><i class="fas fa-play-circle me-2"></i> Tutorials</a>
    <a class="nav-link" href="progress_tracker.php"><i class="fas fa-chart-line me-2"></i> Progress</a>
    
    <a class="nav-link text-white" href="avail_promo.php">
        <i class="fas fa-rocket me-2"></i> 
        <span>Avail Advance Steps</span>
        <span class="badge bg-warning text-dark ms-1">PROMO</span>
    </a>
</nav>
</div>

<div class="main-content">
    <div class="welcome-banner shadow-sm d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Welcome back, <?= htmlspecialchars($user_name) ?>!</h1>
            <p class="mb-0 opacity-75">Your fitness journey continues at Parc Regency Residences.</p>
        </div>
        <div class="text-end">
            <span class="membership-badge bg-<?php echo ($membership == 'Premium') ? 'warning text-dark' : 'light text-dark'; ?> fw-bold text-uppercase">
                <i class="fas fa-crown me-1"></i> <?= $membership ?> Member
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <small class="text-uppercase text-muted fw-bold">Recent Progress</small>
                <h2 class="fw-bold mt-2"><?= number_format($total_steps) ?> Steps</h2>
                <div class="progress mt-3" style="height: 8px;"><div class="progress-bar bg-success" style="width: 75%"></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left-color: #ff9800;">
                <small class="text-uppercase text-muted fw-bold">Calories Burned</small>
                <h2 class="fw-bold mt-2"><?= number_format($total_calories) ?> kcal</h2>
                <p class="text-success mb-0 small"><i class="fas fa-bolt"></i> Keep moving!</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left-color: #2196f3;">
                <small class="text-uppercase text-muted fw-bold">AI Prediction (Tomorrow)</small>
                <h2 class="fw-bold mt-2"><?= $predicted_steps ?> Steps</h2>
                <small class="text-muted">Linear Regression Analysis</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="ai-card p-4 mb-4 border-0 shadow-sm" style="background: #f8fdf9; border: 1px solid #e1eee2 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-success mb-1">Exclusive Promos</h5>
                        <p class="text-muted small mb-0">Unlock Advanced Tutorials & AI Choreography</p>
                    </div>
                    <?php if ($membership == 'Free'): ?>
                        <button class="btn btn-success btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#promoModal">View Offers</button>
                    <?php else: ?>
                        <span class="text-success fw-bold small"><i class="fas fa-check-circle"></i> Premium Active</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ai-card p-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-brain text-success me-2"></i> Data Interpretation</h5>
                <div class="ai-message-bubble p-3 mb-4 bg-light border-0 text-dark">
                    <strong>AI Analysis:</strong> 
                    <?php echo ($predicted_steps > ($total_steps/max(1, count($logs)))) ? 
                        "Your activity levels are trending upwards! You are becoming more efficient." : 
                        "Steady pace maintained. Focus on consistent daily sessions."; ?>
                </div>

                <h6 class="fw-bold mt-3 text-dark">Recent Activity Logs</h6>
                <table class="table table-hover mt-3">
                    <thead><tr><th>Routine</th><th class="text-end">Steps</th><th class="text-end">Calories</th></tr></thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-dark"><i class="fas fa-check-circle text-success me-2"></i> <?= htmlspecialchars($log['genre_name'] ?? 'Routine') ?></td>
                            <td class="text-end fw-bold text-dark"><?= number_format($log['steps_completed']) ?></td>
                            <td class="text-end text-muted small"><?= number_format($log['steps_completed'] * 0.04, 1) ?> kcal</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ai-card p-4 text-center bg-white shadow-sm border-0">
                <h6 class="fw-bold text-muted">Daily Goal</h6>
                <div class="my-4"><i class="fas fa-walking fa-4x text-success"></i></div>
                <h3 class="fw-bold text-dark">85%</h3>
                <p class="small text-muted mb-4">One more session to reach your target!</p>
                <a href="tutorials.php" class="btn btn-success rounded-pill px-4 w-100 py-2 fw-bold">Start AI Routine</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="promoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow text-dark">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-bold">Upgrade to Premium</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
        <h4>Advance Steps Package</h4>
        <p class="text-muted small">Unlock advanced dance routines, higher intensity workouts, and deep AI performance metrics.</p>
        <div class="bg-light p-3 rounded mb-4">
            <h2 class="fw-bold mb-0 text-success">₱499.00</h2>
            <p class="small text-muted mb-0 text-uppercase tracking-wider">Lifetime Access</p>
        </div>
        <a href="upgrade_process.php" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow">Avail Promo Now</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>