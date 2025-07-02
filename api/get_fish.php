<?php
require_once '../include/db_connect.php';

header('Content-Type: application/json');

// Prevent accidental output before JSON
ob_start();

$fisher_id = $_GET['fisher_id'] ?? null;

if (!$fisher_id) {
    echo json_encode(['status' => false, 'message' => 'Fisher ID missing']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM fish WHERE fisher_id = ?");
    $stmt->execute([$fisher_id]);
    $fishList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => true, 'fish' => $fishList]);
} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
?>