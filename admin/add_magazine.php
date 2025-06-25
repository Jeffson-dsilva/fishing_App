<?php
require_once '../include/db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Create upload directory if it doesn't exist
$uploadDir = '../uploads/magazine/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['publish_date'])) {
        $_SESSION['error'] = "Please fill all required fields";
        header("Location: manage_magazines.php");
        exit();
    }

    if (empty($_FILES['file']['name'])) {
        $_SESSION['error'] = "Please upload a PDF file";
        header("Location: manage_magazines.php");
        exit();
    }

    try {
        $pdfFile = $_FILES['file'];
        if ($pdfFile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $pdfFile['error']);
        }

        $pdfMime = mime_content_type($pdfFile['tmp_name']);
        if ($pdfMime !== 'application/pdf') {
            throw new Exception("Only PDF files are allowed");
        }

        $pdfFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $pdfFile['name']);
        $pdfTargetPath = $uploadDir . $pdfFileName;

        if (!move_uploaded_file($pdfFile['tmp_name'], $pdfTargetPath)) {
            throw new Exception("Failed to move uploaded PDF file");
        }

        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $imageFile = $_FILES['image'];

            if ($imageFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload error: " . $imageFile['error']);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $imageMime = mime_content_type($imageFile['tmp_name']);
            if (!in_array($imageMime, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }

            $imageFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $imageFile['name']);
            $imageTargetPath = $uploadDir . $imageFileName;

            if (!move_uploaded_file($imageFile['tmp_name'], $imageTargetPath)) {
                throw new Exception("Failed to move uploaded image file");
            }

            $imageUrl = 'uploads/magazine/' . $imageFileName;
        }

        $stmt = $pdo->prepare("INSERT INTO magazine (title, content, publish_date, file_url, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $_POST['publish_date'],
            'uploads/magazine/' . $pdfFileName,
            $imageUrl
        ]);

        $_SESSION['success'] = "Magazine added successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
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
