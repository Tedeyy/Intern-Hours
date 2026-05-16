<?php
require_once __DIR__ . '/config.php';

$email = 'tedju@example.com'; // I'll check a likely email or just list some
if (isset($argv[1])) $email = $argv[1];

$stmt = $pdo->prepare("SELECT email, password, role FROM users LIMIT 5");
$stmt->execute();
$users = $stmt->fetchAll();

foreach ($users as $u) {
    echo "Email: " . $u['email'] . " | Password (Hash): " . substr($u['password'], 0, 10) . "... (Length: " . strlen($u['password']) . ")\n";
}
