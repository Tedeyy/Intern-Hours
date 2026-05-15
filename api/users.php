<?php
require_once __DIR__ . '/../config.php';

session_start();

// Check if user is logged in and is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['error' => 'Unauthorized', 'success' => false]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // List users
        $stmt = $pdo->query("
            SELECT u.id, u.name, u.email, u.role, o.office_name, org.organization_name, u.office_id, u.organization_id
            FROM users u
            LEFT JOIN office o ON u.office_id = o.id
            LEFT JOIN organization org ON u.organization_id = org.id
            ORDER BY u.role ASC, u.name ASC
        ");
        $users = $stmt->fetchAll();
        echo json_encode(['success' => true, 'users' => $users]);
    } 
    elseif ($method === 'POST') {
        // Update user (including role)
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $office_id = $_POST['office_id'] ?? '';
        $organization_id = $_POST['organization_id'] ?? '';

        if (empty($id) || empty($name) || empty($email) || empty($role)) {
            echo json_encode(['error' => 'All fields are required.', 'success' => false]);
            exit;
        }

        // Prevent self-demotion or self-deletion if needed, but let's keep it simple for now
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, email = ?, role = ?, office_id = ?, organization_id = ? 
            WHERE id = ?
        ");
        $stmt->execute([$name, $email, $role, $office_id, $organization_id, $id]);

        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    }
    elseif ($method === 'DELETE') {
        // Delete user
        // DELETE method doesn't support $_POST, need to parse input
        parse_str(file_get_contents("php://input"), $vars);
        $id = $vars['id'] ?? '';

        if (empty($id)) {
            echo json_encode(['error' => 'User ID is required.', 'success' => false]);
            exit;
        }

        if ($id == $_SESSION['user_id']) {
            echo json_encode(['error' => 'You cannot delete your own account.', 'success' => false]);
            exit;
        }

        // Delete associated hours first (or rely on CASCADE if set, but let's be safe)
        $pdo->prepare("DELETE FROM hours_log WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM absences WHERE intern_id = ?")->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'success' => false]);
}
?>
