<?php
require_once '../include/db_connect.php'; // ✅ Corrected path to DB file
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Total users
$result = $pdo->query("SELECT COUNT(*) AS total_users FROM user");
$data = $result->fetch(PDO::FETCH_ASSOC);
$totalUsers = $data['total_users'] ?? 0;

// Generate all months for the current year
$currentYear = date('Y');
$allMonths = [];
for ($i = 1; $i <= 12; $i++) {
    $monthKey = $currentYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $allMonths[$monthKey] = 0;
}

// Fetch user registration count grouped by month
$monthsQuery = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
    FROM user 
    WHERE YEAR(created_at) = $currentYear
    GROUP BY month 
    ORDER BY month ASC
";
$monthsResult = $pdo->query($monthsQuery);
while ($row = $monthsResult->fetch(PDO::FETCH_ASSOC)) {
    $allMonths[$row['month']] = (int) $row['count'];
}

// Convert YYYY-MM to Month Name
$labels = [];
foreach (array_keys($allMonths) as $key) {
    $labels[] = date("F", strtotime($key . '-01'));
}

$monthlyData['labels'] = $labels;
$monthlyData['counts'] = array_values($allMonths);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>
    <div class="d-flex flex-nowrap">
        <!-- Sidebar -->
        <div class="sidebar">
            <div style="display: flex; align-items: center; gap: 10px;margin-bottom:35px;padding-left:10px;">
                <span class="material-icons logo-icon" style="font-size: 36px;">anchor</span>
                <h2 style="margin: 0; font-weight: 600; font-size: 24px;">FishersNet</h2>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><span class="icon"><i class="fa fa-tachometer-alt"></i></span><span
                            class="label">Dashboard</span></a></li>
                <li><a href="manage_users.php"><span class="icon"><i class="fa fa-users"></i></span><span class="label">Manage Users</span></a></li>
                <li><a href="manage_fishers.php"><i class="fa fa-fish"></i> Manage Fishers</a></li>
                <li><a href="manage_magazines.php"><i class="fa fa-book"></i> Manage Magazines</a></li>
                <li><a href="manage_feedback.php"><i class="fa fa-comments"></i> Feedback</a></li>
                <li><a href="logout.php" class="text-danger"><span class="icon"><i class="fa fa-sign-out-alt"></i></span><span class="label">Logout</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2 class="mb-4">Admin Dashboard</h2>
            <div class="col-md-4">
                <div class="card total-user-card text-white mb-4 bg-primary">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Users</h6>
                            <h3 class="mb-0"><?= $totalUsers ?></h3>
                        </div>
                        <div><i class="bi bi-people-fill" style="font-size: 2rem;"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Chart -->
                <div class="col-lg-8 col-md-12">
                    <div class="card shadow p-4 bg-white h-100">
                        <h4 class="mb-3">User Registrations (<?= $currentYear ?>)</h4>
                        <p><strong>Total Users:</strong> <?= $totalUsers ?></p>
                        <div class="chart-container">
                            <canvas id="monthlyUserChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="col-lg-4 col-md-12">
                    <div class="card shadow p-4 bg-white h-100">
                        <h5 class="mb-3">Calendar</h5>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        const ctx = document.getElementById('monthlyUserChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 123, 255, 0.3)');
        gradient.addColorStop(1, 'rgba(0, 123, 255, 0.8)');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($monthlyData['labels']) ?>,
                datasets: [{
                    label: 'Users Registered',
                    data: <?= json_encode($monthlyData['counts']) ?>,
                    backgroundColor: gradient,
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Month' } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        title: { display: true, text: 'Users' }
                    }
                }
            }
        });
    </script>
</body>

</html>