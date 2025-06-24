<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

try {
    $stmt = $pdo->query("
        SELECT r.rescue_id, r.description, r.location, r.status, r.reported_at, 
               f.name as fisher_name
        FROM rescue r
        LEFT JOIN fisher f ON r.fisher_id = f.fisher_id
        ORDER BY r.reported_at DESC
    ");
    $rescues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $rescues]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>