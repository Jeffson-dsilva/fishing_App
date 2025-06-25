<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$rescueId = $input['rescue_id'] ?? null;
$status = $input['status'] ?? null;

if (!$rescueId || !$status) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE rescue SET status = ? WHERE rescue_id = ?");
    $stmt->execute([$status, $rescueId]);
    
    echo json_encode(['status' => 'success', 'message' => 'Rescue status updated']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}