<?php
$admin_page = 'caching';
$admin_title = 'Caching | CSNExplore';
require_once 'admin-header.php';
?>

<div class="max-w-4xl mx-auto space-y-6 animate-slide-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Caching Settings</h2>
            <p class="text-sm text-slate-500 mt-1">Manage caching for various modules to optimize performance.</p>
        </div>
    </div>

    <!-- Cache Controls -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Global Caching</h3>
                <p class="text-sm text-slate-500 mt-1">Enable or disable caching across the entire application.</p>
                <div class="mt-2 text-xs text-slate-400">
                    Applies to: Dashboard, Database Listings, Bookings, Trip Planner, Blogs, Gallery, Users, Content Editor, Activity Logs
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="cachingToggle" class="sr-only peer">
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                <span class="ml-3 text-sm font-medium text-slate-700" id="cachingStatusText">Disabled</span>
            </label>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const toggle = document.getElementById('cachingToggle');
    const statusText = document.getElementById('cachingStatusText');

    // Load initial status
    try {
        const res = await api('../php/api/settings.php');
        if (res && res.success && res.settings && res.settings.features && res.settings.features.caching) {
            toggle.checked = res.settings.features.caching.enabled === true;
            statusText.textContent = toggle.checked ? 'Enabled' : 'Disabled';
        }
    } catch (e) {
        console.error('Failed to load caching status', e);
    }

    // Toggle logic
    toggle.addEventListener('change', async (e) => {
        const enabled = e.target.checked;
        statusText.textContent = enabled ? 'Enabled' : 'Disabled';
        
        try {
            const res = await api('../php/api/settings.php', {
                method: 'POST',
                body: JSON.stringify({
                    features: {
                        caching: {
                            enabled: enabled
                        }
                    }
                })
            });
            
            if (res && res.success) {
                showAdminToast(enabled ? 'Caching enabled successfully' : 'Caching disabled successfully');
            } else {
                throw new Error('Failed to update settings');
            }
        } catch (err) {
            e.target.checked = !enabled; // revert
            statusText.textContent = !enabled ? 'Enabled' : 'Disabled';
            showAdminToast('Error updating caching settings', 'error');
        }
    });
});
</script>

<?php require_once 'admin-footer.php'; ?>
