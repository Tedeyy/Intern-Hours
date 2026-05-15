<?php
// Ensure this is included through feed.php and user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../feed.php?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
$office_name = $_SESSION['office_name'] ?? 'Not Assigned';
$organization_name = $_SESSION['organization_name'] ?? 'Not Assigned';

// Base URL for assets
$base_url = "../";
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Profile Header -->
        <div class="bg-gray-900 px-8 py-12 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full mb-4 text-gray-900 text-3xl font-bold">
                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
            <h1 class="text-3xl font-bold text-white"><?php echo htmlspecialchars($user_name); ?></h1>
            <p class="text-gray-400 mt-2"><?php echo htmlspecialchars($user_role); ?></p>
        </div>

        <!-- Profile Details -->
        <div class="p-8">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500 uppercase tracking-wider">Email Address</label>
                        <p class="text-lg font-semibold text-gray-900 mt-1"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 uppercase tracking-wider">Role</label>
                        <p class="text-lg font-semibold text-gray-900 mt-1"><?php echo htmlspecialchars($user_role); ?></p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500 uppercase tracking-wider">Office</label>
                        <p class="text-lg font-semibold text-gray-900 mt-1"><?php echo htmlspecialchars($office_name); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 uppercase tracking-wider">Organization</label>
                        <p class="text-lg font-semibold text-gray-900 mt-1"><?php echo htmlspecialchars($organization_name); ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                <a href="feed.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Back to Dashboard
                </a>
                <button onclick="alert('Profile editing coming soon!')" class="px-6 py-2 bg-gray-900 text-white rounded-lg font-semibold hover:bg-black transition">
                    Edit Profile
                </button>
            </div>
        </div>
    </div>

    <!-- Security Info -->
    <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-6 flex items-start space-x-4">
        <div class="text-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h4 class="text-blue-900 font-bold">Privacy Note</h4>
            <p class="text-blue-800 text-sm mt-1">Your profile information is only visible to you and your assigned supervisors. Keep your credentials secure.</p>
        </div>
    </div>
</div>
