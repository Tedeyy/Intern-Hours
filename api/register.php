<?php
require_once __DIR__ . '/../config.php';

// Ensure tables exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS office (
            id INT AUTO_INCREMENT PRIMARY KEY,
            office_name VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS organization (
            id INT AUTO_INCREMENT PRIMARY KEY,
            organization_name VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
        )
    ");
} catch (PDOException $e) {
    error_log('Table creation error: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $office_id = $_POST['office_id'] ?? '';
    $organization_id = $_POST['organization_id'] ?? '';
    $new_office_name = trim($_POST['new_office_name'] ?? '');
    $new_organization_name = trim($_POST['new_organization_name'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($role) || empty($office_id) || empty($organization_id)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            try {
                $pdo->beginTransaction();

                // Handle new office
                if ($office_id === 'new' && !empty($new_office_name)) {
                    $stmt = $pdo->prepare("SELECT id FROM office WHERE office_name = ?");
                    $stmt->execute([$new_office_name]);
                    $existing = $stmt->fetch();
                    if ($existing) {
                        $office_id = $existing['id'];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO office (office_name) VALUES (?)");
                        $stmt->execute([$new_office_name]);
                        $office_id = $pdo->lastInsertId();
                    }
                }

                // Handle new organization
                if ($organization_id === 'new' && !empty($new_organization_name)) {
                    $stmt = $pdo->prepare("SELECT id FROM organization WHERE organization_name = ?");
                    $stmt->execute([$new_organization_name]);
                    $existing = $stmt->fetch();
                    if ($existing) {
                        $organization_id = $existing['id'];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO organization (organization_name) VALUES (?)");
                        $stmt->execute([$new_organization_name]);
                        $organization_id = $pdo->lastInsertId();
                    }
                }

                // Insert new user
                $secret_key = $_ENV['SECRET_KEY'] ?? 'default-secret-key';
                $hashed_password = hash_hmac('sha256', $password, $secret_key);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, office_id, organization_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role, $office_id, $organization_id]);
                
                $pdo->commit();
                header("Location: ../views/feed.php?page=login&success=registered");
                exit;
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = "Registration failed. Please try again.";
            }
        }
    }
    // Redirect back with error if set
    if (isset($error)) {
        header("Location: ../views/feed.php?page=register&error=" . urlencode($error));
        exit;
    }
} else {
    // If someone tries to access this file directly via GET
    header("Location: ../views/feed.php?page=register");
    exit;
}
?>
