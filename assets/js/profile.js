/**
 * Profile Page Management
 */

document.addEventListener('DOMContentLoaded', function() {
    initTabs();
    initPrivacyToggle();
    initDarkModeToggle();
    initPasswordForm();
});

/**
 * Handle Tab Switching
 */
function initTabs() {
    const tabs = document.querySelectorAll('.settings-tab');
    const sections = document.querySelectorAll('.settings-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.target;

            // Update active tab
            tabs.forEach(t => t.classList.remove('active', 'bg-gray-900', 'text-white'));
            tabs.forEach(t => t.classList.add('text-gray-600', 'hover:bg-gray-50'));
            
            tab.classList.add('active', 'bg-gray-900', 'text-white');
            tab.classList.remove('text-gray-600', 'hover:bg-gray-50');

            // Show target section
            sections.forEach(s => s.classList.add('hidden'));
            document.getElementById('section-' + target).classList.remove('hidden');
        });
    });
}

/**
 * Initialize Privacy Toggle
 */
function initPrivacyToggle() {
    const toggle = document.getElementById('privacy-toggle');
    if (!toggle) return;

    toggle.addEventListener('change', function() {
        const isPublic = this.checked ? 1 : 0;
        const formData = new FormData();
        formData.append('is_public', isPublic);

        fetch(apiBasePath + 'api/profile_update.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Privacy settings updated');
            }
        });
    });
}

/**
 * Initialize Dark Mode Toggle
 */
function initDarkModeToggle() {
    const toggle = document.getElementById('darkmode-toggle');
    if (!toggle) return;

    // Sync toggle state with current theme
    const isDark = document.documentElement.classList.contains('dark');
    toggle.checked = isDark;

    toggle.addEventListener('change', function() {
        toggleDarkMode();
        showToast('Theme updated');
    });
}

/**
 * Initialize Password Form
 */
function initPasswordForm() {
    const form = document.getElementById('password-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = document.getElementById('new_password').value;
        const confirm = document.getElementById('confirm_password').value;
        const alertBox = document.getElementById('password-alert');

        if (password.length < 6) {
            showAlert(alertBox, 'Password must be at least 6 characters', 'error');
            return;
        }

        if (password !== confirm) {
            showAlert(alertBox, 'Passwords do not match', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('password', password);
        // Add other required fields from current user data (fetched from session or hidden inputs)
        formData.append('name', userName);
        formData.append('email', userEmail);
        formData.append('office_id', officeId);
        formData.append('organization_id', organizationId);

        fetch(apiBasePath + 'api/profile.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert(alertBox, 'Password updated successfully', 'success');
                form.reset();
            } else {
                showAlert(alertBox, data.error || 'Update failed', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showAlert(alertBox, 'Connection error', 'error');
        });
    });
}

/**
 * Helper: Show Alert
 */
function showAlert(box, message, type) {
    box.textContent = message;
    box.className = 'p-4 rounded-xl text-sm font-medium mb-6 ' + 
                    (type === 'success' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600');
    box.classList.remove('hidden');
    setTimeout(() => {
        box.classList.add('hidden');
    }, 5000);
}

/**
 * Helper: Show Toast
 */
function showToast(message) {
    const status = document.getElementById('save-status');
    if (status) {
        status.textContent = message.toUpperCase();
        status.style.opacity = '1';
        setTimeout(() => {
            status.style.opacity = '0';
        }, 2000);
    }
}
