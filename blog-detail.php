<?php
require_once 'php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: blogs'); exit; }

$db   = getDB();
$blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ? AND status = 'published'", [$id]);
if (!$blog) { header('Location: blogs'); exit; }

$blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];

// Related blogs: same category, exclude current
$related = $db->fetchAll(
    "SELECT id, title, image, category, read_time, created_at FROM blogs WHERE status='published' AND category = ? AND id != ? ORDER BY created_at DESC LIMIT 3",
    [$blog['category'], $id]
);

$page_title   = htmlspecialchars($blog['title']) . ' | CSNExplore';
$current_page = 'blogs.php';

$page_meta = [
    'seo_type'    => 'blog',
    'item'        => $blog,
    'description' => htmlspecialchars($blog['meta_description'] ?? substr(strip_tags($blog['content']), 0, 160)),
    'canonical'   => 'https://csnexplore.com/blogs/' . $blog['id'] . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $blog['title']), '-')),
    'image'       => get_working_image_url($blog['image'] ?? ''),
    'type'        => 'article',
    'breadcrumbs' => [
        ['name' => 'Home',  'url' => '/'],
        ['name' => 'Blogs', 'url' => '/blogs'],
        ['name' => htmlspecialchars($blog['title']), 'url' => ''],
    ],
];

$extra_styles = "
    .prose h2 { font-size:1.5rem; font-weight:800; margin:2rem 0 0.75rem; color:inherit; }
    .prose h3 { font-size:1.2rem; font-weight:700; margin:1.5rem 0 0.5rem; color:inherit; }
    .prose p  { margin-bottom:1.1rem; line-height:1.85; color:inherit; }
    .prose ul { list-style:disc; padding-left:1.5rem; margin-bottom:1.1rem; }
    .prose ul li { margin-bottom:0.4rem; line-height:1.7; }
    .prose strong { font-weight:700; }
    .prose img { max-width:100%; height:auto; border-radius:12px; margin:2rem auto; display:block; box-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
    .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
";
require 'header.php';
?>

<main class="bg-white min-h-screen">

    <!-- Hero: shared bg image with breadcrumb at top, blog title at bottom -->
    <div class="w-full h-[420px] md:h-[500px] relative overflow-hidden pt-28">
        <?php 
            $detailHeroImg = get_working_image_url($blog['image'] ?? '');
            if (!$detailHeroImg) {
                $detailHeroImg = 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80&auto=format';
            }
        ?>
        <img loading="lazy" width="800" height="600" src="<?php echo htmlspecialchars($detailHeroImg); ?>"
             alt="Blog Hero"
             class="absolute inset-0 w-full h-full object-cover"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
        <!-- Breadcrumb at very top of hero -->
        <div class="absolute top-0 left-0 right-0 z-10 pt-28">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-2 text-sm text-white/60 flex-wrap">
                <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">home</span>Home
                </a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <a href="<?php echo BASE_PATH; ?>/blogs" class="hover:text-white transition-colors">Blogs</a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <a href="<?php echo BASE_PATH; ?>/blogs?category=<?php echo urlencode($blog['category']); ?>" class="hover:text-white transition-colors">
                    <?php echo htmlspecialchars($blog['category']); ?>
                </a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <span class="text-white/80 font-semibold truncate max-w-xs"><?php echo htmlspecialchars($blog['title']); ?></span>
            </div>
        </div>
        <!-- Blog title at bottom -->
        <div data-reveal class="absolute bottom-0 left-0 right-0 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
            <span class="inline-block bg-primary text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                <?php echo htmlspecialchars($blog['category']); ?>
            </span>
            <h1 class="text-white text-3xl md:text-4xl lg:text-5xl font-serif font-black leading-tight">
                <?php echo htmlspecialchars($blog['title']); ?>
            </h1>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Meta bar -->
        <div data-reveal="fade" class="flex flex-wrap items-center gap-5 mb-10 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-2 text-slate-500 text-sm">
                <span class="material-symbols-outlined text-base">person</span>
                <span class="font-semibold"><?php echo htmlspecialchars($blog['author']); ?></span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 text-sm">
                <span class="material-symbols-outlined text-base">calendar_today</span>
                <span><?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
            </div>
            <?php if ($blog['read_time']): ?>
            <div class="flex items-center gap-2 text-slate-500 text-sm">
                <span class="material-symbols-outlined text-base">schedule</span>
                <span><?php echo htmlspecialchars($blog['read_time']); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($blog['meta_description']): ?>
            <p class="w-full text-slate-500 text-base italic mt-1">
                <?php echo htmlspecialchars($blog['meta_description']); ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Article Content -->
        <article class="prose text-slate-800 max-w-none text-base leading-relaxed">
            <?php echo $blog['content']; // HTML content from DB — intentionally not escaped ?>
        </article>

        <!-- Admin Edit Button (Client-side authenticated) -->
        <a href="<?php echo BASE_PATH; ?>/admin/blog-editor-new.php?id=<?php echo $blog['id']; ?>" id="admin-edit-btn" style="display:none;" class="fixed bottom-6 right-6 bg-slate-900 text-white px-5 py-3 rounded-full shadow-2xl z-50 flex items-center gap-2 hover:bg-primary transition-colors font-bold text-sm">
            <span class="material-symbols-outlined text-lg">edit</span>
            Edit this Post
        </a>
        <script>
            if (localStorage.getItem('csn_admin_token')) {
                document.getElementById('admin-edit-btn').style.display = 'inline-flex';
            }
        </script>

        <!-- Tags -->
        <?php if (!empty($blog['tags'])): ?>
        <div class="mt-10 pt-8 border-t border-slate-100 flex flex-wrap gap-2 items-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Tags:</span>
            <?php foreach ($blog['tags'] as $tag): ?>
            <a href="<?php echo BASE_PATH; ?>/blogs?search=<?php echo urlencode($tag); ?>"
               class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-semibold hover:bg-primary hover:text-white transition-colors">
                <?php echo htmlspecialchars($tag); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Share -->
        <div class="mt-8 flex items-center gap-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Share:</span>
            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($blog['title']); ?>&url=<?php echo urlencode('https://'.($_SERVER['HTTP_HOST'] ?? 'csnexplore.com').($_SERVER['REQUEST_URI'] ?? '')); ?>"
               target="_blank" rel="noopener"
               class="flex items-center gap-1.5 px-4 py-2 bg-[#1DA1F2] text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X / Twitter
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://'.($_SERVER['HTTP_HOST'] ?? 'csnexplore.com').($_SERVER['REQUEST_URI'] ?? '')); ?>"
               target="_blank" rel="noopener"
               class="flex items-center gap-1.5 px-4 py-2 bg-[#1877F2] text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
            </a>
            <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link copied!'))"
                    class="flex items-center gap-1.5 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-base">link</span>
                Copy Link
            </button>
        </div>

        <!-- Back button -->
        <div class="mt-10">
            <a href="<?php echo BASE_PATH; ?>/blogs" onclick="if(document.referrer.indexOf(window.location.hostname) !== -1) { window.history.back(); event.preventDefault(); event.stopPropagation(); return false; }" class="inline-flex items-center gap-2 text-primary font-bold hover:underline text-sm">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Go Back
            </a>
        </div>
    </div>

    <?php
    // ── Linked Listings ──────────────────────────────────────────────────────
    $linked = json_decode($blog['linked_listings'] ?? 'null', true);
    if (!empty($linked) && is_array($linked)):
        // Fetch full listing data for each linked item
        $linkedData = [];
        $typeLabels = ['stays'=>'Hotel','cars'=>'Car','bikes'=>'Bike','attractions'=>'Attraction','restaurants'=>'Restaurant','buses'=>'Bus'];
        $typeIcons  = ['stays'=>'bed','cars'=>'directions_car','bikes'=>'motorcycle','attractions'=>'confirmation_number','restaurants'=>'restaurant','buses'=>'directions_bus'];
        foreach ($linked as $ll) {
            $lType = $ll['type'] ?? ''; $lId = (int)($ll['id'] ?? 0);
            if (!$lType || !$lId) continue;
            try {
                $validTypes = ['stays','cars','bikes','attractions','restaurants','buses'];
                if (!in_array($lType, $validTypes)) continue;
                $lItem = $db->fetchOne("SELECT id, name" . ($lType==='buses'?', operator AS name2':'') . ", image, rating, description FROM `$lType` WHERE id=? AND is_active=1", [$lId]);
                if ($lItem) {
                    $lItem['_type'] = $lType;
                    $lItem['_url']  = BASE_PATH . '/listing-detail/' . generateSlug($lType, $lId, $lItem['name'] ?? $ll['name'] ?? '');
                    $lItem['_label'] = $typeLabels[$lType] ?? $lType;
                    $lItem['_icon']  = $typeIcons[$lType] ?? 'storefront';
                    $linkedData[] = $lItem;
                }
            } catch(Exception $e) {}
        }
    ?>
    <?php if (!empty($linkedData)): ?>
    <div class="border-t border-slate-100 bg-gradient-to-b from-slate-50 to-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-2xl font-serif font-black mb-2 flex items-center gap-3">
                <span class="w-8 h-1 bg-primary rounded-full inline-block"></span>
                Featured Listings in This Article
            </h3>
            <p class="text-slate-500 text-sm mb-8">Hand-picked options recommended in this blog post</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($linkedData as $li): ?>
                <a href="<?php echo $li['_url']; ?>" class="group flex gap-5 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all p-4">
                    <div class="w-36 sm:w-44 h-32 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                        <?php $imgSrc = $li['image'] ?? ''; ?>
                        <img loading="lazy" width="180" height="130"
                             src="<?php echo htmlspecialchars(get_working_image_url($imgSrc) ?: 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=300&q=70&auto=format'); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=300&q=70&auto=format'"
                             alt="<?php echo htmlspecialchars($li['name']??''); ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                    </div>
                    <div class="py-2 flex flex-col justify-center flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="material-symbols-outlined text-primary text-base"><?php echo $li['_icon']; ?></span>
                            <span class="text-xs font-bold text-primary uppercase tracking-wider"><?php echo $li['_label']; ?></span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-lg leading-tight group-hover:text-primary transition-colors mb-2 truncate"><?php echo htmlspecialchars($li['name']??''); ?></h4>
                        <?php if (!empty($li['rating'])): ?>
                        <div class="flex items-center gap-1 text-amber-400 text-sm mb-2">
                            <span class="material-symbols-outlined text-sm">star</span>
                            <span class="font-bold"><?php echo number_format((float)$li['rating'],1); ?></span>
                        </div>
                        <?php endif; ?>
                        <span class="mt-auto inline-flex items-center gap-1 text-primary text-sm font-semibold group-hover:underline">View Details <span class="material-symbols-outlined text-sm">arrow_forward</span></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- ── Comment Section ─────────────────────────────────────────────────── -->
    <div class="border-t border-slate-100 py-14" id="comments-section">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-2xl font-serif font-black mb-2 flex items-center gap-3">
                <span class="w-8 h-1 bg-primary rounded-full inline-block"></span>
                Comments <span id="comment-count-badge" class="text-base font-normal text-slate-400 ml-2"></span>
            </h3>
            <p class="text-slate-500 text-sm mb-8">Share your thoughts, tips or questions about this article.</p>

            <!-- Write comment (shown only when logged in via JS) -->
            <div id="comment-form-wrap" class="mb-10 hidden">
                <div class="flex gap-3 items-start">
                    <div id="comment-avatar" class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-black text-sm shrink-0">?</div>
                    <div class="flex-1">
                        <textarea id="comment-input" rows="3"
                            placeholder="Write a comment..."
                            maxlength="1000"
                            class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            oninput="document.getElementById('comment-char').textContent=this.value.length+'/1000'"></textarea>
                        <div class="flex items-center justify-between mt-2">
                            <span id="comment-char" class="text-xs text-slate-400">0/1000</span>
                            <button id="comment-submit-btn" onclick="submitComment('blog', <?php echo $id; ?>)"
                                class="px-5 py-2 bg-primary text-white rounded-full text-sm font-bold hover:bg-orange-600 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">send</span> Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Not logged in prompt -->
            <div id="comment-login-prompt" class="mb-10 hidden">
                <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-2xl p-5">
                    <span class="material-symbols-outlined text-3xl text-slate-300">chat_bubble</span>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-700 mb-1">Join the conversation</p>
                        <p class="text-sm text-slate-500">You need to be logged in to post a comment.</p>
                    </div>
                    <a href="<?php echo BASE_PATH; ?>/login" class="px-4 py-2 bg-primary text-white rounded-full text-sm font-bold hover:bg-orange-600 transition-all shrink-0">Log In</a>
                </div>
            </div>

            <!-- Comments list -->
            <div id="comments-list" class="space-y-5">
                <div class="flex items-center gap-3 text-slate-400 text-sm py-6 justify-center" id="comments-loading">
                    <span class="material-symbols-outlined animate-spin">progress_activity</span> Loading comments...
                </div>
            </div>
        </div>
    </div>

    <!-- Related Blogs -->
    <?php if (!empty($related)): ?>
    <div class="border-t border-slate-100 bg-slate-50 py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-2xl font-serif font-black mb-8 flex items-center gap-3">
                <span class="w-8 h-1 bg-primary rounded-full inline-block"></span>
                Related Stories
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($related as $r): ?>
                <a href="<?php echo BASE_PATH; ?>/blog-detail?id=<?php echo $r['id']; ?>" class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-slate-100 hover:shadow-lg transition-shadow">
                    <div class="aspect-video overflow-hidden">
                        <?php
                            $imgSrc = $r['image'] ?? '';
                            $srcset = '';
                            $sizesAttr = 'sizes="(max-width: 640px) 100vw, (max-width: 1024px) 33vw, 33vw"';
                            if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                                $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                                $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                                $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                                $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                                $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                                if (file_exists($p400) && file_exists($p700)) {
                                    $imgFinal = BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                                    $srcset = 'srcset="'.$v400.' 400w, '.$v700.' 700w, '.$imgFinal.' 800w"';
                                }
                            }
                        ?>
                        <img loading="lazy" decoding="async" width="800" height="600" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             src="<?php echo htmlspecialchars($imgSrc); ?>" <?php echo $srcset; ?> <?php echo $sizesAttr; ?>
                             alt="<?php echo htmlspecialchars($r['title']); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80&auto=format'"/>
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <span class="text-xs font-bold text-primary uppercase mb-2"><?php echo htmlspecialchars($r['category']); ?></span>
                        <h4 class="text-sm font-bold line-clamp-2 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($r['title']); ?></h4>
                        <div class="mt-auto pt-3 flex items-center gap-3 text-xs text-slate-400">
                            <span><?php echo date('M d, Y', strtotime($r['created_at'])); ?></span>
                            <?php if ($r['read_time']): ?>
                            <span>· <?php echo htmlspecialchars($r['read_time']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<script>
// ── Comment Engine ────────────────────────────────────────────────────────────
(function() {
    const BASE = '<?php echo BASE_PATH; ?>';
    let _currentUser = null;
    let _token = null;

    function init() {
        _token = localStorage.getItem('csn_token');
        _currentUser = JSON.parse(localStorage.getItem('csn_user') || 'null');

        // Show correct UI state
        const formWrap    = document.getElementById('comment-form-wrap');
        const loginPrompt = document.getElementById('comment-login-prompt');

        if (_currentUser && _token) {
            if (formWrap) formWrap.classList.remove('hidden');
            const av = document.getElementById('comment-avatar');
            if (av) av.textContent = (_currentUser.name || 'U')[0].toUpperCase();
        } else {
            if (loginPrompt) loginPrompt.classList.remove('hidden');
        }
    }

    window.loadComments = async function(refType, refId) {
        const list = document.getElementById('comments-list');
        const badge = document.getElementById('comment-count-badge');
        try {
            const res  = await fetch(`${BASE}/php/api/comments.php?ref_type=${refType}&ref_id=${refId}`);
            const data = await res.json();
            const comments = data.comments || [];

            if (badge) badge.textContent = comments.length > 0 ? `(${comments.length})` : '';
            list.innerHTML = '';

            if (comments.length === 0) {
                list.innerHTML = '<div class="text-center py-12 text-slate-400"><span class="material-symbols-outlined text-5xl block mb-3 opacity-30">chat_bubble_outline</span><p class="text-sm">No comments yet. Be the first to comment!</p></div>';
                return;
            }
            comments.forEach(c => list.appendChild(renderComment(c, refType, refId)));
        } catch(e) {
            list.innerHTML = '<div class="text-slate-400 text-sm text-center py-6">Could not load comments.</div>';
        }
    };

    function renderComment(c, refType, refId) {
        const isOwn   = _currentUser && _currentUser.id == c.user_id;
        const isAdmin = _currentUser && _currentUser.role === 'admin';
        const date    = new Date(c.created_at).toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' });
        const initial = (c.user_name || 'U')[0].toUpperCase();

        const div = document.createElement('div');
        div.id = `comment-${c.id}`;
        div.className = 'flex gap-3 group';
        div.innerHTML = `
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-orange-400 flex items-center justify-center text-white font-black text-sm shrink-0">${initial}</div>
            <div class="flex-1 bg-slate-50 rounded-2xl px-4 py-3">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm">${c.user_name}</span>
                        <span class="text-xs text-slate-400">${date}</span>
                    </div>
                    ${(isOwn || isAdmin) ? `<button onclick="deleteComment(${c.id},'${refType}',${refId})" class="opacity-0 group-hover:opacity-100 transition-opacity text-slate-400 hover:text-red-500 text-xs flex items-center gap-0.5"><span class="material-symbols-outlined text-sm">delete</span></button>` : ''}
                </div>
                <p class="text-slate-700 text-sm leading-relaxed">${c.content}</p>
            </div>`;
        return div;
    }

    window.submitComment = async function(refType, refId) {
        const input  = document.getElementById('comment-input');
        const btn    = document.getElementById('comment-submit-btn');
        const content = (input?.value || '').trim();
        if (!content) { input?.focus(); return; }
        if (!_token)  { window.location.href = BASE + '/login'; return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">progress_activity</span> Posting...';

        try {
            const res  = await fetch(`${BASE}/php/api/comments.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + _token },
                body: JSON.stringify({ ref_type: refType, ref_id: refId, content })
            });
            const data = await res.json();
            if (!res.ok || data.error) throw new Error(data.error || 'Failed');
            
            input.value = '';
            document.getElementById('comment-char').textContent = '0/1000';

            // Prepend the new comment
            const list = document.getElementById('comments-list');
            const emptyMsg = list.querySelector('[class*="chat_bubble_outline"]');
            if (emptyMsg) list.innerHTML = '';
            list.prepend(renderComment(data.comment, refType, refId));

            // Update badge
            const badge = document.getElementById('comment-count-badge');
            if (badge) {
                const cur = parseInt(badge.textContent.replace(/\D/g,'')) || 0;
                badge.textContent = `(${cur + 1})`;
            }
        } catch(e) {
            alert('Error: ' + e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-base">send</span> Post Comment';
        }
    };

    window.deleteComment = async function(id, refType, refId) {
        if (!confirm('Delete this comment?')) return;
        try {
            const res = await fetch(`${BASE}/php/api/comments.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + _token }
            });
            const data = await res.json();
            if (data.success) {
                const el = document.getElementById(`comment-${id}`);
                if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }
                const badge = document.getElementById('comment-count-badge');
                if (badge) {
                    const cur = parseInt(badge.textContent.replace(/\D/g,'')) || 1;
                    badge.textContent = cur > 1 ? `(${cur - 1})` : '';
                }
            }
        } catch(e) { alert('Could not delete comment'); }
    };

    document.addEventListener('DOMContentLoaded', () => {
        init();
        window.loadComments('blog', <?php echo $id; ?>);
    });
})();
</script>

<?php require 'footer.php'; ?>
