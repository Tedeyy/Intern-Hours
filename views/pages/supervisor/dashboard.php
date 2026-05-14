<?php
session_start();

// Check if user is logged in and is Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../../feed.php?page=login");
    exit;
}

$base_url = "../../../";
require_once '../../components/header.php';
?>
<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        min-height: 60vh;
    }
    .welcome-card {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
</style>
</head>
<body class="bg-gray-50">
    <?php require_once '../../components/navbar.php'; ?>

    <div class="dashboard-container">
        <div class="welcome-card">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome, Supervisor <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <p class="text-gray-600">The supervisor dashboard is currently under development. Here you will soon be able to track and approve intern hours.</p>
        </div>
    </div>

    <?php require_once '../../components/footer.php'; ?>
</body>
</html>
