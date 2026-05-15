<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../../feed.php?page=login");
    exit;
}

require_once '../../../config.php';
$base_url = "../../../";
require_once '../../components/header.php';

// Fetch organizations and offices for filters
$orgs = $pdo->query("SELECT * FROM organization ORDER BY organization_name")->fetchAll();
$offices = $pdo->query("SELECT * FROM office ORDER BY office_name")->fetchAll();
?>
</head>
<body class="bg-gray-50">
    <?php require_once '../../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">All Internship Hours</h1>
            <p class="text-gray-500">View and filter hours logged by all interns</p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <form id="filter-form" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Search Intern</label>
                    <input type="text" name="search" id="search" placeholder="Name..." 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900 transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Organization</label>
                    <select name="organization_id" id="organization_id" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900 transition text-sm">
                        <option value="">All Organizations</option>
                        <?php foreach ($orgs as $org): ?>
                            <option value="<?php echo $org['id']; ?>"><?php echo htmlspecialchars($org['organization_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Office</label>
                    <select name="office_id" id="office_id" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900 transition text-sm">
                        <option value="">All Offices</option>
                        <?php foreach ($offices as $o): ?>
                            <option value="<?php echo $o['id']; ?>"><?php echo htmlspecialchars($o['office_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">From Date</label>
                    <input type="date" name="from_date" id="from_date" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900 transition text-sm">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">To Date</label>
                        <input type="date" name="to_date" id="to_date" 
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900 transition text-sm">
                    </div>
                    <button type="submit" class="bg-gray-900 text-white p-2 rounded-lg hover:bg-black transition self-end">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <div id="hours-container" class="space-y-8">
            <!-- Data will be loaded here, grouped by organization -->
            <div class="text-center py-20 text-gray-500 italic">Loading hours data...</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadHours();
            
            document.getElementById('filter-form').addEventListener('submit', (e) => {
                e.preventDefault();
                loadHours();
            });
        });

        function loadHours() {
            const container = document.getElementById('hours-container');
            const search = document.getElementById('search').value;
            const orgId = document.getElementById('organization_id').value;
            const officeId = document.getElementById('office_id').value;
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;

            const url = `../../../api/all-hours.php?search=${search}&organization_id=${orgId}&office_id=${officeId}&from_date=${fromDate}&to_date=${toDate}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderHours(data.hours);
                    } else {
                        container.innerHTML = `<div class="text-red-500 text-center py-20 font-bold">${data.error}</div>`;
                    }
                });
        }

        function renderHours(hours) {
            const container = document.getElementById('hours-container');
            if (hours.length === 0) {
                container.innerHTML = '<div class="bg-white rounded-2xl p-20 text-center text-gray-500 italic border border-gray-100 shadow-sm">No records found matching your filters.</div>';
                return;
            }

            // Group by organization
            const groups = {};
            hours.forEach(row => {
                const org = row.organization_name || 'Uncategorized';
                if (!groups[org]) groups[org] = [];
                groups[org].push(row);
            });

            container.innerHTML = '';
            for (const org in groups) {
                const orgSection = document.createElement('div');
                orgSection.className = 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden';
                
                let totalOrgHours = groups[org].reduce((sum, h) => sum + parseFloat(h.hours), 0);

                orgSection.innerHTML = `
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900">${org}</h2>
                        <span class="text-sm font-bold text-gray-500">Total: ${totalOrgHours.toFixed(1)} hrs</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Intern</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Office</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Hours</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                ${groups[org].map(h => `
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">${h.intern_name}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">${formatDate(h.date)}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${h.office_name}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">${parseFloat(h.hours).toFixed(1)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                container.appendChild(orgSection);
            }
        }

        function formatDate(dateStr) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const [y, m, d] = dateStr.split('-');
            return `${months[parseInt(m) - 1]} ${parseInt(d)}, ${y}`;
        }
    </script>

    <?php require_once '../../components/footer.php'; ?>
</body>
</html>
