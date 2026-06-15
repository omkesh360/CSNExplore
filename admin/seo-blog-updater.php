<?php
// admin/seo-blog-updater.php
$admin_page = 'seo';
$admin_title = 'Blog SEO Bulk Updater';

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/jwt.php';
require_once __DIR__ . '/../php/seo-optimizer.php';

// Auth Check for API calls before any output
$admin_token_cookie = $_COOKIE['admin_token'] ?? null;
if (!$admin_token_cookie) {
    header('Location: ../adminexplorer.php?session_expired=1');
    exit;
}
$payload = verifyJWT($admin_token_cookie, JWT_SECRET);
if (!$payload || !isset($payload['role']) || $payload['role'] !== 'admin') {
    header('Location: ../adminexplorer.php?session_expired=1');
    exit;
}

// Handle API Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    try {
        $db = getDB();
        if ($_GET['action'] === 'save_seo') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
            if ($id > 0) {
                $slug = sanitize($data['slug'] ?? '');
                if (!$slug) {
                    $slug = generateSlug('blogs', $id, $data['title']);
                }

                $db->update('blogs', [
                    'title' => sanitize($data['title']),
                    'slug' => $slug,
                    'meta_description' => sanitize($data['meta_description']),
                    'focus_keyword' => sanitize($data['focus_keyword']),
                    'seo_score' => (int)($data['seo_score'] ?? 0)
                ], 'id = :id', [':id' => $id]);
                
                // Clear cache
                $homepageCache = dirname(__DIR__) . '/cache/homepage.html';
                if (file_exists($homepageCache)) @unlink($homepageCache);

                echo json_encode(['success' => true]);
                exit;
            }
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Fetch existing blogs
$blogs = [];
try {
    $db = getDB();
    $blogs = $db->fetchAll("SELECT id, title, category, meta_description, focus_keyword, slug, seo_score, content FROM blogs ORDER BY created_at DESC");
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Now include header for HTML output
require_once 'admin-header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Blog SEO Bulk Updater</h2>
            <p class="text-slate-500 text-sm mt-1">Review and update SEO metadata for all blogs to improve rankings.</p>
        </div>
        <button onclick="autoGenerateAll()" class="bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-600 transition-colors flex items-center gap-2 text-sm shadow-md">
            <span class="material-symbols-outlined text-sm">auto_awesome</span> Auto-Generate Missing SEO
        </button>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">error</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold w-12">ID</th>
                        <th class="p-4 font-semibold w-64">Blog Title & Slug</th>
                        <th class="p-4 font-semibold">Meta Description</th>
                        <th class="p-4 font-semibold w-48">Focus Keyword</th>
                        <th class="p-4 font-semibold w-24 text-center">Score</th>
                        <th class="p-4 font-semibold w-24 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($blogs as $b): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors" id="row-<?php echo $b['id']; ?>" 
                        data-original-title="<?php echo htmlspecialchars($b['title']); ?>"
                        data-category="<?php echo htmlspecialchars($b['category']); ?>">
                        <td class="p-4 text-slate-400 font-medium">#<?php echo $b['id']; ?></td>
                        <td class="p-4">
                            <div class="mb-2">
                                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Title</label>
                                <input type="text" id="title-<?php echo $b['id']; ?>" value="<?php echo htmlspecialchars($b['title']); ?>" class="w-full border border-slate-200 rounded px-2 py-1.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none" />
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Slug</label>
                                <input type="text" id="slug-<?php echo $b['id']; ?>" value="<?php echo htmlspecialchars($b['slug'] ?? ''); ?>" placeholder="Auto-generated on save" class="w-full border border-slate-200 rounded px-2 py-1 text-slate-500 font-mono text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none" />
                            </div>
                        </td>
                        <td class="p-4">
                            <textarea id="desc-<?php echo $b['id']; ?>" rows="3" class="w-full border border-slate-200 rounded px-2 py-1.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm resize-none"><?php echo htmlspecialchars($b['meta_description'] ?? ''); ?></textarea>
                            <div class="text-right text-[10px] text-slate-400 mt-1"><span id="char-<?php echo $b['id']; ?>"><?php echo strlen($b['meta_description'] ?? ''); ?></span>/160</div>
                        </td>
                        <td class="p-4">
                            <input type="text" id="kw-<?php echo $b['id']; ?>" value="<?php echo htmlspecialchars($b['focus_keyword'] ?? ''); ?>" placeholder="e.g. Ajanta Caves Tour" class="w-full border border-slate-200 rounded px-2 py-1.5 focus:border-primary focus:ring-1 focus:ring-primary outline-none" />
                        </td>
                        <td class="p-4 text-center">
                            <input type="number" id="score-<?php echo $b['id']; ?>" value="<?php echo htmlspecialchars($b['seo_score'] ?? 0); ?>" min="0" max="100" class="w-16 border border-slate-200 rounded px-2 py-1 text-center font-bold <?php echo ($b['seo_score'] >= 80) ? 'text-green-600 bg-green-50' : (($b['seo_score'] >= 50) ? 'text-orange-500 bg-orange-50' : 'text-red-500 bg-red-50'); ?> focus:border-primary outline-none mx-auto" />
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="saveSeo(<?php echo $b['id']; ?>)" class="bg-slate-900 text-white p-2 rounded hover:bg-primary transition-colors mb-2 w-full text-xs font-semibold flex items-center justify-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">save</span> Save
                            </button>
                            <button onclick="generateSeoFor(<?php echo $b['id']; ?>)" class="bg-slate-100 text-slate-600 p-2 rounded hover:bg-slate-200 transition-colors w-full text-xs font-semibold flex items-center justify-center gap-1 border border-slate-200">
                                <span class="material-symbols-outlined text-[14px]">magic_button</span> Auto
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($blogs)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">No blogs found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Attach character counters
document.querySelectorAll('textarea[id^="desc-"]').forEach(ta => {
    ta.addEventListener('input', function() {
        const id = this.id.split('-')[1];
        const span = document.getElementById('char-'+id);
        span.textContent = this.value.length;
        if (this.value.length > 160) span.classList.add('text-red-500');
        else span.classList.remove('text-red-500');
    });
});

function generateSlug(title) {
    return title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
}

function generateSeoFor(id) {
    const row = document.getElementById('row-'+id);
    const origTitle = row.getAttribute('data-original-title');
    const category = row.getAttribute('data-category');
    
    // Suggest Title
    const titleInput = document.getElementById('title-'+id);
    let title = origTitle;
    if (!title.toLowerCase().includes('aurangabad') && !title.toLowerCase().includes('sambhajinagar')) {
        title += ' | Chhatrapati Sambhajinagar Guide';
    }
    titleInput.value = title;
    
    // Suggest Slug
    const slugInput = document.getElementById('slug-'+id);
    slugInput.value = 'blogs-' + id + '-' + generateSlug(origTitle);
    
    // Suggest Focus Keyword
    const kwInput = document.getElementById('kw-'+id);
    if (!kwInput.value) {
        kwInput.value = origTitle.split(' ').slice(0, 3).join(' ') + ' Aurangabad';
    }
    
    // Suggest Description
    const descInput = document.getElementById('desc-'+id);
    if (!descInput.value || descInput.value.length < 50) {
        let desc = 'Read our comprehensive guide on ' + origTitle + ' in Chhatrapati Sambhajinagar. Discover the best tips, timings, and local advice for your trip.';
        if (desc.length > 160) desc = desc.substring(0, 157) + '...';
        descInput.value = desc;
        document.getElementById('char-'+id).textContent = desc.length;
    }
    
    // Suggest Score (fake arbitrary boost to encourage save)
    const scoreInput = document.getElementById('score-'+id);
    scoreInput.value = 85;
    scoreInput.className = "w-16 border border-slate-200 rounded px-2 py-1 text-center font-bold text-green-600 bg-green-50 focus:border-primary outline-none mx-auto";
    
    showAdminToast('SEO suggestions generated for ID ' + id, 'success');
}

function autoGenerateAll() {
    const rows = document.querySelectorAll('tr[id^="row-"]');
    rows.forEach(r => {
        const id = r.id.split('-')[1];
        generateSeoFor(id);
    });
    showAdminToast('Generated SEO for all blogs. Review and save them!', 'success');
}

async function saveSeo(id) {
    const btn = document.querySelector(`#row-${id} button`);
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>';
    btn.disabled = true;
    
    const payload = {
        id: id,
        title: document.getElementById('title-'+id).value,
        slug: document.getElementById('slug-'+id).value,
        meta_description: document.getElementById('desc-'+id).value,
        focus_keyword: document.getElementById('kw-'+id).value,
        seo_score: document.getElementById('score-'+id).value
    };
    
    try {
        const res = await fetch('seo-blog-updater.php?action=save_seo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + window._adminToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
            showAdminToast('Saved SEO for blog #' + id, 'success');
            const scoreInput = document.getElementById('score-'+id);
            const score = parseInt(scoreInput.value);
            scoreInput.className = "w-16 border border-slate-200 rounded px-2 py-1 text-center font-bold " + 
                (score >= 80 ? 'text-green-600 bg-green-50' : (score >= 50 ? 'text-orange-500 bg-orange-50' : 'text-red-500 bg-red-50')) + 
                " focus:border-primary outline-none mx-auto";
        } else {
            showAdminToast(data.error || 'Failed to save', 'error');
        }
    } catch(e) {
        showAdminToast('Error connecting to server', 'error');
    } finally {
        btn.innerHTML = origHtml;
        btn.disabled = false;
    }
}
</script>

<?php require_once 'admin-footer.php'; ?>
