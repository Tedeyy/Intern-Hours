<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../../feed.php?page=login");
    exit;
}

require_once '../../../config.php';
$base_url = "../../../";
require_once '../../components/header.php';

// Fetch offices and organizations for edit modal
$offices = $pdo->query("SELECT * FROM office ORDER BY office_name")->fetchAll();
$orgs = $pdo->query("SELECT * FROM organization ORDER BY organization_name")->fetchAll();
?>
</head>
<body class="bg-gray-50">
    <?php require_once '../../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
                <p class="text-gray-500">Manage all registered interns and administrators</p>
            </div>
            <div class="flex gap-2">
                <input type="text" id="user-search" placeholder="Search users..." 
                    class="px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-gray-900 outline-none transition w-64">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">User</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">Role</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">Office</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">Organization</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="divide-y divide-gray-50">
                        <!-- Users will be loaded here -->
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[100] p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="edit-user-form" class="space-y-4">
                <input type="hidden" name="id" id="edit-id">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit-email" required class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <select name="role" id="edit-role" required class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900">
                        <option value="Intern">Intern</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Office</label>
                        <select name="office_id" id="edit-office_id" required class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900">
                            <?php foreach ($offices as $o): ?>
                                <option value="<?php echo $o['id']; ?>"><?php echo htmlspecialchars($o['office_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Organization</label>
                        <select name="organization_id" id="edit-organization_id" required class="w-full px-4 py-2 rounded-lg border border-gray-200 outline-none focus:ring-2 focus:ring-gray-900">
                            <?php foreach ($orgs as $org): ?>
                                <option value="<?php echo $org['id']; ?>"><?php echo htmlspecialchars($org['organization_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" onclick="closeModal()" class="px-6 py-2 rounded-lg border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-8 py-2 rounded-lg bg-gray-900 text-white font-bold hover:bg-black transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let allUsers = [];

        document.addEventListener('DOMContentLoaded', loadUsers);

        function loadUsers() {
            fetch('../../../api/users.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allUsers = data.users;
                        renderUsers(allUsers);
                    } else {
                        alert('Error loading users: ' + data.error);
                    }
                });
        }

        function renderUsers(users) {
            const body = document.getElementById('users-table-body');
            if (users.length === 0) {
                body.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">No users found.</td></tr>';
                return;
            }

            body.innerHTML = '';
            users.forEach(user => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition';
                row.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center font-bold">
                                ${user.name.charAt(0)}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">${user.name}</div>
                                <div class="text-xs text-gray-500">${user.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-md text-xs font-bold ${user.role === 'Admin' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600'}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">${user.office_name || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${user.organization_name || 'N/A'}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="openEditModal(${JSON.stringify(user).replace(/"/g, '&quot;')})" class="text-gray-400 hover:text-gray-900 transition p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button onclick="deleteUser(${user.id})" class="text-gray-400 hover:text-red-600 transition p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                `;
                body.appendChild(row);
            });
        }

        document.getElementById('user-search').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const filtered = allUsers.filter(u => 
                u.name.toLowerCase().includes(query) || 
                u.email.toLowerCase().includes(query) ||
                (u.office_name && u.office_name.toLowerCase().includes(query)) ||
                (u.organization_name && u.organization_name.toLowerCase().includes(query))
            );
            renderUsers(filtered);
        });

        function openEditModal(user) {
            document.getElementById('edit-id').value = user.id;
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-email').value = user.email;
            document.getElementById('edit-role').value = user.role;
            document.getElementById('edit-office_id').value = user.office_id;
            document.getElementById('edit-organization_id').value = user.organization_id;
            
            const modal = document.getElementById('edit-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('edit-user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../../../api/users.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    loadUsers();
                } else {
                    alert(data.error || 'Error updating user');
                }
            });
        });

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all their logged hours.')) {
                fetch('../../../api/users.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUsers();
                    } else {
                        alert(data.error || 'Error deleting user');
                    }
                });
            }
        }
    </script>

    <?php require_once '../../components/footer.php'; ?>
</body>
</html>
