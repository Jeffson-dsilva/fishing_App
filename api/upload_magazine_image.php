<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || !isset($_POST['magazine_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Image and magazine_id are required']);
        exit;
    }

    $magazine_id = (int)$_POST['magazine_id'];
    $image = $_FILES['image'];
    $uploadDir = '../magazine/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    // Validate file
    if (!in_array($image['type'], $allowedTypes)) {
        
        echo json_encode(['status' => 'error', 'message' => 'Invalid image type. Use JPEG, PNG, or GIF']);
        exit;
    }
    if ($image['size'] > $maxFileSize) {
        echo json_encode(['status' => 'error', 'message' => 'Image size exceeds 5MB']);
        exit;
    }

    // Check if magazine exists
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM magazine WHERE magazine_id = :magazine_id");
        $stmt->execute([':magazine_id' => $magazine_id]);
        if ($stmt->fetchColumn() == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Magazine not found']);
            exit;
        }

        // Generate unique filename
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = 'magazine_' . $magazine_id . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            // Update magazine image_url
            $stmt = $pdo->prepare("UPDATE magazine SET image_url = :image_url WHERE magazine_id = :magazine_id");
            $stmt->execute([
                ':image_url' => 'magazine/' . $filename,
                ':magazine_id' => $magazine_id
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>