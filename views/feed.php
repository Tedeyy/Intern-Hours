<?php
require_once __DIR__ . '/../config.php';
session_start();

// Redirect logic
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $requested_page = $_GET['page'] ?? '';
    // If logged in and on landing or login page, go to dashboard
    if (empty($requested_page) || $requested_page === 'login') {
        header("Location: feed.php?page=dashboard");
        exit;
    }
} else {
    // If not logged in and trying to access restricted page, go to login
    $requested_page = $_GET['page'] ?? 'login';
    if (!in_array($requested_page, ['login', 'register'])) {
        header("Location: feed.php?page=login");
        exit;
    }
}

$page = $_GET['page'] ?? 'login';
$showNavbar = !in_array($page, ['login', 'register']);
?>

<?php 
$base_url = "../";
require_once __DIR__ . '/components/header.php'; 
?>
</head>
<body class="<?php echo isset($_SESSION['user_id']) ? '' : 'bg-gray-50'; ?>">

<?php if ($showNavbar): ?>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>
<?php endif; ?>

<main>
    <?php
    switch ($page) {
        case 'register':
            require_once __DIR__ . '/pages/auth/registry.php';
            break;
        case 'profile':
            require_once __DIR__ . '/pages/intern/profile.php';
            break;
        case 'dashboard':
            if ($_SESSION['user_role'] === 'Admin') {
                require_once __DIR__ . '/pages/supervisor/dashboard.php';
            } else {
                require_once __DIR__ . '/pages/intern/dashboard.php';
            }
            break;
        case 'colleagues':
            require_once __DIR__ . '/pages/intern/colleagues.php';
            break;
        case 'login':
        default:
            require_once __DIR__ . '/pages/auth/login.php';
            break;
    }
    ?>
</main>

<?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>
