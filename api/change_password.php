<?php
header('Content-Type: application/json');
error_reporting(0);

require_once '../include/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'], $data['oldPassword'], $data['newPassword'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$user_id = intval($data['user_id']);
$oldPassword = $data['oldPassword'];
$newPassword = $data['newPassword'];

try {
    // Fetch existing password hash
    $stmt = $pdo->prepare("SELECT password FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit;
    }

    // Verify old password using password_verify
    if (!password_verify($oldPassword, $user['password'])) {
        echo json_encode(["success" => false, "message" => "Invalid current password"]);
        exit;
    }

    // Hash new password
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $updateStmt = $pdo->prepare("UPDATE user SET password = ? WHERE user_id = ?");
    $updateStmt->execute([$hashedNewPassword, $user_id]);

    echo json_encode(["success" => true, "message" => "Password updated successfully"]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Update failed", "error" => $e->getMessage()]);
}
?>
