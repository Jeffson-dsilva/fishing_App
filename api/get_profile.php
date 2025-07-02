<?php
ob_clean();
header('Content-Type: application/json');
error_reporting(0);

require_once '../include/db_connect.php';

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT user_id, name, email, phone FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(["success" => true, "data" => $user]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "error" => $e->getMessage() // This will reveal the real SQL issue
    ]);
}
exit;
?>
