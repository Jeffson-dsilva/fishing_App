<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $magazine_id = $_POST['magazine_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $publish_date = $_POST['publish_date'];
    
    // Get current magazine data
    $stmt = $pdo->prepare("SELECT file_url, image_url FROM magazine WHERE magazine_id = ?");
    $stmt->execute([$magazine_id]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $file_url = $current['file_url'];
    $image_url = $current['image_url'];
    
    $uploadDir = '../magazine/';
    
    // Handle PDF file update if new file is uploaded
    if (!empty($_FILES['file']['name'])) {
        $pdfFile = $_FILES['file'];
        $pdfFileName = uniqid() . '_' . basename($pdfFile['name']);
        $pdfTargetPath = $uploadDir . $pdfFileName;
        
        if (move_uploaded_file($pdfFile['tmp_name'], $pdfTargetPath)) {
            // Delete old PDF file if it exists
            if ($file_url && file_exists('../' . $file_url)) {
                unlink('../' . $file_url);
            }
            $file_url = 'magazine/' . $pdfFileName;
        }
    }
    
    // Handle image file update if new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $imageFile = $_FILES['image'];
        $imageFileName = uniqid() . '_' . basename($imageFile['name']);
        $imageTargetPath = $uploadDir . $imageFileName;
        
        if (move_uploaded_file($imageFile['tmp_name'], $imageTargetPath)) {
            // Delete old image file if it exists
            if ($image_url && file_exists('../' . $image_url)) {
                unlink('../' . $image_url);
            }
            $image_url = 'magazine/' . $imageFileName;
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE magazine SET title = ?, content = ?, publish_date = ?, file_url = ?, image_url = ? WHERE magazine_id = ?");
        $stmt->execute([$title, $content, $publish_date, $file_url, $image_url, $magazine_id]);
        
        $_SESSION['success'] = "Magazine updated successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating magazine: " . $e->getMessage();
    }
    
    header("Location: manage_magazines.php");
    exit();
}
?>