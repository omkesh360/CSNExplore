<?php
$vendor_page  = 'rooms';
$vendor_title = 'Room Management | Vendor Portal';
require 'vendor-header.php';
?>

<style>
.page-header h1 { font-size: 28px; margin-bottom: 5px; }
.page-header p { font-size: 14px; color: #666; }
.header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.btn { background: #ec5b13; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; }
.btn:hover { background: #d94a0f; }
.btn-secondary { background: #666; }
.btn-secondary:hover { background: #555; }
.btn-small { padding: 6px 12px; font-size: 12px; }
.room-types { display: grid; gap: 20px; }
.room-type-card { background: white; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
.room-type-header { background: #f5f5f5; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
.room-type-title { font-size: 16px; font-weight: 500; color: #333; }
.room-type-info { font-size: 12px; color: #666; margin-top: 3px; }
.room-type-actions { display: flex; gap: 8px; }
.room-type-actions button { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; }
.btn-add-room { background: #28a745; color: white; }
.btn-add-room:hover { background: #218838; }
.btn-edit { background: #007bff; color: white; }
.btn-edit:hover { background: #0056b3; }
.btn-delete { background: #dc3545; color: white; }
.btn-delete:hover { background: #c82333; }
.rooms-list { padding: 15px; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
.room-item { background: #f9f9f9; padding: 12px; border-radius: 4px; border: 1px solid #eee; }
.room-number { font-weight: 500; color: #333; }
.room-details { font-size: 12px; color: #666; margin-top: 3px; }
.room-status { display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 11px; font-weight: 500; margin-top: 5px; }
.status-available { background: #d4edda; color: #155724; }
.status-occupied { background: #f8d7da; color: #721c24; }
.status-maintenance { background: #fff3cd; color: #856404; }
.room-actions { display: flex; gap: 5px; margin-top: 8px; }
.room-actions button { flex: 1; padding: 5px; font-size: 11px; border: none; border-radius: 3px; cursor: pointer; }
.empty-state { text-align: center; padding: 40px 20px; color: #999; }
.empty-state p { margin: 10px 0; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
.modal.show { display: flex; align-items: center; justify-content: center; }
.modal-content { background: white; padding: 30px; border-radius: 5px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h2 { margin: 0; font-size: 20px; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #666; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 5px; }
.form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: Arial, sans-serif; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
</style>

<div class="page-header">
    <h1>Room Management</h1>
    <p>Manage your room types and individual rooms</p>
</div>

<div class="header-action">
    <div></div>
    <button class="btn" onclick="openTypeModal()">+ Add Room Type</button>
</div>

<div id="room-types-container" class="room-types">
    <div class="empty-state">
        <p>Loading room types...</p>
    </div>
</div>

<!-- Room Type Modal -->
<div id="type-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="type-modal-title">Add Room Type</h2>
            <button class="close-btn" onclick="closeTypeModal()">×</button>
        </div>
        <form id="type-form" onsubmit="submitTypeForm(event)">
            <input type="hidden" id="type-id">
            <div class="form-group">
                <label>Room Type Name *</label>
                <input type="text" id="type-name" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" id="type-desc">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Base Price (₹/night) *</label>
                    <input type="number" id="type-price" min="1" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Max Guests</label>
                    <input type="number" id="type-guests" min="1" value="2">
                </div>
            </div>
            <div class="form-group">
                <label>Amenities (comma-separated)</label>
                <input type="text" id="type-amenities" placeholder="AC, WiFi, TV">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Save Room Type</button>
                <button type="button" class="btn btn-secondary" onclick="closeTypeModal()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Room Modal -->
<div id="room-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="room-modal-title">Add Room</h2>
            <button class="close-btn" onclick="closeRoomModal()">×</button>
        </div>
        <form id="room-form" onsubmit="submitRoomForm(event)">
            <input type="hidden" id="room-id">
            <input type="hidden" id="room-type-id">
            <div class="form-row">
                <div class="form-group">
                    <label>Room Number *</label>
                    <input type="text" id="room-number" required placeholder="101">
                </div>
                <div class="form-group">
                    <label>Floor</label>
                    <input type="text" id="room-floor" placeholder="1st Floor">
                </div>
            </div>
            <div class="form-group">
                <label>Price Override (₹/night) - 0 to use type price</label>
                <input type="number" id="room-price" min="0" step="0.01" value="0">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="room-status">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Save Room</button>
                <button type="button" class="btn btn-secondary" onclick="closeRoomModal()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

</div> <!-- End main-content -->

<script>
const BASE = '<?php echo VENDOR_API_BASE; ?>';
let allRoomTypes = [];

async function loadRoomTypes() {
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=list`);
    if (!data) {
        document.getElementById('room-types-container').innerHTML = '<div class="empty-state"><p>Error loading room types</p></div>';
        return;
    }
    
    allRoomTypes = data.rooms || [];
    renderRoomTypes(allRoomTypes);
}

function renderRoomTypes(types) {
    const container = document.getElementById('room-types-container');
    
    if (types.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No room types yet</p><button class="btn" onclick="openTypeModal()">+ Add Your First Room Type</button></div>';
        return;
    }
    
    container.innerHTML = types.map(t => `
        <div class="room-type-card">
            <div class="room-type-header">
                <div>
                    <div class="room-type-title">${t.name}</div>
                    <div class="room-type-info">₹${parseFloat(t.base_price).toLocaleString()}/night · ${t.max_guests} guests · ${t.rooms_count || 0} rooms</div>
                </div>
                <div class="room-type-actions">
                    <button class="btn-add-room btn-small" onclick="openAddRoomModal(${t.id})">+ Add Room</button>
                    <button class="btn-edit btn-small" onclick="editType(${t.id})">Edit</button>
                    <button class="btn-delete btn-small" onclick="deleteType(${t.id}, '${t.name.replace(/'/g, "\\'")}')" >Delete</button>
                </div>
            </div>
            <div id="rooms-${t.id}" class="rooms-list">
                <p style="text-align: center; color: #999; font-size: 12px;">Loading rooms...</p>
            </div>
        </div>
    `).join('');
    
    types.forEach(t => loadRoomsUnderType(t.id));
}

async function loadRoomsUnderType(typeId) {
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=get&id=${typeId}`);
    const container = document.getElementById(`rooms-${typeId}`);
    
    if (!data || !data.rooms || data.rooms.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #999; font-size: 12px; padding: 15px;">No rooms added yet</p>';
        return;
    }
    
    container.innerHTML = data.rooms.map(r => `
        <div class="room-item">
            <div class="room-number">Room ${r.room_number}</div>
            <div class="room-details">${r.floor || 'N/A'} · ${r.price > 0 ? '₹' + parseFloat(r.price).toLocaleString() : 'Type price'}</div>
            <span class="room-status ${r.status === 'available' ? 'status-available' : r.status === 'maintenance' ? 'status-maintenance' : 'status-occupied'}">${r.status}</span>
            <div class="room-actions">
                <button class="btn-edit" onclick="editRoom(${r.id}, ${typeId})">Edit</button>
                <button class="btn-toggle" onclick="toggleRoomStatus(${r.id}, '${r.status}', ${typeId})" style="background: #17a2b8; color: white;">Toggle</button>
                <button class="btn-delete" onclick="deleteRoom(${r.id}, ${typeId})">Delete</button>
            </div>
        </div>
    `).join('');
}

function openTypeModal(id) {
    document.getElementById('type-modal').classList.add('show');
    document.getElementById('type-form').reset();
    document.getElementById('type-id').value = '';
    document.getElementById('type-modal-title').textContent = 'Add Room Type';
    
    if (id) {
        const t = allRoomTypes.find(x => x.id == id);
        if (!t) return;
        document.getElementById('type-modal-title').textContent = 'Edit Room Type';
        document.getElementById('type-id').value = t.id;
        document.getElementById('type-name').value = t.name;
        document.getElementById('type-desc').value = t.description || '';
        document.getElementById('type-price').value = t.base_price;
        document.getElementById('type-guests').value = t.max_guests;
        document.getElementById('type-amenities').value = t.amenities || '';
    }
}

function closeTypeModal() {
    document.getElementById('type-modal').classList.remove('show');
}

function editType(id) {
    openTypeModal(id);
}

async function submitTypeForm(e) {
    e.preventDefault();
    const id = document.getElementById('type-id').value;
    const action = id ? 'update_type' : 'create_type';
    const payload = {
        name: document.getElementById('type-name').value.trim(),
        description: document.getElementById('type-desc').value.trim(),
        base_price: parseFloat(document.getElementById('type-price').value) || 0,
        max_guests: parseInt(document.getElementById('type-guests').value) || 2,
        amenities: document.getElementById('type-amenities').value.trim(),
    };
    if (id) payload.id = parseInt(id);
    
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=${action}`, {
        method: 'POST',
        body: JSON.stringify(payload)
    });
    
    if (data?.success) {
        showToast(data.message || 'Room type saved', 'success');
        closeTypeModal();
        loadRoomTypes();
    } else {
        showToast(data?.error || 'Failed', 'error');
    }
}

async function deleteType(id, name) {
    if (!confirm(`Delete "${name}"?`)) return;
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=delete_type&id=${id}`);
    if (data?.success) {
        showToast('Room type deleted', 'success');
        loadRoomTypes();
    } else {
        showToast(data?.error || 'Failed', 'error');
    }
}

function openAddRoomModal(typeId) {
    document.getElementById('room-modal').classList.add('show');
    document.getElementById('room-form').reset();
    document.getElementById('room-id').value = '';
    document.getElementById('room-type-id').value = typeId;
    document.getElementById('room-modal-title').textContent = 'Add Room';
}

function closeRoomModal() {
    document.getElementById('room-modal').classList.remove('show');
}

async function editRoom(roomId, typeId) {
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=get&id=${typeId}`);
    if (!data || !data.rooms) return;
    const r = data.rooms.find(x => x.id == roomId);
    if (!r) return;
    
    document.getElementById('room-modal').classList.add('show');
    document.getElementById('room-modal-title').textContent = 'Edit Room';
    document.getElementById('room-id').value = r.id;
    document.getElementById('room-type-id').value = typeId;
    document.getElementById('room-number').value = r.room_number;
    document.getElementById('room-floor').value = r.floor || '';
    document.getElementById('room-price').value = r.price || 0;
    document.getElementById('room-status').value = r.status;
}

async function submitRoomForm(e) {
    e.preventDefault();
    const id = document.getElementById('room-id').value;
    const typeId = document.getElementById('room-type-id').value;
    const action = id ? 'update_room' : 'create_room';
    const payload = {
        room_type_id: parseInt(typeId),
        room_number: document.getElementById('room-number').value.trim(),
        floor: document.getElementById('room-floor').value.trim(),
        price: parseFloat(document.getElementById('room-price').value) || 0,
        status: document.getElementById('room-status').value,
    };
    if (id) payload.id = parseInt(id);
    
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=${action}`, {
        method: 'POST',
        body: JSON.stringify(payload)
    });
    
    if (data?.success) {
        showToast(data.message || 'Room saved', 'success');
        closeRoomModal();
        loadRoomsUnderType(typeId);
        loadRoomTypes();
    } else {
        showToast(data?.error || 'Failed', 'error');
    }
}

async function deleteRoom(roomId, typeId) {
    if (!confirm('Delete this room?')) return;
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=delete_room&id=${roomId}`);
    if (data?.success) {
        showToast('Room deleted', 'success');
        loadRoomsUnderType(typeId);
        loadRoomTypes();
    } else {
        showToast(data?.error || 'Failed', 'error');
    }
}

async function toggleRoomStatus(roomId, currentStatus, typeId) {
    // Cycle through statuses: available -> occupied -> maintenance -> available
    const statusCycle = { 'available': 'occupied', 'occupied': 'maintenance', 'maintenance': 'available' };
    const newStatus = statusCycle[currentStatus] || 'available';
    
    const data = await vendorApi(`${BASE}/php/api/vendor-rooms.php?action=update_room`, {
        method: 'POST',
        body: JSON.stringify({
            id: roomId,
            status: newStatus
        })
    });
    
    if (data?.success) {
        showToast(`Room status changed to ${newStatus}`, 'success');
        loadRoomsUnderType(typeId);
    } else {
        showToast(data?.error || 'Failed to update status', 'error');
    }
}

loadRoomTypes();
</script>

<?php require 'vendor-footer.php'; ?>
