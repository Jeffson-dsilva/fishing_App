<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../include/db_connect.php');

try {
    $fisherId = $_GET['fisher_id'];

    $query = "SELECT 
                o.order_id, 
                f.name AS fish_name, 
                o.quantity, 
                (o.quantity * f.price) AS total_price, 
                u.name AS user_name, 
                o.status 
              FROM orders o
              JOIN fish f ON o.fish_id = f.fish_id
              JOIN user u ON o.user_id = u.user_id
              JOIN fisher fi ON f.fisher_id = fi.fisher_id
              WHERE fi.fisher_id = :fisher_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fisher_id', $fisherId, PDO::PARAM_INT);
    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => true,
        "orders" => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => false,
        "message" => "Failed to fetch orders",
        "error" => $e->getMessage()
    ]);
}
?>
