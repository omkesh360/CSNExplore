<?php
$admin_page  = 'blogs';
$admin_title = 'Blogs | CSNExplore Admin';
$extra_head  = '
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet"/>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
.ql-toolbar { border-radius: 12px 12px 0 0 !important; border-color: #e2e8f0 !important; }
.ql-container { border-radius: 0 0 12px 12px !important; border-color: #e2e8f0 !important; font-size: 14px; min-height: 220px; }
.ql-editor { min-height: 200px; }
.line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
</style>';
require 'admin-header.php';
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Travel Blogs</h2>
            <p class="text-xs text-slate-500 font-medium">Manage insights, articles, and travel guides</p>
        </div>
        <button onclick="openBlogModal()"
                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-orange-600 transition-all shadow-sm">
            <span class="material-symbols-outlined text-base">add</span> New Post
        </button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col lg:flex-row gap-4 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex gap-1 p-1 bg-slate-100 rounded-lg overflow-x-auto w-full lg:w-auto">
            <?php foreach (['all'=>'All','published'=>'Published','draft'=>'Drafts'] as $k=>$v): ?>
            <button onclick="filterBlogs('<?php echo $k; ?>')" data-status="<?php echo $k; ?>"
                    class="blog-tab px-4 py-2 rounded-md text-xs font-semibold whitespace-nowrap transition-all text-slate-500 hover:text-slate-900">
                <?php echo $v; ?>
            </button>
            <?php endforeach; ?>
        </div>
        
        <div class="flex-1 w-full relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input id="blog-search" type="text" placeholder="Search by title, author, or category..."
                   class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary/20 focus:border-primary transition-all"/>
        </div>
    </div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="blogs-grid">
    <div class="col-span-3 text-center py-12 text-slate-400 font-medium">Loading blogs...</div>
</div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentBlogStatus = 'all';
var _allBlogs = [];

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('blog-search').addEventListener('input', function() {
        renderBlogGrid(_allBlogs);
    });
});

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function filterBlogs(s) {
    currentBlogStatus = s;
    document.querySelectorAll('.blog-tab').forEach(function(b){
        var active = b.dataset.status === s;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
    });
    loadBlogs();
}

async function loadBlogs() {
    var url = '../php/api/blogs.php' + (currentBlogStatus !== 'all' ? '?status=' + currentBlogStatus : '');
    var grid = document.getElementById('blogs-grid');
    grid.innerHTML = '<div class="col-span-3 text-center py-12 text-slate-400 font-medium">Loading...</div>';
    var blogs = await api(url);
    _allBlogs = blogs || [];
    renderBlogGrid(_allBlogs);
}

function renderBlogGrid(blogs) {
    var q = (document.getElementById('blog-search')?.value || '').toLowerCase();
    var filtered = q ? blogs.filter(function(b){ return (b.title||'').toLowerCase().includes(q) || (b.category||'').toLowerCase().includes(q) || (b.author||'').toLowerCase().includes(q); }) : blogs;
    var grid = document.getElementById('blogs-grid');
    if (!filtered.length) {
        grid.innerHTML = '<div class="col-span-3 text-center py-12 text-slate-400">No blogs found. Go create one!</div>';
        return;
    }
    grid.innerHTML = filtered.map(function(b) {
        var sc = b.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500';
        var img = b.image
            ? '<img src="' + escHtml(b.image) + '" class="w-full h-40 object-cover" loading="lazy" onerror="this.style.display=\'none\'"/>'
            : '<div class="w-full h-40 bg-slate-100 flex items-center justify-center"><span class="material-symbols-outlined text-slate-300 text-4xl">article</span></div>';
        return '<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">' +
            img +
            '<div class="p-5">' +
                '<div class="flex items-start justify-between gap-2 mb-2">' +
                    '<h3 class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">' + escHtml(b.title) + '</h3>' +
                    '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 ' + sc + '">' + escHtml(b.status) + '</span>' +
                '</div>' +
                '<p class="text-xs text-slate-400 mb-4 font-medium">' + escHtml(b.author) + ' · ' + escHtml(b.category) + '</p>' +
                '<div class="flex gap-2">' +
                    '<a href="blog-editor.php?id=' + b.id + '" class="flex-1 text-center text-xs font-bold text-slate-600 border border-slate-200 py-2 rounded-lg hover:bg-slate-50 transition-all">Edit Post</a>' +
                    (b.status === 'published'
                        ? '<a href="../blog-detail.php?id=' + b.id + '" target="_blank" class="px-3 text-xs font-semibold text-primary border border-primary/20 py-2 rounded-lg hover:bg-primary/5 transition-all flex items-center gap-1"><span class="material-symbols-outlined text-sm">open_in_new</span></a>'
                        : '') +
                    '<button onclick="deleteBlog(' + b.id + ')" class="px-3 text-xs font-semibold text-red-500 border border-red-200 py-2 rounded-lg hover:bg-red-50 transition-all">Del</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function openBlogModal() {
    window.location.href = 'blog-editor.php';
}

async function deleteBlog(id) {
    if (!confirm('Delete this blog post? This cannot be undone.')) return;
    await api('../php/api/blogs.php?id=' + id, { method: 'DELETE' });
    loadBlogs();
}

filterBlogs('all');
</script>
JS;
require 'admin-footer.php';
?>
