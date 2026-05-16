<?php
// Ensure this is included through feed.php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: ../../feed.php?page=colleagues");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$office_name = $_SESSION['office_name'] ?? 'N/A';
$organization_name = $_SESSION['organization_name'] ?? 'N/A';

$base_url = "../";
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/colleagues.css">

<div class="colleagues-page">
        <!-- Page Header -->
        <div class="colleagues-page-header">
            <div>
                <h1>Office Colleagues</h1>
                <p class="subtitle"><?php echo htmlspecialchars($office_name); ?> • <?php echo htmlspecialchars($organization_name); ?></p>
            </div>
            <div class="colleagues-search">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="search-input" placeholder="Search by name or email..." oninput="onSearchInput(event)">
            </div>
        </div>

        <!-- Stats Row -->
        <div class="colleagues-stats">
            <div class="stat-pill">
                <div class="stat-icon blue">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="stat-total-colleagues">0</div>
                    <div class="stat-desc">Colleagues</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="stat-icon green">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="stat-public-count">0</div>
                    <div class="stat-desc">Public Profiles</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="stat-icon amber">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="stat-total-team-hours">0</div>
                    <div class="stat-desc">Team Hours</div>
                </div>
            </div>
        </div>

        <!-- Sort Controls -->
        <div class="colleagues-sort" style="margin-bottom: 20px;">
            <label>Sort by:</label>
            <button class="sort-btn active" data-sort="name" onclick="setSort('name')">Name</button>
            <button class="sort-btn" data-sort="hours" onclick="setSort('hours')">Hours ↓</button>
        </div>

        <!-- Colleagues Grid -->
        <div class="colleagues-grid" id="colleagues-container">
            <div class="colleagues-loading">
                <div class="spinner"></div>
                <p>Loading colleagues...</p>
            </div>
        </div>
    </div>

    <!-- Intern Hours Detail Modal -->
    <div class="intern-hours-modal" id="intern-hours-modal">
        <div class="intern-hours-modal-content">
            <div class="intern-modal-header">
                <div class="intern-info">
                    <div class="modal-avatar" id="intern-modal-avatar"></div>
                    <div>
                        <h3 id="intern-modal-name">Loading...</h3>
                        <div class="modal-subtitle" id="intern-modal-subtitle"></div>
                    </div>
                </div>
                <button class="intern-modal-close" onclick="closeInternModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="intern-modal-stats">
                <div class="intern-modal-stat">
                    <div class="value" id="intern-stat-total">—</div>
                    <div class="label">Total Hours</div>
                </div>
                <div class="intern-modal-stat">
                    <div class="value" id="intern-stat-days">—</div>
                    <div class="label">Days Logged</div>
                </div>
                <div class="intern-modal-stat">
                    <div class="value" id="intern-stat-avg">—</div>
                    <div class="label">Avg/Day</div>
                </div>
            </div>
            <div id="intern-modal-body">
                <!-- Calendar or private notice will render here -->
            </div>
        </div>
    </div>

    <script>
        const currentUserId = <?php echo $user_id; ?>;
        const apiBasePath = '../';
    </script>
    <script src="../assets/js/colleagues.js"></script>
