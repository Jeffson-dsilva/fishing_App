<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $rescue_id = $_POST['rescue_id'];
        $new_status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE rescue SET status = ? WHERE rescue_id = ?");
        $stmt->execute([$new_status, $rescue_id]);
        $_SESSION['success'] = "Rescue status updated successfully!";
    } elseif (isset($_POST['add_rescue'])) {
        $fisher_id = !empty($_POST['fisher_id']) ? $_POST['fisher_id'] : null;
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);
        $status = 'Pending';
        $stmt = $pdo->prepare("INSERT INTO rescue (fisher_id, description, location, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fisher_id, $description, $location, $status]);
        $_SESSION['success'] = "New rescue report added successfully!";
    }
    header("Location: manage_rescues.php");
    exit();
}

$fishers = $pdo->query("SELECT fisher_id, name FROM fisher ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$rescues = $pdo->query("
    SELECT r.rescue_id, r.description, r.location, r.status, r.reported_at, 
           f.name as fisher_name, f.fisher_id
    FROM rescue r
    LEFT JOIN fisher f ON r.fisher_id = f.fisher_id
    ORDER BY r.reported_at ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Rescue Reports</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .main {
            margin-left: 100px;
            padding: 20px;
            min-height: 100vh;
            width: calc(100% - 120px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        /* Adjust main area when sidebar is hovered */
        .sidebar:hover~.main {
            margin-left: 260px;
            /* Sidebar expands to 220px + spacing */
            width: calc(100% - 280px);
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .sidebar:hover~.main {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
        }

        .container-custom {
            box-shadow: 5px 5px 15px rgba(27, 26, 26, 0.79);
            border-radius: 10px;
            background: #fff;
            padding: 25px;
        }

        .table-custom th,
        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            background: #fff;
        }

        .table-custom thead th {
            background: #F7F7F7;
            color: #3674B5;
            text-transform: uppercase;
            font-size: 13px;
            border: none;
        }

        .table-custom tbody tr:hover {
            background: #f2f6fb;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-pending {
            background-color: #ffc107;
        }

        .dot-in-progress {
            background-color: #0dcaf0;
        }

        .dot-resolved {
            background-color: #198754;
        }


        .action-btns .btn {
            padding: 4px 8px;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
    </style>
</head>

<body>

    <div class="d-flex flex-nowrap">

        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main">
            <div class="container-fluid container-custom">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-3 mb-md-0 text-center text-md-start">Manage Rescue Reports</h2>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Add New Rescue Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header  text-white" style="background-color:#3674B5">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Rescue Report</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Fisher (Optional)</label>
                                    <select class="form-select" name="fisher_id">
                                        <option value="">-- Select Fisher --</option>
                                        <?php foreach ($fishers as $fisher): ?>
                                            <option value="<?= $fisher['fisher_id'] ?>">
                                                <?= htmlspecialchars($fisher['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="add_rescue" class="btn text-light" style="background-color:#3674B5">
                                        <i class="fas fa-save me-2"></i>Add Rescue Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Rescue Reports Table -->
                <div class="table-responsive">
                    <table class="table table-borderless align-middle table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fisher</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Reported At</th>
                                <th class="text-end">Actions</th>
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
                                        <span class="d-flex align-items-center gap-2">
                                            <span
                                                class="status-dot <?=
                                                    $rescue['status'] == 'Pending' ? 'dot-pending' :
                                                    ($rescue['status'] == 'In Progress' ? 'dot-in-progress' : 'dot-resolved') ?>">
                                            </span>
                                            <?= $rescue['status'] ?>
                                        </span>
                                    </td>

                                    <td><?= date('M d, Y H:i', strtotime($rescue['reported_at'])) ?></td>
                                    <td class="text-end action-btns">
                                        <form method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="rescue_id" value="<?= $rescue['rescue_id'] ?>">
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="Pending" <?= $rescue['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="In Progress" <?= $rescue['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="Resolved" <?= $rescue['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                            </select>
                                            <button type="submit" name="update_status"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-save"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>