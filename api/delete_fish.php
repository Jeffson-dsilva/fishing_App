<?php
require_once '../include/db_connect.php';
header('Content-Type: application/json');

$fish_id = $_POST['fish_id'] ?? null;

if (!$fish_id) {
    echo json_encode(['status' => false, 'message' => 'Fish ID missing']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM fish WHERE fish_id = ?");
    $stmt->execute([$fish_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => true, 'message' => 'Fish deleted successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Fish not found or already deleted']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
