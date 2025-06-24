<?php
require_once '../include/db_connect.php'; // Include your PDO connection
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM user");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Users</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <style>
    .sidebar {
      width: 200px;
      background-color: #0d6efd;
      min-height: 100vh;
      color: #fff;
      padding-top: 20px;
      position: fixed;
      flex-shrink: 0;
    }
    .main {
      margin-left: 200px;
      padding: 20px;
      width: calc(100% - 200px);
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
      background-color: #fff;
    }
    .table th, .table td {
      padding: 16px 20px;
      text-align: center;
      vertical-align: middle;
      border-bottom: 1px solid #dee2e6;
    }
    .table thead th {
      background-color: #0d6efd;
      color: #fff;
      text-transform: uppercase;
      font-weight: 600;
      font-size: 14px;
      border-bottom: 2px solid #0d6efd;
    }
    .table tbody tr:hover {
      background-color: #f8f9fa;
      transition: background-color 0.2s ease;
    }
    .table td button, .table td a.btn {
      margin: 0 4px;
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
      <li><a href="manage_feedback.php"><i class="fa fa-comments"></i> Feedback</a></li>
      <li><a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main">
    <h2 class="mb-4">Manage Users</h2>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>UserID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $row): ?>
        <tr>
          <td><?= $row['user_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['phone'] ?? '' ?></td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?= $row['user_id'] ?>">
              <i class="fas fa-edit me-1"></i> Edit
            </button>
            <a href="delete_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-outline-danger">
              <i class="fas fa-trash-alt me-1"></i> Delete
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Update Modals -->
    <?php foreach ($users as $row): ?>
    <div class="modal fade" id="updateModal<?= $row['user_id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form action="update_user.php" method="POST" class="modal-content">
          <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
          <div class="modal-header">
            <h5 class="modal-title">Update User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="text" name="name" class="form-control mb-3" value="<?= $row['name'] ?>" required>
            <input type="email" name="email" class="form-control mb-3" value="<?= $row['email'] ?>" required>
            <input type="text" name="phone" class="form-control mb-3" value="<?= $row['phone'] ?? '' ?>">
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
