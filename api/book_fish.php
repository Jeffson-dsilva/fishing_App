<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? 0;
    $fish_id = $data['fish_id'] ?? 0;
    $quantity = $data['quantity'] ?? 0;

    if ($user_id <= 0 || $fish_id <= 0 || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user_id, fish_id, or quantity']);
        exit;
    }

    try {
        // Check available quantity
        $stmt = $pdo->prepare("SELECT available_quantity FROM fish WHERE fish_id = :fish_id");
        $stmt->execute([':fish_id' => $fish_id]);
        $fish = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fish || $fish['available_quantity'] < $quantity) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient fish quantity']);
            exit;
        }

        // Start transaction
        $pdo->beginTransaction();

        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, fish_id, quantity, status)
            VALUES (:user_id, :fish_id, :quantity, 'Pending')
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':fish_id' => $fish_id,
            ':quantity' => $quantity
        ]);

        // Update fish quantity
        $stmt = $pdo->prepare("
            UPDATE fish 
            SET available_quantity = available_quantity - :quantity 
            WHERE fish_id = :fish_id
        ");
        $stmt->execute([
            ':quantity' => $quantity,
            ':fish_id' => $fish_id
        ]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Fish booked successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>