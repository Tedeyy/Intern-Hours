<nav class="bg-white shadow-sm sticky top-0 w-full z-50 py-4 mb-6">
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-gray-900">
            <a href="<?php echo $base_url ?? ''; ?>index.html">OurTracker</a>
        </div>
        <div class="flex items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $base_url ?? ''; ?>api/logout.php" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition font-semibold">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>