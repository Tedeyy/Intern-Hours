<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['error' => 'Unauthorized', 'success' => false]);
    exit;
}

try {
    // 1. Total hours per Intern
    $intern_hours = $pdo->query("
        SELECT u.name, SUM(h.hours) as total_hours
        FROM users u
        JOIN hours_log h ON u.id = h.user_id
        GROUP BY u.id
        ORDER BY total_hours DESC
    ")->fetchAll();

    // 2. Total hours per Organization
    $org_hours = $pdo->query("
        SELECT org.organization_name as name, SUM(h.hours) as total_hours
        FROM organization org
        JOIN users u ON org.id = u.organization_id
        JOIN hours_log h ON u.id = h.user_id
        GROUP BY org.id
        ORDER BY total_hours DESC
    ")->fetchAll();

    // 3. Total hours per Office
    $office_hours = $pdo->query("
        SELECT o.office_name as name, SUM(h.hours) as total_hours
        FROM office o
        JOIN users u ON o.id = u.office_id
        JOIN hours_log h ON u.id = h.user_id
        GROUP BY o.id
        ORDER BY total_hours DESC
    ")->fetchAll();

    echo json_encode([
        'success' => true,
        'intern_hours' => $intern_hours,
        'org_hours' => $org_hours,
        'office_hours' => $office_hours
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'success' => false]);
}
?>
