<?php
session_start();
include 'db_connect.php';

// 1. Initialize with default values
$display_steps = 0;
$calculated_calories = "0.0";
$sessions = [];
$labels = [];

// 2. Identify the user (Check both just to be safe)
$current_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;

if ($current_id) {
    try {
        // Fetch Totals
        $stmt = $pdo->prepare("SELECT SUM(steps_completed) as total, SUM(calories_burned) as kcal 
                               FROM activity_log WHERE user_id = ?");
        $stmt->execute([$current_id]);
        $res = $stmt->fetch();

        if ($res) {
            $display_steps = $res['total'] ?? 0;
            $calculated_calories = number_format($res['kcal'] ?? 0, 1);
        }

        // Fetch Chart Data
        $chartStmt = $pdo->prepare("SELECT steps_completed FROM activity_log 
                                    WHERE user_id = ? AND steps_completed > 0 
                                    ORDER BY id ASC");
        $chartStmt->execute([$current_id]);
        $sessions = $chartStmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($sessions as $key => $val) {
            $labels[] = "S" . ($key + 1);
        }
    } catch (PDOException $e) {
        // Log error silently or handle it
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Tracker - Parc Divas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-header">
        <h4 class="mb-0">PARC DIVAS</h4>
        <small class="text-white-50">AI Dance System</small>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link" href="user_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a class="nav-link" href="tutorials.php"><i class="fas fa-play-circle"></i> Tutorials</a>
        <a class="nav-link active" href="progress_tracker.php"><i class="fas fa-chart-line"></i> Progress</a>
    </nav>
    <a class="nav-link logout-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <a class="nav-link text-white" href="avail_promo.php">
        <i class="fas fa-rocket me-2"></i> 
        <span>Avail Advance Steps</span>
        <span class="badge bg-warning text-dark ms-1">PROMO</span>
    </a>
</div>

<div class="main-content">
    <div class="container-fluid px-4">
        <h3 class="fw-bold mb-4" style="color: #1b3022;">Your Fitness Journey</h3>
        
        <div class="row g-3 mb-4">
            
        <div class="row g-4 mb-4"> <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border-start border-success border-5">
                <small class="text-muted fw-bold">TOTAL STEPS</small>
                <h2 class="fw-bold"><?php echo $display_steps; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border-start border-warning border-5">
                <small class="text-muted fw-bold">EST. CALORIES</small>
                <h2 class="fw-bold"><?php echo $calculated_calories; ?> <span class="fs-6 text-muted">kcal</span></h2>
                <p class="text-warning small mb-0"><i class="fas fa-bolt"></i> Energy Burned</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border-start border-primary border-5">
                <small class="text-muted fw-bold">TIME ACTIVE</small>
                <h2 class="fw-bold">0 <span class="fs-6 text-muted">mins</span></h2>
            </div>
        </div>

        

        <div class="row">
    <div class="col-12">
        <div class="bg-white p-4 rounded-4 shadow-sm chart-container">
            <h5 class="fw-bold mb-3" style="color: #1b3022;">Activity Progression</h5>
            <canvas id="stepsChart"></canvas>
        </div>
    </div>
</div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h5 class="fw-bold mb-4">Activity Trends</h5>
                    <canvas id="progressChart" height="250"></canvas>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h5 class="fw-bold mb-3">Recent Activity</h5>
                    <div class="activity-feed">
                        <?php foreach($logs as $log): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="icon-box bg-light-success p-2 rounded-3 me-3">
                                <i class="fas fa-running text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($log['genre_name']) ?></h6>
                                <small class="text-muted">
                                    <?= $log['steps_completed'] ?> steps • 
                                    <?= date('M d, Y', strtotime($log['date_completed'])) ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('stepsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Steps Progression',
            data: <?php echo json_encode($sessions); ?>,
            borderColor: '#1b3022',
            tension: 0.3,
            fill: true,
            backgroundColor: 'rgba(27, 48, 34, 0.05)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // Forces the wide landscape look
        scales: {
            y: { beginAtZero: true, grace: '10%' }
        }
    }
});
</script>
</body>
</html>