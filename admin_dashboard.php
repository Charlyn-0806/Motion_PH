<?php
session_start();
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php?error=unauthorized"); exit();
}

include 'db_connect.php';

/** * 1. DYNAMIC DATA FETCHING
 * This query automatically groups your users by the month they registered.
 * It only counts users with the 'user' role.
 */
$query = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%b %Y') as month_label,
        COUNT(id) as member_count,
        SUM(CASE WHEN membership_type = 'Premium' THEN 1 ELSE 0 END) as promo_count,
        YEAR(created_at) as yr,
        MONTH(created_at) as mt
    FROM users 
    WHERE role = 'user'
    GROUP BY yr, mt
    ORDER BY yr ASC, mt ASC
");

$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Prepare arrays for the chart and table
$months_labels = [];
$members_data  = [];
$promo_data    = [];

foreach ($results as $row) {
    $months_labels[] = $row['month_label'];
    $members_data[]  = (int)$row['member_count'];
    $promo_data[]    = (int)$row['promo_count'];
}

// Calculate Summary Stats
$grand_total = array_sum($members_data);
$promo_total = array_sum($promo_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MOTION_PH | Dynamic Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        .top-nav { background: #1b3022; color: white; padding: 15px 5%; border-bottom: 4px solid #2e7d32; }
        .main-container { padding: 30px 5%; max-width: 1400px; margin: auto; }
        .stat-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table thead th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #666; font-size: 0.8rem; text-transform: uppercase; }
    </style>
</head>
<body>

<nav class="top-nav d-flex justify-content-between align-items-center shadow-sm">
    <h4 class="fw-bold m-0 text-success">MOTION_PH <span class="text-white opacity-50 fw-light">| Analytics</span></h4>
    <div class="text-end">
        <div class="small fw-bold">System Synchronized</div>
        <div class="small opacity-75"><?= date('l, F d, Y') ?></div>
    </div>
    <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-4">Logout</a>
</nav>

<div class="main-container">
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="stat-card p-4 border-start border-success border-5">
                <small class="text-muted fw-bold">TOTAL USER REGISTRATIONS</small>
                <h2 class="fw-bold m-0 mt-2"><?= $grand_total ?></h2>
                <p class="small text-muted mb-0">Total across all synchronized months</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card p-4 border-start border-primary border-5">
                <small class="text-muted fw-bold">PREMIUM MEMBERSHIPS</small>
                <h2 class="fw-bold m-0 mt-2 text-primary"><?= $promo_total ?></h2>
                <p class="small text-muted mb-0">Total advanced steps availed</p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="table-container">
                <h6 class="fw-bold mb-4">Account Growth Trajectory</h6>
                <div style="height: 300px;"><canvas id="growthChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <h6 class="fw-bold mb-3">Timeline Breakdown</h6>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Billing/Reporting Month</th>
                                <th>New Members</th>
                                <th>Promos Availed</th>
                                <th>Growth Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($months_labels as $i => $label): ?>
                            <tr>
                                <td class="fw-bold"><?= $label ?></td>
                                <td><span class="badge bg-success-subtle text-success px-3"><?= $members_data[$i] ?></span></td>
                                <td><span class="badge bg-primary-subtle text-primary px-3"><?= $promo_data[$i] ?></span></td>
                                <td>
                                    <?php 
                                    if($i > 0) {
                                        $diff = $members_data[$i] - $members_data[$i-1];
                                        echo $diff >= 0 ? "<span class='text-success small'>+$diff Users</span>" : "<span class='text-danger small'>$diff Users</span>";
                                    } else {
                                        echo "<span class='text-muted small'>Baseline</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months_labels) ?>,
            datasets: [{
                label: 'New Accounts',
                data: <?= json_encode($members_data) ?>,
                backgroundColor: '#2e7d32',
                borderRadius: 4
            }, {
                label: 'Promos',
                data: <?= json_encode($promo_data) ?>,
                backgroundColor: '#0d6efd',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

</body>
</html>