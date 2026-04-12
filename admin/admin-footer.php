    </main><!-- /main -->
</div><!-- /flex-1 -->
</div><!-- /flex -->

<!-- ── Gallery Picker Modal (shared) ──────────────────────────────────── -->
<div id="gallery-picker-modal" class="hidden fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col" style="height:min(92vh,780px)">
        <!-- Header -->
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 shrink-0">
            <span class="material-symbols-outlined text-primary">photo_library</span>
            <h3 class="font-bold text-base">Select Image</h3>
            <span id="picker-count" class="text-xs text-slate-400 font-medium"></span>
            <div class="ml-auto flex items-center gap-2">
                <label class="flex items-center gap-1.5 bg-primary text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-orange-600 cursor-pointer transition-all">
                    <span class="material-symbols-outlined text-sm">upload</span> Upload New
                    <input type="file" id="picker-upload-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="hidden"/>
                </label>
                <button onclick="closeGalleryPicker()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        </div>
        <!-- Search -->
        <div class="px-5 py-3 border-b border-slate-100 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
                <input id="picker-search" type="text" placeholder="Filter by filename..."
                       class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
            </div>
        </div>
        <!-- Grid — fixed height, always scrollable -->
        <div id="picker-grid" class="overflow-y-auto p-4" style="flex:1 1 0;min-height:0;">
            <div style="text-align:center;padding:3rem 0;color:#94a3b8;">Loading...</div>
        </div>
        <!-- Upload progress -->
        <div id="picker-upload-progress" class="hidden px-5 py-3 border-t border-slate-100 text-sm text-slate-600 flex items-center gap-3 shrink-0">
            <div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin shrink-0"></div>
            <span id="picker-upload-status">Uploading...</span>
        </div>
    </div>
</div>

<script>
// Populate user info
(function(){
    var u = window._adminUser;
    if (u) {
        var n = document.getElementById('admin-name');
        var e = document.getElementById('admin-email');
        if (n) n.textContent = u.name || 'Admin';
        if (e) e.textContent = u.email || '';
    }
})();

// Sidebar toggle (mobile)
document.getElementById('sidebar-toggle')?.addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
});
document.getElementById('sidebar-close')?.addEventListener('click', function(){
    document.getElementById('sidebar').classList.add('-translate-x-full');
});

// Logout
function adminLogout() {
    localStorage.removeItem('csn_admin_token');
    localStorage.removeItem('csn_admin_user');
    localStorage.removeItem('csn_token');
    localStorage.removeItem('csn_user');
    window.location.href = '../adminexplorer.php';
}

// Load pending bookings count
async function loadPendingCount() {
    try {
        var data = await api('../php/api/bookings.php?status=pending');
        if (data && Array.isArray(data) && data.length > 0) {
            var count = data.length;
            // Top-bar badge
            document.getElementById('pending-count').textContent = count;
            document.getElementById('pending-badge').classList.remove('hidden');
            document.getElementById('pending-badge').classList.add('flex');
            // Sidebar badge
            var sb = document.getElementById('sidebar-pending-badge');
            if (sb) { sb.textContent = count; sb.classList.remove('hidden'); }
        }
    } catch(e) {}
}
loadPendingCount();

// ── Gallery Picker ──────────────────────────────────────────────────────
var _pickerCallback = null;
var _pickerImages   = [];

function openGalleryPicker(callback) {
    _pickerCallback = callback;
    document.getElementById('gallery-picker-modal').classList.remove('hidden');
    document.getElementById('picker-search').value = '';
    loadPickerImages();
}

function closeGalleryPicker() {
    document.getElementById('gallery-picker-modal').classList.add('hidden');
    _pickerCallback = null;
}

async function loadPickerImages() {
    var grid = document.getElementById('picker-grid');
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem 0;color:#94a3b8;"><div style="width:24px;height:24px;border:2px solid #ec5b13;border-top-color:transparent;border-radius:50%;animation:spin .7s linear infinite;margin:0 auto 8px;"></div>Loading...</div>';
    _pickerImages = await api('../php/api/gallery.php') || [];
    renderPickerGrid(_pickerImages);
}

function renderPickerGrid(images) {
    var grid = document.getElementById('picker-grid');
    var countEl = document.getElementById('picker-count');
    if (countEl) countEl.textContent = images.length ? images.length + ' image' + (images.length !== 1 ? 's' : '') : '';

    if (!images.length) {
        grid.innerHTML = '<div style="text-align:center;padding:3rem 0;color:#94a3b8;"><span class="material-symbols-outlined" style="font-size:2.5rem;display:block;margin-bottom:0.5rem;">photo_library</span>No images yet. Upload some first.</div>';
        return;
    }

    // Build grid wrapper
    var wrapper = document.createElement('div');
    wrapper.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;align-content:start;';

    images.forEach(function(img) {
        var relPath = 'images/uploads/' + img.filename;
        var displaySrc = '../images/uploads/' + img.filename;

        // Card
        var card = document.createElement('div');
        card.style.cssText = 'cursor:pointer;border-radius:10px;overflow:hidden;border:2px solid #e2e8f0;background:#f8fafc;transition:border-color .15s,transform .15s,box-shadow .15s;';
        card.addEventListener('mouseenter', function(){ this.style.borderColor='#ec5b13'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 16px rgba(236,91,19,0.2)'; });
        card.addEventListener('mouseleave', function(){ this.style.borderColor='#e2e8f0'; this.style.transform=''; this.style.boxShadow=''; });
        card.addEventListener('click', function(){ selectPickerImage(relPath); });

        // Image container
        var imgWrap = document.createElement('div');
        imgWrap.style.cssText = 'width:100%;aspect-ratio:4/3;overflow:hidden;background:#f1f5f9;';

        var imgEl = document.createElement('img');
        imgEl.src = displaySrc;
        imgEl.alt = img.filename;
        imgEl.loading = 'lazy';
        imgEl.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
        imgEl.onerror = function() {
            this.parentElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#cbd5e1;"><span class="material-symbols-outlined" style="font-size:2rem;">broken_image</span></div>';
        };
        imgWrap.appendChild(imgEl);

        // Filename label
        var label = document.createElement('div');
        label.style.cssText = 'padding:5px 8px;background:#fff;border-top:1px solid #f1f5f9;';
        var p = document.createElement('p');
        p.style.cssText = 'font-size:10px;color:#64748b;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;';
        p.title = img.filename;
        p.textContent = img.filename;
        label.appendChild(p);

        card.appendChild(imgWrap);
        card.appendChild(label);
        wrapper.appendChild(card);
    });

    grid.innerHTML = '';
    grid.appendChild(wrapper);
}

function selectPickerImage(path) {
    if (_pickerCallback) _pickerCallback(path);
    closeGalleryPicker();
}

// Picker search filter
document.getElementById('picker-search').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    var filtered = _pickerImages.filter(function(img){ return img.filename.toLowerCase().includes(q); });
    renderPickerGrid(filtered);
});

// Picker upload
document.getElementById('picker-upload-input').addEventListener('change', async function(e) {
    var files = Array.from(e.target.files);
    if (!files.length) return;
    var progress = document.getElementById('picker-upload-progress');
    var status   = document.getElementById('picker-upload-status');
    progress.classList.remove('hidden');
    var done = 0;
    for (var f of files) {
        status.textContent = 'Uploading ' + (done+1) + ' of ' + files.length + ': ' + f.name;
        var fd = new FormData();
        fd.append('file', f);
        try {
            var res = await fetch('../php/api/upload.php', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + window._adminToken },
                body: fd
            });
            var data = await res.json();
            if (data.error) showAdminToast('Error: ' + data.error, 'error');
        } catch(ex) {
            showAdminToast('Upload failed', 'error');
        }
        done++;
    }
    progress.classList.add('hidden');
    e.target.value = '';
    loadPickerImages();
});

// Close picker on backdrop click
document.getElementById('gallery-picker-modal').addEventListener('click', function(e) {
    if (e.target === this) closeGalleryPicker();
});
</script>
<?php if (!empty($extra_js)) echo $extra_js; ?>
</body>
</html>
