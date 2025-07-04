<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../include/db_connect.php');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['order_id']) && isset($data['status'])) {
    try {
        $orderId = $data['order_id'];
        $status = $data['status'];

        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :order_id");
        $stmt->execute([
            ':status' => $status,
            ':order_id' => $orderId
        ]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => true, "message" => "Order status updated successfully"]);
        } else {
            echo json_encode(["status" => false, "message" => "Order not found or status unchanged"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid parameters"]);
}
?>
