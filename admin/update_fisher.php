<?php
require_once '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['fisher_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['phone']; // Correct field name from form

    try {
        $stmt = $pdo->prepare("UPDATE fisher SET name = ?, email = ?, phone = ? WHERE fisher_id = ?");
        $stmt->execute([$name, $email, $contact, $id]);
    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}

header("Location: manage_fishers.php");
exit();
