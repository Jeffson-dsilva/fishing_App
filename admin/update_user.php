<?php
require_once '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['phone']; // Correct field name from form

    try {
        $stmt = $pdo->prepare("UPDATE user SET name = ?, email = ?, phone = ? WHERE user_id = ?");
        $stmt->execute([$name, $email, $contact, $id]);
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}

header("Location: manage_users.php");
exit();
