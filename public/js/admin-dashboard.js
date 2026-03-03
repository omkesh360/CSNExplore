// Dynamic Admin Dashboard
(function() {
    'use strict';
    
    async function loadDashboardStats() {
        try {
            const token = localStorage.getItem('token');
            if (!token) return;
            
            const response = await fetch('/api/dashboard', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            if (!response.ok) throw new Error('Failed to load stats');
            
            const data = await response.json();
            
            // Update revenue
            if (data.revenue) {
                document.getElementById('stat-revenue')?.textContent = `₹${data.revenue.total.toLocaleString()}`;
                document.getElementById('stat-revenue-change')?.textContent = `${data.revenue.change > 0 ? '+' : ''}${data.revenue.change}%`;
            }
            
            // Update bookings
            if (data.bookings) {
                document.getElementById('stat-bookings')?.textContent = data.bookings.total;
                document.getElementById('stat-bookings-change')?.textContent = `${data.bookings.change > 0 ? '+' : ''}${data.bookings.change}%`;
            }
            
            // Update users
            if (data.users) {
                document.getElementById('stat-users')?.textContent = data.users.total;
                document.getElementById('stat-users-change')?.textContent = `${data.users.change > 0 ? '+' : ''}${data.users.change}%`;
            }
            
            // Update listings count
            if (data.listings) {
                const total = Object.values(data.listings).reduce((a, b) => a + b, 0);
                document.getElementById('stat-listings')?.textContent = total;
            }
            
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        }
    }
    
    // Load on page ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadDashboardStats);
    } else {
        loadDashboardStats();
    }
    
    // Refresh every 30 seconds
    setInterval(loadDashboardStats, 30000);
})();
