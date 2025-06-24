<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../include/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
file_put_contents("debug.log", json_encode($data) . PHP_EOL, FILE_APPEND);

if (!isset($data['user_id'], $data['oldPassword'], $data['newPassword'])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$userId = intval($data['user_id']);
$oldPassword = $data['oldPassword'];
$newPassword = $data['newPassword'];

// Get user
$stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

// Verify current password
if (!password_verify($oldPassword, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Current password is incorrect"]);
    exit;
}

// Update with new hashed password
$newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
$update->bind_param("si", $newHashed, $userId);

if ($update->execute()) {
    echo json_encode(["success" => true, "message" => "Password changed successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update password"]);
}

$update->close();
$conn->close();
?>
