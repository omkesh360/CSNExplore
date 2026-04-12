<?php
$admin_page  = 'listings';
$admin_title = 'Manage Listings | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Listings Gallery</h2>
            <p class="text-xs text-slate-500 font-medium">Manage your platform inventory</p>
        </div>
        <button onclick="openAddModal()"
                class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-600 transition-all shadow-sm">
            Add New Listing
        </button>
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
            <input id="search-input" type="text" placeholder="Search by name, type, or location..."
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
                        <th class="py-3 px-4 text-left">Listing Info</th>
                        <th class="py-3 px-4 text-left">Location</th>
                        <th class="py-3 px-4 text-center">Price</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="listings-tbody" class="divide-y divide-slate-50">
                    <tr><td colspan="6" class="text-center py-12 text-slate-400">Loading listings...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="listing-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modal-title" class="text-base font-bold">Add Listing</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="listing-form" class="p-6 space-y-4">
            <input type="hidden" id="edit-id"/>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Name *</label>
                    <input id="f-name" type="text" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Type</label>
                    <input id="f-type" type="text" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Location *</label>
                    <input id="f-location" type="text" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1" id="price-label">Price *</label>
                    <input id="f-price" type="number" min="0" step="0.01" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Rating (0-5)</label>
                    <input id="f-rating" type="number" min="0" max="5" step="0.1" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Badge</label>
                    <input id="f-badge" type="text" placeholder="e.g. Top Rated" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Display Order</label>
                    <input id="f-display_order" type="number" min="0" value="0" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select id="f-active" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="1">Active</option>
                        <option value="0">Hidden</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Main Image</label>
                    <div class="flex gap-2 items-start">
                        <div class="flex-1">
                            <input id="f-image" type="text" placeholder="images/uploads/filename.jpg"
                                   class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                   oninput="updateMainPreview(this.value)"/>
                        </div>
                        <button type="button" onclick="openGalleryPicker(function(url){ document.getElementById('f-image').value=url; updateMainPreview(url); })"
                                class="flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all whitespace-nowrap">
                            <span class="material-symbols-outlined text-sm">photo_library</span> Pick
                        </button>
                    </div>
                    <div id="main-img-preview" class="mt-2 hidden">
                        <img id="main-img-preview-img" src="" class="h-24 rounded-xl object-cover border border-slate-200"/>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Gallery Images <span class="text-slate-400 font-normal">(up to 6)</span></label>
                    <div id="gallery-thumbs" class="flex flex-wrap gap-2 mb-2 min-h-[40px]"></div>
                    <button type="button" onclick="openGalleryPicker(addGalleryImage)"
                            class="flex items-center gap-1.5 px-3 py-2 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/20 rounded-xl text-xs font-semibold transition-all">
                        <span class="material-symbols-outlined text-sm">add_photo_alternate</span> Add Gallery Image
                    </button>
                    <input type="hidden" id="f-gallery"/>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                    <textarea id="f-description" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Google Maps Embed <span class="text-slate-400 font-normal">(paste your &lt;iframe&gt; code)</span></label>
                    <textarea id="f-map_embed" rows="3" placeholder="&lt;iframe src=&quot;https://www.google.com/maps/embed?...&quot; ...&gt;&lt;/iframe&gt;" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-y font-mono text-xs"></textarea>
                </div>
                <!-- Extra fields shown per category -->
                <div id="extra-fields" class="col-span-2 grid grid-cols-2 gap-4"></div>
            </div>
            <div id="form-error" class="hidden text-sm text-red-600 bg-red-50 px-4 py-2 rounded-xl"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Save Listing</button>
            </div>
        </form>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentCat = 'stays';
var editingId   = null;

var priceLabels = {
    stays: 'Price / Night (₹)',
    cars: 'Price / Day (₹)',
    bikes: 'Price / Day (₹)',
    restaurants: 'Price / Person (₹)',
    attractions: 'Entry Fee (₹)',
    buses: 'Price (₹)',
};

var extraFieldsDef = {
    stays:       [['f-room_type','Room Type','text'],['f-max_guests','Max Guests','number'],['f-amenities','Amenities (comma-sep)','text']],
    cars:        [['f-fuel_type','Fuel Type','text'],['f-transmission','Transmission','text'],['f-seats','Seats','number']],
    bikes:       [['f-fuel_type','Fuel Type','text'],['f-cc','CC','text']],
    restaurants: [['f-cuisine','Cuisine','text']],
    attractions: [['f-opening_hours','Opening Hours','text'],['f-best_time','Best Time to Visit','text']],
    buses:       [['f-operator','Operator','text'],['f-from_location','From','text'],['f-to_location','To','text'],['f-departure_time','Departure','text'],['f-arrival_time','Arrival','text'],['f-duration','Duration','text']],
};

function imgSrc(path) {
    if (!path) return '';
    // If already absolute or has ../ prefix, use as-is
    if (path.startsWith('http') || path.startsWith('../') || path.startsWith('/')) return path;
    return '../' + path;
}

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function switchCat(cat) {
    currentCat = cat;
    document.querySelectorAll('.cat-tab').forEach(function(b){
        var active = b.dataset.cat === cat;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    loadListings();
}

async function loadListings() {
    var search = document.getElementById('search-input').value;
    var url = '../php/api/listings.php?category=' + currentCat + (search ? '&search=' + encodeURIComponent(search) : '');
    var tbody = document.getElementById('listings-tbody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    var items = await api(url);
    if (!items || !items.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-12 text-slate-400">No listings found</td></tr>';
        return;
    }
    // Sort by display_order, then by id
    items.sort(function(a, b) { 
        var orderA = parseInt(a.display_order) || 0;
        var orderB = parseInt(b.display_order) || 0;
        if (orderA !== orderB) return orderA - orderB;
        return a.id - b.id;
    });
    var priceKey = {stays:'price_per_night',cars:'price_per_day',bikes:'price_per_day',restaurants:'price_per_person',attractions:'entry_fee',buses:'price'};
    tbody.innerHTML = items.map(function(item) {
        var statusColor = item.is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-slate-50 text-slate-400 border-slate-100';
        var statusTxt = item.is_active ? 'Active' : 'Hidden';
        var imgUrl = item.image ? imgSrc(item.image) : '../images/placeholder.jpg';
        var displayOrder = parseInt(item.display_order) || 0;
        return '<tr class="hover:bg-slate-50 transition-colors group draggable-row" data-id="' + item.id + '" draggable="true">' +
            '<td class="py-4 px-6">' +
                '<div class="flex items-center gap-2">' +
                    '<span class="material-symbols-outlined text-slate-300 cursor-move drag-handle group-hover:text-primary transition-colors">drag_indicator</span>' +
                    '<span class="text-[11px] font-black text-slate-400 order-number">' + displayOrder + '</span>' +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-6">' +
                '<div class="flex items-center gap-3">' +
                    '<img src="' + escHtml(imgUrl) + '" class="w-12 h-12 rounded-2xl object-cover border border-slate-100 shadow-sm" onerror="this.src=\'../images/placeholder.jpg\'">' +
                    '<div>' +
                        '<p class="font-bold text-slate-900 leading-tight">' + escHtml(item.name) + '</p>' +
                        '<p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">' + escHtml(item.type || 'Standard') + '</p>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-6 text-[13px] font-semibold text-slate-500">' + escHtml(item.location) + '</td>' +
            '<td class="py-4 px-6 text-center">' +
                '<p class="text-[15px] font-black text-primary tracking-tight">₹' + (item[priceKey[currentCat]] || 0) + '</p>' +
            '</td>' +
            '<td class="py-4 px-6 text-center">' +
                '<span class="inline-block px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border ' + statusColor + '">' + statusTxt + '</span>' +
            '</td>' +
            '<td class="py-4 px-6 text-right">' +
                '<div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">' +
                    '<button onclick="openEditModal(' + item.id + ')" class="p-2.5 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-xl transition-all"><span class="material-symbols-outlined text-lg">edit_note</span></button>' +
                    '<button onclick="toggleActive(' + item.id + ',' + (item.is_active ? 0 : 1) + ')" class="p-2.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-xl transition-all"><span class="material-symbols-outlined text-lg">' + (item.is_active ? 'visibility_off' : 'visibility') + '</span></button>' +
                    '<button onclick="deleteListing(' + item.id + ')" class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all"><span class="material-symbols-outlined text-lg">delete</span></button>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }).join('');
    initDragAndDrop();
}

function buildExtraFields(cat) {
    var container = document.getElementById('extra-fields');
    container.innerHTML = '';
    (extraFieldsDef[cat] || []).forEach(function(f) {
        var div = document.createElement('div');
        div.innerHTML = '<label class="block text-xs font-semibold text-slate-600 mb-1">' + f[1] + '</label>' +
            '<input id="' + f[0] + '" type="' + f[2] + '" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>';
        container.appendChild(div);
    });
    document.getElementById('price-label').textContent = priceLabels[cat] || 'Price';
}

function openAddModal() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Add ' + currentCat.charAt(0).toUpperCase() + currentCat.slice(1);
    document.getElementById('listing-form').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('f-gallery').value = '';
    document.getElementById('gallery-thumbs').innerHTML = '';
    document.getElementById('main-img-preview').classList.add('hidden');
    document.getElementById('f-map_embed').value = '';
    buildExtraFields(currentCat);
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('listing-modal').classList.remove('hidden');
}

async function openEditModal(id) {
    editingId = id;
    var item = await api('../php/api/listings.php?category=' + currentCat + '&id=' + id);
    if (!item) return;
    document.getElementById('modal-title').textContent = 'Edit Listing';
    buildExtraFields(currentCat);
    document.getElementById('edit-id').value = id;
    document.getElementById('f-name').value = item.name || '';
    document.getElementById('f-type').value = item.type || '';
    document.getElementById('f-location').value = item.location || '';
    document.getElementById('f-badge').value = item.badge || '';
    document.getElementById('f-image').value = item.image || '';
    document.getElementById('f-description').value = item.description || '';
    document.getElementById('f-rating').value = item.rating || '';
    document.getElementById('f-active').value = item.is_active ?? 1;
    document.getElementById('f-display_order').value = item.display_order || 0;
    document.getElementById('f-map_embed').value = item.map_embed || '';
    updateMainPreview(item.image || '');
    // Gallery
    var gallery = Array.isArray(item.gallery) ? item.gallery : (item.gallery ? JSON.parse(item.gallery) : []);
    document.getElementById('f-gallery').value = JSON.stringify(gallery);
    renderGalleryThumbs(gallery);
    var priceKey = {stays:'price_per_night',cars:'price_per_day',bikes:'price_per_day',restaurants:'price_per_person',attractions:'entry_fee',buses:'price'};
    document.getElementById('f-price').value = item[priceKey[currentCat]] || '';
    // Extra fields
    (extraFieldsDef[currentCat] || []).forEach(function(f) {
        var el = document.getElementById(f[0]);
        if (!el) return;
        var key = f[0].replace('f-','');
        var val = item[key];
        if (Array.isArray(val)) val = val.join(', ');
        el.value = val || '';
    });
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('listing-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('listing-modal').classList.add('hidden');
}

function updateMainPreview(url) {
    var prev = document.getElementById('main-img-preview');
    var img  = document.getElementById('main-img-preview-img');
    if (url) { img.src = imgSrc(url); prev.classList.remove('hidden'); }
    else { prev.classList.add('hidden'); }
}

function getGalleryList() {
    var val = document.getElementById('f-gallery').value;
    try { return JSON.parse(val) || []; } catch(e) { return []; }
}

function renderGalleryThumbs(list) {
    var container = document.getElementById('gallery-thumbs');
    if (!list.length) { 
        container.innerHTML = '<div class="w-full py-8 border-2 border-dashed border-slate-100 rounded-2xl flex flex-col items-center justify-center gap-2 text-slate-400">' +
            '<span class="material-symbols-outlined text-3xl">photo_library</span>' +
            '<p class="text-xs font-semibold uppercase tracking-wider">No gallery images</p></div>'; 
        return; 
    }
    container.innerHTML = list.map(function(url, i) {
        return '<div class="relative group w-32 h-24">' +
            '<img src="' + escHtml(imgSrc(url)) + '" class="w-full h-full rounded-2xl object-cover border-2 border-slate-100 shadow-sm transition-all duration-300 group-hover:scale-[1.03] group-hover:border-primary/50 group-hover:shadow-md"/>' +
            '<button type="button" onclick="removeGalleryImage(' + i + ')" ' +
                'class="absolute -top-2 -right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-lg hover:bg-red-600 hover:scale-110">' +
                '<span class="material-symbols-outlined text-[16px]">close</span>' +
            '</button>' +
        '</div>';
    }).join('');
}

function addGalleryImage(url) {
    var list = getGalleryList();
    if (list.length >= 6) { showAdminToast('Max 6 gallery images', 'error'); return; }
    if (!list.includes(url)) list.push(url);
    document.getElementById('f-gallery').value = JSON.stringify(list);
    renderGalleryThumbs(list);
}

function removeGalleryImage(index) {
    var list = getGalleryList();
    list.splice(index, 1);
    document.getElementById('f-gallery').value = JSON.stringify(list);
    renderGalleryThumbs(list);
}

document.getElementById('listing-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = document.getElementById('save-btn');
    btn.disabled = true; btn.textContent = 'Saving...';
    var priceKey = {stays:'price_per_night',cars:'price_per_day',bikes:'price_per_day',restaurants:'price_per_person',attractions:'entry_fee',buses:'price'};
    var data = {
        name: document.getElementById('f-name').value,
        type: document.getElementById('f-type').value,
        location: document.getElementById('f-location').value,
        badge: document.getElementById('f-badge').value,
        image: document.getElementById('f-image').value,
        gallery: getGalleryList(),
        description: document.getElementById('f-description').value,
        rating: parseFloat(document.getElementById('f-rating').value) || 0,
        is_active: parseInt(document.getElementById('f-active').value),
        display_order: parseInt(document.getElementById('f-display_order').value) || 0,
        map_embed: document.getElementById('f-map_embed').value.trim() || null,
    };
    data[priceKey[currentCat]] = parseFloat(document.getElementById('f-price').value) || 0;    // Extra fields
    (extraFieldsDef[currentCat] || []).forEach(function(f) {
        var el = document.getElementById(f[0]);
        if (!el) return;
        var key = f[0].replace('f-','');
        var val = el.value;
        if (key === 'amenities' || key === 'features' || key === 'menu_highlights') {
            val = val.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
        }
        data[key] = val;
    });

    try {
        var url = '../php/api/listings.php?category=' + currentCat;
        var method = 'POST';
        if (editingId) { url += '&id=' + editingId; method = 'PUT'; }
        var res = await api(url, { method: method, body: JSON.stringify(data) });
        if (res && res.error) throw new Error(res.error);
        closeModal();
        loadListings();
    } catch(ex) {
        var errEl = document.getElementById('form-error');
        errEl.textContent = ex.message;
        errEl.classList.remove('hidden');
    }
    btn.disabled = false; btn.textContent = 'Save Listing';
});

async function toggleActive(id, val) {
    await api('../php/api/listings.php?category=' + currentCat + '&id=' + id, {
        method: 'PUT', body: JSON.stringify({ is_active: val })
    });
    loadListings();
}

async function deleteListing(id) {
    if (!confirm('Delete this listing? It will be hidden from the site.')) return;
    await api('../php/api/listings.php?category=' + currentCat + '&id=' + id, { method: 'DELETE' });
    loadListings();
}

document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(window._searchTimer);
    window._searchTimer = setTimeout(loadListings, 400);
});

// Drag and drop reordering
var draggedRow = null;
var draggedOverRow = null;

function initDragAndDrop() {
    var rows = document.querySelectorAll('.draggable-row');
    rows.forEach(function(row) {
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragenter', handleDragEnter);
        row.addEventListener('dragover', handleDragOver);
        row.addEventListener('dragleave', handleDragLeave);
        row.addEventListener('drop', handleDrop);
        row.addEventListener('dragend', handleDragEnd);
    });
}

function handleDragStart(e) {
    draggedRow = this;
    this.style.opacity = '0.5';
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragEnter(e) {
    if (draggedRow !== this) {
        this.classList.add('drag-over');
        draggedOverRow = this;
    }
}

function handleDragOver(e) {
    if (e.preventDefault) e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragLeave(e) {
    this.classList.remove('drag-over');
}

function handleDrop(e) {
    if (e.stopPropagation) e.stopPropagation();
    e.preventDefault();
    
    if (draggedRow !== this) {
        var tbody = document.getElementById('listings-tbody');
        var allRows = Array.from(tbody.querySelectorAll('.draggable-row'));
        var draggedIndex = allRows.indexOf(draggedRow);
        var targetIndex = allRows.indexOf(this);
        
        if (draggedIndex < targetIndex) {
            this.parentNode.insertBefore(draggedRow, this.nextSibling);
        } else {
            this.parentNode.insertBefore(draggedRow, this);
        }
        
        // Update display orders
        updateDisplayOrders();
    }
    return false;
}

function handleDragEnd(e) {
    this.style.opacity = '1';
    this.classList.remove('dragging');
    var rows = document.querySelectorAll('.draggable-row');
    rows.forEach(function(row) {
        row.classList.remove('drag-over');
    });
}

async function updateDisplayOrders() {
    var rows = document.querySelectorAll('.draggable-row');
    var updates = [];
    rows.forEach(function(row, index) {
        var id = parseInt(row.dataset.id);
        updates.push({ id: id, display_order: index });
        // Update the display order text in the row
        var orderCell = row.querySelector('.order-number');
        if (orderCell) orderCell.textContent = index;
    });
    
    // Send batch update to server
    try {
        await api('../php/api/listings.php?category=' + currentCat + '&action=reorder', {
            method: 'POST',
            body: JSON.stringify({ updates: updates })
        });
        showAdminToast('Order updated successfully', 'success');
    } catch(ex) {
        showAdminToast('Failed to update order: ' + ex.message, 'error');
        loadListings(); // Reload to restore original order
    }
}

// Init
switchCat('stays');
</script>
JS;
require 'admin-footer.php';
?>
