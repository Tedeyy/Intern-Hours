/**
 * System-wide Theme Management
 */

(function() {
    // Check if we are on the landing page (index.php or /)
    const isLandingPage = window.location.pathname.endsWith('index.php') || 
                         window.location.pathname.endsWith('index.html') || 
                         window.location.pathname.endsWith('/') || 
                         window.location.pathname === '';

    if (isLandingPage) {
        // Force light mode on landing page
        document.documentElement.classList.remove('dark');
        document.body && document.body.classList.remove('dark-mode');
        return;
    }

    // Apply theme as soon as possible to prevent flicker
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
        // We apply to body when DOM is ready or if body already exists
        if (document.body) {
            document.body.classList.add('dark-mode');
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('dark-mode');
            });
        }
    }
})();

/**
 * Toggle theme between light and dark
 */
function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle('dark');
    document.body.classList.toggle('dark-mode', isDark);
    
    const theme = isDark ? 'dark' : 'light';
    localStorage.setItem('theme', theme);

    // Sync with backend if user is logged in
    fetch(apiBasePath + 'api/profile_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'is_darkmode=' + (isDark ? '1' : '0')
    })
    .catch(err => console.error('Failed to sync theme with backend:', err));
    
    return isDark;
}
