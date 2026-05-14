<?php
require_once __DIR__ . '/../config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if login is blocked due to too many attempts
    if (isset($_SESSION['login_blocked_until']) && time() < $_SESSION['login_blocked_until']) {
        $remaining_time = $_SESSION['login_blocked_until'] - time();
        echo json_encode(['error' => 'Too many failed attempts. Please wait ' . $remaining_time . ' seconds.']);
        exit;
    }

    // Validation
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
        echo json_encode(['error' => $error]);
        exit;
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $secret_key = getenv('SECRET_KEY') ?: 'default-secret-key';
            $hashed_password = hash_hmac('sha256', $password, $secret_key);
            
            if (hash_equals($user['password'], $hashed_password)) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Reset failed attempts on successful login
            unset($_SESSION['failed_attempts']);
            unset($_SESSION['login_blocked_until']);

            // Return success response with redirect URL
            $redirectUrl = $user['role'] === 'Admin' ? '../views/pages/supervisor/dashboard.php' : '../views/pages/intern/dashboard.php';
            echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
            exit;
            }
        }
        
        // Increment failed attempts
        $_SESSION['failed_attempts'] = ($_SESSION['failed_attempts'] ?? 0) + 1;
        
        // Block login if 3 failed attempts
        if ($_SESSION['failed_attempts'] >= 3) {
            $_SESSION['login_blocked_until'] = time() + 30;
            echo json_encode(['error' => 'Too many failed attempts. Please wait 30 seconds.', 'blocked' => true, 'blocked_until' => $_SESSION['login_blocked_until']]);
            exit;
        }
        
        $error = "Invalid email or password.";
        echo json_encode(['error' => $error, 'attempts' => $_SESSION['failed_attempts']]);
        exit;
    }
}

// Return current attempt count for frontend check
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = [
        'attempts' => $_SESSION['failed_attempts'] ?? 0,
        'blocked' => isset($_SESSION['login_blocked_until']) && time() < $_SESSION['login_blocked_until'],
        'blocked_until' => $_SESSION['login_blocked_until'] ?? null
    ];
    echo json_encode($response);
    exit;
}
?>
