<?php
ob_start();
header('Content-Type: application/json');
error_reporting(0);

require_once '../include/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['name']) || !isset($data['phone'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$user_id = intval($data['user_id']);
$name = trim($data['name']);
$phone = trim($data['phone']);

$stmt = $conn->prepare("UPDATE user SET name = ?, phone = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $name, $phone, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}

$stmt->close();
$conn->close();
ob_end_clean();
?>
