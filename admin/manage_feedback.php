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
    .feedback-card {
      border-left: 7px solid #0d6efd;
      margin-bottom: 15px;
    }
    .resolved {
      border-left-color: #198754;
    }
    .response-card {
      background-color: #f8f9fa;
      border-left: 4px solid #6c757d;
    }
  </style>
</head>
<body>
<div class="d-flex flex-nowrap">
  <!-- Sidebar -->
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
      <li><a href="manage_rescues.php"><i class="fa fa-life-ring"></i> Manage Rescues</a></li>
      <li><a href="manage_feedback.php"><i class="fa fa-comments"></i> Feedback</a></li>
      <li><a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 p-4">
    <h2>Manage Feedback</h2>
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

    <div class="row">
      <?php foreach ($feedbackList as $feedback): ?>
        <?php $status = $feedback['status'] ?? 'Pending'; ?>

        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card feedback-card <?= $status === 'Resolved' ? 'resolved' : '' ?>">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <h5 class="card-title">
                  <span class="text-primary" style="font-weight:bold;"><?= $feedback['user_name'] ? htmlspecialchars($feedback['user_name']) : 'Anonymous' ?></span>
                  <small class="text-muted p-2 py=1" style="font-size:15px;position:absolute;top:10px;right:0;"><?= date('M d, Y H:i', strtotime($feedback['submitted_at'])) ?></small>
                </h5>
              </div>
              <p class="card-text"><?= nl2br(htmlspecialchars($feedback['message'])) ?></p>

              <?php if (!empty($feedback['admin_response'])): ?>
                <div class="card response-card mt-3">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">FishersNet</h6>
                    <p class="card-text"><?= nl2br(htmlspecialchars($feedback['admin_response'])) ?></p>
                  </div>
                </div>
              <?php endif; ?>
            <div class="d-flex justify-content-between">
              <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#responseModal<?= $feedback['feedback_id'] ?>">
                <i class="fas fa-reply me-1"></i> Respond
              </button>
              <span class="badge bg-<?= $status === 'Resolved' ? 'success' : 'warning' ?> p-2 mt-3">
                  <?= htmlspecialchars($status) ?>
              </span>
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
