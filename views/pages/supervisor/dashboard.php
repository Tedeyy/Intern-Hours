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
<link rel="stylesheet" href="../../../assets/css/supervisor-dashboard.css">
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
