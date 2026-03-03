/**
 * Admin Profile Loader
 * Dynamically loads admin user info into sidebar footer
 */
(function() {
    'use strict';
    
    function loadAdminProfile() {
        try {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            
            if (!user || !user.name) {
                console.warn('No user data found in localStorage');
                return;
            }
            
            // Update admin name
            const adminNameEl = document.getElementById('admin-name');
            if (adminNameEl) {
                adminNameEl.textContent = user.name;
            }
            
            // Update admin avatar with first letter
            const adminAvatarEl = document.getElementById('admin-avatar');
            if (adminAvatarEl && user.name) {
                adminAvatarEl.textContent = user.name.charAt(0).toUpperCase();
            }
            
            // Update admin email if element exists
            const adminEmailEl = document.getElementById('admin-email');
            if (adminEmailEl && user.email) {
                adminEmailEl.textContent = user.email;
            }
            
        } catch (error) {
            console.error('Error loading admin profile:', error);
        }
    }
    
    // Load profile when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAdminProfile);
    } else {
        loadAdminProfile();
    }
})();
