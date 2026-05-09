<?php
$admin_page  = 'regenerate';
$admin_title = 'Regenerate Pages | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-800">HTML Cache Generator</h2>
            <p class="text-xs md:text-sm text-slate-500 font-medium">Manually regenerate static pages for listings and blogs</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-card p-6 md:p-8 flex flex-col items-center text-center max-w-3xl mx-auto mt-8">
        <div class="w-20 h-20 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl">autorenew</span>
        </div>
        
        <h3 class="text-lg font-bold text-slate-900 mb-2">Rebuild Static Pages</h3>
        <p class="text-sm text-slate-600 mb-8 max-w-md">
            If you have made manual changes to the database or if pages are not reflecting the latest prices, details, or blog content, click the button below to force a full regeneration of all static HTML files.
        </p>

        <button id="regen-btn" onclick="triggerRegeneration()" class="bg-primary text-white font-bold py-3 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]" id="regen-icon">build</span>
            <span id="regen-text">Start Full Regeneration</span>
        </button>

        <div id="regen-status" class="mt-6 w-full max-w-md hidden">
            <div class="flex justify-between text-xs font-bold mb-2">
                <span id="regen-msg" class="text-slate-600">Processing...</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div id="regen-progress" class="bg-primary h-2 rounded-full transition-all duration-300 w-0"></div>
            </div>
        </div>
        
        <div id="regen-success" class="mt-6 p-4 bg-green-50 text-green-700 border border-green-200 rounded-xl text-sm font-semibold flex items-center gap-2 hidden">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            All pages regenerated successfully!
        </div>
        <div id="regen-error" class="mt-6 p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl text-sm font-semibold flex items-center gap-2 hidden">
            <span class="material-symbols-outlined text-lg">error</span>
            <span id="regen-error-text">Failed to regenerate pages.</span>
        </div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
async function triggerRegeneration() {
    var btn = document.getElementById('regen-btn');
    var icon = document.getElementById('regen-icon');
    var text = document.getElementById('regen-text');
    var status = document.getElementById('regen-status');
    var progress = document.getElementById('regen-progress');
    var msg = document.getElementById('regen-msg');
    var success = document.getElementById('regen-success');
    var errorBox = document.getElementById('regen-error');
    
    // UI Reset
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    icon.textContent = 'autorenew';
    icon.classList.add('animate-spin');
    text.textContent = 'Regenerating...';
    
    status.classList.remove('hidden');
    success.classList.add('hidden');
    errorBox.classList.add('hidden');
    
    // Fake progress animation for UX
    progress.style.width = '10%';
    msg.textContent = 'Connecting to generation service...';
    
    setTimeout(() => { progress.style.width = '40%'; msg.textContent = 'Building listing pages...'; }, 800);
    setTimeout(() => { progress.style.width = '70%'; msg.textContent = 'Building blog pages...'; }, 1800);
    
    try {
        // Call the API endpoint
        const response = await fetch('../php/api/generate_html.php?secret=csnexplore_seed&format=json');
        
        progress.style.width = '95%';
        msg.textContent = 'Finalizing...';
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        setTimeout(() => {
            progress.style.width = '100%';
            msg.textContent = 'Done!';
            
            setTimeout(() => {
                status.classList.add('hidden');
                success.classList.remove('hidden');
                
                // Reset button
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                icon.textContent = 'build';
                icon.classList.remove('animate-spin');
                text.textContent = 'Regenerate Again';
            }, 500);
        }, 500);
        
    } catch (e) {
        progress.style.width = '100%';
        progress.classList.remove('bg-primary');
        progress.classList.add('bg-red-500');
        msg.textContent = 'Error occurred';
        msg.classList.replace('text-slate-600', 'text-red-600');
        
        setTimeout(() => {
            status.classList.add('hidden');
            document.getElementById('regen-error-text').textContent = 'Error: ' + e.message;
            errorBox.classList.remove('hidden');
            
            // Reset button
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            icon.textContent = 'build';
            icon.classList.remove('animate-spin');
            text.textContent = 'Retry Regeneration';
        }, 1000);
    }
}
</script>
JS;
require 'admin-footer.php';
?>
