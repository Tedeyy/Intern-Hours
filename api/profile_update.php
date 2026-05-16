<?php
require_once __DIR__ . '/../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $is_public = isset($_POST['is_public']) ? (int)$_POST['is_public'] : null;
    $is_darkmode = isset($_POST['is_darkmode']) ? (int)$_POST['is_darkmode'] : null;

    try {
        if ($is_public !== null) {
            $stmt = $pdo->prepare("UPDATE users SET is_public = ? WHERE id = ?");
            $stmt->execute([$is_public, $user_id]);
            $_SESSION['is_public'] = $is_public;
        }
        
        if ($is_darkmode !== null) {
            $stmt = $pdo->prepare("UPDATE users SET is_darkmode = ? WHERE id = ?");
            $stmt->execute([$is_darkmode, $user_id]);
            $_SESSION['is_darkmode'] = $is_darkmode;
        }

        echo json_encode(['success' => true, 'message' => 'Settings updated']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>
