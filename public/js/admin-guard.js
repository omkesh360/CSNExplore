// Admin Authentication Guard
// Validates token with the server before granting access to admin pages
(function () {
    'use strict';

    async function checkAdminAccess() {
        const userStr = localStorage.getItem('user');
        const token = localStorage.getItem('token');

        // Quick local check first
        if (!userStr || !token) {
            redirectToLogin('Please log in to access the admin panel.');
            return;
        }

        let user;
        try {
            user = JSON.parse(userStr);
        } catch (e) {
            redirectToLogin('Session corrupted. Please log in again.');
            return;
        }

        if (user.role !== 'admin' && user.role !== 'Admin') {
            redirectToLogin('Access denied. Admin privileges required.');
            return;
        }

        // Verify token with server (catches expired tokens)
        try {
            const response = await fetch('/api/auth/verify', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) {
                // Token expired or invalid
                localStorage.removeItem('user');
                localStorage.removeItem('token');
                redirectToLogin('Your session has expired. Please log in again.');
                return;
            }

            const data = await response.json();
            const userRole = (data.user && data.user.role) ? data.user.role : '';
            if (userRole !== 'admin' && userRole !== 'Admin') {
                redirectToLogin('Access denied. Admin privileges required.');
            }

        } catch (err) {
            // Network error — allow access if local data looks valid (offline mode)
            console.warn('Could not verify token with server, using local session.');
        }
    }

    function redirectToLogin(message) {
        // Store message to show on login page
        sessionStorage.setItem('auth_redirect_msg', message);
        window.location.href = 'login.html';
    }

    // Admin Logout
    window.adminLogout = function () {
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        window.location.href = 'login.html';
    };

    // Setup logout button & admin profile info
    document.addEventListener('DOMContentLoaded', () => {
        const logoutBtn = document.getElementById('admin-logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.adminLogout();
            });
        }

        // Populate admin name + avatar in sidebar footer
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user && user.name) {
            const nameEl = document.getElementById('admin-name');
            const avatarEl = document.getElementById('admin-avatar');
            if (nameEl) nameEl.textContent = user.name;
            if (avatarEl) avatarEl.textContent = user.name.charAt(0).toUpperCase();
        }
    });

    // Run guard
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAdminAccess);
    } else {
        checkAdminAccess();
    }
})();
