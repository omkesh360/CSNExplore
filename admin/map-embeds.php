<?php
$admin_page  = 'map-embeds';
$admin_title = 'Manage Map Embeds | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Map Embeds</h2>
            <p class="text-xs text-slate-500 font-medium">Add Google Maps embed codes to listings</p>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col lg:flex-row gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <!-- Category tabs -->
        <div class="flex gap-1 p-1 bg-slate-100 rounded-lg overflow-x-auto w-full lg:w-auto no-scrollbar">
            <?php
            $cats = [
                ['key'=>'stays',       'icon'=>'bed',                'label'=>'Hotels'],
                ['key'=>'cars',        'icon'=>'directions_car',     'label'=>'Cars'],
                ['key'=>'bikes',       'icon'=>'motorcycle',         'label'=>'Bikes'],
                ['key'=>'restaurants', 'icon'=>'restaurant',         'label'=>'Dining'],
                ['key'=>'attractions', 'icon'=>'confirmation_number','label'=>'Attractions'],
                ['key'=>'buses',       'icon'=>'directions_bus',     'label'=>'Buses'],
            ];
            foreach ($cats as $cat): ?>
            <button onclick="switchCat('<?php echo $cat['key']; ?>')"
                    data-cat="<?php echo $cat['key']; ?>"
                    class="cat-tab flex items-center gap-1.5 px-4 py-2 rounded-md text-xs font-semibold whitespace-nowrap transition-all">
                <span class="material-symbols-outlined text-lg"><?php echo $cat['icon']; ?></span>
                <?php echo $cat['label']; ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="flex-1 w-full relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input id="search-input" type="text" placeholder="Search by name or location..."
                   class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary/20 focus:border-primary transition-all"/>
        </div>
    </div>

    <!-- Table -->
    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 bg-slate-50 border-b border-slate-100">
                        <th class="py-3 px-4 text-left w-16">#</th>
                        <th class="py-3 px-4 text-left">Name</th>
                        <th class="py-3 px-4 text-left">Location</th>
                        <th class="py-3 px-4 text-left">Map Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="listings-tbody" class="divide-y divide-slate-50">
                    <tr><td colspan="5" class="text-center py-12 text-slate-400">Loading listings...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Map Modal -->
<div id="map-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modal-title" class="text-base font-bold">Edit Map Embed</h3>
            <button onclick="closeMapModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="map-form" class="p-6 space-y-4">
            <input type="hidden" id="edit-category"/>
            <input type="hidden" id="edit-id"/>
            
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Listing Name</label>
                <p id="listing-name" class="text-sm font-medium text-slate-800"></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">Google Maps Embed Code</label>
                <p class="text-xs text-slate-500 mb-3">
                    Get embed code from Google Maps: Right-click location → Share → Embed a map → Copy HTML
                </p>
                <textarea id="map-embed" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary" rows="6" placeholder="Paste the iframe code here..."></textarea>
            </div>

            <div class="flex gap-3 justify-end pt-4">
                <button type="button" onclick="closeMapModal()" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-semibold hover:bg-slate-50 transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-orange-600 transition-all">
                    Save Map
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.cat-tab { background: transparent; color: #64748b; }
.cat-tab.active { background: white; color: #ec5b13; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.admin-card { background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
</style>

<script>
let currentCategory = 'stays';
let allListings = {};

async function loadListings(category) {
    try {
        const response = await fetch(`../php/api/listings.php?category=${category}`);
        const data = await response.json();
        
        if (data.success) {
            allListings[category] = data.listings || [];
            renderTable();
        }
    } catch (error) {
        console.error('Error loading listings:', error);
    }
}

function switchCat(cat) {
    currentCategory = cat;
    document.querySelectorAll('.cat-tab').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.cat === cat);
    });
    loadListings(cat);
}

function renderTable() {
    const tbody = document.getElementById('listings-tbody');
    const listings = allListings[currentCategory] || [];
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    const filtered = listings.filter(l => 
        (l.name || l.operator || '').toLowerCase().includes(searchTerm) ||
        (l.location || '').toLowerCase().includes(searchTerm)
    );
    
    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-slate-400">No listings found</td></tr>';
        return;
    }
    
    tbody.innerHTML = filtered.map((listing, idx) => `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-3 px-4 text-slate-600">${listing.id}</td>
            <td class="py-3 px-4">
                <div class="font-semibold text-slate-800">${listing.name || listing.operator}</div>
            </td>
            <td class="py-3 px-4 text-slate-600">${listing.location || '-'}</td>
            <td class="py-3 px-4">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold ${listing.map_embed ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
                    <span class="material-symbols-outlined text-sm">${listing.map_embed ? 'check_circle' : 'pending'}</span>
                    ${listing.map_embed ? 'Added' : 'Missing'}
                </span>
            </td>
            <td class="py-3 px-4 text-right">
                <button onclick="openMapModal('${currentCategory}', ${listing.id}, '${(listing.name || listing.operator).replace(/'/g, "\\'")}', '${(listing.map_embed || '').replace(/'/g, "\\'")}')" 
                        class="text-primary hover:text-orange-600 font-semibold text-sm">
                    Edit Map
                </button>
            </td>
        </tr>
    `).join('');
}

function openMapModal(category, id, name, mapEmbed) {
    document.getElementById('edit-category').value = category;
    document.getElementById('edit-id').value = id;
    document.getElementById('listing-name').textContent = name;
    document.getElementById('map-embed').value = mapEmbed;
    document.getElementById('map-modal').classList.remove('hidden');
}

function closeMapModal() {
    document.getElementById('map-modal').classList.add('hidden');
}

document.getElementById('map-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const category = document.getElementById('edit-category').value;
    const id = document.getElementById('edit-id').value;
    const mapEmbed = document.getElementById('map-embed').value;
    
    try {
        const response = await fetch('../php/api/update-map-embed.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + (localStorage.getItem('csn_admin_token') || '')
            },
            body: JSON.stringify({ category, id, map_embed: mapEmbed })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Map embed updated successfully!');
            closeMapModal();
            loadListings(category);
        } else {
            alert('Error: ' + (result.error || 'Failed to update'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating map embed');
    }
});

document.getElementById('search-input').addEventListener('input', renderTable);

// Initialize
document.querySelectorAll('.cat-tab')[0].classList.add('active');
loadListings('stays');
</script>

<?php require 'admin-footer.php'; ?>
