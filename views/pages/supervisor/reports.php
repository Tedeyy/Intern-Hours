<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../../feed.php?page=login");
    exit;
}

require_once '../../../config.php';
$base_url = "../../../";
require_once '../../components/header.php';
?>
</head>
<body class="bg-gray-50">
    <?php require_once '../../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Administrative Reports</h1>
            <p class="text-gray-500">Summary statistics and performance metrics across the system</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Organization Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Hours by Organization
                </h2>
                <div id="org-report" class="space-y-4">
                    <!-- Progress bars will be here -->
                    <p class="italic text-gray-500">Loading data...</p>
                </div>
            </div>

            <!-- Office Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Hours by Office
                </h2>
                <div id="office-report" class="space-y-4">
                    <!-- Progress bars will be here -->
                    <p class="italic text-gray-500">Loading data...</p>
                </div>
            </div>

            <!-- Intern Rankings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Top Performers (Total Hours)
                </h2>
                <div id="intern-report" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards will be here -->
                    <p class="italic text-gray-500 col-span-full">Loading data...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadReports);

        function loadReports() {
            fetch('../../../api/reports.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderOrgReport(data.org_hours);
                        renderOfficeReport(data.office_hours);
                        renderInternReport(data.intern_hours);
                    } else {
                        alert('Error loading reports: ' + data.error);
                    }
                });
        }

        function renderOrgReport(data) {
            const container = document.getElementById('org-report');
            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 italic">No data available.</p>';
                return;
            }

            const max = Math.max(...data.map(d => parseFloat(d.total_hours)));
            container.innerHTML = data.map(d => {
                const percentage = (parseFloat(d.total_hours) / max) * 100;
                return `
                    <div>
                        <div class="flex justify-between text-sm font-bold text-gray-700 mb-1">
                            <span>${d.name}</span>
                            <span>${parseFloat(d.total_hours).toFixed(1)} hrs</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderOfficeReport(data) {
            const container = document.getElementById('office-report');
            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 italic">No data available.</p>';
                return;
            }

            const max = Math.max(...data.map(d => parseFloat(d.total_hours)));
            container.innerHTML = data.map(d => {
                const percentage = (parseFloat(d.total_hours) / max) * 100;
                return `
                    <div>
                        <div class="flex justify-between text-sm font-bold text-gray-700 mb-1">
                            <span>${d.name}</span>
                            <span>${parseFloat(d.total_hours).toFixed(1)} hrs</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-1000" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderInternReport(data) {
            const container = document.getElementById('intern-report');
            if (data.length === 0) {
                container.innerHTML = '<p class="text-gray-500 italic">No data available.</p>';
                return;
            }

            container.innerHTML = data.map((d, i) => `
                <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4 border border-transparent hover:border-green-100 hover:bg-green-50 transition group">
                    <div class="text-2xl font-black ${i < 3 ? 'text-green-600' : 'text-gray-300'}">#${i + 1}</div>
                    <div>
                        <div class="font-bold text-gray-900 group-hover:text-green-900">${d.name}</div>
                        <div class="text-sm text-gray-500">${parseFloat(d.total_hours).toFixed(1)} total hours</div>
                    </div>
                </div>
            `).join('');
        }
    </script>

    <?php require_once '../../components/footer.php'; ?>
</body>
</html>
