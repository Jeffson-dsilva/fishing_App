<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Handle status filter
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter === 'resolved') {
    $where = 'WHERE status = "Resolved"';
} elseif ($filter === 'pending') {
    $where = 'WHERE status = "Pending"';
}

// Fetch feedback with user names
$query = "
    SELECT f.*, u.name as user_name 
    FROM feedback f
    LEFT JOIN user u ON f.user_id = u.user_id
    $where
    ORDER BY submitted_at DESC
";
$stmt = $pdo->query($query);
$feedbackList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    $feedback_id = $_POST['feedback_id'];
    $response = $_POST['response'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE feedback SET admin_response = ?, status = ? WHERE feedback_id = ?");
        $stmt->execute([$response, $status, $feedback_id]);
        $_SESSION['success'] = "Response submitted successfully";
        header("Location: manage_feedback.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting response: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Feedback</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
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
        .feedback-card {
            border: none;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .feedback-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .feedback-card.resolved {
            border-left: 5px solid #3674B5;
        }
        .response-card {
            background-color: #f1f3f5;
            border-radius: 6px;
            padding: 0.75rem;
            margin-top: 1rem;
        }
        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            margin-top: 0.5rem;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-pending {
            background-color: #ffc107;
        }
        .dot-resolved {
            background-color: #198754;
        }
        .feedback-message {
            font-size: 15px;
            color: #343a40;
            margin-bottom: 0.5rem;
        }
        .username {
            font-weight: 600;
            color: #0d6efd;
        }
        .date {
            font-size: 13px;
            color: #6c757d;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
        }
    </style>
</head>
<body>

<div class="d-flex flex-nowrap">
    <!-- Sidebar -->
    <?php include 'components/sidebar.php' ?>

    <!-- Main Content -->
    <div class="main p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="mb-0">Manage Feedback</h2>
            <div>
                <div class="btn-group">
                    <a href="manage_feedback.php?filter=all" class="btn btn-outline-primary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
                    <a href="manage_feedback.php?filter=pending" class="btn btn-outline-primary <?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
                    <a href="manage_feedback.php?filter=resolved" class="btn btn-outline-primary <?= $filter === 'resolved' ? 'active' : '' ?>">Resolved</a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($feedbackList)): ?>
            <div class="alert alert-info">No feedback found</div>
        <?php else: ?>

            <div class="row g-4">
                <?php foreach ($feedbackList as $feedback): ?>
                    <?php $status = $feedback['status'] ?? 'Pending'; ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="feedback-card <?= $status === 'Resolved' ? 'resolved' : '' ?>">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="username"><?= $feedback['user_name'] ? htmlspecialchars($feedback['user_name']) : 'Anonymous' ?></span>
                                    <span class="date"><?= date('M d, Y H:i', strtotime($feedback['submitted_at'])) ?></span>
                                </div>
                                <p class="feedback-message"><?= nl2br(htmlspecialchars($feedback['message'])) ?></p>

                                <?php if (!empty($feedback['admin_response'])): ?>
                                    <div class="response-card">
                                        <h6 class="text-muted mb-1">Admin Response:</h6>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($feedback['admin_response'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#responseModal<?= $feedback['feedback_id'] ?>">
                                    <i class="fas fa-reply me-1"></i> Respond
                                </button>
                                <div class="status-badge">
                                    <span class="dot <?= $status === 'Resolved' ? 'dot-resolved' : 'dot-pending' ?>"></span>
                                    <?= htmlspecialchars($status) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Modal -->
                    <div class="modal fade" id="responseModal<?= $feedback['feedback_id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <input type="hidden" name="feedback_id" value="<?= $feedback['feedback_id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Respond to Feedback</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Feedback</label>
                                        <p><?= htmlspecialchars($feedback['message']) ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Your Response</label>
                                        <textarea name="response" class="form-control" rows="3" required><?= htmlspecialchars($feedback['admin_response'] ?? '') ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Response</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
