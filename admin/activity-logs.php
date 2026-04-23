<?php
$admin_page  = 'activity-logs';
$admin_title = 'Activity Logs | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Activity Logs</h2>
            <p class="text-xs text-slate-500 mt-0.5">Every user and admin action on CSNExplore</p>
        </div>
        <button onclick="clearOldLogs()" class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 border border-red-200 rounded-xl hover:bg-red-50 transition-all">
            <span class="material-symbols-outlined text-sm">delete_sweep</span> Clear logs older than 30 days
        </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
            <input id="log-search" type="text" placeholder="Search by name or action…"
                   class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
        </div>
        <select id="log-type" class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white">
            <option value="">All Actions</option>
            <option value="user_login">User Login</option>
            <option value="user_register">Registration</option>
            <option value="email_verified">Email Verified</option>
            <option value="password_reset">Password Reset</option>
            <option value="admin_user_update">Admin: User Update</option>
            <option value="admin_user_delete">Admin: User Delete</option>
            <option value="admin_login">Admin Login</option>
            <option value="booking_created">Booking Created</option>
            <option value="booking_updated">Booking Updated</option>
            <option value="booking_deleted">Booking Deleted</option>
            <option value="blog_created">Blog Created</option>
            <option value="blog_updated">Blog Updated</option>
            <option value="listing_updated">Listing Updated</option>
        </select>
        <button onclick="loadLogs(1)" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-all">
            Filter
        </button>
    </div>

    <!-- Stats row -->
    <div id="log-stats" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 w-36">Time</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Actor</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Action</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Description</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 w-28">IP</th>
                    </tr>
                </thead>
                <tbody id="logs-tbody">
                    <tr><td colspan="5" class="text-center py-12 text-slate-400">Loading…</td></tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="log-pagination" class="flex items-center justify-between px-4 py-3 border-t border-slate-100 text-xs text-slate-500"></div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var _logPage  = 1;
var _logTotal = 0;
var _logLimit = 50;

var ACTION_COLORS = {
    user_login:         'bg-blue-100 text-blue-700',
    user_register:      'bg-green-100 text-green-700',
    email_verified:     'bg-emerald-100 text-emerald-700',
    password_reset:     'bg-amber-100 text-amber-700',
    admin_user_update:  'bg-purple-100 text-purple-700',
    admin_user_delete:  'bg-red-100 text-red-700',
    admin_login:        'bg-indigo-100 text-indigo-700',
    booking_created:    'bg-cyan-100 text-cyan-700',
    booking_updated:    'bg-sky-100 text-sky-700',
    booking_deleted:    'bg-red-100 text-red-600',
    blog_created:       'bg-pink-100 text-pink-700',
    blog_updated:       'bg-rose-100 text-rose-700',
    listing_updated:    'bg-orange-100 text-orange-700',
    system_init:        'bg-slate-100 text-slate-600',
};

var ACTION_ICONS = {
    user_login:         'login',
    user_register:      'person_add',
    email_verified:     'verified',
    password_reset:     'lock_reset',
    admin_user_update:  'manage_accounts',
    admin_user_delete:  'person_remove',
    admin_login:        'admin_panel_settings',
    booking_created:    'calendar_add_on',
    booking_updated:    'edit_calendar',
    booking_deleted:    'event_busy',
    blog_created:       'article',
    blog_updated:       'edit_note',
    listing_updated:    'database',
    system_init:        'settings',
};

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function fmtDate(dt) {
    if (!dt) return '—';
    var d = new Date(dt.replace(' ','T'));
    return d.toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'}) + ' ' +
           d.toLocaleTimeString('en-IN', {hour:'2-digit',minute:'2-digit'});
}

async function loadLogs(page) {
    _logPage = page || 1;
    var search = document.getElementById('log-search').value;
    var type   = document.getElementById('log-type').value;
    var url    = '../php/api/activity_log.php?page=' + _logPage + '&limit=' + _logLimit;
    if (search) url += '&search=' + encodeURIComponent(search);
    if (type)   url += '&type='   + encodeURIComponent(type);

    var tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-slate-400"><div class="inline-block w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div></td></tr>';

    var data = await api(url);
    if (!data) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-red-400">Failed to load logs</td></tr>';
        return;
    }

    _logTotal = data.total || 0;
    var logs  = data.logs  || [];

    if (!logs.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-slate-400">No activity logs yet</td></tr>';
        renderPagination();
        return;
    }

    tbody.innerHTML = logs.map(function(l) {
        var color = ACTION_COLORS[l.action_type] || 'bg-slate-100 text-slate-600';
        var icon  = ACTION_ICONS[l.action_type]  || 'info';
        var roleColor = l.actor_role === 'admin' ? 'text-primary font-bold' : 'text-slate-500';
        return '<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">' +
            '<td class="py-2.5 px-4 text-slate-400 text-xs whitespace-nowrap">' + fmtDate(l.created_at) + '</td>' +
            '<td class="py-2.5 px-4">' +
                '<p class="font-semibold text-slate-800 text-xs">' + escHtml(l.actor_name) + '</p>' +
                '<p class="text-[10px] ' + roleColor + '">' + escHtml(l.actor_role) + '</p>' +
            '</td>' +
            '<td class="py-2.5 px-4">' +
                '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ' + color + '">' +
                    '<span class="material-symbols-outlined text-[11px]">' + icon + '</span>' +
                    escHtml(l.action_type.replace(/_/g,' ')) +
                '</span>' +
            '</td>' +
            '<td class="py-2.5 px-4 text-slate-600 text-xs max-w-xs">' + escHtml(l.description) + '</td>' +
            '<td class="py-2.5 px-4 text-slate-400 text-[10px] font-mono">' + escHtml(l.ip_address || '—') + '</td>' +
        '</tr>';
    }).join('');

    renderPagination();
    loadStats();
}

function renderPagination() {
    var pages = Math.ceil(_logTotal / _logLimit);
    var el = document.getElementById('log-pagination');
    if (pages <= 1) { el.innerHTML = '<span>' + _logTotal + ' total entries</span>'; return; }
    var btns = '';
    for (var i = 1; i <= Math.min(pages, 10); i++) {
        var active = i === _logPage ? 'bg-primary text-white' : 'bg-white text-slate-600 hover:bg-slate-50';
        btns += '<button onclick="loadLogs(' + i + ')" class="w-7 h-7 rounded-lg text-xs font-bold border border-slate-200 ' + active + '">' + i + '</button>';
    }
    el.innerHTML = '<span>' + _logTotal + ' total entries</span><div class="flex gap-1">' + btns + '</div>';
}

async function loadStats() {
    // Quick counts by type
    var types = ['user_login','user_register','email_verified','password_reset'];
    var labels = ['Logins','Registrations','Verifications','Password Resets'];
    var colors = ['bg-blue-50 text-blue-700 border-blue-100','bg-green-50 text-green-700 border-green-100','bg-emerald-50 text-emerald-700 border-emerald-100','bg-amber-50 text-amber-700 border-amber-100'];
    var icons  = ['login','person_add','verified','lock_reset'];

    var statsEl = document.getElementById('log-stats');
    statsEl.innerHTML = types.map(function(t,i) {
        return '<div class="bg-white border ' + colors[i].split(' ')[2] + ' rounded-xl p-3 flex items-center gap-3">' +
            '<span class="material-symbols-outlined text-xl ' + colors[i].split(' ')[1] + '">' + icons[i] + '</span>' +
            '<div><p class="text-xs text-slate-500">' + labels[i] + '</p>' +
            '<p id="stat-' + t + '" class="text-lg font-black text-slate-800">—</p></div></div>';
    }).join('');

    for (var t of types) {
        var d = await api('../php/api/activity_log.php?type=' + t + '&limit=1');
        if (d) {
            var el = document.getElementById('stat-' + t);
            if (el) el.textContent = (d.total || 0).toLocaleString();
        }
    }
}

async function clearOldLogs() {
    if (!confirm('Delete all activity logs older than 30 days?')) return;
    // Direct DB call via a simple endpoint
    var res = await api('../php/api/activity_log.php?action=clear_old', { method: 'DELETE' });
    showAdminToast(res && res.success ? 'Old logs cleared' : 'Failed', res && res.success ? 'success' : 'error');
    loadLogs(1);
}

// Search debounce
document.getElementById('log-search').addEventListener('input', function(){
    clearTimeout(window._lst);
    window._lst = setTimeout(function(){ loadLogs(1); }, 400);
});
document.getElementById('log-type').addEventListener('change', function(){ loadLogs(1); });

loadLogs(1);
</script>
JS;
require 'admin-footer.php';
?>
