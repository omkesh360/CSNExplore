<?php
$admin_page  = 'regenerate';
$admin_title = 'Regenerate Pages | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6 animate-slide-in">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Regenerate Pages</h2>
            <p class="text-xs text-slate-500 font-medium">Rebuild static HTML files and update the sitemap</p>
        </div>
        <!-- Last run info -->
        <div id="last-run-badge" class="hidden flex items-center gap-2 text-xs text-slate-400 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg">
            <span class="material-symbols-outlined text-sm">schedule</span>
            <span id="last-run-text">Never run</span>
        </div>
    </div>

    <!-- ── Two action cards side by side ─────────────────────────────────── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Card 1: Rebuild Static Pages -->
        <div class="admin-card p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">autorenew</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Rebuild Static Pages</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Regenerate all listing &amp; blog HTML files</p>
                </div>
            </div>

            <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                If you have made manual changes to the database or if pages are not reflecting the latest prices, details, or blog content, click the button below to force a full regeneration of all static HTML files.
            </p>

            <!-- Stats row -->
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                    <div class="text-xl font-black text-slate-800" id="html-listing-count">--</div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Listing Pages</div>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                    <div class="text-xl font-black text-slate-800" id="html-blog-count">--</div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Blog Pages</div>
                </div>
            </div>

            <!-- Progress -->
            <div id="html-progress-wrap" class="hidden mb-4">
                <div class="flex justify-between text-xs font-semibold mb-1.5">
                    <span id="html-progress-msg" class="text-slate-600">Starting...</span>
                    <span id="html-progress-pct" class="text-primary">0%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div id="html-progress-bar" class="h-full bg-primary rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>

            <!-- Result -->
            <div id="html-result" class="hidden mb-4 p-3 rounded-xl text-xs font-semibold flex items-start gap-2"></div>

            <div class="mt-auto">
                <button id="html-btn" onclick="runHTMLRegen()"
                        class="w-full bg-primary text-white font-bold py-2.5 px-6 rounded-xl hover:bg-orange-600 transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" id="html-btn-icon">build</span>
                    <span id="html-btn-text">Start Full Regeneration</span>
                </button>
            </div>
        </div>

        <!-- Card 2: Update Sitemap -->
        <div class="admin-card p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">travel_explore</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Update Sitemap</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Rebuild sitemap.xml &amp; ping search engines</p>
                </div>
            </div>

            <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                Regenerates all sitemap XML files (static pages, listings, blogs) and writes them to the website root. Also pings Google and Bing to notify them of the update.
            </p>

            <!-- Sitemap stats -->
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                    <div class="text-xl font-black text-slate-800" id="sitemap-url-count">--</div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Total URLs</div>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                    <div class="text-xl font-black text-slate-800" id="sitemap-file-count">1</div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Sitemap File</div>
                </div>
            </div>

            <!-- Progress -->
            <div id="sitemap-progress-wrap" class="hidden mb-4">
                <div class="flex justify-between text-xs font-semibold mb-1.5">
                    <span id="sitemap-progress-msg" class="text-slate-600">Starting...</span>
                    <span id="sitemap-progress-pct" class="text-blue-600">0%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div id="sitemap-progress-bar" class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>

            <!-- Result -->
            <div id="sitemap-result" class="hidden mb-4 p-3 rounded-xl text-xs font-semibold flex items-start gap-2"></div>

            <!-- Sitemap files list -->
            <div id="sitemap-files-list" class="hidden mb-4 space-y-1"></div>

            <div class="mt-auto">
                <button id="sitemap-btn" onclick="runSitemapRegen()"
                        class="w-full bg-blue-600 text-white font-bold py-2.5 px-6 rounded-xl hover:bg-blue-700 transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" id="sitemap-btn-icon">travel_explore</span>
                    <span id="sitemap-btn-text">Update Sitemap</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── Run Both Together ──────────────────────────────────────────────── -->
    <div class="admin-card p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl">rocket_launch</span>
            <div>
                <h4 class="text-sm font-bold text-slate-800">Full Rebuild + Sitemap Update</h4>
                <p class="text-xs text-slate-500">Regenerate all HTML pages then immediately update the sitemap</p>
            </div>
        </div>
        <button onclick="runBoth()"
                id="both-btn"
                class="shrink-0 flex items-center gap-2 bg-slate-900 text-white font-bold py-2.5 px-6 rounded-xl hover:bg-slate-700 transition-all shadow-sm text-sm">
            <span class="material-symbols-outlined text-[18px]" id="both-icon">rocket_launch</span>
            <span id="both-text">Run Both</span>
        </button>
    </div>

    <!-- ── Sitemap link ───────────────────────────────────────────────────── -->
    <div class="admin-card p-5">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Sitemap File</h4>
        <div class="flex flex-wrap gap-3 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center">
                <a href="../sitemap.xml" target="_blank"
                   class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-xl text-sm font-bold text-blue-700 hover:bg-blue-100 transition-all">
                    <span class="material-symbols-outlined text-base">description</span>
                    sitemap.xml
                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                </a>
                <span class="text-xs text-slate-400">Single file · includes all pages, listings &amp; blogs</span>
            </div>
            <button onclick="deleteSitemap()" id="delete-sitemap-btn"
                    class="flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-xl text-sm font-bold text-red-700 hover:bg-red-100 transition-all">
                <span class="material-symbols-outlined text-base">delete</span>
                Delete Sitemap
            </button>
        </div>
    </div>

</div>

<?php
$extra_js = <<<'JS'
<script>
// ── Load current file counts on page load ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    const stats = await api('../php/api/cache-stats.php');
    if (stats) {
        document.getElementById('html-listing-count').textContent = stats.html_files ?? '--';
        document.getElementById('html-blog-count').textContent    = stats.blog_files ?? '--';
    }
    // Load last run from localStorage
    const lastRun = localStorage.getItem('csn_last_regen');
    if (lastRun) {
        document.getElementById('last-run-badge').classList.remove('hidden');
        document.getElementById('last-run-text').textContent = 'Last run: ' + lastRun;
    }
});

// ── HTML Regeneration ─────────────────────────────────────────────────────────
async function runHTMLRegen() {
    const btn     = document.getElementById('html-btn');
    const icon    = document.getElementById('html-btn-icon');
    const text    = document.getElementById('html-btn-text');
    const wrap    = document.getElementById('html-progress-wrap');
    const bar     = document.getElementById('html-progress-bar');
    const msg     = document.getElementById('html-progress-msg');
    const pct     = document.getElementById('html-progress-pct');
    const result  = document.getElementById('html-result');

    btn.disabled = true;
    icon.textContent = 'autorenew';
    icon.classList.add('animate-spin');
    text.textContent = 'Regenerating...';
    wrap.classList.remove('hidden');
    result.classList.add('hidden');

    // Animated progress steps
    const steps = [
        [10, 'Connecting to generation service...'],
        [25, 'Fetching listings from database...'],
        [45, 'Building listing HTML pages...'],
        [65, 'Fetching blogs from database...'],
        [80, 'Building blog HTML pages...'],
        [92, 'Writing files to disk...'],
    ];
    let stepIdx = 0;
    const stepTimer = setInterval(() => {
        if (stepIdx < steps.length) {
            setProgress('html', steps[stepIdx][0], steps[stepIdx][1]);
            stepIdx++;
        }
    }, 900);

    try {
        const data = await api('../php/api/generate_html.php?format=json');
        clearInterval(stepTimer);

        if (!data || data.error) throw new Error(data?.error || 'No response from server');

        setProgress('html', 100, 'Done!');
        pct.textContent = '100%';

        // Update counts
        if (data.breakdown) {
            document.getElementById('html-listing-count').textContent = data.breakdown.listings ?? '--';
            document.getElementById('html-blog-count').textContent    = data.breakdown.blogs ?? '--';
        }

        showResult('html', 'success',
            `✓ Generated ${data.total ?? 0} pages — ${data.breakdown?.blogs ?? 0} blogs, ${data.breakdown?.listings ?? 0} listings`
        );

        // Save last run
        const now = new Date().toLocaleString();
        localStorage.setItem('csn_last_regen', now);
        document.getElementById('last-run-badge').classList.remove('hidden');
        document.getElementById('last-run-text').textContent = 'Last run: ' + now;

    } catch (e) {
        clearInterval(stepTimer);
        setProgress('html', 100, 'Error', true);
        showResult('html', 'error', 'Error: ' + e.message);
    } finally {
        btn.disabled = false;
        icon.classList.remove('animate-spin');
        icon.textContent = 'build';
        text.textContent = 'Regenerate Again';
        setTimeout(() => wrap.classList.add('hidden'), 3000);
    }
}

// ── Sitemap Regeneration ──────────────────────────────────────────────────────
async function runSitemapRegen() {
    const btn    = document.getElementById('sitemap-btn');
    const icon   = document.getElementById('sitemap-btn-icon');
    const text   = document.getElementById('sitemap-btn-text');
    const wrap   = document.getElementById('sitemap-progress-wrap');
    const bar    = document.getElementById('sitemap-progress-bar');
    const msg    = document.getElementById('sitemap-progress-msg');
    const pct    = document.getElementById('sitemap-progress-pct');
    const result = document.getElementById('sitemap-result');
    const list   = document.getElementById('sitemap-files-list');

    btn.disabled = true;
    icon.textContent = 'travel_explore';
    icon.classList.add('animate-spin');
    text.textContent = 'Updating...';
    wrap.classList.remove('hidden');
    result.classList.add('hidden');
    list.classList.add('hidden');

    const steps = [
        [15, 'Connecting...'],
        [30, 'Building static pages sitemap...'],
        [50, 'Building listings sitemaps...'],
        [70, 'Building blogs sitemap...'],
        [85, 'Writing sitemap index...'],
        [92, 'Pinging search engines...'],
    ];
    let stepIdx = 0;
    const stepTimer = setInterval(() => {
        if (stepIdx < steps.length) {
            setProgress('sitemap', steps[stepIdx][0], steps[stepIdx][1]);
            stepIdx++;
        }
    }, 700);

    try {
        const data = await api('../php/api/regenerate-sitemap.php', { method: 'POST' });
        clearInterval(stepTimer);

        if (!data || data.error) throw new Error(data?.error || 'No response from server');

        setProgress('sitemap', 100, 'Done!');
        pct.textContent = '100%';

        // Update URL count
        document.getElementById('sitemap-url-count').textContent = data.total ?? '--';

        showResult('sitemap', 'success',
            `✓ sitemap.xml updated — ${data.total ?? 0} total URLs (${data.breakdown?.static ?? 0} static, ${data.breakdown?.listings_total ?? 0} listings, ${data.breakdown?.blogs ?? 0} blogs)`
        );

        // Show breakdown list
        if (data.breakdown) {
            list.classList.remove('hidden');
            const b = data.breakdown;
            const rows = [
                ['description', `Static pages: ${b.static ?? 0}`],
                ['bed',         `Hotels (stays): ${b.stays ?? 0}`],
                ['directions_car', `Cars: ${b.cars ?? 0}`],
                ['motorcycle',  `Bikes: ${b.bikes ?? 0}`],
                ['restaurant',  `Restaurants: ${b.restaurants ?? 0}`],
                ['confirmation_number', `Attractions: ${b.attractions ?? 0}`],
                ['directions_bus', `Buses: ${b.buses ?? 0}`],
                ['article',     `Blogs: ${b.blogs ?? 0}`],
            ];
            list.innerHTML = rows.map(([icon, label]) =>
                `<div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                    <span class="material-symbols-outlined text-[13px] text-green-500">${icon}</span>
                    ${escHtml(label)}
                </div>`
            ).join('');
        }

        // Show ping results
        if (data.ping && data.ping.length) {
            list.innerHTML += '<div class="mt-2 pt-2 border-t border-slate-100">' +
                data.ping.map(p =>
                    `<div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <span class="material-symbols-outlined text-[13px] ${p.includes('OK') ? 'text-green-400' : 'text-amber-400'}">${p.includes('OK') ? 'wifi' : 'wifi_off'}</span>
                        ${escHtml(p)}
                    </div>`
                ).join('') + '</div>';
        }

    } catch (e) {
        clearInterval(stepTimer);
        setProgress('sitemap', 100, 'Error', true);
        showResult('sitemap', 'error', 'Error: ' + e.message);
    } finally {
        btn.disabled = false;
        icon.classList.remove('animate-spin');
        icon.textContent = 'travel_explore';
        text.textContent = 'Update Sitemap';
        setTimeout(() => wrap.classList.add('hidden'), 3000);
    }
}

// ── Run Both ──────────────────────────────────────────────────────────────────
async function runBoth() {
    const btn  = document.getElementById('both-btn');
    const icon = document.getElementById('both-icon');
    const text = document.getElementById('both-text');

    btn.disabled = true;
    icon.classList.add('animate-spin');
    text.textContent = 'Running...';

    await runHTMLRegen();
    await runSitemapRegen();

    btn.disabled = false;
    icon.classList.remove('animate-spin');
    icon.textContent = 'rocket_launch';
    text.textContent = 'Run Both';
    showAdminToast('✓ Full rebuild + sitemap update complete');
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function setProgress(prefix, percent, message, isError = false) {
    const bar = document.getElementById(prefix + '-progress-bar');
    const msg = document.getElementById(prefix + '-progress-msg');
    const pct = document.getElementById(prefix + '-progress-pct');
    if (bar) {
        bar.style.width = percent + '%';
        bar.className = 'h-full rounded-full transition-all duration-500 ' + (isError ? 'bg-red-500' : (prefix === 'sitemap' ? 'bg-blue-500' : 'bg-primary'));
    }
    if (msg) {
        msg.textContent = message;
        msg.className = 'text-xs font-semibold ' + (isError ? 'text-red-600' : 'text-slate-600');
    }
    if (pct) pct.textContent = percent + '%';
}

function showResult(prefix, type, message) {
    const el = document.getElementById(prefix + '-result');
    if (!el) return;
    el.classList.remove('hidden');
    if (type === 'success') {
        el.className = 'mb-4 p-3 rounded-xl text-xs font-semibold flex items-start gap-2 bg-green-50 border border-green-200 text-green-700';
        el.innerHTML = '<span class="material-symbols-outlined text-base shrink-0">check_circle</span><span>' + escHtml(message) + '</span>';
    } else {
        el.className = 'mb-4 p-3 rounded-xl text-xs font-semibold flex items-start gap-2 bg-red-50 border border-red-200 text-red-700';
        el.innerHTML = '<span class="material-symbols-outlined text-base shrink-0">error</span><span>' + escHtml(message) + '</span>';
    }
}

function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Delete Sitemap ────────────────────────────────────────────────────────────
async function deleteSitemap() {
    if (!confirm('⚠️ Are you sure you want to delete sitemap.xml?\n\nThis will remove the sitemap file from your website. You can regenerate it anytime by clicking "Update Sitemap".')) {
        return;
    }

    const btn = document.getElementById('delete-sitemap-btn');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<div class="w-4 h-4 border-2 border-red-600 border-t-transparent rounded-full animate-spin"></div> Deleting...';

    try {
        const response = await fetch('../php/api/delete-sitemap.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('csn_admin_token')
            }
        });

        const data = await response.json();

        if (data.success) {
            showAdminToast('✓ Sitemap deleted successfully');
            
            // Update UI
            document.getElementById('sitemap-url-count').textContent = '0';
            document.getElementById('sitemap-files-list').innerHTML = '<div class="text-xs text-slate-400 italic">No sitemap file exists</div>';
            
            // Show success message
            const result = document.getElementById('sitemap-result');
            result.classList.remove('hidden');
            result.className = 'mb-4 p-3 rounded-xl text-xs font-semibold flex items-start gap-2 bg-green-50 border border-green-200 text-green-700';
            result.innerHTML = '<span class="material-symbols-outlined text-base shrink-0">check_circle</span><span>Sitemap deleted successfully. Click "Update Sitemap" to regenerate.</span>';
        } else {
            throw new Error(data.error || 'Failed to delete sitemap');
        }
    } catch (error) {
        showAdminToast('✗ Error: ' + error.message, 'error');
        console.error('Delete sitemap error:', error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}
</script>
JS;
require 'admin-footer.php';
?>
