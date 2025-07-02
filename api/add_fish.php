<?php
require_once '../include/db_connect.php';

header('Content-Type: application/json');

// Check required fields
if (
    !isset($_POST['fisher_id']) ||
    !isset($_POST['name']) ||
    !isset($_POST['price']) ||
    !isset($_POST['available_quantity'])
) {
    echo json_encode(['status' => false, 'message' => 'Missing required fields']);
    exit();
}

$fisher_id = $_POST['fisher_id'];
$name = $_POST['name'];
$description = $_POST['description'] ?? '';
$price = $_POST['price'];
$quantity = $_POST['available_quantity'];
$image_url = '';

// Handle image upload if exists
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/fish/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $image_url = 'fish/' . $filename;
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to upload image']);
        exit();
    }
}

// Insert fish record
$stmt = $pdo->prepare("INSERT INTO fish (fisher_id, name, description, price, available_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([$fisher_id, $name, $description, $price, $quantity, $image_url]);

if ($success) {
    echo json_encode(['status' => true, 'message' => 'Fish added successfully']);
} else {
    echo json_encode(['status' => false, 'message' => 'Failed to add fish']);
}
?>
