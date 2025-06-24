<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? null;
    $message = $data['message'] ?? '';

    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message is required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, message) VALUES (:user_id, :message)");
        $stmt->execute([
            ':user_id' => $user_id ? (int)$user_id : null,
            ':message' => $message
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Feedback submitted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>