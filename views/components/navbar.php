<nav class="bg-white shadow-sm sticky top-0 w-full z-50 py-4 mb-6">
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center gap-8">
            <div class="text-2xl font-bold text-gray-900">
                <a href="<?php echo $base_url ?? ''; ?>index.html">OurTracker</a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="hidden md:flex items-center gap-6">
                    <a href="<?php echo $base_url ?? ''; ?>views/pages/<?php echo $_SESSION['user_role'] === 'Admin' ? 'supervisor/dashboard.php' : 'intern/dashboard.php'; ?>" class="text-gray-600 hover:text-gray-900 font-medium transition">Dashboard</a>
                    
                    <?php if ($_SESSION['user_role'] !== 'Admin'): ?>
                        <a href="<?php echo $base_url ?? ''; ?>views/pages/intern/colleagues.php" class="text-gray-600 hover:text-gray-900 font-medium transition">Colleagues</a>
                    <?php endif; ?>

                    <?php if ($_SESSION['user_role'] === 'Admin'): ?>
                        <a href="<?php echo $base_url ?? ''; ?>views/pages/supervisor/all-hours.php" class="text-gray-600 hover:text-gray-900 font-medium transition">All Hours</a>
                        <a href="<?php echo $base_url ?? ''; ?>views/pages/supervisor/user-management.php" class="text-gray-600 hover:text-gray-900 font-medium transition">Users</a>
                        <a href="<?php echo $base_url ?? ''; ?>views/pages/supervisor/reports.php" class="text-gray-600 hover:text-gray-900 font-medium transition">Reports</a>
                    <?php endif; ?>
                    
                    <a href="<?php echo $base_url ?? ''; ?>views/pages/profile.php" class="text-gray-600 hover:text-gray-900 font-medium transition">Profile</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-sm text-gray-500 hidden sm:inline">Hello, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
                <a href="<?php echo $base_url ?? ''; ?>api/logout.php" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition font-semibold text-sm">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_url ?? ''; ?>views/feed.php?page=login" class="text-gray-600 hover:text-gray-900 font-medium transition">Login</a>
                <a href="<?php echo $base_url ?? ''; ?>views/feed.php?page=register" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-black transition font-medium">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>