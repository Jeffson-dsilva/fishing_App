<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

$userId = $_GET['user_id'];
error_log("Fetching feedback for user ID: " . $_GET['user_id']);


try {
    $stmt = $pdo->prepare("SELECT feedback_id, message, submitted_at, status, admin_response FROM feedback WHERE user_id = :user_id ORDER BY submitted_at DESC");
    $stmt->execute([':user_id' => $userId]);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $feedbacks]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
