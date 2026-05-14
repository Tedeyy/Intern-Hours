<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$email = $_GET['email'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'message' => 'Invalid email']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode(['available' => false]);
} else {
    echo json_encode(['available' => true]);
}
?>
