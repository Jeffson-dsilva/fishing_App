<?php
require_once '../include/db_connect.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
        $stmt->execute([$_GET['id']]); // ✅ This is the correct PDO method
    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
}

header("Location: manage_users.php");
exit();
