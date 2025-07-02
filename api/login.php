<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../include/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
        exit;
    }

    try {
        // 1. Check in user table
        $stmt = $pdo->prepare("SELECT user_id AS id, name, email, password FROM user WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $user['role'] = 'user';
            echo json_encode(['status' => 'success', 'message' => 'Login successful', 'user' => $user]);
            exit;
        }

        // 2. Check in fisher table
        $stmt = $pdo->prepare("SELECT fisher_id AS id, name, email, password FROM fisher WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $fisher = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fisher && password_verify($password, $fisher['password'])) {
            unset($fisher['password']);
            $fisher['role'] = 'fisher';
            echo json_encode(['status' => 'success', 'message' => 'Login successful', 'user' => $fisher]);
            exit;
        }

        // 3. If not found in both tables
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
