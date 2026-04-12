<?php
$admin_page  = 'regenerate-animations';
$admin_title = 'Regenerate Pages with Animations | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Regenerate Pages with Animations</h2>
            <p class="text-xs text-slate-500 font-medium">Add smooth animations to all listing and blog pages</p>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex gap-4">
            <span class="material-symbols-outlined text-blue-600 text-2xl">info</span>
            <div>
                <h3 class="font-bold text-blue-900 mb-2">What This Does</h3>
                <p class="text-sm text-blue-800 mb-3">
                    This will regenerate all listing detail pages and blog pages with beautiful scroll-based animations.
                </p>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Fade-in animations when scrolling</li>
                    <li>Hover effects on cards and images</li>
                    <li>Image zoom effects</li>
                    <li>Stagger delays for sequential animations</li>
                    <li>Mobile-optimized performance</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Action Card -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-slate-800 mb-1">Regenerate All Pages</h3>
                <p class="text-sm text-slate-600">This will update all listing detail and blog HTML files</p>
            </div>
            <button id="regenerate-btn" class="px-6 py-3 rounded-lg bg-primary text-white font-bold hover:bg-orange-600 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">auto_awesome</span>
                Start Regeneration
            </button>
        </div>

        <!-- Progress -->
        <div id="progress-container" class="hidden space-y-4">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="font-semibold text-slate-700">Processing...</span>
                    <span id="progress-text" class="text-slate-600">Starting...</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-primary h-2 rounded-full transition-all" style="width: 0%"></div>
                </div>
            </div>
            <div id="status-log" class="bg-slate-50 rounded-lg p-4 max-h-96 overflow-y-auto text-sm font-mono text-slate-700 space-y-1">
                <div>Initializing...</div>
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
            <div class="flex gap-3">
                <a href="../" target="_blank" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition font-bold text-sm">
                    View Homepage
                </a>
                <a href="../blogs" target="_blank" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition font-bold text-sm">
                    View Blogs
                </a>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="admin-card p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-2xl">animation</span>
                <h4 class="font-bold text-slate-800">Scroll Animations</h4>
            </div>
            <p class="text-sm text-slate-600">Elements fade in smoothly as users scroll down the page</p>
        </div>

        <div class="admin-card p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-2xl">touch_app</span>
                <h4 class="font-bold text-slate-800">Hover Effects</h4>
            </div>
            <p class="text-sm text-slate-600">Cards lift and images zoom on hover for better interactivity</p>
        </div>

        <div class="admin-card p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-2xl">phone_iphone</span>
                <h4 class="font-bold text-slate-800">Mobile Optimized</h4>
            </div>
            <p class="text-sm text-slate-600">Faster animations and reduced motion on mobile devices</p>
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
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    btn.disabled = true;
    progressContainer.classList.remove('hidden');
    resultsContainer.classList.add('hidden');
    statusLog.innerHTML = '<div>🚀 Starting regeneration...</div>';
    
    try {
        // Simulate progress (since we can't get real-time updates from PHP)
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';
        }, 500);
        
        statusLog.innerHTML += '<div>📄 Regenerating listing detail pages...</div>';
        
        // Call the regeneration script
        const response = await fetch('../regenerate-all-with-animations.php', {
            method: 'GET'
        });
        
        clearInterval(progressInterval);
        
        if (response.ok) {
            const text = await response.text();
            
            // Update progress to 100%
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            
            // Parse the output
            statusLog.innerHTML += '<div>📝 Regenerating blog pages...</div>';
            statusLog.innerHTML += '<div>✅ All pages regenerated successfully!</div>';
            
            // Show results
            setTimeout(() => {
                progressContainer.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
                
                // Extract numbers from response if possible
                const listingMatch = text.match(/(\d+)\s+listing detail pages/i);
                const blogMatch = text.match(/(\d+)\s+blog pages/i);
                
                let summary = '<div><strong>Regeneration Complete!</strong></div>';
                if (listingMatch) {
                    summary += `<div>• Listing Detail Pages: ${listingMatch[1]} pages</div>`;
                }
                if (blogMatch) {
                    summary += `<div>• Blog Pages: ${blogMatch[1]} pages</div>`;
                }
                summary += '<div class="mt-2">All pages now include smooth animations!</div>';
                
                document.getElementById('results-summary').innerHTML = summary;
            }, 500);
        } else {
            throw new Error('Regeneration failed');
        }
    } catch (error) {
        console.error('Error:', error);
        statusLog.innerHTML += `<div class="text-red-600">✗ Error: ${error.message}</div>`;
        progressBar.style.width = '0%';
    } finally {
        btn.disabled = false;
    }
});
</script>

<?php require 'admin-footer.php'; ?>
