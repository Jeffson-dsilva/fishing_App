<?php
require_once '../include/db_connect.php';
header('Content-Type: application/json');

$fish_id = $_POST['fish_id'] ?? null;
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? '';
$available_quantity = $_POST['available_quantity'] ?? '';

if (!$fish_id || $name == '' || $price == '' || $available_quantity == '') {
    echo json_encode(['status' => false, 'message' => 'Missing required fields']);
    exit();
}

// Fetch current fish details to check existence and get old image name
$stmt = $pdo->prepare("SELECT image_url FROM fish WHERE fish_id = ?");
$stmt->execute([$fish_id]);
$fish = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fish) {
    echo json_encode(['status' => false, 'message' => 'Fish not found']);
    exit();
}

$imageFileName = $fish['image_url'];

// Check if new image is uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image = $_FILES['image'];
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $ext;
    $uploadDir = '../uploads/fish/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($image['tmp_name'], $uploadDir . $newFileName)) {
        // Delete old image if exists
        if ($imageFileName && file_exists($uploadDir . $imageFileName)) {
            unlink($uploadDir . $imageFileName);
        }
        $imageFileName = 'fish/' . $newFileName;
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to upload new image']);
        exit();
    }
}

$updateStmt = $pdo->prepare("UPDATE fish SET name = ?, description = ?, price = ?, available_quantity = ?, image_url = ? WHERE fish_id = ?");
$updated = $updateStmt->execute([$name, $description, $price, $available_quantity, $imageFileName, $fish_id]);

if ($updated) {
    echo json_encode(['status' => true, 'message' => 'Fish updated successfully']);
} else {
    echo json_encode(['status' => false, 'message' => 'Failed to update fish']);
}
