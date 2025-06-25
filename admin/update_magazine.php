<?php
require_once '../include/db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['publish_date'])) {
        $_SESSION['error'] = "Please fill all required fields";
        header("Location: manage_magazines.php");
        exit();
    }

    $magazine_id = $_POST['magazine_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $publish_date = $_POST['publish_date'];
    
    // Get current magazine data
    try {
        $stmt = $pdo->prepare("SELECT file_url, image_url FROM magazine WHERE magazine_id = ?");
        $stmt->execute([$magazine_id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            throw new Exception("Magazine not found");
        }
        
        $file_url = $current['file_url'];
        $image_url = $current['image_url'];
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: manage_magazines.php");
        exit();
    }
    
    // Set upload directory
    $uploadDir = '../uploads/magazine/';
    
    // Handle PDF file update if new file is uploaded
    if (!empty($_FILES['file']['name'])) {
        try {
            $pdfFile = $_FILES['file'];
            
            // Check for upload errors
            if ($pdfFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload error: " . $pdfFile['error']);
            }

            // Validate file type
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
            
            // Delete old PDF file if it exists
            if ($file_url && file_exists('../' . $file_url)) {
                unlink('../' . $file_url);
            }
            
            $file_url = 'uploads/magazine/' . $pdfFileName;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: manage_magazines.php");
            exit();
        }
    }
    
    // Handle image file update if new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        try {
            $imageFile = $_FILES['image'];
            
            // Check for upload errors
            if ($imageFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload error: " . $imageFile['error']);
            }

            // Validate image type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $imageMime = mime_content_type($imageFile['tmp_name']);
            if (!in_array($imageMime, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }

            // Generate unique filename
            $imageFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $imageFile['name']);
            $imageTargetPath = $uploadDir . $imageFileName;
            
            // Move uploaded image
            if (!move_uploaded_file($imageFile['tmp_name'], $imageTargetPath)) {
                throw new Exception("Failed to move uploaded image file");
            }
            
            // Delete old image file if it exists
            if ($image_url && file_exists('../' . $image_url)) {
                unlink('../' . $image_url);
            }
            
            $image_url = 'uploads/magazine/' . $imageFileName;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: manage_magazines.php");
            exit();
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE magazine SET title = ?, content = ?, publish_date = ?, file_url = ?, image_url = ? WHERE magazine_id = ?");
        $stmt->execute([$title, $content, $publish_date, $file_url, $image_url, $magazine_id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("No changes were made to the magazine");
        }
        
        $_SESSION['success'] = "Magazine updated successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating magazine: " . $e->getMessage();
    }
    
    header("Location: manage_magazines.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: manage_magazines.php");
    exit();
}
?>