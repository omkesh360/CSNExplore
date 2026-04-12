<?php
$admin_page  = 'regenerate';
$admin_title = 'Regenerate Listing Pages | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Regenerate Listing Pages</h2>
            <p class="text-xs text-slate-500 font-medium">Add default maps to all listings</p>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex gap-4">
            <span class="material-symbols-outlined text-blue-600 text-2xl">info</span>
            <div>
                <h3 class="font-bold text-blue-900 mb-2">What This Does</h3>
                <p class="text-sm text-blue-800 mb-3">
                    This will add the default Aurangabad map to all listings that don't have a custom map yet. 
                    Listings with existing maps will not be affected.
                </p>
                <p class="text-sm text-blue-700">
                    After regeneration, you can customize individual maps from the Map Embeds panel.
                </p>
            </div>
        </div>
    </div>

    <!-- Action Card -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-slate-800 mb-1">Regenerate All Listings</h3>
                <p class="text-sm text-slate-600">Rebuild all listing detail HTML pages with latest data</p>
            </div>
            <button id="regenerate-btn" class="px-6 py-3 rounded-lg bg-primary text-white font-bold hover:bg-orange-600 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">refresh</span>
                Regenerate Now
            </button>
        </div>

        <!-- Progress -->
        <div id="progress-container" class="hidden space-y-4">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="font-semibold text-slate-700">Processing...</span>
                    <span id="progress-text" class="text-slate-600">0%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-primary h-2 rounded-full transition-all" style="width: 0%"></div>
                </div>
            </div>
            <div id="status-log" class="bg-slate-50 rounded-lg p-4 max-h-64 overflow-y-auto text-sm font-mono text-slate-700 space-y-1">
                <div>Starting regeneration...</div>
            </div>
        </div>

        <!-- Results -->
        <div id="results-container" class="hidden space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <h4 class="font-bold text-green-900">Regeneration Complete!</h4>
                </div>
                <div id="results-summary" class="text-sm text-green-800 space-y-1"></div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="admin-card p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-2xl">map</span>
                <h4 class="font-bold text-slate-800">Maps Added</h4>
            </div>
            <p class="text-2xl font-bold text-primary mb-1" id="maps-count">0</p>
            <p class="text-xs text-slate-600">Default Aurangabad map</p>
        </div>

        <div class="admin-card p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-2xl">category</span>
                <h4 class="font-bold text-slate-800">Categories</h4>
            </div>
            <p class="text-2xl font-bold text-primary mb-1">6</p>
            <p class="text-xs text-slate-600">Hotels, Cars, Bikes, Dining, Attractions, Buses</p>
        </div>

        <div class="admin-card p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-2xl">visibility</span>
                <h4 class="font-bold text-slate-800">Similar Listings</h4>
            </div>
            <p class="text-2xl font-bold text-primary mb-1">4</p>
            <p class="text-xs text-slate-600">Random listings per page</p>
        </div>
    </div>
</div>

<style>
.admin-card { background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
</style>

<script>
document.getElementById('regenerate-btn').addEventListener('click', async () => {
    const btn = document.getElementById('regenerate-btn');
    const progressContainer = document.getElementById('progress-container');
    const resultsContainer = document.getElementById('results-container');
    const statusLog = document.getElementById('status-log');
    
    btn.disabled = true;
    btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Running...';
    progressContainer.classList.remove('hidden');
    resultsContainer.classList.add('hidden');
    statusLog.innerHTML = '<div>Starting regeneration...</div>';
    
    try {
        const response = await fetch('../php/api/run-regenerate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + (localStorage.getItem('csn_admin_token') || '')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('progress-bar').style.width = '100%';
            document.getElementById('progress-text').textContent = '100%';
            
            result.log?.forEach(line => {
                statusLog.innerHTML += `<div>${line}</div>`;
            });
            statusLog.innerHTML += '<div class="text-green-600 font-bold">✓ All pages regenerated!</div>';
            statusLog.scrollTop = statusLog.scrollHeight;
            
            setTimeout(() => {
                progressContainer.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
                let summary = `<div><strong>Total Pages:</strong> ${result.total} listing pages generated</div>`;
                Object.entries(result.breakdown || {}).forEach(([cat, count]) => {
                    summary += `<div>• ${cat.charAt(0).toUpperCase() + cat.slice(1)}: ${count} pages</div>`;
                });
                document.getElementById('results-summary').innerHTML = summary;
                document.getElementById('maps-count').textContent = result.total;
            }, 800);
        } else {
            statusLog.innerHTML += `<div class="text-red-600">✗ Error: ${result.error}</div>`;
        }
    } catch (error) {
        statusLog.innerHTML += `<div class="text-red-600">✗ Error: ${error.message}</div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">refresh</span> Regenerate Now';
    }
});
</script>

<?php require 'admin-footer.php'; ?>
