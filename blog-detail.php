<?php
require_once 'php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: blogs.php'); exit; }

$db   = getDB();
$blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ? AND status = 'published'", [$id]);
if (!$blog) { header('Location: blogs.php'); exit; }

$blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];

// Related blogs: same category, exclude current
$related = $db->fetchAll(
    "SELECT id, title, image, category, read_time, created_at FROM blogs WHERE status='published' AND category = ? AND id != ? ORDER BY created_at DESC LIMIT 3",
    [$blog['category'], $id]
);

$page_title   = htmlspecialchars($blog['title']) . ' | CSNExplore';
$current_page = 'blogs.php';

$page_meta = [
    'description' => htmlspecialchars($blog['meta_description'] ?? substr(strip_tags($blog['content']), 0, 160)),
    'canonical' => "https://csnexplore.com/blog-detail.php?id=" . $id,
    'image' => (strpos($blog['image'] ?? '', 'http') === 0) ? $blog['image'] : "https://csnexplore.com/" . ltrim($blog['image'] ?? '', '/'),
    'type' => 'article'
];

$extra_styles = "
    .prose h2 { font-size:1.5rem; font-weight:800; margin:2rem 0 0.75rem; color:inherit; }
    .prose h3 { font-size:1.2rem; font-weight:700; margin:1.5rem 0 0.5rem; color:inherit; }
    .prose p  { margin-bottom:1.1rem; line-height:1.85; color:inherit; }
    .prose ul { list-style:disc; padding-left:1.5rem; margin-bottom:1.1rem; }
    .prose ul li { margin-bottom:0.4rem; line-height:1.7; }
    .prose strong { font-weight:700; }
    .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
";
require 'header.php';
?>

<main class="bg-white min-h-screen">

    <!-- Hero: shared bg image with breadcrumb at top, blog title at bottom -->
    <div class="w-full h-[420px] md:h-[500px] relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1600&q=80"
             alt="Blog Hero"
             class="w-full h-full object-cover"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
        <!-- Breadcrumb at very top of hero -->
        <div class="absolute top-0 left-0 right-0 z-10 pt-5">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-2 text-sm text-white/60 flex-wrap">
                <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">home</span>Home
                </a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <a href="<?php echo BASE_PATH; ?>/blogs" class="hover:text-white transition-colors">Blogs</a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
                <a href="<?php echo BASE_PATH; ?>/blogs.php?category=<?php echo urlencode($blog['category']); ?>" class="hover:text-white transition-colors">
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
        <article data-reveal class="prose text-slate-800 max-w-none text-base leading-relaxed">
            <?php echo $blog['content']; // HTML content from DB — intentionally not escaped ?>
        </article>

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

        <!-- Back to blogs -->
        <div class="mt-10">
            <a href="<?php echo BASE_PATH; ?>/blogs" class="inline-flex items-center gap-2 text-primary font-bold hover:underline text-sm">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Back to all blogs
            </a>
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
                <a href="<?php echo BASE_PATH; ?>/blog-detail.php?id=<?php echo $r['id']; ?>" class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-slate-100 hover:shadow-lg transition-shadow">
                    <div class="aspect-video overflow-hidden">
                        <img src="<?php echo htmlspecialchars($r['image'] ?? ''); ?>"
                             alt="<?php echo htmlspecialchars($r['title']); ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80'"/>
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

<?php require 'footer.php'; ?>
