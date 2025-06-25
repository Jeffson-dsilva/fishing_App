<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM magazine ORDER BY publish_date DESC");
    $magazines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $magazines = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Magazines</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .content-snippet {
            overflow: hidden;
            text-overflow: ellipsis;
        }


        .magazine-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .sidebar {
            background-color: #0d6efd;
            min-width: 220px;
            height: 110vh;
            color: #fff;
            padding: 15px 0;
            overflow-y: auto;
            overflow-x: hidden;
        }


        .sidebar-menu li a {
            color: #ddd;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: #495057;
            color: #fff;
        }

        .main {
            flex-grow: 1;
            padding: 30px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #0d6efd;
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid #0d6efd;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="d-flex">

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
            <div class="card shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Manage Magazines</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMagazineModal">
                        <i class="fas fa-plus me-1"></i> Add New Magazine
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-bordered table-hover table-striped">
                        <thead class="text-white text-center">
                            <tr>
                                <th style="width: 100px;">Image</th>
                                <th>Title</th>
                                <th style="width: 300px;">Content</th>
                                <th style="width: 150px;">Publish Date</th>
                                <th style="width: 240px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php if (!empty($magazines)): ?>
                                <?php foreach ($magazines as $magazine): ?>
                                    <tr>
                                        <td>
                                            <?php if ($magazine['image_url'] && file_exists('../' . $magazine['image_url'])): ?>
                                                <img src="../<?= htmlspecialchars($magazine['image_url']) ?>" class="magazine-img"
                                                    alt="Magazine Cover">
                                            <?php else: ?>
                                                <div class="img-placeholder text-muted">
                                                    <i class="fas fa-book fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($magazine['title']) ?></td>
                                        <td class="content-snippet">
                                            <?= htmlspecialchars(substr($magazine['content'], 0, 100)) ?>...
                                        </td>
                                        <td><?= date('M d, Y', strtotime($magazine['publish_date'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success mb-1" data-bs-toggle="modal"
                                                data-bs-target="#pdfModal<?= $magazine['magazine_id'] ?>">
                                                <i class="fas fa-eye me-1"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary mb-1" data-bs-toggle="modal"
                                                data-bs-target="#editModal<?= $magazine['magazine_id'] ?>">
                                                <i class="fas fa-edit me-1"></i>
                                            </button>
                                            <a href="delete_magazine.php?id=<?= $magazine['magazine_id'] ?>"
                                                onclick="return confirm('Are you sure?')"
                                                class="btn btn-sm btn-outline-danger mb-1">
                                                <i class="fas fa-trash-alt me-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No magazines found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Magazine Modal -->
            <div class="modal fade" id="addMagazineModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form action="add_magazine.php" method="POST" enctype="multipart/form-data" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Magazine</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea name="content" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Publish Date</label>
                                <input type="date" name="publish_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PDF File</label>
                                <input type="file" name="file" class="form-control" accept=".pdf" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cover Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Magazine</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Add this edit modal section right before the closing </div> of the main content -->
            <?php foreach ($magazines as $magazine): ?>
                <div class="modal fade" id="editModal<?= $magazine['magazine_id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <form action="update_magazine.php" method="POST" enctype="multipart/form-data"
                            class="modal-content">
                            <input type="hidden" name="magazine_id" value="<?= $magazine['magazine_id'] ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Magazine</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control"
                                        value="<?= htmlspecialchars($magazine['title']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea name="content" class="form-control" rows="5" required><?=
                                        htmlspecialchars($magazine['content']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Publish Date <span class="text-danger">*</span></label>
                                    <input type="date" name="publish_date" class="form-control"
                                        value="<?= $magazine['publish_date'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current PDF: <?= basename($magazine['file_url']) ?></label>
                                    <input type="file" name="file" class="form-control" accept=".pdf">
                                    <small class="text-muted">Leave blank to keep current file</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Cover Image:</label>
                                    <?php if ($magazine['image_url']): ?>
                                        <img src="../<?= htmlspecialchars($magazine['image_url']) ?>"
                                            class="img-thumbnail mb-2 d-block" style="max-height: 150px;">
                                    <?php endif; ?>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Leave blank to keep current image</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- Add this PDF viewer modal with the other modals -->
            <?php foreach ($magazines as $magazine): ?>
                <div class="modal fade" id="pdfModal<?= $magazine['magazine_id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($magazine['title']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="height: 80vh;">
                                <embed src="../<?= $magazine['file_url'] ?>#toolbar=0&navpanes=0" type="application/pdf"
                                    width="100%" height="100%" />
                            </div>
                            <div class="modal-footer">
                                <a href="../<?= $magazine['file_url'] ?>" download class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>