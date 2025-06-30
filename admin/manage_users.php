<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: index.php");
  exit();
}

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
    }

    .table-custom th,
    .table-custom td {
      vertical-align: middle;
      padding: 12px 16px;
      background: #fff;
    }

    .table-custom thead th {
      background: #F7F7F7;
      color: #0d6efd;
      text-transform: uppercase;
      font-size: 13px;
      border: none;
    }

    .table-custom tbody tr:hover {
      background: #f2f6fb;
    }

    .action-btns i {
      font-size: 1rem;
    }

    .action-btns .btn {
      padding: 4px 8px;
    }

    .avatar {
      width: 32px;
      height: 32px;
      object-fit: cover;
      border-radius: 50%;
    }

    .table-container {
      background: #fff;
      overflow: hidden;
      padding: 20px;
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

    <?php include 'components/sidebar.php'; ?>

    <div class="main">
      <div class="container-fluid container-custom" style="background-color:#fff;padding:25px;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-3 mb-md-0 text-center text-md-start">Manage Users</h2>
          <button class="btn btn-dark">+ Add User</button>
        </div>

        <div class="table-container">
          <div class="table-responsive">
            <table class="table table-borderless align-middle table-custom">
              <thead>
                <tr>
                  <th>UserID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Contact</th>
                  <th>Created</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $row): ?>
                  <tr>
                    <td><?= $row['user_id'] ?></td>
                    <td class="d-flex align-items-center gap-2">
                      <?= htmlspecialchars($row['name']) ?>
                    </td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['phone'] ?? '' ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td class="text-end action-btns">
                      <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#updateModal<?= $row['user_id'] ?>">
                        <i class="fas fa-edit"></i>
                      </button>
                      <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this user?')">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Update Modals -->
        <?php foreach ($users as $row): ?>
          <div class="modal fade" id="updateModal<?= $row['user_id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <form action="update_user.php" method="POST" class="modal-content">
                <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                <div class="modal-header">
                  <h5 class="modal-title">Update User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>"
                      required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $row['email'] ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= $row['phone'] ?? '' ?>">
                  </div>
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

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>