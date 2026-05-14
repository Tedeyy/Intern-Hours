<?php
$page = $_GET['page'] ?? 'login';
$showNavbar = !in_array($page, ['login', 'register']);
?>

<?php require_once __DIR__ . '/components/header.php'; ?>

<?php if ($showNavbar): ?>
    <?php require_once __DIR__ . '/components/navbar.php'; ?>
<?php endif; ?>

<main>
    <?php
    switch ($page) {
        case 'register':
            require_once __DIR__ . '/pages/auth/regitry.php';
            break;
        case 'login':
        default:
            require_once __DIR__ . '/pages/auth/login.php';
            break;
    }
    ?>
</main>

<?php require_once __DIR__ . '/components/footer.php'; ?>
