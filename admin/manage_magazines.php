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

        .magazine-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
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

        <!-- Sidebar Include -->
        <?php include 'components/sidebar.php'; ?>

        <div class="main">
            <div class="container-fluid container-custom">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-3 mb-md-0 text-center text-md-start">Manage Magazines</h2>
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addMagazineModal">+ Add
                        Magazine</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle table-custom">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Publish Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($magazines)): ?>
                                <?php foreach ($magazines as $magazine): ?>
                                    <tr>
                                        <td>
                                            <?php if ($magazine['image_url'] && file_exists('../' . $magazine['image_url'])): ?>
                                                <img src="../<?= htmlspecialchars($magazine['image_url']) ?>" class="magazine-img"
                                                    alt="Cover">
                                            <?php else: ?>
                                                <div class="text-muted">No Image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($magazine['title']) ?></td>
                                        <td><?= htmlspecialchars(substr($magazine['content'], 0, 100)) ?>...</td>
                                        <td><?= date('M d, Y', strtotime($magazine['publish_date'])) ?></td>
                                        <td class="text-end action-btns">
                                            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#pdfModal<?= $magazine['magazine_id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal<?= $magazine['magazine_id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="delete_magazine.php?id=<?= $magazine['magazine_id'] ?>"
                                                class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash-alt"></i>
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

                <!-- Add Magazine Modal -->
                <div class="modal fade" id="addMagazineModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <form action="add_magazine.php" method="POST" enctype="multipart/form-data"
                            class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Magazine</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary">Save Magazine</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit and PDF Modals -->
                <?php foreach ($magazines as $magazine): ?>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $magazine['magazine_id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <form action="update_magazine.php" method="POST" enctype="multipart/form-data"
                                class="modal-content">
                                <input type="hidden" name="magazine_id" value="<?= $magazine['magazine_id'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Magazine</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control"
                                            value="<?= htmlspecialchars($magazine['title']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Content</label>
                                        <textarea name="content" class="form-control" rows="5"
                                            required><?= htmlspecialchars($magazine['content']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Publish Date</label>
                                        <input type="date" name="publish_date" class="form-control"
                                            value="<?= $magazine['publish_date'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Current PDF:
                                            <?= basename($magazine['file_url']) ?></label>
                                        <input type="file" name="file" class="form-control" accept=".pdf">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Current Cover Image</label>
                                        <?php if ($magazine['image_url']): ?>
                                            <img src="../<?= htmlspecialchars($magazine['image_url']) ?>"
                                                class="img-thumbnail mb-2" style="max-height: 150px;">
                                        <?php endif; ?>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- PDF View Modal -->
                    <div class="modal fade" id="pdfModal<?= $magazine['magazine_id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= htmlspecialchars($magazine['title']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body" style="height: 80vh;">
                                    <embed src="../<?= $magazine['file_url'] ?>#toolbar=0&navpanes=0" type="application/pdf"
                                        width="100%" height="100%" />
                                </div>
                                <div class="modal-footer">
                                    <a href="../<?= $magazine['file_url'] ?>" download
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>