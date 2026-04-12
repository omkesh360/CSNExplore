<?php
// ── Admin: Newsletter Subscribers [B3.1] ─────────────────────────────────────
$admin_page  = 'subscribers';
$admin_title = 'Newsletter Subscribers | CSNExplore Admin';
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/jwt.php';

// Auth check (JS handles redirect; PHP guards API)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $admin_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#ec5b13'},fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>body{font-family:'Inter',sans-serif;background:#f8fafc;}.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;font-family:'Material Symbols Outlined';font-style:normal;display:inline-block;line-height:1;}</style>
</head>
<body>
<?php include 'admin-header.php'; ?>

<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Newsletter Subscribers</h1>
            <p class="text-slate-500 text-sm mt-1">View and export all newsletter subscribers</p>
        </div>
        <div class="flex gap-3">
            <span id="total-count" class="px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-bold">Loading...</span>
            <button onclick="exportCSV()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">
                <span class="material-symbols-outlined text-sm">download</span>Export CSV
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm text-center">
            <p class="text-3xl font-black text-slate-900" id="stat-total">—</p>
            <p class="text-xs text-slate-500 font-semibold mt-1 uppercase tracking-wider">Total Subscribers</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm text-center">
            <p class="text-3xl font-black text-green-600" id="stat-active">—</p>
            <p class="text-xs text-slate-500 font-semibold mt-1 uppercase tracking-wider">Active</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm text-center">
            <p class="text-3xl font-black text-slate-400" id="stat-today">—</p>
            <p class="text-xs text-slate-500 font-semibold mt-1 uppercase tracking-wider">Joined Today</p>
        </div>
    </div>

    <!-- Search -->
    <div class="flex items-center gap-3 mb-4">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input type="text" id="search-input" placeholder="Search by email..."
                   oninput="filterTable(this.value)"
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary">
        </div>
        <select id="status-filter" onchange="filterTable()" class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary">
            <option value="">All Statuses</option>
            <option value="1">Active</option>
            <option value="0">Unsubscribed</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Subscribed On</th>
                    <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody id="subscribers-table">
                <tr><td colspan="5" class="text-center py-10 text-slate-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>

    <p class="text-xs text-slate-400 mt-4 text-center" id="showing-count"></p>
</div>

<script>
let allSubs = [];
const token = localStorage.getItem('csn_admin_token');
if (!token) { window.location.href = '../login.php'; }

async function loadSubscribers() {
    const res = await fetch('../php/api/subscribers.php', {
        headers: {'Authorization': 'Bearer ' + token}
    });
    if (res.status === 401) { window.location.href = '../login.php'; return; }
    const data = await res.json();
    allSubs = data.subscribers || [];

    document.getElementById('stat-total').textContent = allSubs.length;
    document.getElementById('stat-active').textContent = allSubs.filter(s => s.is_active == 1).length;
    document.getElementById('total-count').textContent = allSubs.length + ' total';
    const today = new Date().toISOString().slice(0,10);
    document.getElementById('stat-today').textContent = allSubs.filter(s => s.subscribed_at && s.subscribed_at.startsWith(today)).length;

    renderTable(allSubs);
}

function renderTable(subs) {
    const tbody = document.getElementById('subscribers-table');
    document.getElementById('showing-count').textContent = 'Showing ' + subs.length + ' of ' + allSubs.length + ' subscribers';
    if (!subs.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-10 text-slate-400 font-medium">No subscribers found.</td></tr>';
        return;
    }
    tbody.innerHTML = subs.map((s,i) => `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
            <td class="px-6 py-3 text-slate-400 text-xs">${i+1}</td>
            <td class="px-6 py-3 font-medium text-slate-900">${s.email}</td>
            <td class="px-6 py-3 text-slate-500">${s.subscribed_at ? s.subscribed_at.slice(0,16).replace('T',' ') : '—'}</td>
            <td class="px-6 py-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold ${s.is_active == 1 ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'}">
                    ${s.is_active == 1 ? 'Active' : 'Unsubscribed'}
                </span>
            </td>
            <td class="px-6 py-3 text-right">
                <button onclick="deleteSubscriber(${s.id})"
                        class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Remove">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </td>
        </tr>
    `).join('');
}

function filterTable(query) {
    const q = (query || document.getElementById('search-input').value || '').toLowerCase();
    const status = document.getElementById('status-filter').value;
    let filtered = allSubs.filter(s => s.email.toLowerCase().includes(q));
    if (status !== '') filtered = filtered.filter(s => String(s.is_active) === status);
    renderTable(filtered);
}

async function deleteSubscriber(id) {
    if (!confirm('Remove this subscriber?')) return;
    await fetch('../php/api/subscribers.php?id=' + id, {
        method: 'DELETE',
        headers: {'Authorization': 'Bearer ' + token}
    });
    loadSubscribers();
}

function exportCSV() {
    const rows = [['#','Email','Subscribed On','Status']];
    allSubs.forEach((s,i) => rows.push([i+1, s.email, s.subscribed_at||'', s.is_active==1?'Active':'Unsubscribed']));
    const csv = rows.map(r => r.map(v => '"'+String(v).replace(/"/g,'""')+'"').join(',')).join('\n');
    const blob = new Blob([csv], {type:'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href=url; a.download='subscribers.csv'; a.click();
    URL.revokeObjectURL(url);
}

loadSubscribers();
</script>
<?php include 'admin-footer.php'; ?>
</body>
</html>
