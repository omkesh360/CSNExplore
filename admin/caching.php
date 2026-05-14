<?php
$admin_page  = 'caching';
$admin_title = 'Caching | CSNExplore';
require_once 'admin-header.php';
?>

<div class="max-w-4xl mx-auto space-y-6 animate-slide-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Caching Settings</h2>
            <p class="text-sm text-slate-500 mt-1">Control caching across the entire website — all pages, listings, blogs, and static assets.</p>
        </div>
        <button onclick="clearAllCache()" id="clear-btn"
                class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-xl text-sm font-bold hover:bg-red-100 transition-all">
            <span class="material-symbols-outlined text-base">delete_sweep</span> Clear All Cache
        </button>
    </div>

    <!-- Status Banner -->
    <div id="status-banner" class="hidden flex items-center gap-3 p-4 rounded-xl border text-sm font-semibold"></div>

    <!-- ── Global Toggle ─────────────────────────────────────────────── -->
    <div class="admin-card p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary text-xl">memory</span>
                    <h3 class="text-base font-bold text-slate-800">Global Caching</h3>
                    <span id="global-badge" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-400">Disabled</span>
                </div>
                <p class="text-sm text-slate-500 mb-3">Enable or disable caching across the <strong>entire website</strong>. Applies to every page, listing, blog, and API response.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px] text-slate-500">
                    <?php
                    $scopes = [
                        ['memory','DB Query Cache'],
                        ['article','Blog Pages'],
                        ['database','Listing Pages'],
                        ['home','Homepage'],
                        ['photo_library','Gallery'],
                        ['api','API Responses'],
                        ['html','Static HTML Files'],
                        ['manage_search','SEO Pages'],
                        ['group','User Pages'],
                        ['calendar_today','Booking Pages'],
                        ['flight_takeoff','Trip Planner'],
                        ['history','Activity Logs'],
                    ];
                    foreach ($scopes as $s): ?>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px] text-slate-400"><?php echo $s[0]; ?></span>
                        <span><?php echo $s[1]; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                <input type="checkbox" id="cachingToggle" class="sr-only peer">
                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
            </label>
        </div>
    </div>

    <!-- ── Cache Layers ───────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- DB Query Cache -->
        <div class="admin-card p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-blue-500 text-xl">storage</span>
                <h4 class="font-bold text-slate-800 text-sm">Database Query Cache</h4>
                <span id="db-cache-badge" class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-400">Off</span>
            </div>
            <p class="text-xs text-slate-500 mb-3">Caches SQL query results in <code class="bg-slate-100 px-1 rounded">cache/db_query_cache/</code> for 1 hour. Speeds up listings, blogs, and all database-driven pages.</p>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">Cache files: <span id="cache-file-count" class="font-bold text-slate-700">--</span></span>
                <button onclick="clearDBCache()" class="text-red-500 hover:text-red-700 font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">delete</span> Clear
                </button>
            </div>
        </div>

        <!-- Browser / HTTP Cache -->
        <div class="admin-card p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-green-500 text-xl">web</span>
                <h4 class="font-bold text-slate-800 text-sm">Browser / HTTP Cache</h4>
                <span id="http-cache-badge" class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-400">Off</span>
            </div>
            <p class="text-xs text-slate-500 mb-3">Controls <code class="bg-slate-100 px-1 rounded">Cache-Control</code> headers sent to browsers and CDNs. When enabled, pages are cached for 1 hour; static assets for 1 year.</p>
            <div class="text-xs text-slate-400">
                Managed via <code class="bg-slate-100 px-1 rounded">.htaccess</code> + PHP headers on every page.
            </div>
        </div>

        <!-- Static HTML Cache -->
        <div class="admin-card p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-orange-500 text-xl">html</span>
                <h4 class="font-bold text-slate-800 text-sm">Static HTML Pages</h4>
                <span id="html-cache-badge" class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-400">Off</span>
            </div>
            <p class="text-xs text-slate-500 mb-3">Pre-generated <code class="bg-slate-100 px-1 rounded">listing-detail/</code> and <code class="bg-slate-100 px-1 rounded">blogs/</code> HTML files. When caching is on, browsers cache them for 24 hours.</p>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-400">HTML files: <span id="html-file-count" class="font-bold text-slate-700">--</span></span>
                <a href="regenerate.php" class="text-primary hover:underline font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">autorenew</span> Regenerate
                </a>
            </div>
        </div>

        <!-- Service Worker Cache -->
        <div class="admin-card p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-purple-500 text-xl">offline_bolt</span>
                <h4 class="font-bold text-slate-800 text-sm">Service Worker Cache</h4>
                <span id="sw-cache-badge" class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-400">Off</span>
            </div>
            <p class="text-xs text-slate-500 mb-3">PWA offline cache via <code class="bg-slate-100 px-1 rounded">sw.js</code>. Caches pages and assets for offline use. Controlled by the global toggle.</p>
            <div class="text-xs text-slate-400">
                Cache version bumped automatically when global caching is toggled.
            </div>
        </div>
    </div>

    <!-- ── Cache TTL Settings ─────────────────────────────────────────── -->
    <div class="admin-card p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">timer</span> Cache Duration (TTL)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">DB Query Cache (minutes)</label>
                <input type="number" id="ttl-db" min="1" max="1440" value="60"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
                <p class="text-[10px] text-slate-400 mt-1">Default: 60 min</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Page Cache (minutes)</label>
                <input type="number" id="ttl-page" min="1" max="1440" value="60"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
                <p class="text-[10px] text-slate-400 mt-1">Default: 60 min</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Static HTML Cache (hours)</label>
                <input type="number" id="ttl-html" min="1" max="168" value="24"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
                <p class="text-[10px] text-slate-400 mt-1">Default: 24 hrs</p>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="saveTTL()" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-orange-600 transition-all">
                Save TTL Settings
            </button>
        </div>
    </div>

    <!-- ── Cache Stats ────────────────────────────────────────────────── -->
    <div class="admin-card p-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">bar_chart</span> Cache Statistics
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-black text-slate-800" id="stat-db-files">--</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">DB Cache Files</div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-black text-slate-800" id="stat-html-files">--</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">HTML Pages</div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-black text-slate-800" id="stat-blog-files">--</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">Blog Pages</div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <div class="text-2xl font-black text-slate-800" id="stat-cache-size">--</div>
                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">Cache Size</div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    await loadSettings();
    await loadStats();
});

async function loadSettings() {
    const res = await api('../php/api/settings.php');
    if (!res || !res.success) return;

    const s = res.settings || {};
    const enabled = s?.features?.caching?.enabled === true;
    const ttl = s?.features?.caching?.ttl || {};

    // Toggle
    document.getElementById('cachingToggle').checked = enabled;
    updateBadges(enabled);

    // TTL
    if (ttl.db)   document.getElementById('ttl-db').value   = ttl.db;
    if (ttl.page) document.getElementById('ttl-page').value = ttl.page;
    if (ttl.html) document.getElementById('ttl-html').value = ttl.html;
}

function updateBadges(enabled) {
    const on  = 'bg-green-100 text-green-700';
    const off = 'bg-slate-100 text-slate-400';
    const txt = enabled ? 'Enabled' : 'Disabled';

    ['global-badge','db-cache-badge','http-cache-badge','html-cache-badge','sw-cache-badge'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = txt;
        el.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ' + (enabled ? on : off);
        if (id === 'global-badge') el.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ml-2 ' + (enabled ? on : off);
    });

    // Status banner
    const banner = document.getElementById('status-banner');
    if (enabled) {
        banner.className = 'flex items-center gap-3 p-4 rounded-xl border text-sm font-semibold bg-green-50 border-green-200 text-green-700';
        banner.innerHTML = '<span class="material-symbols-outlined text-xl">check_circle</span> Caching is <strong>ENABLED</strong> — all website pages, listings, blogs, and API responses are being cached.';
    } else {
        banner.className = 'flex items-center gap-3 p-4 rounded-xl border text-sm font-semibold bg-amber-50 border-amber-200 text-amber-700';
        banner.innerHTML = '<span class="material-symbols-outlined text-xl">info</span> Caching is <strong>DISABLED</strong> — every request hits the database directly. Enable caching for better performance.';
    }
    banner.classList.remove('hidden');
}

async function loadStats() {
    const res = await api('../php/api/cache-stats.php');
    if (!res) return;

    const dbFiles   = res.db_cache_files   ?? '--';
    const htmlFiles = res.html_files       ?? '--';
    const blogFiles = res.blog_files       ?? '--';
    const cacheSize = res.cache_size_kb    ?? '--';

    document.getElementById('cache-file-count').textContent = dbFiles;
    document.getElementById('html-file-count').textContent  = htmlFiles;
    document.getElementById('stat-db-files').textContent    = dbFiles;
    document.getElementById('stat-html-files').textContent  = htmlFiles;
    document.getElementById('stat-blog-files').textContent  = blogFiles;
    document.getElementById('stat-cache-size').textContent  = cacheSize !== '--' ? cacheSize + ' KB' : '--';
}

// Toggle handler
document.getElementById('cachingToggle').addEventListener('change', async (e) => {
    const enabled = e.target.checked;
    updateBadges(enabled);

    const res = await api('../php/api/settings.php', {
        method: 'POST',
        body: JSON.stringify({ features: { caching: { enabled } } })
    });

    if (res && res.success) {
        showAdminToast(enabled ? '✓ Caching enabled — all pages now cached' : '✓ Caching disabled — cache cleared');
        await loadStats();
    } else {
        // Revert
        e.target.checked = !enabled;
        updateBadges(!enabled);
        showAdminToast('Error updating caching settings', 'error');
    }
});

async function saveTTL() {
    const ttl = {
        db:   parseInt(document.getElementById('ttl-db').value)   || 60,
        page: parseInt(document.getElementById('ttl-page').value) || 60,
        html: parseInt(document.getElementById('ttl-html').value) || 24,
    };

    const res = await api('../php/api/settings.php', {
        method: 'POST',
        body: JSON.stringify({ features: { caching: { ttl } } })
    });

    if (res && res.success) {
        showAdminToast('✓ TTL settings saved');
    } else {
        showAdminToast('Error saving TTL settings', 'error');
    }
}

async function clearAllCache() {
    const btn = document.getElementById('clear-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Clearing...';

    const res = await api('../php/api/cache-stats.php', { method: 'DELETE' });

    btn.disabled = false;
    btn.innerHTML = '<span class="material-symbols-outlined text-base">delete_sweep</span> Clear All Cache';

    if (res && res.success) {
        showAdminToast('✓ All cache cleared successfully');
        await loadStats();
    } else {
        showAdminToast('Error clearing cache', 'error');
    }
}

async function clearDBCache() {
    const res = await api('../php/api/cache-stats.php', {
        method: 'DELETE',
        body: JSON.stringify({ type: 'db' })
    });
    if (res && res.success) {
        showAdminToast('✓ DB query cache cleared');
        await loadStats();
    } else {
        showAdminToast('Error clearing DB cache', 'error');
    }
}
</script>

<?php require_once 'admin-footer.php'; ?>
