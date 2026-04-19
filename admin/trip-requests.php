<?php
$admin_page  = 'trip-requests';
$admin_title = 'Trip Planner Requests | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Trip Planner Requests</h2>
            <p class="text-xs text-slate-500 font-medium">Manage AI-planner trip inquiries from users</p>
        </div>
        <button onclick="loadRequests()" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-all text-slate-600">
            <span class="material-symbols-outlined text-sm">refresh</span> Refresh
        </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex gap-1 p-1 bg-slate-100 rounded-lg">
            <?php foreach (['all'=>'All','pending'=>'Pending','contacted'=>'Contacted','completed'=>'Completed'] as $k=>$v): ?>
            <button onclick="filterStatus('<?php echo $k; ?>')" data-status="<?php echo $k; ?>"
                    class="status-tab px-4 py-1.5 rounded-md text-xs font-semibold transition-all">
                <?php echo $v; ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="flex-1 w-full relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input id="search-input" type="text" placeholder="Search by name or phone..."
                   class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-12 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary/20 focus:border-primary transition-all"/>
        </div>
    </div>

    <!-- Table -->
    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto overflow-y-hidden custom-scrollbar">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 bg-slate-50 border-b border-slate-100">
                        <th class="py-3 px-4 text-left">Customer</th>
                        <th class="py-3 px-4 text-left">Preferences</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="requests-tbody" class="divide-y divide-slate-50">
                    <tr><td colspan="5" class="text-center py-20 text-slate-400 italic">Initiating data fetch...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="request-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-base font-bold">Trip Plan Request Details</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="modal-body" class="p-6 space-y-6"></div>
        <div class="p-6 border-t border-slate-100 flex gap-3 sticky bottom-0 bg-white rounded-b-2xl">
            <button onclick="updateStatus('completed')" class="flex-1 bg-green-500 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-green-600 transition-all">Mark Completed</button>
            <button onclick="updateStatus('contacted')" class="flex-1 bg-blue-500 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-blue-600 transition-all">Mark Contacted</button>
            <button onclick="updateStatus('pending')" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Pending</button>
        </div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentStatus = 'all';
var currentId = null;

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function filterStatus(s) {
    currentStatus = s;
    document.querySelectorAll('.status-tab').forEach(function(b){
        var active = b.dataset.status === s;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    loadRequests();
}

async function loadRequests() {
    var search = document.getElementById('search-input').value;
    var url = '../php/api/trip_requests.php?';
    if (currentStatus !== 'all') url += 'status=' + currentStatus + '&';
    if (search) url += 'search=' + encodeURIComponent(search);
    var tbody = document.getElementById('requests-tbody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    var items = await api(url);
    if (!items || !items.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-slate-400">No requests found</td></tr>';
        return;
    }
    
    tbody.innerHTML = items.map(function(r) {
        var statusColor = r.status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : r.status === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-blue-50 text-blue-600 border-blue-100';
        return '<tr class="hover:bg-slate-50 transition-colors cursor-pointer group" onclick="openModal(' + r.id + ')">' +
            '<td class="py-4 px-6">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="w-10 h-10 bg-slate-100 rounded-lg flex-shrink-0 flex items-center justify-center font-bold text-slate-400">' + escHtml(r.full_name).charAt(0).toUpperCase() + '</div>' +
                    '<div>' +
                        '<p class="font-bold text-slate-900">' + escHtml(r.full_name) + '</p>' +
                        '<p class="text-[10px] text-slate-400 font-semibold">' + escHtml(r.phone) + '</p>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-6">' +
                '<div class="flex items-center gap-2">' +
                    '<span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold text-slate-600"><span class="material-symbols-outlined text-[10px] mr-1">bed</span>' + escHtml(r.stay_type || 'Any') + '</span>' +
                    '<span class="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold text-slate-600"><span class="material-symbols-outlined text-[10px] mr-1">directions_car</span>' + escHtml(r.travel_mode || 'Any') + '</span>' +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-6 text-center">' +
                '<span class="inline-block px-3 py-1 rounded-md text-[10px] font-bold uppercase border ' + statusColor + '">' + escHtml(r.status) + '</span>' +
            '</td>' +
            '<td class="py-4 px-6 text-right">' +
                '<p class="text-[13px] font-bold text-slate-700">' + escHtml(r.created_at?.split(' ')[0] || '—') + '</p>' +
            '</td>' +
            '<td class="py-4 px-6 text-right">' +
                '<button onclick="event.stopPropagation(); deleteRequest(' + r.id + ')" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"><span class="material-symbols-outlined text-lg">delete</span></button>' +
            '</td>' +
        '</tr>';
    }).join('');
}

async function openModal(id) {
    currentId = id;
    var r = await api('../php/api/trip_requests.php?id=' + id);
    if (!r || r.error) return;
    
    var statusColor = r.status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : r.status === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-blue-50 text-blue-600 border-blue-100';

    document.getElementById('modal-body').innerHTML = 
        '<div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-4">' +
            '<div>' +
                '<h4 class="text-xl font-bold text-slate-900">' + escHtml(r.full_name) + '</h4>' +
                '<a href="tel:' + r.phone + '" class="text-primary text-sm font-bold mt-1 inline-block hover:underline">' + escHtml(r.phone) + '</a>' +
            '</div>' +
            '<span class="px-3 py-1 rounded-md text-[10px] font-bold uppercase border ' + statusColor + '">' + escHtml(r.status) + '</span>' +
        '</div>' +
        
        '<div class="grid grid-cols-2 gap-4">' +
            '<div class="bg-slate-50 border border-slate-100 rounded-xl p-4">' +
                '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"><span class="material-symbols-outlined text-[12px] align-middle mr-1">favorite</span> Interests</p>' +
                '<p class="text-sm font-semibold text-slate-800">' + (escHtml(r.interests) || '—') + '</p>' +
            '</div>' +
            '<div class="bg-slate-50 border border-slate-100 rounded-xl p-4">' +
                '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"><span class="material-symbols-outlined text-[12px] align-middle mr-1">bed</span> Stay Preference</p>' +
                '<p class="text-sm font-semibold text-slate-800">' + (escHtml(r.stay_type) || '—') + '</p>' +
            '</div>' +
            '<div class="bg-slate-50 border border-slate-100 rounded-xl p-4">' +
                '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"><span class="material-symbols-outlined text-[12px] align-middle mr-1">directions_car</span> Travel Mode</p>' +
                '<p class="text-sm font-semibold text-slate-800">' + (escHtml(r.travel_mode) || '—') + '</p>' +
            '</div>' +
            '<div class="bg-slate-50 border border-slate-100 rounded-xl p-4">' +
                '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"><span class="material-symbols-outlined text-[12px] align-middle mr-1">info</span> Travel Details</p>' +
                '<p class="text-sm font-semibold text-slate-800">' + (escHtml(r.travel_details) || '—') + '</p>' +
            '</div>' +
        '</div>' +
        
        (r.extra_notes ? 
        '<div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-xl">' +
            '<p class="text-[10px] font-bold text-orange-600 uppercase tracking-widest mb-2"><span class="material-symbols-outlined text-[12px] align-middle mr-1">sticky_note_2</span> Special Requests / Notes</p>' +
            '<p class="text-sm text-slate-700">' + escHtml(r.extra_notes) + '</p>' +
        '</div>' : '') +
        
        '<p class="text-xs text-slate-400 text-center font-medium mt-6">Requested on ' + r.created_at + '</p>';
        
    document.getElementById('request-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('request-modal').classList.add('hidden');
}

async function updateStatus(status) {
    if (!currentId) return;
    await api('../php/api/trip_requests.php?id=' + currentId, {
        method: 'PUT', body: JSON.stringify({ status: status })
    });
    closeModal();
    loadRequests();
}

async function deleteRequest(id) {
    if (!confirm('Are you sure you want to delete this trim request?')) return;
    await api('../php/api/trip_requests.php?id=' + id, { method: 'DELETE' });
    loadRequests();
}

document.getElementById('search-input').addEventListener('input', function(){
    clearTimeout(window._st);
    window._st = setTimeout(loadRequests, 400);
});

filterStatus('all');
</script>
JS;
require 'admin-footer.php';
?>
