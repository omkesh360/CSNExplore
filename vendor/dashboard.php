<?php
$vendor_page  = 'dashboard';
$vendor_title = 'Dashboard | Vendor Portal';
require 'vendor-header.php';
?>

<style>
.page-header h1 { font-size: 28px; margin-bottom: 5px; }
.page-header p { font-size: 14px; color: #666; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center; }
.stat-number { font-size: 32px; font-weight: bold; color: #ec5b13; margin: 10px 0; }
.stat-label { font-size: 14px; color: #666; }
.stat-icon { font-size: 32px; color: #ec5b13; }
.section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.section h2 { font-size: 18px; margin-bottom: 15px; color: #333; }
.listing-item { display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
.listing-item:last-child { border-bottom: none; }
.listing-icon { font-size: 32px; color: #ec5b13; }
.listing-info { flex: 1; }
.listing-title { font-weight: 500; color: #333; }
.listing-subtitle { font-size: 12px; color: #666; margin-top: 3px; }
.listing-price { font-weight: bold; color: #ec5b13; }
.empty-state { text-align: center; padding: 40px 20px; color: #999; }
.empty-state p { margin: 10px 0; }
.btn { background: #ec5b13; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block; }
.btn:hover { background: #d94a0f; }
</style>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Welcome to your vendor dashboard</p>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🏨</div>
        <div class="stat-number" id="stat-stays">0</div>
        <div class="stat-label">Hotel & Stays</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🛏️</div>
        <div class="stat-number" id="stat-rooms">0</div>
        <div class="stat-label">Rooms</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🚗</div>
        <div class="stat-number" id="stat-cars">0</div>
        <div class="stat-label">Cars</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-number" id="stat-bookings">0</div>
        <div class="stat-label">Bookings</div>
    </div>
</div>

<!-- Recent Stays -->
<div class="section">
    <h2>Recent Hotel & Stays</h2>
    <div id="recent-stays">
        <div class="empty-state">
            <p>No stays yet</p>
            <a href="<?php echo VENDOR_API_BASE; ?>/vendor/stays.php" class="btn">Add Your First Stay</a>
        </div>
    </div>
</div>

<!-- Recent Cars -->
<div class="section">
    <h2>Recent Cars</h2>
    <div id="recent-cars">
        <div class="empty-state">
            <p>No cars yet</p>
            <a href="<?php echo VENDOR_API_BASE; ?>/vendor/cars.php" class="btn">Add Your First Car</a>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="section">
    <h2>Recent Bookings</h2>
    <div id="recent-bookings">
        <div class="empty-state">
            <p>No bookings yet</p>
        </div>
    </div>
</div>

</div> <!-- End main-content -->

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';

async function loadDashboard() {
    // Load statistics
    const stats = await vendorApi(`${BASE}/php/api/vendor-profile.php?action=summary`);
    if (stats) {
        document.getElementById('stat-stays').textContent = stats.stays || 0;
        document.getElementById('stat-rooms').textContent = stats.rooms || 0;
        document.getElementById('stat-cars').textContent = stats.cars || 0;
        document.getElementById('stat-bookings').textContent = stats.bookings || 0;
    }

    // Load recent stays
    const staysData = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=list&limit=3`);
    if (staysData?.stays?.length) {
        document.getElementById('recent-stays').innerHTML = staysData.stays.map(s => `
            <div class="listing-item">
                <div class="listing-icon">🏨</div>
                <div class="listing-info">
                    <div class="listing-title">${s.name}</div>
                    <div class="listing-subtitle">📍 ${s.location}</div>
                </div>
                <div class="listing-price">${fmt(s.price_per_night)}/night</div>
            </div>
        `).join('');
    }

    // Load recent cars
    const carsData = await vendorApi(`${BASE}/php/api/vendor-cars.php?action=list&limit=3`);
    if (carsData?.cars?.length) {
        document.getElementById('recent-cars').innerHTML = carsData.cars.map(c => `
            <div class="listing-item">
                <div class="listing-icon">🚗</div>
                <div class="listing-info">
                    <div class="listing-title">${c.name}</div>
                    <div class="listing-subtitle">📍 ${c.location}</div>
                </div>
                <div class="listing-price">${fmt(c.price_per_day)}/day</div>
            </div>
        `).join('');
    }

    // Load recent bookings
    const bookingsData = await vendorApi(`${BASE}/php/api/vendor-stays.php?action=bookings&limit=3`);
    if (bookingsData?.bookings?.length) {
        document.getElementById('recent-bookings').innerHTML = bookingsData.bookings.map(b => `
            <div class="listing-item">
                <div class="listing-icon">📅</div>
                <div class="listing-info">
                    <div class="listing-title">${b.full_name || 'Guest'}</div>
                    <div class="listing-subtitle">${b.listing_name || 'Booking'}</div>
                </div>
                <div class="listing-price">${b.status}</div>
            </div>
        `).join('');
    }
}

loadDashboard();
</script>

<?php require 'vendor-footer.php'; ?>
