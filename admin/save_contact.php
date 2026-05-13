<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        echo json_encode(['success' => true, 'message' => 'Your message has been received, thank you!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>