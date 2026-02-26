// Authentication and Profile Management
(function () {
    'use strict';

    // Wait for DOM to be ready
    function initAuth() {
        const authButtons = document.getElementById('auth-buttons');
        const userProfile = document.getElementById('user-profile');
        const logoutBtn = document.getElementById('logout-btn');

        // Check authentication status
        function checkAuth() {
            const user = JSON.parse(localStorage.getItem('user') || 'null');
            const token = localStorage.getItem('token');

            if (user && token) {
                // User is logged in
                if (authButtons) authButtons.classList.add('hidden');
                if (userProfile) {
                    userProfile.classList.remove('hidden');

                    // Populate display name
                    const displayNameEl = document.getElementById('user-display-name');
                    if (displayNameEl && user.name) {
                        displayNameEl.textContent = user.name.split(' ')[0]; // First name only
                    }

                    // Populate avatar initial
                    const avatarIcon = document.getElementById('user-avatar-icon');
                    const avatarCircle = document.getElementById('user-avatar-circle');
                    if (avatarIcon && user.name) {
                        avatarIcon.style.display = 'none'; // hide the icon
                        // Set initial letter as text
                        const initial = user.name.charAt(0).toUpperCase();
                        const span = document.createElement('span');
                        span.textContent = initial;
                        span.className = 'text-white font-bold text-base';
                        if (avatarCircle) avatarCircle.appendChild(span);
                    }
                }
            } else {
                // User is not logged in
                if (authButtons) authButtons.classList.remove('hidden');
                if (userProfile) userProfile.classList.add('hidden');
            }
        }

        // Logout functionality
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function (e) {
                e.preventDefault();

                // Clear all authentication data
                localStorage.removeItem('user');
                localStorage.removeItem('token');

                // Show notification
                if (window.utils && window.utils.showNotification) {
                    window.utils.showNotification('Logged out successfully', 'success');
                }

                // Redirect to homepage
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 500);
            });
        }

        // Initialize auth state
        checkAuth();
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAuth);
    } else {
        initAuth();
    }
})();
