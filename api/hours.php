<?php
require_once __DIR__ . '/../config.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized', 'success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Intern';

// Allow admins to view other users' hours
if ($user_role === 'Admin' && isset($_GET['userId'])) {
    $user_id = $_GET['userId'];
}

// Ensure hours table exists
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hours_log (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            date DATE NOT NULL,
            hours DECIMAL(5,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_date (user_id, date),
            FOREIGN KEY(user_id) REFERENCES users(id)
        )
    ");
} catch (PDOException $e) {
    // Table might already exist or other error, continue anyway
    error_log('Table creation error: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $hours = [];
    
    // Check if requesting all hours
    if (isset($_GET['all']) && $_GET['all'] === 'true') {
        $stmt = $pdo->prepare("
            SELECT date, hours 
            FROM hours_log 
            WHERE user_id = ?
            ORDER BY date ASC
        ");
        $stmt->execute([$user_id]);
    }
    // Check if requesting filtered date range
    else if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
        $fromDate = $_GET['from_date'];
        $toDate = $_GET['to_date'];
        
        // Validate dates
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate)) {
            echo json_encode(['error' => 'Invalid date format', 'success' => false]);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT date, hours 
            FROM hours_log 
            WHERE user_id = ? AND date BETWEEN ? AND ?
            ORDER BY date ASC
        ");
        $stmt->execute([$user_id, $fromDate, $toDate]);
    }
    // Default: get hours for a specific month
    else {
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
    }
    
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
                if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'UNIQUE') !== false) {
                    $stmt = $pdo->prepare("
                        UPDATE hours_log 
                        SET hours = ?, updated_at = NOW() 
                        WHERE user_id = ? AND date = ?
                    ");
                    $stmt->execute([$hours, $user_id, $date]);
                } else {
                    throw $e;
                }
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
