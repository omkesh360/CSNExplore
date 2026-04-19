<?php
$admin_page  = 'bookings';
$admin_title = 'Bookings | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Booking Management</h2>
            <p class="text-xs text-slate-500 font-medium">Review and process customer reservations</p>
        </div>
        <button onclick="loadBookings()" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-all text-slate-600">
            <span class="material-symbols-outlined text-sm">refresh</span> Refresh
        </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex gap-1 p-1 bg-slate-100 rounded-lg">
            <?php foreach (['all'=>'All Bookings','pending'=>'Pending','completed'=>'Completed','cancelled'=>'Cancelled'] as $k=>$v): ?>
            <button onclick="filterStatus('<?php echo $k; ?>')" data-status="<?php echo $k; ?>"
                    class="status-tab px-4 py-1.5 rounded-md text-xs font-semibold transition-all">
                <?php echo $v; ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="flex-1 w-full relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input id="search-input" type="text" placeholder="Search by customer name, phone, or email..."
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
                        <th class="py-3 px-4 text-left">Service / Item</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookings-tbody" class="divide-y divide-slate-50">
                    <tr><td colspan="5" class="text-center py-20 text-slate-400 italic">Initiating data fetch...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="booking-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-bold">Booking Details</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="modal-body" class="p-6 space-y-4"></div>
        <div class="p-6 pt-0 flex gap-3">
            <button onclick="updateStatus('completed')" class="flex-1 bg-green-500 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-green-600 transition-all">Mark Completed</button>
            <button onclick="updateStatus('cancelled')" class="flex-1 bg-red-500 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-red-600 transition-all">Cancel</button>
            <button onclick="updateStatus('pending')" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Pending</button>
        </div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentStatus = 'all';
var currentBookingId = null;

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
    loadBookings();
}

async function loadBookings() {
    var search = document.getElementById('search-input').value;
    var url = '../php/api/bookings.php?';
    if (currentStatus !== 'all') url += 'status=' + currentStatus + '&';
    if (search) url += 'search=' + encodeURIComponent(search);
    var tbody = document.getElementById('bookings-tbody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    var items = await api(url);
    if (!items || !items.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-12 text-slate-400">No bookings found</td></tr>';
        return;
    }
    // Cache all loaded bookings for fast modal access
    items.forEach(function(b){ bookingsCache[b.id] = b; });
    tbody.innerHTML = items.map(function(b) {
        var statusColor = b.status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : b.status === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100';
        var img = b.listing_image ? '../' + b.listing_image : '../images/placeholder.jpg';
        return '<tr class="hover:bg-slate-50 transition-colors group cursor-pointer" onclick="openModal(' + b.id + ')">' +
            '<td class="py-4 px-6">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="w-10 h-10 bg-slate-100 rounded-lg flex-shrink-0 flex items-center justify-center font-bold text-slate-400 text-xs">' + b.full_name.charAt(0) + '</div>' +
                    '<div>' +
                        '<p class="font-bold text-slate-900">' + escHtml(b.full_name) + '</p>' +
                        '<p class="text-[10px] text-slate-400 font-semibold">' + escHtml(b.phone) + '</p>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-6">' +
                '<div class="flex items-center gap-3">' +
                    '<img src="' + img + '" class="w-10 h-10 rounded-lg object-cover border border-slate-100" onerror="this.src=\'../images/placeholder.jpg\'">' +
                    '<div>' +
                        '<p class="text-[13px] font-bold text-slate-700">' + escHtml(b.listing_name || b.service_type || '—') + '</p>' +
                        '<p class="text-[10px] text-primary uppercase font-bold tracking-wider">' + escHtml(b.service_type) + '</p>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-6 text-center">' +
                '<span class="inline-block px-3 py-1 rounded-md text-[10px] font-bold uppercase border ' + statusColor + '">' + escHtml(b.status) + '</span>' +
            '</td>' +
            '<td class="py-4 px-6 text-right">' +
                '<p class="text-[13px] font-bold text-slate-700">' + escHtml(b.booking_date || b.created_at?.split(' ')[0] || '—') + '</p>' +
                '<p class="text-[10px] text-slate-400 font-medium uppercase">' + (b.number_of_people || 1) + ' Guest(s)</p>' +
            '</td>' +
            '<td class="py-4 px-6 text-right">' +
                '<button onclick="event.stopPropagation(); deleteBooking(' + b.id + ')" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"><span class="material-symbols-outlined text-lg">delete</span></button>' +
            '</td>' +
        '</tr>';
    }).join('');
}

var bookingsCache = {};
async function openModal(id) {
    currentBookingId = id;
    // Use cached data if available, otherwise fetch single booking
    var b = bookingsCache[id];
    if (!b) {
        var all = await api('../php/api/bookings.php');
        (all || []).forEach(function(x){ bookingsCache[x.id] = x; });
        b = bookingsCache[id];
    }
    if (!b) return;
    var statusColor = b.status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : b.status === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100';
    var img = b.listing_image ? '../' + b.listing_image : '../images/placeholder.jpg';
    var catLabels = {stays:'Hotel Stay', cars:'Car Rental', bikes:'Bike Rental', restaurants:'Restaurant', attractions:'Attraction', buses:'Bus Transfer'};
    var catLabel = catLabels[b.service_type] || b.service_type || 'General Service';

    document.getElementById('modal-body').innerHTML =
        '<div class="relative -mx-6 -mt-6 h-56 mb-8 overflow-hidden rounded-t-2xl">' +
            '<img src="' + img + '" class="w-full h-full object-cover" onerror="this.src=\'../images/placeholder.jpg\'">' +
            '<div class="absolute inset-0 bg-slate-900/60"></div>' +
            '<div class="absolute bottom-6 left-6 right-6">' +
                '<p class="text-[10px] font-bold text-primary/80 uppercase tracking-widest mb-1.5">Reservation Detail</p>' +
                '<h4 class="text-xl font-bold text-white leading-tight">' + escHtml(b.listing_name || catLabel) + '</h4>' +
                '<div class="flex items-center gap-3 mt-4">' +
                    '<span class="px-2 py-1 bg-white/10 border border-white/20 rounded-md text-[10px] font-bold text-white uppercase tracking-wider">#' + b.id + '</span>' +
                    '<span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border ' + statusColor + '">' + b.status + '</span>' +
                '</div>' +
            '</div>' +
        '</div>' +
        '<div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-8">' +
            modalRow('Customer Name', b.full_name, 'person') +
            modalRow('Category', catLabel, 'category') +
            modalRow('Contact Phone', '<a href="tel:' + b.phone + '" class="text-primary hover:underline font-bold">' + b.phone + '</a>', 'call') +
            modalRow('Email Address', b.email || '—', 'mail') +
            modalRow('Booking Date', '<span class="text-slate-900 font-bold">' + (b.booking_date || b.created_at || '—') + '</span>', 'calendar_today') +
            modalRow('Guests', b.number_of_people + ' Person(s)', 'group') +
            modalRow('Booked On', b.created_at || '—', 'history') +
            modalRow('Last Update', b.updated_at || '—', 'update') +
        '</div>' +
        (b.notes ? '<div class="mt-10 p-5 bg-slate-50 rounded-3xl border border-slate-100 group-hover:bg-white transition-all">' +
            '<div class="flex items-center gap-2 mb-2">' +
                '<span class="material-symbols-outlined text-slate-400 text-sm">sticky_note_2</span>' +
                '<p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer Notes</p>' +
            '</div>' +
            '<p class="text-[13px] text-slate-600 leading-relaxed font-medium">' + escHtml(b.notes) + '</p>' +
        '</div>' : '');
    
    document.getElementById('booking-modal').classList.remove('hidden');
}

function modalRow(label, val, icon) {
    return '<div class="flex items-start gap-3">' +
        '<div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">' +
            '<span class="material-symbols-outlined text-slate-400 text-lg">' + icon + '</span>' +
        '</div>' +
        '<div class="min-w-0">' +
            '<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">' + label + '</p>' +
            '<p class="text-[13px] font-bold text-slate-800">' + val + '</p>' +
        '</div>' +
    '</div>';
}

function closeModal() {
    document.getElementById('booking-modal').classList.add('hidden');
}

async function updateStatus(status) {
    if (!currentBookingId) return;
    await api('../php/api/bookings.php?id=' + currentBookingId, {
        method: 'PUT', body: JSON.stringify({ status: status })
    });
    closeModal();
    loadBookings();
}

async function deleteBooking(id) {
    if (!confirm('Delete this booking?')) return;
    await api('../php/api/bookings.php?id=' + id, { method: 'DELETE' });
    loadBookings();
}

document.getElementById('search-input').addEventListener('input', function(){
    clearTimeout(window._st);
    window._st = setTimeout(loadBookings, 400);
});

filterStatus('all');
</script>
JS;
require 'admin-footer.php';
?>
