<?php
require_once __DIR__ . '/config.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Connection successful. User count: " . $count . "\n";
    
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in 'users' table: " . implode(', ', $columns) . "\n";
    
} catch (Exception $e) {
    echo "Connection failed or query error: " . $e->getMessage() . "\n";
}
