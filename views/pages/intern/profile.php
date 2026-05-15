<?php
// Ensure this is included through feed.php and user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../feed.php?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch latest user data including is_public and total hours
$stmt = $pdo->prepare("
    SELECT u.*, 
    (SELECT SUM(hours) FROM hours_log WHERE user_id = u.id) as total_hours
    FROM users u WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$user_name = $user['name'];
$user_email = $user['email'];
$user_role = $user['role'];
$is_public = (bool)$user['is_public'];
$total_hours = $user['total_hours'] ?? 0;

$office_name = $_SESSION['office_name'] ?? 'Not Assigned';
$organization_name = $_SESSION['organization_name'] ?? 'Not Assigned';

// Base URL for assets
$base_url = "../";
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Profile Header -->
        <div class="bg-gray-900 px-8 py-12 text-center relative">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full mb-4 text-gray-900 text-3xl font-bold">
                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
            <h1 class="text-3xl font-bold text-white"><?php echo htmlspecialchars($user_name); ?></h1>
            <p class="text-gray-400 mt-2"><?php echo htmlspecialchars($user_role); ?></p>
            
            <!-- Total Hours Badge -->
            <div class="mt-6 inline-block bg-blue-600 px-6 py-2 rounded-full text-white font-bold">
                Total Logged: <?php echo number_format($total_hours, 1); ?> hrs
            </div>
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

            <!-- Privacy Setting Section -->
            <div class="mt-12 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Privacy Settings</h3>
                        <p class="text-gray-600 text-sm">Allow colleagues in your organization to see your total hours.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="privacy-toggle" class="sr-only peer" <?php echo $is_public ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-100 flex justify-between items-center">
                <a href="feed.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Back to Dashboard
                </a>
                <span id="save-status" class="text-sm font-medium text-green-600 opacity-0 transition-opacity duration-300">Saved successfully!</span>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('privacy-toggle').addEventListener('change', function() {
    const isPublic = this.checked ? 1 : 0;
    const formData = new FormData();
    formData.append('is_public', isPublic);

    fetch('../api/profile_update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const status = document.getElementById('save-status');
            status.style.opacity = '1';
            setTimeout(() => { status.style.opacity = '0'; }, 2000);
        } else {
            alert('Error updating privacy: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error updating privacy settings.');
    });
});
</script>

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
