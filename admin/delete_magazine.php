<?php
require_once '../include/db_connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    try {
        // Get magazine data first to delete files
        $stmt = $pdo->prepare("SELECT file_url, image_url FROM magazine WHERE magazine_id = ?");
        $stmt->execute([$_GET['id']]);
        $magazine = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete the magazine record
        $stmt = $pdo->prepare("DELETE FROM magazine WHERE magazine_id = ?");
        $stmt->execute([$_GET['id']]);
        
        // Delete associated files
        if ($magazine['file_url'] && file_exists('../' . $magazine['file_url'])) {
            unlink('../' . $magazine['file_url']);
        }
        if ($magazine['image_url'] && file_exists('../' . $magazine['image_url'])) {
            unlink('../' . $magazine['image_url']);
        }
        
        $_SESSION['success'] = "Magazine deleted successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting magazine: " . $e->getMessage();
    }
}

header("Location: manage_magazines.php");
exit();
?>