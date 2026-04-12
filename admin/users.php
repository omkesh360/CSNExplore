<?php
$admin_page  = 'users';
$admin_title = 'Users | CSNExplore Admin';
require 'admin-header.php';
?>
<div class="space-y-5">
<div class="flex flex-wrap gap-3 items-center">
    <input id="search-input" type="text" placeholder="Search users..."
           class="flex-1 min-w-[200px] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Name</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Email</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Phone</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Role</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Verified</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Joined</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody id="users-tbody">
                <tr><td colspan="8" class="text-center py-12 text-slate-400">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Change Password Modal -->
<div id="pw-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-bold">Change Password</h3>
            <button onclick="closePwModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p id="pw-modal-name" class="font-semibold text-slate-900"></p>
            <div id="pw-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2 rounded-xl"></div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">New Password</label>
                <input id="pw-new" type="password" placeholder="Min 6 characters"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Confirm Password</label>
                <input id="pw-confirm" type="password" placeholder="Repeat password"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
            <div class="flex gap-3">
                <button onclick="closePwModal()" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancel</button>
                <button onclick="savePassword()" class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="user-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-bold">Edit User Role</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p id="user-modal-name" class="font-semibold text-slate-900"></p>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Role</label>
                <select id="user-role" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="vendor">Vendor</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50">Cancel</button>
                <button onclick="saveRole()" class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600">Save</button>
            </div>
        </div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var editingUserId = null;

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

async function loadUsers() {
    var search = document.getElementById('search-input').value;
    var url = '../php/api/users.php' + (search ? '?search=' + encodeURIComponent(search) : '');
    var tbody = document.getElementById('users-tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    var users = await api(url);
    if (!users || !users.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">No users found</td></tr>';
        return;
    }
    tbody.innerHTML = users.map(function(u) {
        var rc = u.role === 'admin' ? 'bg-primary/10 text-primary' : u.role === 'vendor' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600';
        var verifiedBadge = u.is_verified == 1
            ? '<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700"><span class="material-symbols-outlined text-xs">verified</span> Verified</span>'
            : '<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700"><span class="material-symbols-outlined text-xs">pending</span> Pending</span>';
        return '<tr class="border-b border-slate-50 hover:bg-slate-50">' +
            '<td class="py-2.5 px-4 text-slate-400 text-xs">' + u.id + '</td>' +
            '<td class="py-2.5 px-4 font-medium">' + escHtml(u.name) + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500">' + escHtml(u.email) + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500">' + escHtml(u.phone || '—') + '</td>' +
            '<td class="py-2.5 px-4"><span class="px-2 py-0.5 rounded-full text-xs font-bold ' + rc + '">' + escHtml(u.role) + '</span></td>' +
            '<td class="py-2.5 px-4">' + verifiedBadge + '</td>' +
            '<td class="py-2.5 px-4 text-slate-500 text-xs">' + escHtml((u.created_at || '').split(' ')[0]) + '</td>' +
            '<td class="py-2.5 px-4">' +
                '<div class="flex gap-1">' +
                '<button onclick="openEditRole(' + u.id + ',\'' + escHtml(u.name) + '\',\'' + escHtml(u.role) + '\')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all" title="Edit Role"><span class="material-symbols-outlined text-base">manage_accounts</span></button>' +
                '<button onclick="openChangePw(' + u.id + ',\'' + escHtml(u.name) + '\')" class="p-1.5 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-all" title="Change Password"><span class="material-symbols-outlined text-base">key</span></button>' +
                (u.is_verified != 1 ? '<button onclick="manualVerify(' + u.id + ')" class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Manually Verify"><span class="material-symbols-outlined text-base">verified</span></button>' : '') +
                '<button onclick="deleteUser(' + u.id + ')" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Delete"><span class="material-symbols-outlined text-base">delete</span></button>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }).join('');
}

function openEditRole(id, name, role) {
    editingUserId = id;
    document.getElementById('user-modal-name').textContent = name;
    document.getElementById('user-role').value = role;
    document.getElementById('user-modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('user-modal').classList.add('hidden'); }

function openChangePw(id, name) {
    editingUserId = id;
    document.getElementById('pw-modal-name').textContent = name;
    document.getElementById('pw-new').value = '';
    document.getElementById('pw-confirm').value = '';
    document.getElementById('pw-error').classList.add('hidden');
    document.getElementById('pw-modal').classList.remove('hidden');
}

function closePwModal() { document.getElementById('pw-modal').classList.add('hidden'); }

async function savePassword() {
    var newPw  = document.getElementById('pw-new').value;
    var confPw = document.getElementById('pw-confirm').value;
    var errEl  = document.getElementById('pw-error');
    errEl.classList.add('hidden');

    if (newPw.length < 6) { errEl.textContent = 'Password must be at least 6 characters.'; errEl.classList.remove('hidden'); return; }
    if (newPw !== confPw) { errEl.textContent = 'Passwords do not match.'; errEl.classList.remove('hidden'); return; }

    var res = await api('../php/api/auth.php?action=change_password', {
        method: 'POST', body: JSON.stringify({ user_id: editingUserId, new_password: newPw })
    });
    if (res && res.error) { errEl.textContent = res.error; errEl.classList.remove('hidden'); return; }
    closePwModal();
}

async function saveRole() {
    if (!editingUserId) return;
    var role = document.getElementById('user-role').value;
    await api('../php/api/users.php?id=' + editingUserId, {
        method: 'PUT', body: JSON.stringify({ role: role })
    });
    closeModal();
    loadUsers();
}

async function deleteUser(id) {
    if (!confirm('Delete this user? This cannot be undone.')) return;
    var res = await api('../php/api/users.php?id=' + id, { method: 'DELETE' });
    if (res && res.error) { alert(res.error); return; }
    loadUsers();
}

async function manualVerify(id) {
    if (!confirm('Manually verify this user\'s email?')) return;
    var res = await api('../php/api/users.php?id=' + id, {
        method: 'PUT', body: JSON.stringify({ is_verified: 1 })
    });
    if (res && res.error) { alert(res.error); return; }
    loadUsers();
}

document.getElementById('search-input').addEventListener('input', function(){
    clearTimeout(window._st);
    window._st = setTimeout(loadUsers, 400);
});

loadUsers();
</script>
JS;
require 'admin-footer.php';
?>
