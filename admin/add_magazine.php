<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Create upload directory if it doesn't exist
$uploadDir = '../uploads/magazine';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $required = ['title', 'content', 'publish_date', 'file'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Please fill all required fields";
            header("Location: manage_magazines.php");
            exit();
        }
    }

    // File upload handling
    try {
        // PDF file validation
        $pdfFile = $_FILES['file'];
        if ($pdfFile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $pdfFile['error']);
        }

        // Check if file is PDF
        $pdfMime = mime_content_type($pdfFile['tmp_name']);
        if ($pdfMime !== 'application/pdf') {
            throw new Exception("Only PDF files are allowed");
        }

        // Generate unique filename
        $pdfFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $pdfFile['name']);
        $pdfTargetPath = $uploadDir . $pdfFileName;

        // Move uploaded file
        if (!move_uploaded_file($pdfFile['tmp_name'], $pdfTargetPath)) {
            throw new Exception("Failed to move uploaded PDF file");
        }

        // Image file handling (optional)
        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $imageFile = $_FILES['image'];
            
            // Check if image upload was successful
            if ($imageFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload error: " . $imageFile['error']);
            }

            // Validate image type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $imageMime = mime_content_type($imageFile['tmp_name']);
            if (!in_array($imageMime, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }

            // Generate unique filename for image
            $imageFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $imageFile['name']);
            $imageTargetPath = $uploadDir . $imageFileName;

            // Move uploaded image
            if (!move_uploaded_file($imageFile['tmp_name'], $imageTargetPath)) {
                throw new Exception("Failed to move uploaded image file");
            }

            $imageUrl = 'magazine/' . $imageFileName;
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO magazine (title, content, publish_date, file_url, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $_POST['publish_date'],
            'magazine/' . $pdfFileName,
            $imageUrl
        ]);

        $_SESSION['success'] = "Magazine added successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        // Clean up if files were uploaded but DB insert failed
        if (isset($pdfTargetPath) && file_exists($pdfTargetPath)) {
            unlink($pdfTargetPath);
        }
        if (isset($imageTargetPath) && file_exists($imageTargetPath)) {
            unlink($imageTargetPath);
        }
    }

    header("Location: manage_magazines.php");
    exit();
}
?>