<?php
$admin_page  = 'seo';
$admin_title = 'SEO Manager | CSNExplore Admin';
require 'admin-header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">SEO Manager</h2>
            <p class="text-xs text-slate-500 font-medium">Optimize your content for search engines</p>
        </div>
        <div class="flex gap-2">
            <button onclick="exportSEOReport()" class="flex items-center gap-2 bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-all">
                <span class="material-symbols-outlined text-base">download</span> Export Report
            </button>
            <button onclick="openKeywordManager()" class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-orange-600 transition-all shadow-sm">
                <span class="material-symbols-outlined text-base">manage_search</span> Manage Keywords
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Avg SEO Score</span>
                <span class="material-symbols-outlined text-primary text-xl">trending_up</span>
            </div>
            <div class="text-3xl font-black text-slate-900" id="avg-score">--</div>
            <div class="text-[10px] text-slate-400 mt-1">Across all content</div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Optimized</span>
                <span class="material-symbols-outlined text-green-500 text-xl">check_circle</span>
            </div>
            <div class="text-3xl font-black text-green-600" id="optimized-count">--</div>
            <div class="text-[10px] text-slate-400 mt-1">Score ≥ 80</div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Needs Work</span>
                <span class="material-symbols-outlined text-amber-500 text-xl">warning</span>
            </div>
            <div class="text-3xl font-black text-amber-600" id="needs-work-count">--</div>
            <div class="text-[10px] text-slate-400 mt-1">Score 50-79</div>
        </div>
        
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Poor</span>
                <span class="material-symbols-outlined text-red-500 text-xl">error</span>
            </div>
            <div class="text-3xl font-black text-red-600" id="poor-count">--</div>
            <div class="text-[10px] text-slate-400 mt-1">Score < 50</div>
        </div>
    </div>

    <!-- Content Type Tabs -->
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar">
            <?php
            $types = [
                ['key'=>'blogs',       'icon'=>'article',            'label'=>'Blogs'],
                ['key'=>'stays',       'icon'=>'bed',                'label'=>'Hotels'],
                ['key'=>'cars',        'icon'=>'directions_car',     'label'=>'Cars'],
                ['key'=>'bikes',       'icon'=>'motorcycle',         'label'=>'Bikes'],
                ['key'=>'restaurants', 'icon'=>'restaurant',         'label'=>'Dining'],
                ['key'=>'attractions', 'icon'=>'confirmation_number','label'=>'Attractions'],
                ['key'=>'buses',       'icon'=>'directions_bus',     'label'=>'Buses'],
            ];
            foreach ($types as $type): ?>
            <button onclick="switchType('<?php echo $type['key']; ?>')"
                    data-type="<?php echo $type['key']; ?>"
                    class="type-tab flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition-all">
                <span class="material-symbols-outlined text-lg"><?php echo $type['icon']; ?></span>
                <?php echo $type['label']; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SEO Items Table -->
    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 bg-slate-50 border-b border-slate-100">
                        <th class="py-3 px-4 text-left">Title</th>
                        <th class="py-3 px-4 text-center">SEO Score</th>
                        <th class="py-3 px-4 text-left">Focus Keyword</th>
                        <th class="py-3 px-4 text-left">Meta Title</th>
                        <th class="py-3 px-4 text-left">Meta Description</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="seo-tbody" class="divide-y divide-slate-50">
                    <tr><td colspan="7" class="text-center py-12 text-slate-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Keyword Manager Modal -->
<div id="keyword-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-bold">Keyword Manager</h3>
            <button onclick="closeKeywordModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <div class="flex gap-2 mb-4">
                <input type="text" id="new-keyword" placeholder="Enter new keyword..." class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
                <button onclick="addKeyword()" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-orange-600 transition-all">Add</button>
            </div>
            <div id="keywords-list" class="space-y-2 max-h-96 overflow-y-auto"></div>
        </div>
    </div>
</div>

<?php
$extra_js = <<<'JS'
<script>
var currentType = 'blogs';
var allItems = [];

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function switchType(type) {
    currentType = type;
    document.querySelectorAll('.type-tab').forEach(function(b){
        var active = b.dataset.type === type;
        b.classList.toggle('bg-primary', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('text-slate-500', !active);
        b.classList.toggle('hover:bg-slate-100', !active);
    });
    loadSEOData();
}

async function loadSEOData() {
    var tbody = document.getElementById('seo-tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">Loading...</td></tr>';
    
    var url = currentType === 'blogs' ? '../php/api/blogs.php' : '../php/api/listings.php?category=' + currentType;
    var items = await api(url);
    allItems = items || [];
    
    if (!allItems.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-12 text-slate-400">No items found</td></tr>';
        updateStats();
        return;
    }
    
    tbody.innerHTML = allItems.map(function(item) {
        var score = parseInt(item.seo_score) || 0;
        var scoreColor = score >= 80 ? 'text-green-600 bg-green-50' : 
                        score >= 50 ? 'text-amber-600 bg-amber-50' : 
                        'text-red-600 bg-red-50';
        var scoreIcon = score >= 80 ? 'check_circle' : score >= 50 ? 'warning' : 'error';
        
        var statusColor = item.is_active || item.status === 'published' ? 'bg-green-50 text-green-600' : 'bg-slate-50 text-slate-400';
        var statusText = item.is_active || item.status === 'published' ? 'Active' : 'Hidden';
        
        var editUrl = currentType === 'blogs' 
            ? 'blog-editor-new.php?id=' + item.id 
            : 'listings.php#edit-' + item.id;
        
        return '<tr class="hover:bg-slate-50 transition-colors">' +
            '<td class="py-4 px-4">' +
                '<div class="font-bold text-slate-900 text-sm">' + escHtml(item.title || item.name) + '</div>' +
                '<div class="text-[10px] text-slate-400 mt-0.5">' + (item.category || item.type || 'N/A') + '</div>' +
            '</td>' +
            '<td class="py-4 px-4 text-center">' +
                '<div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-black text-sm ' + scoreColor + '">' +
                    '<span class="material-symbols-outlined text-base">' + scoreIcon + '</span>' + score +
                '</div>' +
            '</td>' +
            '<td class="py-4 px-4">' +
                '<span class="text-xs font-semibold text-slate-600">' + escHtml(item.focus_keyword || '-') + '</span>' +
            '</td>' +
            '<td class="py-4 px-4">' +
                '<div class="text-xs text-slate-600 max-w-xs truncate">' + escHtml(item.meta_title || '-') + '</div>' +
                '<div class="text-[10px] text-slate-400 mt-0.5">' + ((item.meta_title || '').length) + ' chars</div>' +
            '</td>' +
            '<td class="py-4 px-4">' +
                '<div class="text-xs text-slate-600 max-w-xs truncate">' + escHtml(item.meta_description || '-') + '</div>' +
                '<div class="text-[10px] text-slate-400 mt-0.5">' + ((item.meta_description || '').length) + ' chars</div>' +
            '</td>' +
            '<td class="py-4 px-4 text-center">' +
                '<span class="inline-block px-2 py-1 rounded-lg text-[10px] font-bold uppercase ' + statusColor + '">' + statusText + '</span>' +
            '</td>' +
            '<td class="py-4 px-4 text-right">' +
                '<a href="' + editUrl + '" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-primary border border-primary/20 rounded-lg hover:bg-primary/5 transition-all">' +
                    '<span class="material-symbols-outlined text-sm">edit</span> Edit' +
                '</a>' +
            '</td>' +
        '</tr>';
    }).join('');
    
    updateStats();
}

function updateStats() {
    var scores = allItems.map(i => parseInt(i.seo_score) || 0);
    var avgScore = scores.length ? Math.round(scores.reduce((a,b) => a+b, 0) / scores.length) : 0;
    var optimized = scores.filter(s => s >= 80).length;
    var needsWork = scores.filter(s => s >= 50 && s < 80).length;
    var poor = scores.filter(s => s < 50).length;
    
    document.getElementById('avg-score').textContent = avgScore;
    document.getElementById('optimized-count').textContent = optimized;
    document.getElementById('needs-work-count').textContent = needsWork;
    document.getElementById('poor-count').textContent = poor;
}

function openKeywordManager() {
    document.getElementById('keyword-modal').classList.remove('hidden');
    loadKeywords();
}

function closeKeywordModal() {
    document.getElementById('keyword-modal').classList.add('hidden');
}

async function loadKeywords() {
    var keywords = await api('../php/api/keywords.php');
    var list = document.getElementById('keywords-list');
    if (!keywords || !keywords.length) {
        list.innerHTML = '<div class="text-center py-8 text-slate-400">No keywords yet</div>';
        return;
    }
    list.innerHTML = keywords.map(function(k) {
        return '<div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">' +
            '<div>' +
                '<div class="font-semibold text-sm text-slate-900">' + escHtml(k.keyword) + '</div>' +
                '<div class="text-[10px] text-slate-400">Used ' + (k.usage_count || 0) + ' times</div>' +
            '</div>' +
            '<button onclick="deleteKeyword(' + k.id + ')" class="p-2 text-slate-400 hover:text-red-500 transition-all">' +
                '<span class="material-symbols-outlined text-lg">delete</span>' +
            '</button>' +
        '</div>';
    }).join('');
}

async function addKeyword() {
    var input = document.getElementById('new-keyword');
    var keyword = input.value.trim();
    if (!keyword) return;
    
    var res = await api('../php/api/keywords.php', 'POST', { keyword });
    if (res && res.success) {
        input.value = '';
        loadKeywords();
    }
}

async function deleteKeyword(id) {
    if (!confirm('Delete this keyword?')) return;
    await api('../php/api/keywords.php?id=' + id, { method: 'DELETE' });
    loadKeywords();
}

function exportSEOReport() {
    var csv = 'Type,Title,SEO Score,Focus Keyword,Meta Title Length,Meta Description Length,Status\n';
    csv += allItems.map(function(item) {
        return [
            currentType,
            '"' + (item.title || item.name || '').replace(/"/g, '""') + '"',
            parseInt(item.seo_score) || 0,
            '"' + (item.focus_keyword || '').replace(/"/g, '""') + '"',
            (item.meta_title || '').length,
            (item.meta_description || '').length,
            item.is_active || item.status === 'published' ? 'Active' : 'Hidden'
        ].join(',');
    }).join('\n');
    
    var blob = new Blob([csv], { type: 'text/csv' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'seo-report-' + currentType + '-' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
}

// Initialize
switchType('blogs');
</script>
JS;
require 'admin-footer.php';
?>
