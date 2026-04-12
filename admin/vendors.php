<?php
$admin_page = 'vendors';
$admin_title = 'Vendor Management | CSNExplore Admin';
$extra_head = '';
require 'admin-header.php';
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Vendor Management</h2>
        <p class="text-sm text-slate-500 mt-1">Manage vendor accounts and permissions</p>
    </div>
    <button onclick="openVendorModal()" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-lg">add</span> Add Vendor
    </button>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="admin-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Total Vendors</p>
                <p id="stat-total" class="text-3xl font-bold text-slate-900">0</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-2xl">store</span>
            </div>
        </div>
    </div>
    <div class="admin-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Active Vendors</p>
                <p id="stat-active" class="text-3xl font-bold text-green-600">0</p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            </div>
        </div>
    </div>
    <div class="admin-card p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Total Listings</p>
                <p id="stat-listings" class="text-3xl font-bold text-primary">0</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">inventory_2</span>
            </div>
        </div>
    </div>
</div>

<!-- Vendors Table -->
<div class="admin-card">
    <div class="p-5 border-b border-slate-100">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900">All Vendors</h3>
            <div class="flex items-center gap-3">
                <input type="text" id="search-input" placeholder="Search vendors..." 
                       class="px-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Vendor</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Username</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Contact</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Listings</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Joined</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="vendors-tbody" class="divide-y divide-slate-100">
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2">hourglass_empty</span>
                        <p class="text-sm">Loading vendors...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Vendor Modal -->
<div id="vendor-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 id="modal-title" class="text-xl font-bold text-slate-900">Add New Vendor</h3>
            <button onclick="closeVendorModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="vendor-form" class="p-6 space-y-5">
            <input type="hidden" id="vendor-id"/>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name *</label>
                    <input type="text" id="vendor-name" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username *</label>
                    <input type="text" id="vendor-username" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="vendor-email"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phone</label>
                    <input type="tel" id="vendor-phone"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Business Name</label>
                <input type="text" id="vendor-business"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password <span id="password-hint" class="text-slate-400 font-normal">(leave blank to keep current)</span></label>
                <input type="password" id="vendor-password"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                <select id="vendor-status"
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-primary-dark transition-all">
                    <span id="submit-text">Create Vendor</span>
                </button>
                <button type="button" onclick="closeVendorModal()" class="px-5 py-3 border border-slate-200 rounded-xl font-bold text-slate-600 hover:bg-slate-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let vendors = [];

async function loadVendors() {
    const data = await api('../php/api/vendors.php?action=list');
    if (!data || !data.vendors) return;
    
    vendors = data.vendors;
    renderVendors(vendors);
    updateStats();
}

function updateStats() {
    const total = vendors.length;
    const active = vendors.filter(v => v.status === 'active').length;
    const listings = vendors.reduce((sum, v) => sum + parseInt(v.stays_count || 0) + parseInt(v.cars_count || 0), 0);
    
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-active').textContent = active;
    document.getElementById('stat-listings').textContent = listings;
}

function renderVendors(list) {
    const tbody = document.getElementById('vendors-tbody');
    
    if (list.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2">store</span>
                    <p class="text-sm">No vendors found</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = list.map(v => `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">store</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 text-sm">${v.name}</p>
                        ${v.business_name ? `<p class="text-xs text-slate-500">${v.business_name}</p>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-5 py-4">
                <span class="text-sm text-slate-600 font-mono">${v.username}</span>
            </td>
            <td class="px-5 py-4">
                <div class="text-sm text-slate-600">
                    ${v.email ? `<p>${v.email}</p>` : ''}
                    ${v.phone ? `<p class="text-xs text-slate-500">${v.phone}</p>` : ''}
                </div>
            </td>
            <td class="px-5 py-4">
                <div class="flex gap-2">
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded">${v.stays_count || 0} Stays</span>
                    <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded">${v.cars_count || 0} Cars</span>
                </div>
            </td>
            <td class="px-5 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold ${v.status === 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}">
                    ${v.status}
                </span>
            </td>
            <td class="px-5 py-4 text-sm text-slate-500">
                ${new Date(v.created_at).toLocaleDateString()}
            </td>
            <td class="px-5 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="loginAsVendor(${v.id}, '${v.name}')" class="p-2 text-slate-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Login as Vendor">
                        <span class="material-symbols-outlined text-lg">login</span>
                    </button>
                    <button onclick="editVendor(${v.id})" class="p-2 text-slate-600 hover:text-primary hover:bg-primary/5 rounded-lg transition-all" title="Edit">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    <button onclick="deleteVendor(${v.id}, '${v.name}')" class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openVendorModal(vendorId = null) {
    document.getElementById('vendor-modal').classList.remove('hidden');
    document.getElementById('vendor-form').reset();
    document.getElementById('vendor-id').value = '';
    
    if (vendorId) {
        document.getElementById('modal-title').textContent = 'Edit Vendor';
        document.getElementById('submit-text').textContent = 'Update Vendor';
        document.getElementById('password-hint').classList.remove('hidden');
        document.getElementById('vendor-password').removeAttribute('required');
        
        const vendor = vendors.find(v => v.id == vendorId);
        if (vendor) {
            document.getElementById('vendor-id').value = vendor.id;
            document.getElementById('vendor-name').value = vendor.name;
            document.getElementById('vendor-username').value = vendor.username;
            document.getElementById('vendor-email').value = vendor.email || '';
            document.getElementById('vendor-phone').value = vendor.phone || '';
            document.getElementById('vendor-business').value = vendor.business_name || '';
            document.getElementById('vendor-status').value = vendor.status;
        }
    } else {
        document.getElementById('modal-title').textContent = 'Add New Vendor';
        document.getElementById('submit-text').textContent = 'Create Vendor';
        document.getElementById('password-hint').classList.add('hidden');
        document.getElementById('vendor-password').setAttribute('required', 'required');
    }
}

function closeVendorModal() {
    document.getElementById('vendor-modal').classList.add('hidden');
}

async function editVendor(id) {
    openVendorModal(id);
}

async function loginAsVendor(id, name) {
    if (!confirm(`Are you sure you want to log in as vendor "${name}"?`)) return;

    const data = await api(`../php/api/vendors.php?action=login_as&id=${id}`);
    if (data && data.success) {
        localStorage.setItem('csn_vendor_token', data.token);
        localStorage.setItem('csn_vendor_user', JSON.stringify(data.vendor));
        window.open('../vendor/dashboard.php', '_blank'); 
    } else {
        showAdminToast(data?.error || 'Failed to generate vendor session', 'error');
    }
}

async function deleteVendor(id, name) {
    if (!confirm(`Are you sure you want to delete vendor "${name}"?\n\nThis action cannot be undone.`)) return;
    
    const data = await api(`../php/api/vendors.php?action=delete&id=${id}`, { method: 'DELETE' });
    if (data && data.success) {
        showAdminToast('Vendor deleted successfully');
        loadVendors();
    } else {
        showAdminToast(data?.error || 'Failed to delete vendor', 'error');
    }
}

document.getElementById('vendor-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('vendor-id').value;
    const action = id ? 'update' : 'create';
    
    const payload = {
        name: document.getElementById('vendor-name').value.trim(),
        username: document.getElementById('vendor-username').value.trim(),
        email: document.getElementById('vendor-email').value.trim(),
        phone: document.getElementById('vendor-phone').value.trim(),
        business_name: document.getElementById('vendor-business').value.trim(),
        status: document.getElementById('vendor-status').value
    };
    
    const password = document.getElementById('vendor-password').value;
    if (password) payload.password = password;
    if (id) payload.id = parseInt(id);
    
    const data = await api(`../php/api/vendors.php?action=${action}`, {
        method: 'POST',
        body: JSON.stringify(payload)
    });
    
    if (data && data.success) {
        showAdminToast(data.message);
        closeVendorModal();
        loadVendors();
    } else {
        showAdminToast(data?.error || 'Operation failed', 'error');
    }
});

// Search functionality
document.getElementById('search-input').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const filtered = vendors.filter(v => 
        v.name.toLowerCase().includes(query) ||
        v.username.toLowerCase().includes(query) ||
        (v.email && v.email.toLowerCase().includes(query)) ||
        (v.business_name && v.business_name.toLowerCase().includes(query))
    );
    renderVendors(filtered);
});

// Load on page load
loadVendors();
</script>

<?php require 'admin-footer.php'; ?>
