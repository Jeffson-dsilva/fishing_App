<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Total Counts
$totalUsers = $pdo->query("SELECT COUNT(*) AS total FROM user")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$totalOrders = $pdo->query("SELECT COUNT(*) AS total FROM orders")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$totalFishers = $pdo->query("SELECT COUNT(*) AS total FROM fisher")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$pendingFishers = $pdo->query("SELECT COUNT(*) AS total FROM rescue WHERE status = 'Pending'")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Monthly Users
$currentYear = date('Y');
$allMonths = [];
for ($i = 1; $i <= 12; $i++) {
    $monthKey = $currentYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    $allMonths[$monthKey] = 0;
}
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
    FROM user 
    WHERE YEAR(created_at) = :currentYear 
    GROUP BY month
");
$stmt->execute(['currentYear' => $currentYear]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $allMonths[$row['month']] = (int) $row['count'];
}
$userMonthlyData = [
    'labels' => array_map(fn($key) => date("F", strtotime($key . '-01')), array_keys($allMonths)),
    'counts' => array_values($allMonths)
];

// Monthly Rescues
$rescueMonths = $allMonths;
$rescueStmt = $pdo->prepare("
    SELECT DATE_FORMAT(reported_at, '%Y-%m') AS month, COUNT(*) AS count 
    FROM rescue 
    WHERE YEAR(reported_at) = :currentYear 
    GROUP BY month
");
$rescueStmt->execute(['currentYear' => $currentYear]);
while ($row = $rescueStmt->fetch(PDO::FETCH_ASSOC)) {
    $rescueMonths[$row['month']] = (int) $row['count'];
}
$rescueMonthlyData = [
    'labels' => array_map(fn($key) => date("F", strtotime($key . '-01')), array_keys($rescueMonths)),
    'counts' => array_values($rescueMonths)
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <style>
        .chart-container {
            height: 300px;
            position: relative;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include 'components/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h2 class="fw-bold text-dark text-center text-md-start">Admin Dashboard</h2>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-6 col-md-3">
                        <div
                            class="card d-flex flex-row align-items-center justify-content-between p-3 h-100 shadow-sm" style="border:2px solid #3674B5">
                            <div>
                                <h6 class="text-muted mb-1">Total Users</h6>
                                <h3 class="mb-0" style="color:#3674B5"><?= $totalUsers ?></h3>
                            </div>
                            <div class="p-3">
                                <i class="bi bi-people fs-3 text-primary"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div
                            class="card d-flex flex-row align-items-center justify-content-between p-3 h-100 shadow-sm" style="border:2px solid #3674B5">
                            <div>
                                <h6 class="text-muted mb-1">Total Orders</h6>
                                <h3 class="mb-0" style="color:#3674B5;"><?= $totalOrders ?></h3>
                            </div>
                            <div class="p-3">
                                <i class="bi bi-bag-check fs-3 text-success"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div
                            class="card d-flex flex-row align-items-center justify-content-between p-3 h-100 shadow-sm" style="border:2px solid #3674B5">
                            <div>
                                <h6 class="text-muted mb-1">Total Fishers</h6>
                                <h3 class="mb-0" style="color:#3674B5"><?= $totalFishers ?></h3>
                            </div>
                            <div class="p-3">
                                <i class="bi bi-person-badge fs-3 text-info"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div
                            class="card d-flex flex-row align-items-center justify-content-between p-3 h-100 shadow-sm" style="border:2px solid #3674B5">
                            <div>
                                <h6 class="text-muted mb-1">Pending Rescues</h6>
                                <h3 class="mb-0" style="color:#3674B5"><?= $pendingFishers ?></h3>
                            </div>
                            <div class="p-3">
                                <i class="bi bi-exclamation-triangle fs-3 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card shadow p-3">
                            <h5 class="text-center mb-3">Monthly User Registrations (<?= $currentYear ?>)</h5>
                            <div class="chart-container">
                                <canvas id="userChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow p-3">
                            <h5 class="text-center mb-3">Total Orders Distribution</h5>
                            <div class="chart-container">
                                <canvas id="ordersChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow p-3">
                            <h5 class="text-center mb-3">Fishers Pending vs Total</h5>
                            <div class="chart-container">
                                <canvas id="fishersChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card shadow p-3">
                            <h5 class="text-center mb-3">Monthly Rescues Reported (<?= $currentYear ?>)</h5>
                            <div class="chart-container">
                                <canvas id="rescuesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Users Chart
            new Chart(document.getElementById('userChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($userMonthlyData['labels']) ?>,
                    datasets: [{
                        label: 'Users Registered',
                        data: <?= json_encode($userMonthlyData['counts']) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Orders Chart
            new Chart(document.getElementById('ordersChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'Cancelled'],
                    datasets: [{
                        data: [30, 10, 5],
                        backgroundColor: ['#198754', '#ffc107', '#dc3545']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
            });

            // Fishers Chart
            new Chart(document.getElementById('fishersChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Pending Rescues', 'Total Fishers'],
                    datasets: [{
                        data: [<?= $pendingFishers ?>, <?= $totalFishers ?>],
                        backgroundColor: ['#ffc107', '#0dcaf0']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Monthly Rescues Chart
            // Monthly Rescues Chart (Horizontal Bar)
            new Chart(document.getElementById('rescuesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($rescueMonthlyData['labels']) ?>,
                    datasets: [{
                        label: 'Rescues Reported',
                        data: <?= json_encode($rescueMonthlyData['counts']) ?>,
                        backgroundColor: '#fd7e14'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y' // Makes it horizontal
                }
            });

        });
    </script>
</body>

</html>