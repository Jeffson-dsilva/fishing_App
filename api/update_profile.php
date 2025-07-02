<?php
ob_clean();
header('Content-Type: application/json');
error_reporting(0);

require_once '../include/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'], $data['name'], $data['phone'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$user_id = intval($data['user_id']);
$name = trim($data['name']);
$phone = trim($data['phone']);

try {
    $stmt = $pdo->prepare("UPDATE user SET name = ?, phone = ? WHERE user_id = ?");
    $stmt->execute([$name, $phone, $user_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "No changes made or user not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}
exit;
?>
