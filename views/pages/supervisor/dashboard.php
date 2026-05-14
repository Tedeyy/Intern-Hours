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
            <p class="text-gray-600 mb-6">Manage intern hours and absence requests from this dashboard.</p>
        </div>

        <div class="absence-requests-section mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Pending Absence Requests</h2>
            <div id="absence-requests-list" class="grid gap-4">
                <p class="text-gray-500 italic">Loading requests...</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadPendingAbsences();
        });

        function loadPendingAbsences() {
            fetch('../../../api/absences.php?pending=true')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('absence-requests-list');
                    if (data.success) {
                        if (data.absences.length === 0) {
                            list.innerHTML = '<div class="welcome-card"><p class="text-gray-500">No pending absence requests.</p></div>';
                            return;
                        }

                        list.innerHTML = '';
                        data.absences.forEach(abs => {
                            const card = document.createElement('div');
                            card.className = 'welcome-card flex flex-col md:flex-row justify-between items-center text-left';
                            card.style.textAlign = 'left';
                            card.innerHTML = `
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg">${abs.intern_name}</h3>
                                    <p class="text-gray-600"><strong>Date:</strong> ${formatDate(abs.date)}</p>
                                    <p class="text-gray-700 mt-2"><strong>Reason:</strong> ${abs.reason || 'No reason provided'}</p>
                                </div>
                                <div class="flex gap-2 mt-4 md:mt-0">
                                    <button onclick="handleAbsence(${abs.absences_id}, 'approve')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-bold">Approve</button>
                                    <button onclick="handleAbsence(${abs.absences_id}, 'reject')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-bold">Reject</button>
                                </div>
                            `;
                            list.appendChild(card);
                        });
                    } else {
                        list.innerHTML = '<p class="text-red-500">Error loading requests.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('absence-requests-list').innerHTML = '<p class="text-red-500">Failed to connect to server.</p>';
                });
        }

        function handleAbsence(id, action) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('id', id);

            fetch('../../../api/absences.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadPendingAbsences();
                } else {
                    alert(data.error || 'Error updating request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Connection error');
            });
        }

        function formatDate(dateStr) {
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const [y, m, d] = dateStr.split('-');
            return `${months[parseInt(m) - 1]} ${parseInt(d)}, ${y}`;
        }
    </script>

    <?php require_once '../../components/footer.php'; ?>
</body>
</html>
