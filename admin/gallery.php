<?php
$admin_page  = 'gallery';
$admin_title = 'Media Gallery | CSNExplore Admin';
require 'admin-header.php';
?>
<style>
.gallery-img { aspect-ratio:4/3; object-fit:cover; width:100%; display:block; }
.img-card { position:relative; overflow:hidden; border-radius:16px; background:#f8fafc; border:1px solid #e2e8f0; cursor:pointer; transition:all 0.2s ease; }
.img-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,0.12); border-color:#ec5b13; }
.img-card:hover .img-actions { opacity:1; }
.img-actions { opacity:0; transition:opacity 0.2s ease; position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 50%); display:flex; flex-direction:column; justify-content:flex-end; padding:10px; gap:6px; }
.img-meta { padding:8px 10px; border-top:1px solid #f1f5f9; }

/* ── Lightbox ── */
#gallery-lightbox {
    display:none; position:fixed; inset:0; z-index:99999;
    background:rgba(0,0,0,0.92);
    align-items:center; justify-content:center;
    flex-direction:column;
}
#gallery-lightbox.open { display:flex; }
#gallery-lightbox img {
    max-width:88vw; max-height:82vh;
    object-fit:contain; border-radius:8px;
    transition:opacity 0.25s ease, transform 0.25s ease;
    box-shadow:0 32px 80px rgba(0,0,0,0.7);
    user-select:none;
}
.lb-close {
    position:fixed; top:20px; right:24px;
    width:42px; height:42px; border-radius:50%;
    background:#ec5b13; border:none; color:#fff;
    font-size:22px; cursor:pointer; display:flex;
    align-items:center; justify-content:center;
    transition:all 0.2s; z-index:100000;
}
.lb-close:hover { background:#d94a0a; transform:scale(1.1); }
.lb-nav {
    position:fixed; top:50%; transform:translateY(-50%);
    width:46px; height:46px; border-radius:50%;
    background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);
    color:#fff; font-size:22px; cursor:pointer; display:flex;
    align-items:center; justify-content:center;
    transition:all 0.2s; z-index:100000; backdrop-filter:blur(8px);
}
.lb-nav:hover { background:#ec5b13; border-color:#ec5b13; }
.lb-prev { left:20px; }
.lb-next { right:20px; }
.lb-counter {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
    background:rgba(255,255,255,0.12); color:#fff;
    padding:6px 16px; border-radius:20px; font-size:13px; font-weight:600;
    backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.2);
}
.lb-filename {
    position:fixed; bottom:64px; left:50%; transform:translateX(-50%);
    color:rgba(255,255,255,0.6); font-size:12px; font-weight:500;
    max-width:80vw; text-align:center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.lb-actions {
    position:fixed; top:20px; left:24px; display:flex; gap:8px; z-index:100000;
}
.lb-action-btn {
    display:flex; align-items:center; gap:5px;
    padding:7px 14px; border-radius:20px;
    background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);
    color:#fff; font-size:12px; font-weight:600; cursor:pointer;
    backdrop-filter:blur(8px); transition:all 0.2s;
}
.lb-action-btn:hover { background:rgba(255,255,255,0.22); }
.lb-action-btn.danger:hover { background:#ef4444; border-color:#ef4444; }

/* Thumbnail strip at bottom of lightbox */
.lb-thumbs {
    position:fixed; bottom:0; left:0; right:0;
    background:rgba(0,0,0,0.6); backdrop-filter:blur(12px);
    display:flex; gap:6px; padding:10px 20px;
    overflow-x:auto; z-index:100000;
}
.lb-thumbs::-webkit-scrollbar { height:3px; }
.lb-thumbs::-webkit-scrollbar-thumb { background:#ec5b13; border-radius:2px; }
.lb-thumb {
    flex-shrink:0; width:52px; height:42px; border-radius:6px;
    object-fit:cover; cursor:pointer; border:2px solid transparent;
    transition:all 0.2s; opacity:0.6;
}
.lb-thumb.active { border-color:#ec5b13; opacity:1; transform:scale(1.08); }
.lb-thumb:hover { opacity:0.9; }
</style>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Media Gallery</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Manage and upload platform assets · Click any image to preview</p>
    </div>
    <div class="flex items-center gap-3">
        <span id="img-count" class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">Loading...</span>
        <label class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow-sm cursor-pointer">
            <span class="material-symbols-outlined text-base">upload</span>Upload Images
            <input type="file" id="upload-input" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="hidden"/>
        </label>
    </div>
</div>

<!-- Upload Progress -->
<div id="upload-progress" class="hidden bg-white border border-slate-200 rounded-2xl p-4 mb-4">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-3 text-sm font-semibold text-slate-700">
            <div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin shrink-0"></div>
            <span id="upload-status">Uploading...</span>
        </div>
        <span id="upload-pct" class="text-xs font-bold text-primary">0%</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-1.5">
        <div id="upload-bar" class="bg-primary h-1.5 rounded-full transition-all duration-300" style="width:0%"></div>
    </div>
</div>

<!-- Gallery Grid -->
<div id="gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
    <div class="col-span-5 text-center py-20 text-slate-400">
        <span class="material-symbols-outlined text-5xl block mb-3">photo_library</span>
        Loading gallery...
    </div>
</div>

<!-- Drag & Drop Zone (shown when empty) -->
<div id="drop-zone" class="hidden border-2 border-dashed border-slate-300 rounded-2xl p-16 text-center text-slate-400 hover:border-primary hover:text-primary transition-all cursor-pointer" onclick="document.getElementById('upload-input').click()">
    <span class="material-symbols-outlined text-6xl block mb-3">cloud_upload</span>
    <p class="font-bold text-lg mb-1">Drop images here or click to upload</p>
    <p class="text-sm">Supports JPG, PNG, WebP, GIF · Max 5MB each</p>
</div>

<!-- ── Lightbox ── -->
<div id="gallery-lightbox">
    <button class="lb-close" onclick="closeLightbox()" title="Close (Esc)">
        <span class="material-symbols-outlined" style="font-size:20px">close</span>
    </button>
    <div class="lb-actions">
        <button class="lb-action-btn" onclick="copyLbUrl()" id="lb-copy-btn">
            <span class="material-symbols-outlined" style="font-size:15px">content_copy</span>Copy URL
        </button>
        <a class="lb-action-btn" id="lb-download-btn" download target="_blank">
            <span class="material-symbols-outlined" style="font-size:15px">download</span>Download
        </a>
        <button class="lb-action-btn danger" onclick="deleteLbImage()">
            <span class="material-symbols-outlined" style="font-size:15px">delete</span>Delete
        </button>
    </div>
    <button class="lb-nav lb-prev" onclick="lbNav(-1)">
        <span class="material-symbols-outlined" style="font-size:22px">chevron_left</span>
    </button>
    <img id="lb-img" src="" alt="Preview"/>
    <button class="lb-nav lb-next" onclick="lbNav(1)">
        <span class="material-symbols-outlined" style="font-size:22px">chevron_right</span>
    </button>
    <div class="lb-filename" id="lb-filename"></div>
    <div class="lb-counter" id="lb-counter">1 / 1</div>
    <div class="lb-thumbs" id="lb-thumbs"></div>
</div>

<script>
var allImages = [];
var lbIndex = 0;

/* ── Load Gallery ── */
async function loadGallery() {
    var data = await api('../php/api/gallery.php');
    allImages = Array.isArray(data) ? data : [];
    renderGallery();
}

function renderGallery() {
    var grid = document.getElementById('gallery-grid');
    var drop = document.getElementById('drop-zone');
    var cnt  = document.getElementById('img-count');
    cnt.textContent = allImages.length + ' image' + (allImages.length !== 1 ? 's' : '');

    if (!allImages.length) {
        grid.innerHTML = '';
        drop.classList.remove('hidden');
        return;
    }
    drop.classList.add('hidden');
    grid.innerHTML = allImages.map(function(img, i) {
        var kb = Math.round((img.size || 0) / 1024);
        var name = img.filename || '';
        var shortName = name.length > 22 ? name.substring(0, 19) + '...' : name;
        return '<div class="img-card" onclick="openLightbox(' + i + ')" title="' + name + '">' +
            '<img class="gallery-img" src="' + img.url + '" loading="lazy" alt="' + name + '"' +
            '     onerror="this.src=\'../images/travelhub.png\'" />' +
            '<div class="img-actions">' +
                '<div class="flex gap-1.5">' +
                    '<button onclick="event.stopPropagation();copyUrl(\'' + img.url + '\')" ' +
                            'class="flex-1 bg-white/20 backdrop-blur text-white text-[11px] font-bold py-1.5 rounded-lg hover:bg-white/30 transition-all flex items-center justify-center gap-1">' +
                        '<span class="material-symbols-outlined text-sm">content_copy</span>Copy' +
                    '</button>' +
                    '<button onclick="event.stopPropagation();deleteImage(\'' + img.filename + '\')" ' +
                            'class="flex-1 bg-red-500/80 backdrop-blur text-white text-[11px] font-bold py-1.5 rounded-lg hover:bg-red-600 transition-all flex items-center justify-center gap-1">' +
                        '<span class="material-symbols-outlined text-sm">delete</span>Del' +
                    '</button>' +
                '</div>' +
            '</div>' +
            '<div class="img-meta">' +
                '<p class="text-[10px] text-slate-600 font-medium truncate">' + shortName + '</p>' +
                '<p class="text-[10px] text-slate-400">' + kb + ' KB</p>' +
            '</div>' +
        '</div>';
    }).join('');
}

/* ── Lightbox ── */
function openLightbox(idx) {
    lbIndex = idx;
    document.getElementById('gallery-lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
    renderLightbox();
    buildThumbStrip();
}
function closeLightbox() {
    document.getElementById('gallery-lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
function renderLightbox() {
    var img = allImages[lbIndex];
    if (!img) return;
    var el = document.getElementById('lb-img');
    el.style.opacity = '0';
    setTimeout(function(){ el.src = img.url; el.style.opacity = '1'; }, 80);
    document.getElementById('lb-filename').textContent = img.filename;
    document.getElementById('lb-counter').textContent = (lbIndex + 1) + ' / ' + allImages.length;
    document.getElementById('lb-download-btn').href = img.url;
    document.getElementById('lb-download-btn').download = img.filename;
    // Update active thumb
    document.querySelectorAll('.lb-thumb').forEach(function(t, i){ t.classList.toggle('active', i === lbIndex); });
    // Scroll active thumb into view
    var activeThumb = document.querySelector('.lb-thumb.active');
    if (activeThumb) activeThumb.scrollIntoView({behavior:'smooth', block:'nearest', inline:'center'});
}
function buildThumbStrip() {
    document.getElementById('lb-thumbs').innerHTML = allImages.map(function(img, i) {
        return '<img class="lb-thumb' + (i === lbIndex ? ' active' : '') + '"' +
               ' src="' + img.url + '" onclick="lbGoto(' + i + ')" loading="lazy"' +
               ' onerror="this.src=\'../images/travelhub.png\'" />';
    }).join('');
}
function lbNav(dir) {
    lbIndex = (lbIndex + dir + allImages.length) % allImages.length;
    renderLightbox();
}
function lbGoto(idx) { lbIndex = idx; renderLightbox(); }
function copyLbUrl() {
    var img = allImages[lbIndex];
    if (!img) return;
    navigator.clipboard.writeText(img.url).then(function(){
        var btn = document.getElementById('lb-copy-btn');
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:15px">check</span>Copied!';
        setTimeout(function(){ btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:15px">content_copy</span>Copy URL'; }, 1800);
    });
}
async function deleteLbImage() {
    var img = allImages[lbIndex];
    if (!img || !confirm('Delete "' + img.filename + '"? This cannot be undone.')) return;
    await api('../php/api/gallery.php', { method:'DELETE', body:JSON.stringify({filename:img.filename}) });
    allImages.splice(lbIndex, 1);
    if (!allImages.length) { closeLightbox(); loadGallery(); return; }
    lbIndex = Math.min(lbIndex, allImages.length - 1);
    renderLightbox(); buildThumbStrip(); renderGallery();
}

/* ── Copy URL from grid ── */
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function(){ showAdminToast('URL copied!'); });
}

/* ── Delete from grid ── */
async function deleteImage(filename) {
    if (!confirm('Delete this image? This cannot be undone.')) return;
    await api('../php/api/gallery.php', { method:'DELETE', body:JSON.stringify({filename:filename}) });
    loadGallery();
}

/* ── Upload ── */
document.getElementById('upload-input').addEventListener('change', async function(e) {
    var files = Array.from(e.target.files);
    if (!files.length) return;
    var progress = document.getElementById('upload-progress');
    var status   = document.getElementById('upload-status');
    var bar      = document.getElementById('upload-bar');
    var pct      = document.getElementById('upload-pct');
    progress.classList.remove('hidden');
    var done = 0;
    for (var f of files) {
        status.textContent = 'Uploading ' + f.name + ' (' + (done+1) + '/' + files.length + ')';
        var p = Math.round((done / files.length) * 100);
        bar.style.width = p + '%'; pct.textContent = p + '%';
        var fd = new FormData();
        fd.append('file', f);
        try {
            var res = await fetch('../php/api/upload.php', {
                method:'POST',
                headers:{'Authorization':'Bearer ' + window._adminToken},
                body:fd
            });
            var data = await res.json();
            if (data.error) showAdminToast('Error: ' + data.error, 'error');
        } catch(ex) {
            showAdminToast('Upload failed: ' + ex.message, 'error');
        }
        done++;
    }
    bar.style.width = '100%'; pct.textContent = '100%';
    setTimeout(function(){ progress.classList.add('hidden'); bar.style.width='0%'; }, 600);
    e.target.value = '';
    loadGallery();
});

/* ── Keyboard navigation ── */
document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('gallery-lightbox');
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft')  lbNav(-1);
    if (e.key === 'ArrowRight') lbNav(1);
});
/* Click backdrop to close */
document.getElementById('gallery-lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});

loadGallery();
</script>
<?php require 'admin-footer.php'; ?>
