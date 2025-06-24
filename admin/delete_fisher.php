<?php
require_once '../include/db_connect.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM fisher WHERE fisher_id = ?");
        $stmt->execute([$_GET['id']]); // Use parameterized query
    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
}

header("Location: manage_fishers.php");
exit();
