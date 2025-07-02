<?php
session_start();
require_once '../include/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($password)) {
        header("Location: manage_fishers.php");
        exit();
    }

    // Hash the password for security
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO fisher (name, email, phone, location, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $location, $passwordHash]);
    } catch (PDOException $e) {
        // Optionally handle errors, e.g., log or show a message
    }

    header("Location: manage_fishers.php");
    exit();
} else {
    header("Location: manage_fishers.php");
    exit();
}
?>
