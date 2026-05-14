<?php
require_once __DIR__ . '/../config.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized', 'success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure hours table exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS hours_log (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        date TEXT NOT NULL,
        hours REAL NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, date),
        FOREIGN KEY(user_id) REFERENCES users(id)
    )
");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get hours for a specific month
    $month = $_GET['month'] ?? date('m');
    $year = $_GET['year'] ?? date('Y');
    
    // Build date range
    $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
    $endDate = date('Y-m-t', strtotime($startDate));
    
    $stmt = $pdo->prepare("
        SELECT date, hours 
        FROM hours_log 
        WHERE user_id = ? AND date BETWEEN ? AND ?
        ORDER BY date ASC
    ");
    $stmt->execute([$user_id, $startDate, $endDate]);
    
    $hours = [];
    foreach ($stmt->fetchAll() as $row) {
        $hours[$row['date']] = $row['hours'];
    }
    
    echo json_encode(['success' => true, 'hours' => $hours]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $hours = $_POST['hours'] ?? '';
    $delete = $_POST['delete'] ?? false;
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo json_encode(['error' => 'Invalid date format', 'success' => false]);
        exit;
    }
    
    try {
        if ($delete) {
            // Delete entry
            $stmt = $pdo->prepare("DELETE FROM hours_log WHERE user_id = ? AND date = ?");
            $stmt->execute([$user_id, $date]);
            echo json_encode(['success' => true, 'message' => 'Entry deleted']);
        } else {
            // Insert or update entry
            $hours = floatval($hours);
            
            if ($hours < 0 || $hours > 24) {
                echo json_encode(['error' => 'Hours must be between 0 and 24', 'success' => false]);
                exit;
            }
            
            // Try insert, if duplicate key then update
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO hours_log (user_id, date, hours) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user_id, $date, $hours]);
            } catch (PDOException $e) {
                // Update if entry already exists
                $stmt = $pdo->prepare("
                    UPDATE hours_log 
                    SET hours = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE user_id = ? AND date = ?
                ");
                $stmt->execute([$hours, $user_id, $date]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Hours logged']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'success' => false]);
    }
    exit;
}

echo json_encode(['error' => 'Method not allowed', 'success' => false]);
?>
