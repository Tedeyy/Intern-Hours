<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['error' => 'Unauthorized', 'success' => false]);
    exit;
}

$organization_id = $_GET['organization_id'] ?? '';
$office_id = $_GET['office_id'] ?? '';
$search = $_GET['search'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

try {
    $sql = "
        SELECT 
            h.id, 
            h.date, 
            h.hours, 
            u.name as intern_name, 
            o.office_name, 
            org.organization_name
        FROM hours_log h
        JOIN users u ON h.user_id = u.id
        LEFT JOIN office o ON u.office_id = o.id
        LEFT JOIN organization org ON u.organization_id = org.id
        WHERE 1=1
    ";
    
    $params = [];

    if (!empty($organization_id)) {
        $sql .= " AND u.organization_id = ?";
        $params[] = $organization_id;
    }

    if (!empty($office_id)) {
        $sql .= " AND u.office_id = ?";
        $params[] = $office_id;
    }

    if (!empty($search)) {
        $sql .= " AND u.name LIKE ?";
        $params[] = "%$search%";
    }

    if (!empty($from_date)) {
        $sql .= " AND h.date >= ?";
        $params[] = $from_date;
    }

    if (!empty($to_date)) {
        $sql .= " AND h.date <= ?";
        $params[] = $to_date;
    }

    $sql .= " ORDER BY org.organization_name ASC, h.date DESC, u.name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $hours = $stmt->fetchAll();

    // Group by organization for the UI if needed, but let's send flat and group in JS
    echo json_encode(['success' => true, 'hours' => $hours]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'success' => false]);
}
?>
