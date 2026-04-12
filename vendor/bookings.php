<?php
$vendor_page  = 'bookings';
$vendor_title = 'Bookings | Vendor Portal';
require 'vendor-header.php';
?>

<style>
.page-header h1 { font-size: 28px; margin-bottom: 5px; }
.page-header p { font-size: 14px; color: #666; }
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
.stat-box { background: white; padding: 15px; border-radius: 5px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.stat-number { font-size: 24px; font-weight: bold; color: #ec5b13; }
.stat-label { font-size: 12px; color: #666; margin-top: 5px; }
.filters { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; display: flex; gap: 10px; flex-wrap: wrap; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.filters input, .filters select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
.filters input { flex: 1; min-width: 200px; }
.bookings-table { background: white; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
.table-header { display: grid; grid-template-columns: 2fr 2fr 1fr 1fr 1fr; gap: 15px; padding: 15px; background: #f5f5f5; font-weight: 500; font-size: 13px; color: #666; border-bottom: 1px solid #ddd; }
.table-row { display: grid; grid-template-columns: 2fr 2fr 1fr 1fr 1fr; gap: 15px; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f9f9f9; }
.guest-name { font-weight: 500; color: #333; }
.guest-contact { font-size: 12px; color: #666; margin-top: 3px; }
.listing-name { color: #333; }
.listing-type { font-size: 12px; color: #666; }
.status-badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 500; }
.status-pending { background: #fff3cd; color: #856404; }
.status-completed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.empty-state { text-align: center; padding: 40px 20px; color: #999; }
.empty-state p { margin: 10px 0; }
@media (max-width: 768px) {
    .table-header, .table-row { grid-template-columns: 1fr; }
    .filters { flex-direction: column; }
    .filters input { min-width: auto; }
}
</style>

<div class="page-header">
    <h1>Bookings</h1>
    <p>All customer bookings on your listings</p>
</div>

<!-- Statistics -->
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-number" id="stat-total">0</div>
        <div class="stat-label">Total Bookings</div>
    </div>
    <div class="stat-box">
        <div class="stat-number" id="stat-pending">0</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-box">
        <div class="stat-number" id="stat-completed">0</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-box">
        <div class="stat-number" id="stat-cancelled">0</div>
        <div class="stat-label">Cancelled</div>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <input type="text" id="search-input" placeholder="Search by guest name or listing..." oninput="filterBookings()">
    <select id="filter-status" onchange="filterBookings()">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

<!-- Bookings Table -->
<div class="bookings-table">
    <div class="table-header">
        <div>Guest</div>
        <div>Listing</div>
        <div>Dates</div>
        <div>Guests</div>
        <div>Status</div>
    </div>
    <div id="bookings-list">
        <div class="empty-state">
            <p>Loading bookings...</p>
        </div>
    </div>
</div>

</div> <!-- End main-content -->

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';
let allBookings = [];

async function loadBookings() {
    const data = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=bookings&limit=200`);
    
    if (!data) {
        document.getElementById('bookings-list').innerHTML = '<div class="empty-state"><p>Error loading bookings</p></div>';
        return;
    }
    
    allBookings = data.bookings || [];
    
    // Update statistics
    document.getElementById('stat-total').textContent = data.total || 0;
    const pending = allBookings.filter(b => b.status === 'pending').length;
    const completed = allBookings.filter(b => b.status === 'completed').length;
    const cancelled = allBookings.filter(b => b.status === 'cancelled').length;
    
    document.getElementById('stat-pending').textContent = pending;
    document.getElementById('stat-completed').textContent = completed;
    document.getElementById('stat-cancelled').textContent = cancelled;
    
    renderBookings(allBookings);
}

function filterBookings() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    
    const filtered = allBookings.filter(b =>
        (!search || (b.full_name || '').toLowerCase().includes(search) || (b.listing_name || '').toLowerCase().includes(search)) &&
        (!status || b.status === status)
    );
    
    renderBookings(filtered);
}

function renderBookings(bookings) {
    const container = document.getElementById('bookings-list');
    
    if (bookings.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No bookings found</p></div>';
        return;
    }
    
    const statusClass = (s) => {
        if (s === 'completed') return 'status-completed';
        if (s === 'cancelled') return 'status-cancelled';
        return 'status-pending';
    };
    
    container.innerHTML = bookings.map(b => `
        <div class="table-row">
            <div>
                <div class="guest-name">${b.full_name || 'Guest'}</div>
                <div class="guest-contact">${b.phone || ''} ${b.email || ''}</div>
            </div>
            <div>
                <div class="listing-name">${b.listing_name || 'Booking'}</div>
                <div class="listing-type">${b.service_type || ''}</div>
            </div>
            <div>${b.checkin_date ? b.checkin_date + ' → ' + (b.checkout_date || '?') : (b.booking_date || '—')}</div>
            <div>${b.number_of_people || 1} ${b.number_of_people == 1 ? 'guest' : 'guests'}</div>
            <div><span class="status-badge ${statusClass(b.status)}">${b.status || 'pending'}</span></div>
        </div>
    `).join('');
}

loadBookings();
</script>

<?php require 'vendor-footer.php'; ?>
