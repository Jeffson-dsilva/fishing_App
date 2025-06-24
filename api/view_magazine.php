<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

try {
    $stmt = $pdo->query("SELECT magazine_id, title, content, publish_date, file_url, image_url FROM magazine ORDER BY publish_date DESC");
    $magazines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Append base URL to image_url and file_url for full path
    $baseUrl = 'http://192.168.0.181/fishing/uploads';
    foreach ($magazines as &$magazine) {
        if ($magazine['image_url']) {
            $magazine['image_url'] = $baseUrl . '/' . $magazine['image_url'];
        }
        if ($magazine['file_url']) {
            $magazine['file_url'] = $baseUrl . '/' . $magazine['file_url'];
        }
    }
    echo json_encode(['status' => 'success', 'data' => $magazines]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>