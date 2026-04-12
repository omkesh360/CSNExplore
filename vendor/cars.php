<?php
$vendor_page  = 'cars';
$vendor_title = 'Car Management | Vendor Portal';
require 'vendor-header.php';
?>

<style>
.page-content { background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.btn { background: #ec5b13; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold; }
.btn:hover { background: #d94a0f; }
.btn-secondary { background: #666; }
.btn-secondary:hover { background: #555; }
.btn-danger { background: #dc3545; }
.btn-danger:hover { background: #c82333; }
.search-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.search-bar input, .search-bar select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
.search-bar input { flex: 1; min-width: 200px; }
.listings { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.listing-card { background: white; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #ddd; }
.listing-image { width: 100%; height: 200px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px; }
.listing-content { padding: 15px; }
.listing-title { font-size: 16px; font-weight: bold; color: #333; margin: 0 0 5px 0; }
.listing-location { font-size: 13px; color: #666; margin: 0 0 8px 0; }
.listing-price { font-size: 18px; font-weight: bold; color: #ec5b13; margin: 10px 0; }
.listing-status { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; margin-bottom: 10px; }
.status-active { background: #d4edda; color: #155724; }
.status-inactive { background: #f8d7da; color: #721c24; }
.listing-actions { display: flex; gap: 8px; margin-top: 10px; }
.listing-actions button { flex: 1; padding: 8px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; }
.btn-edit { background: #007bff; color: white; }
.btn-edit:hover { background: #0056b3; }
.btn-delete { background: #dc3545; color: white; }
.btn-delete:hover { background: #c82333; }
.btn-toggle { background: #28a745; color: white; }
.btn-toggle:hover { background: #218838; }
.empty-state { background: white; padding: 40px; text-align: center; border-radius: 5px; }
.empty-state p { color: #666; margin: 10px 0; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
.modal.show { display: flex; align-items: center; justify-content: center; }
.modal-content { background: white; padding: 30px; border-radius: 5px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h2 { margin: 0; font-size: 22px; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; font-size: 14px; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: Arial, sans-serif; }
.form-group textarea { resize: vertical; min-height: 80px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.info-box { background: #e7f3ff; border-left: 4px solid #2196F3; padding: 12px; margin-bottom: 15px; border-radius: 4px; }
.info-box p { margin: 0; color: #1565c0; font-size: 13px; }
.info-box strong { display: block; margin-bottom: 5px; }
.toast { position: fixed; bottom: 20px; right: 20px; background: #333; color: white; padding: 15px 20px; border-radius: 4px; z-index: 2000; }
.toast.success { background: #28a745; }
.toast.error { background: #dc3545; }
@media (max-width: 768px) {
    .search-bar { flex-direction: column; }
    .search-bar input, .search-bar select { width: 100%; }
    .listings { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
}
</style>

<div class="page-content">
    <div class="page-header">
        <h1>Car Rental Management</h1>
        <p>Manage your car fleet</p>
    </div>

    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search by car name or location..." oninput="filterListings()">
        <select id="filter-status" onchange="filterListings()">
            <option value="">All Status</option>
            <option value="1">Available</option>
            <option value="0">Unavailable</option>
        </select>
        <button class="btn" onclick="openModal()">+ Add New Car</button>
    </div>

    <div id="listings-container" class="listings">
        <div class="empty-state">
            <p>Loading cars...</p>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Add New Car</h2>
            <button class="close-btn" onclick="closeModal()">×</button>
        </div>
        <form id="listing-form" onsubmit="submitForm(event)">
            <input type="hidden" id="listing-id">

            <div class="form-group">
                <label>Car Name *</label>
                <input type="text" id="name" required placeholder="e.g. Toyota Innova">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Car Type *</label>
                    <select id="type" required>
                        <option>Sedan</option>
                        <option>SUV</option>
                        <option>Hatchback</option>
                        <option>MUV</option>
                        <option>Luxury</option>
                        <option>Van</option>
                        <option>Electric</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location *</label>
                    <input type="text" id="location" required placeholder="e.g. Chhatrapati Sambhajinagar">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price per Day (₹) *</label>
                    <input type="number" id="price" min="1" step="0.01" required placeholder="e.g. 1500">
                </div>
                <div class="form-group">
                    <label>Fuel Type</label>
                    <select id="fuel">
                        <option>Petrol</option>
                        <option>Diesel</option>
                        <option>CNG</option>
                        <option>Electric</option>
                        <option>Hybrid</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Transmission</label>
                    <select id="transmission">
                        <option>Manual</option>
                        <option>Automatic</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Seating Capacity</label>
                    <input type="number" id="seats" min="2" max="20" value="5">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea id="description" placeholder="Describe your car..."></textarea>
            </div>

            <div class="form-group">
                <label>Features (comma-separated)</label>
                <input type="text" id="features" placeholder="e.g. AC, GPS, Bluetooth, Child Seat">
            </div>

            <div class="form-group">
                <label>Google Maps Embed Code</label>
                <textarea id="map-embed" placeholder="Paste your Google Maps embed code here. Get it from: https://www.google.com/maps → Share → Embed a map"></textarea>
                <div class="info-box">
                    <strong>How to get Google Maps embed code:</strong>
                    <p>1. Go to Google Maps and find your location<br>2. Click "Share" button<br>3. Click "Embed a map"<br>4. Copy the entire iframe code and paste it here</p>
                </div>
            </div>

            <div class="form-group">
                <div class="info-box">
                    <strong>📸 Images</strong>
                    <p>To add or update images for your car, please contact the website admin at admin@csnexplore.com</p>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="available" checked> Available for Booking
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="active" checked> Active (visible on website)
                </label>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Save Car</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';
let allListings = [];

async function loadListings() {
    const data = await vendorApi(`${BASE}/php/api/vendor-cars.php?action=list&limit=100`);
    if (!data) {
        document.getElementById('listings-container').innerHTML = '<div class="empty-state"><p>Error loading cars. Please refresh.</p></div>';
        return;
    }
    allListings = data.cars || [];
    renderListings(allListings);
}

function filterListings() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const status = document.getElementById('filter-status').value;
    const filtered = allListings.filter(c =>
        (!search || c.name.toLowerCase().includes(search) || (c.location || '').toLowerCase().includes(search)) &&
        (!status || String(c.is_available) === status)
    );
    renderListings(filtered);
}

function renderListings(listings) {
    const container = document.getElementById('listings-container');
    if (listings.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No cars found</p><button class="btn" onclick="openModal()">+ Add Your First Car</button></div>';
        return;
    }
    container.innerHTML = listings.map(c => `
        <div class="listing-card">
            <div class="listing-image">🚗 No Image</div>
            <div class="listing-content">
                <p class="listing-title">${c.name}</p>
                <p class="listing-location">📍 ${c.location}</p>
                <p class="listing-price">₹${parseFloat(c.price_per_day).toLocaleString()}/day</p>
                <span class="listing-status ${c.is_available ? 'status-active' : 'status-inactive'}">${c.is_available ? 'Available' : 'Unavailable'}</span>
                <div class="listing-actions">
                    <button class="btn-toggle" onclick="toggleAvailability(${c.id})">${c.is_available ? 'Mark Unavailable' : 'Mark Available'}</button>
                    <button class="btn-edit" onclick="editListing(${c.id})">Edit</button>
                    <button class="btn-delete" onclick="deleteListing(${c.id}, '${c.name.replace(/'/g, "\\'")}')" >Delete</button>
                </div>
            </div>
        </div>
    `).join('');
}

function openModal(id) {
    document.getElementById('modal').classList.add('show');
    document.getElementById('listing-form').reset();
    document.getElementById('listing-id').value = '';
    document.getElementById('modal-title').textContent = 'Add New Car';
    
    if (id) {
        const listing = allListings.find(c => c.id == id);
        if (!listing) return;
        document.getElementById('modal-title').textContent = 'Edit Car';
        document.getElementById('listing-id').value = listing.id;
        document.getElementById('name').value = listing.name || '';
        document.getElementById('type').value = listing.type || 'Sedan';
        document.getElementById('location').value = listing.location || '';
        document.getElementById('price').value = listing.price_per_day || '';
        document.getElementById('fuel').value = listing.fuel_type || 'Petrol';
        document.getElementById('transmission').value = listing.transmission || 'Manual';
        document.getElementById('seats').value = listing.seats || 5;
        document.getElementById('features').value = listing.features || '';
        document.getElementById('available').checked = !!listing.is_available;
        document.getElementById('active').checked = !!listing.is_active;
        
        vendorApi(`${BASE}/php/api/vendor-cars.php?action=get&id=${id}`).then(d => {
            if (d?.car) {
                document.getElementById('description').value = d.car.description || '';
                document.getElementById('map-embed').value = d.car.map_embed || '';
            }
        });
    }
}

function closeModal() {
    document.getElementById('modal').classList.remove('show');
}

function editListing(id) {
    openModal(id);
}

async function submitForm(e) {
    e.preventDefault();
    const id = document.getElementById('listing-id').value;
    const payload = {
        name: document.getElementById('name').value.trim(),
        type: document.getElementById('type').value,
        location: document.getElementById('location').value.trim(),
        price_per_day: parseFloat(document.getElementById('price').value) || 0,
        fuel_type: document.getElementById('fuel').value,
        transmission: document.getElementById('transmission').value,
        seats: parseInt(document.getElementById('seats').value) || 5,
        description: document.getElementById('description').value.trim(),
        features: document.getElementById('features').value.trim(),
        map_embed: document.getElementById('map-embed').value.trim(),
        is_available: document.getElementById('available').checked ? 1 : 0,
        is_active: document.getElementById('active').checked ? 1 : 0,
    };
    if (id) payload.id = parseInt(id);
    
    const action = id ? 'update' : 'create';
    const data = await vendorApi(`${BASE}/php/api/vendor-cars.php?action=${action}`, {
        method: 'POST',
        body: JSON.stringify(payload)
    });
    
    if (data?.success) {
        showToast(data.message || 'Car saved successfully', 'success');
        closeModal();
        loadListings();
    } else {
        showToast(data?.error || 'Failed to save car', 'error');
    }
}

async function toggleAvailability(id) {
    const data = await vendorApi(`${BASE}/php/api/vendor-cars.php?action=toggle_availability&id=${id}`);
    if (data?.success) {
        showToast('Availability updated', 'success');
        loadListings();
    } else {
        showToast(data?.error || 'Failed to update', 'error');
    }
}

async function deleteListing(id, name) {
    if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;
    const data = await vendorApi(`${BASE}/php/api/vendor-cars.php?action=delete&id=${id}`);
    if (data?.success) {
        showToast('Car deleted', 'success');
        loadListings();
    } else {
        showToast(data?.error || 'Failed to delete', 'error');
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

loadListings();
</script>

<?php require 'vendor-footer.php'; ?>
