<?php
require_once '../include/db_connect.php';
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $rescue_id = $_POST['rescue_id'];
        $new_status = $_POST['status'];

        $stmt = $pdo->prepare("UPDATE rescue SET status = ? WHERE rescue_id = ?");
        $stmt->execute([$new_status, $rescue_id]);

        $_SESSION['success'] = "Rescue status updated successfully!";
    } 
    elseif (isset($_POST['add_rescue'])) {
        $fisher_id = !empty($_POST['fisher_id']) ? $_POST['fisher_id'] : null;
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);
        $status = 'Pending'; // Default status for new rescues

        $stmt = $pdo->prepare("INSERT INTO rescue (fisher_id, description, location, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fisher_id, $description, $location, $status]);

        $_SESSION['success'] = "New rescue report added successfully!";
    }

    header("Location: manage_rescues.php");
    exit();
}

// Fetch all fishers for the dropdown
$fishers = $pdo->query("SELECT fisher_id, name FROM fisher ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all rescue reports with fisher names
$rescues = $pdo->query("
    SELECT r.rescue_id, r.description, r.location, r.status, r.reported_at, 
           f.name as fisher_name, f.fisher_id
    FROM rescue r
    LEFT JOIN fisher f ON r.fisher_id = f.fisher_id
    ORDER BY r.reported_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Rescue Reports</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <style>
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-in-progress { background-color: #0dcaf0; color: #000; }
        .badge-resolved { background-color: #198754; color: #fff; }
        .add-rescue-card { margin-bottom: 2rem; }
    </style>
</head>

<body>
    <div class="d-flex flex-nowrap">
        <!-- Include sidebar -->
        <div class="sidebar">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom:35px; padding-left:10px;">
                <span class="material-icons logo-icon" style="font-size: 36px;">anchor</span>
                <h2 style="margin: 0; font-weight: 600; font-size: 24px;">FishersNet</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage_users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                <li><a href="manage_fishers.php"><i class="fa fa-fish"></i> Manage Fishers</a></li>
                <li><a href="manage_magazines.php"><i class="fa fa-book"></i> Manage Magazines</a></li>
                <li><a href="manage_feedback.php"><i class="fa fa-comments"></i> Feedback</a></li>
                <li><a href="manage_rescues.php"><i class="fa fa-life-ring"></i> Manage Rescues</a></li>
                <li><a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2 class="mb-4">Manage Rescue Reports</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Add New Rescue Card -->
            <div class="card shadow add-rescue-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Rescue Report</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fisher_id" class="form-label">Fisher (Optional)</label>
                                <select class="form-select" id="fisher_id" name="fisher_id">
                                    <option value="">-- Select Fisher --</option>
                                    <?php foreach ($fishers as $fisher): ?>
                                        <option value="<?= $fisher['fisher_id'] ?>"><?= htmlspecialchars($fisher['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="add_rescue" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Add Rescue Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Rescues Table -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-life-ring me-2"></i>Rescue Reports</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fisher</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Reported At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rescues as $rescue): ?>
                                    <tr>
                                        <td><?= $rescue['rescue_id'] ?></td>
                                        <td>
                                            <?php if ($rescue['fisher_id']): ?>
                                                <a href="manage_fishers.php?edit=<?= $rescue['fisher_id'] ?>">
                                                    <?= htmlspecialchars($rescue['fisher_name'] ?? 'Anonymous') ?>
                                                </a>
                                            <?php else: ?>
                                                Anonymous
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($rescue['description']) ?></td>
                                        <td><?= htmlspecialchars($rescue['location']) ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?=
                                                $rescue['status'] == 'Pending' ? 'badge-pending' :
                                                ($rescue['status'] == 'In Progress' ? 'badge-in-progress' : 'badge-resolved')
                                                ?>">
                                                <?= $rescue['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y H:i', strtotime($rescue['reported_at'])) ?></td>
                                        <td>
                                            <form method="POST" class="d-flex align-items-center gap-2">
                                                <input type="hidden" name="rescue_id" value="<?= $rescue['rescue_id'] ?>">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="Pending" <?= $rescue['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="In Progress" <?= $rescue['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                    <option value="Resolved" <?= $rescue['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-save"></i> Update
                                                </button>
                                            </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>