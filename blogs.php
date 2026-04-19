<?php
$page_title   = "Travel Blogs & Stories | CSNExplore";
$current_page = "blogs.php";
require_once 'php/config.php';

$page_meta = [
    'description' => 'Read our latest travel blogs, local tips, and stories from Chhatrapati Sambhajinagar. Discover Ellora, Ajanta, and hidden gems.',
    'canonical'   => BASE_PATH . '/blogs',
    'type'        => 'website',
    'image'       => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1600&q=80'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "CSNExplore Travel Blogs",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/blogs",
  "publisher": {
    "@type": "Organization",
    "name": "CSNExplore"
  }
}
</script>';
$db = getDB();

// Load More: initial 9, then 9 more per click
$per_page = 9;
$page     = max(1, (int)($_GET['page'] ?? 1));
$offset   = ($page - 1) * $per_page;

// Filters
$cat_filter    = trim($_GET['category'] ?? '');
$search_filter = trim($_GET['search'] ?? '');

// Build query
$where  = ["status = 'published'"];
$params = [];
if ($cat_filter) { $where[] = 'category = ?'; $params[] = $cat_filter; }
if ($search_filter) { $where[] = '(title LIKE ? OR content LIKE ?)'; $params[] = '%'.$search_filter.'%'; $params[] = '%'.$search_filter.'%'; }
$where_sql = implode(' AND ', $where);

$total_blogs = $db->fetchOne("SELECT COUNT(*) as cnt FROM blogs WHERE $where_sql", $params)['cnt'];
$total_pages = max(1, (int)ceil($total_blogs / $per_page));

$blogs = $db->fetchAll("SELECT * FROM blogs WHERE $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET $offset", $params);
foreach ($blogs as &$b) $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
unset($b);

// Featured = first blog on page 1 with no filters
$featured = null;
if ($page === 1 && !$cat_filter && !$search_filter && !empty($blogs)) {
    $featured = array_shift($blogs);
}

// Categories for filter
$categories = $db->fetchAll("SELECT DISTINCT category FROM blogs WHERE status='published' ORDER BY category ASC");

function blogSlug($blog) {
    $t = strtolower(trim($blog['title']));
    $t = preg_replace('/[^a-z0-9\s-]/', '', $t);
    $t = preg_replace('/[\s-]+/', '-', $t);
    return BASE_PATH . '/blogs/' . $blog['id'] . '-' . substr(trim($t, '-'), 0, 60) . '.html';
}

$extra_styles = "
    .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
    .blog-hidden { display:none !important; }
";
require 'header.php';
?>

<main style="background: #f8f6f6;">

<!-- Shared hero with breadcrumb at top -->
<section class="relative h-[420px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover"
             src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1600&q=80"
             alt="CSNExplore Blogs"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black"></div>
    </div>
    <!-- Breadcrumb at very top of hero -->
    <div class="absolute top-0 left-0 right-0 z-20 pt-5">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold">Blogs</span>
            <?php if ($cat_filter): ?>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-primary font-semibold"><?php echo htmlspecialchars($cat_filter); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div data-reveal class="relative z-10 text-center px-5 max-w-[1140px] mx-auto w-full">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-4 py-1.5 rounded-full bg-primary/20 text-primary font-bold text-xs uppercase tracking-widest mb-4">Travel Stories</span>
            <h1 class="text-5xl md:text-6xl font-serif font-black text-white mb-4 leading-tight">
                <?php echo $cat_filter ? htmlspecialchars($cat_filter) : 'Explore Our Blogs'; ?>
            </h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">Guides, tips and stories from Chhatrapati Sambhajinagar.</p>
        </div>
    </div>
</section>

<?php if ($featured): ?>
<!-- Featured Article strip below hero -->
<div data-reveal="scale" class="bg-white py-8 border-b border-slate-100">
    <div class="max-w-[1140px] mx-auto px-5 text-slate-900">
        <a href="<?php echo blogSlug($featured); ?>" class="group flex flex-col md:flex-row gap-6 items-center bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl p-5 transition-all shadow-sm">
            <div class="w-full md:w-64 h-40 rounded-xl overflow-hidden shrink-0">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     src="<?php echo htmlspecialchars($featured['image'] ?? ''); ?>"
                     alt="<?php echo htmlspecialchars($featured['title']); ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80'"/>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-primary text-white text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full">Featured Story</span>
                    <span class="text-slate-500 text-sm"><?php echo htmlspecialchars($featured['read_time'] ?? '5 min read'); ?> · <?php echo htmlspecialchars($featured['category']); ?></span>
                </div>
                <h2 class="text-slate-900 text-xl md:text-2xl font-serif font-black leading-tight mb-2 group-hover:text-primary transition-colors">
                    <?php echo htmlspecialchars($featured['title']); ?>
                </h2>
                <p class="text-slate-600 text-sm line-clamp-2"><?php echo htmlspecialchars($featured['meta_description'] ?? ''); ?></p>
                <span class="mt-3 inline-flex items-center gap-1 text-primary font-bold text-sm">Read Full Story <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </div>
        </a>
    </div>
</div>
<?php endif; ?>

<?php
// Re-fetch ALL blogs for this filter (for client-side load more)
$all_blogs_for_filter = $db->fetchAll("SELECT * FROM blogs WHERE $where_sql ORDER BY created_at DESC", $params);
foreach ($all_blogs_for_filter as &$b) $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
unset($b);
// Remove featured from the list
if ($featured) {
    $all_blogs_for_filter = array_filter($all_blogs_for_filter, fn($b) => $b['id'] !== $featured['id']);
    $all_blogs_for_filter = array_values($all_blogs_for_filter);
}
$total_grid_blogs = count($all_blogs_for_filter);
?>
<div class="max-w-[1140px] mx-auto px-5 py-12">

    <!-- Filters -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
        <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-2 snap-x snap-mandatory -mx-6 px-6 md:mx-0 md:px-0">
            <a href="<?php echo BASE_PATH; ?>/blogs" class="whitespace-nowrap px-6 py-2.5 snap-start <?php echo !$cat_filter ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white border border-slate-200 text-slate-700 hover:border-primary'; ?> rounded-full font-bold text-sm transition-all active:scale-95">
                All Stories
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_PATH; ?>/blogs?category=<?php echo urlencode($cat['category']); ?>"
               class="whitespace-nowrap px-6 py-2.5 snap-start <?php echo $cat_filter === $cat['category'] ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white border border-slate-200 text-slate-700 hover:border-primary'; ?> rounded-full font-bold text-sm transition-all active:scale-95">
                <?php echo htmlspecialchars($cat['category']); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <form method="GET" action="blogs" class="flex items-center gap-2 w-full lg:w-auto">
            <?php if ($cat_filter): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($cat_filter); ?>"/><?php endif; ?>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_filter); ?>"
                   placeholder="Search blogs..."
                   class="border border-slate-200 bg-white rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 w-48 text-slate-900"/>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition-all">Search</button>
        </form>
    </div>

    <!-- Blog Grid -->
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-2xl font-serif font-black flex items-center gap-3 text-slate-900">
            <span class="w-8 h-1 bg-primary rounded-full inline-block"></span>
            <?php echo $cat_filter ? htmlspecialchars($cat_filter) : 'Latest Insights'; ?>
            <span class="text-sm font-normal text-slate-400">(<?php echo $total_blogs; ?> posts)</span>
        </h3>
    </div>

    <?php if (empty($all_blogs_for_filter) && !$featured): ?>
    <div class="text-center py-20 text-slate-400">
        <span class="material-symbols-outlined text-5xl mb-3 block">article</span>
        <p class="text-lg font-semibold">No blog posts found</p>
        <p class="text-sm mt-1">Try a different search or category</p>
        <a href="<?php echo BASE_PATH; ?>/blogs" class="mt-4 inline-block text-primary font-bold hover:underline">View all blogs</a>
    </div>
    <?php else: ?>
    <div data-reveal-stagger id="blogs-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($all_blogs_for_filter as $bi => $blog): ?>
        <article data-reveal class="flex flex-col group h-full relative bg-white p-5 rounded-2xl shadow-sm border border-slate-100<?php echo $bi >= 9 ? ' blog-hidden' : ''; ?>">
            <!-- Entire Card Link -->
            <a href="<?php echo blogSlug($blog); ?>" class="absolute inset-0 z-10" aria-label="<?php echo htmlspecialchars($blog['title']); ?>"></a>

            <div class="relative rounded-2xl overflow-hidden mb-5 aspect-video shadow-lg">
                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                     src="<?php echo htmlspecialchars($blog['image'] ?? ''); ?>"
                     alt="<?php echo htmlspecialchars($blog['title']); ?>"
                     loading="lazy"
                     onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80'"/>
                <div class="absolute top-4 left-4">
                    <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-primary uppercase relative z-20">
                        <?php echo htmlspecialchars($blog['category']); ?>
                    </span>
                </div>
            </div>
            <div class="flex flex-col flex-grow">
                <div class="flex items-center gap-4 mb-3 text-slate-500 text-xs font-semibold">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        <?php echo htmlspecialchars($blog['read_time'] ?? '5 min read'); ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                        <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                    </span>
                </div>
                <h4 class="text-xl font-serif font-bold leading-snug mb-3 group-hover:text-primary transition-colors text-slate-900">
                    <?php echo htmlspecialchars($blog['title']); ?>
                </h4>
                <p class="text-slate-600 text-sm line-clamp-2 mb-6">
                    <?php echo htmlspecialchars($blog['meta_description'] ?? strip_tags(substr($blog['content'], 0, 150)) . '...'); ?>
                </p>
                <div class="mt-auto pt-4 border-t border-slate-100 flex justify-between items-center">
                    <a class="text-primary font-bold text-sm flex items-center gap-1 group/btn" href="<?php echo blogSlug($blog); ?>">
                        Read More
                        <span class="material-symbols-outlined text-base transition-transform group-hover/btn:translate-x-1">chevron_right</span>
                    </a>
                    <span class="text-xs text-slate-400"><?php echo htmlspecialchars($blog['author']); ?></span>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Load More -->
    <?php if ($total_grid_blogs > 9): ?>
    <div class="mt-12 flex flex-col items-center gap-2" id="blog-load-more-wrap">
        <button id="blog-load-more-btn" onclick="loadMoreBlogs()"
                class="px-8 py-3 bg-primary text-white font-bold rounded-2xl hover:bg-orange-600 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
            <span class="material-symbols-outlined">expand_more</span>
            Load More Stories
        </button>
        <p id="blog-load-more-count" class="text-sm text-slate-400">Showing 9 of <?php echo $total_grid_blogs; ?></p>
    </div>
    <script>
    var _bShown = 9, _bBatch = 9;
    function loadMoreBlogs() {
        var hidden = document.querySelectorAll('#blogs-grid .blog-hidden');
        var toShow = Array.from(hidden).slice(0, _bBatch);
        toShow.forEach(function(el){ el.classList.remove('blog-hidden'); });
        _bShown += toShow.length;
        var total = <?php echo $total_grid_blogs; ?>;
        document.getElementById('blog-load-more-count').textContent = 'Showing ' + _bShown + ' of ' + total;
        if (_bShown >= total) {
            document.getElementById('blog-load-more-wrap').style.display = 'none';
        }
    }
    </script>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Newsletter -->
    <section class="mt-20 mb-8 p-8 md:p-12 rounded-3xl bg-primary/10 border border-primary/20 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1">
            <h3 class="text-3xl font-serif font-black mb-4 text-slate-900">Never miss a hidden gem</h3>
            <p class="text-slate-600">Weekly travel inspiration, local tips, and exclusive stories from Sambhajinagar.</p>
        </div>
        <div class="flex-1 w-full max-w-md">
            <form method="POST" action="subscribe" class="flex flex-col gap-4">
                <input type="email" name="email" required placeholder="Your email address"
                       class="flex-grow rounded-xl border border-slate-200 bg-white focus:ring-primary focus:border-primary px-6 py-4 text-sm outline-none text-slate-900"/>
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-xl transition-all shadow-lg shadow-primary/20">Subscribe</button>
            </form>
        </div>
    </section>
</div>
</main>

<?php require 'footer.php'; ?>
