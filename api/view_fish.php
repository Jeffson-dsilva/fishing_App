<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

try {
    $stmt = $pdo->query("
        SELECT f.fish_id, f.name, f.description, f.price, f.available_quantity, f.image_url, 
               fi.name as fisher_name
        FROM fish f
        LEFT JOIN fisher fi ON f.fisher_id = fi.fisher_id
        ORDER BY f.added_at DESC
    ");
    $fish = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Append base URL to image_url for full path
    $baseUrl = 'http://192.168.0.181/fishing/uploads'; // Update with your server URL
    foreach ($fish as &$item) {
        if ($item['image_url']) {
            $item['image_url'] = $baseUrl . '/' . $item['image_url'];
        }
    }
    echo json_encode(['status' => 'success', 'data' => $fish]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>