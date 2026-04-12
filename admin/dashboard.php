<?php
$admin_page  = 'dashboard';
$admin_title = 'Dashboard | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">System Overview</h2>
            <p class="text-xs text-slate-500 font-medium">Dashboard metrics and recent activity</p>
        </div>
        <button onclick="loadDashboard()" class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-all text-slate-600">
            <span class="material-symbols-outlined text-sm">refresh</span> Refresh
        </button>
    </div>

    <!-- Stats grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="stats-grid">
        <?php
        $cards = [
            ['id'=>'stat-bookings', 'icon'=>'confirmation_number', 'label'=>'Total Bookings',  'bg'=>'bg-blue-50'],
            ['id'=>'stat-pending',  'icon'=>'pending_actions',     'label'=>'Pending Orders',  'bg'=>'bg-orange-50'],
            ['id'=>'stat-users',    'icon'=>'group',               'label'=>'Registered Users','bg'=>'bg-green-50'],
            ['id'=>'stat-blogs',    'icon'=>'article',             'label'=>'Published Blogs', 'bg'=>'bg-purple-50'],
        ];
        foreach ($cards as $card): ?>
        <div class="admin-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 <?php echo $card['bg']; ?> rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-slate-600 text-[24px]"><?php echo $card['icon']; ?></span>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-0.5"><?php echo $card['label']; ?></p>
                <h3 id="<?php echo $card['id']; ?>" class="text-xl font-bold text-slate-900">—</h3>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Inventory -->
        <div class="lg:col-span-1 admin-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-800">Inventory Status</h3>
                <span class="material-symbols-outlined text-slate-400">inventory_2</span>
            </div>
            <div class="space-y-3" id="listings-grid">
                <?php
                $cats = [
                    ['key'=>'stays',       'icon'=>'bed',                'label'=>'Hotels',    'bg'=>'bg-primary/10', 'text'=>'text-primary'],
                    ['key'=>'cars',        'icon'=>'directions_car',     'label'=>'Cars',      'bg'=>'bg-blue-50',    'text'=>'text-blue-600'],
                    ['key'=>'bikes',       'icon'=>'motorcycle',         'label'=>'Bikes',     'bg'=>'bg-purple-50',  'text'=>'text-purple-600'],
                    ['key'=>'restaurants', 'icon'=>'restaurant',         'label'=>'Dining',    'bg'=>'bg-emerald-50', 'text'=>'text-emerald-600'],
                    ['key'=>'attractions', 'icon'=>'confirmation_number','label'=>'Attractions','bg'=>'bg-indigo-50',  'text'=>'text-indigo-600'],
                    ['key'=>'buses',       'icon'=>'directions_bus',     'label'=>'Buses',     'bg'=>'bg-orange-50',  'text'=>'text-orange-600'],
                ];
                foreach ($cats as $cat): ?>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-white transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 <?php echo $cat['bg']; ?> <?php echo $cat['text']; ?> rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg"><?php echo $cat['icon']; ?></span>
                        </div>
                        <span class="text-sm font-medium text-slate-700"><?php echo $cat['label']; ?></span>
                    </div>
                    <span id="stat-<?php echo $cat['key']; ?>" class="text-sm font-bold text-slate-900">—</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 admin-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-800">Recent Activity</h3>
                <a href="bookings.php" class="text-xs font-semibold text-primary hover:underline">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-bold text-slate-400 border-b border-slate-100">
                            <th class="pb-3 px-2">Customer</th>
                            <th class="pb-3 px-2">Service</th>
                            <th class="pb-3 px-2 text-center">Status</th>
                            <th class="pb-3 px-2 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody id="recent-bookings" class="divide-y divide-slate-50">
                        <tr><td colspan="4" class="py-10 text-center text-slate-400">Loading activity...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<?php
$extra_js = <<<'JS'
<script>
async function loadDashboard() {
    var data = await api('../php/api/dashboard.php');
    if (!data) return;

    document.getElementById('stat-bookings').textContent = data.bookings?.total ?? 0;
    document.getElementById('stat-pending').textContent  = data.bookings?.pending ?? 0;
    document.getElementById('stat-users').textContent    = data.users ?? 0;
    document.getElementById('stat-blogs').textContent    = data.blogs ?? 0;

    var cats = ['stays','cars','bikes','restaurants','attractions','buses'];
    cats.forEach(function(c) {
        var el = document.getElementById('stat-' + c);
        if (el) el.textContent = data.listings?.[c] ?? 0;
    });

    var tbody = document.getElementById('recent-bookings');
    var bookings = data.recent_bookings || [];
    if (!bookings.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-400 text-sm">No bookings yet</td></tr>';
        return;
    }
    tbody.innerHTML = bookings.map(function(b) {
        var statusColor = b.status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : b.status === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100';
        return '<tr class="hover:bg-slate-50 transition-colors group">' +
            '<td class="py-4 px-2">' +
                '<p class="font-bold text-slate-800">' + escHtml(b.full_name) + '</p>' +
                '<p class="text-[10px] text-slate-400 font-semibold">' + escHtml(b.phone) + '</p>' +
            '</td>' +
            '<td class="py-4 px-2">' +
                '<p class="text-[13px] font-bold text-slate-600">' + escHtml(b.listing_name || b.service_type || '—') + '</p>' +
                '<p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">' + escHtml(b.service_type) + '</p>' +
            '</td>' +
            '<td class="py-4 px-2 text-center">' +
                '<span class="inline-block px-3 py-1 rounded-md text-[10px] font-bold uppercase border ' + statusColor + '">' + escHtml(b.status) + '</span>' +
            '</td>' +
            '<td class="py-4 px-2 text-right">' +
                '<p class="text-[13px] font-bold text-slate-700">' + (b.booking_date || b.created_at?.split(' ')[0] || '—') + '</p>' +
            '</td>' +
        '</tr>';
    }).join('');
}

function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadDashboard();
</script>
JS;
require 'admin-footer.php';
?>
