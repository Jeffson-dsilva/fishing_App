<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page";
    header("Location: index.php");
    exit();
}

// Create upload directory if it doesn't exist
$uploadDir = __DIR__ . '/../../uploads/magazine/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $_SESSION['error'] = "Failed to create upload directory";
        header("Location: manage_magazines.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $required = ['title', 'content', 'publish_date'];
    $missingFields = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }
    
    // Special handling for file upload
    if (empty($_FILES['file']['name'])) {
        $missingFields[] = 'file';
    }
    
    if (!empty($missingFields)) {
        $_SESSION['error'] = "Please fill all required fields: " . implode(', ', $missingFields);
        header("Location: manage_magazines.php");
        exit();
    }

    // File upload handling
    try {
        // PDF file validation
        $pdfFile = $_FILES['file'];
        
        // Check for upload errors
        if ($pdfFile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $pdfFile['error']);
        }


        // Check file type
        $allowedPdfTypes = ['application/pdf'];
        $pdfMime = mime_content_type($pdfFile['tmp_name']);
        if (!in_array($pdfMime, $allowedPdfTypes)) {
            throw new Exception("Only PDF files are allowed");
        }

        // Check file size (limit to 10MB)
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        if ($pdfFile['size'] > $maxFileSize) {
            throw new Exception("PDF file size exceeds maximum limit of 10MB");
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
            
            // Check for upload errors
            if ($imageFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload error: " . $imageFile['error']);
            }

            // Validate image type
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $imageMime = mime_content_type($imageFile['tmp_name']);
            if (!in_array($imageMime, $allowedImageTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }

            // Check image size (limit to 5MB)
            $maxImageSize = 5 * 1024 * 1024; // 5MB
            if ($imageFile['size'] > $maxImageSize) {
                throw new Exception("Image file size exceeds maximum limit of 5MB");
            }

            // Generate unique filename for image
            $imageFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $imageFile['name']);
            $imageTargetPath = $uploadDir . $imageFileName;

            // Move uploaded image
            if (!move_uploaded_file($imageFile['tmp_name'], $imageTargetPath)) {
                throw new Exception("Failed to move uploaded image file");
            }

            $imageUrl = 'uploads/magazine/' . $imageFileName;
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO magazine (title, content, publish_date, file_url, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $_POST['publish_date'],
            'uploads/magazine/' . $pdfFileName,
            $imageUrl
        ]);

        // Check if insert was successful
        if ($stmt->rowCount() === 0) {
            throw new Exception("Failed to save magazine to database");
        }

        $_SESSION['success'] = "Magazine added successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        
        // Clean up uploaded files if database insert failed
        if (isset($pdfTargetPath) && file_exists($pdfTargetPath)) {
            @unlink($pdfTargetPath);
        }
        if (isset($imageTargetPath) && file_exists($imageTargetPath)) {
            @unlink($imageTargetPath);
        }
    }

    header("Location: manage_magazines.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: manage_magazines.php");
    exit();
}