<?php
$page_title   = "Travel Blogs & Stories | CSNExplore – Chhatrapati Sambhajinagar (Aurangabad)";
$current_page = "blogs.php";
require_once 'php/config.php';

$page_meta = [
    'description' => 'Read travel blogs, local tips, and stories from Chhatrapati Sambhajinagar (Aurangabad). Explore Ajanta Caves, Ellora Caves, Bibi Ka Maqbara and hidden gems.',
    'canonical'   => 'https://csnexplore.com/blogs',
    'type'        => 'website',
    'image'       => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80&auto=format',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Blogs', 'url' => '/blogs'],
    ],
];

$db = getDB();

// Filters
$cat_filter    = trim($_GET['category'] ?? '');
$search_filter = trim($_GET['search'] ?? '');

// Build query
$where  = ["status = 'published'"];
$params = [];
if ($cat_filter) { $where[] = 'category = ?'; $params[] = $cat_filter; }
if ($search_filter) { $where[] = '(title LIKE ? OR content LIKE ?)'; $params[] = '%'.$search_filter.'%'; $params[] = '%'.$search_filter.'%'; }
$where_sql = implode(' AND ', $where);

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$total_cache_key = 'blogs_count_' . md5($where_sql . serialize($params));
$total_blogs = ObjectCache::get($total_cache_key);
if ($total_blogs === false) {
    $total_blogs = $db->fetchOne("SELECT COUNT(*) as cnt FROM blogs WHERE $where_sql", $params)['cnt'];
    ObjectCache::set($total_cache_key, $total_blogs, 3600);
}

$cache_key = 'blogs_page_' . $page . '_' . md5($where_sql . serialize($params));
$all_blogs_for_filter = ObjectCache::get($cache_key);
if ($all_blogs_for_filter === false) {
    $all_blogs_for_filter = $db->fetchAll("SELECT id, title, image, category, read_time, created_at, author, excerpt, meta_description, tags FROM blogs WHERE $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset", $params);
    ObjectCache::set($cache_key, $all_blogs_for_filter, 3600);
}

foreach ($all_blogs_for_filter as &$b) $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
unset($b);

// Featured = first blog on page 1 with no filters
$featured = null;
if (!$cat_filter && !$search_filter && $page === 1 && !empty($all_blogs_for_filter)) {
    $featured = array_shift($all_blogs_for_filter);
}

// Categories for filter
$cat_cache_key = 'blogs_categories';
$categories = ObjectCache::get($cat_cache_key);
if ($categories === false) {
    $categories = $db->fetchAll("SELECT DISTINCT category FROM blogs WHERE status='published' ORDER BY category ASC");
    ObjectCache::set($cat_cache_key, $categories, 86400);
}

function blogSlug($blog) {
    $t = strtolower(trim($blog['title']));
    $t = preg_replace('/[^a-z0-9\s-]/', '', $t);
    $t = preg_replace('/[\s-]+/', '-', $t);
    return BASE_PATH . '/blogs/' . $blog['id'] . '-' . substr(trim($t, '-'), 0, 60) . '.html';
}

// Dynamic Pagination Meta Links and CollectionPage Schema
$totalPages = (int)ceil($total_blogs / $limit);
$prev_link = '';
$next_link = '';
$cat_query = $cat_filter ? '&category=' . urlencode($cat_filter) : '';
$search_query = $search_filter ? '&search=' . urlencode($search_filter) : '';

if ($page > 1) {
    $prev_url = 'https://csnexplore.com/blogs?page=' . ($page - 1) . $cat_query . $search_query;
    $prev_link = '<link rel="prev" href="' . htmlspecialchars($prev_url) . '">' . "\n";
}
if ($page < $totalPages) {
    $next_url = 'https://csnexplore.com/blogs?page=' . ($page + 1) . $cat_query . $search_query;
    $next_link = '<link rel="next" href="' . htmlspecialchars($next_url) . '">' . "\n";
}

$extra_head = $prev_link . $next_link . '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "CSNExplore Travel Blogs & Stories",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/blogs?page=' . $page . $cat_query . $search_query . '",
  "isPartOf": {
    "@type": "WebSite",
    "name": "CSNExplore",
    "url": "https://csnexplore.com"
  },
  "about": {
    "@type": "Place",
    "name": "Chhatrapati Sambhajinagar",
    "alternateName": "Aurangabad"
  },
  "publisher": {
    "@type": "Organization",
    "name": "CSNExplore",
    "logo": {
      "@type": "ImageObject",
      "url": "https://csnexplore.com/images/Logo-light-optimized.webp"
    }
  }
}
</script>';

$extra_styles = "
    .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
    .blog-hidden { display:none !important; }
";
require 'header.php';
?>

<main style="background: #f8f6f6;">

<!-- Shared hero with video background -->
<section class="relative min-h-[500px] flex items-center justify-center overflow-hidden pt-28">
    <div class="absolute inset-0 z-0">
        <video class="w-full h-full object-cover" autoplay muted loop playsinline>
            <source src="<?php echo BASE_PATH; ?>/videos/blog-vid.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-br from-primary/70 to-primary/30 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-black/30"></div>
    </div>
    <!-- Breadcrumb at very top of hero -->
    <div class="absolute top-0 left-0 right-0 z-20 pt-28">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold">Blogs</span>
            <?php if ($cat_filter): ?>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold"><?php echo htmlspecialchars($cat_filter); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div data-reveal class="relative z-10 text-center px-5 max-w-[1140px] mx-auto w-full py-16">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif font-black text-white mb-6 leading-tight drop-shadow-lg">
                <?php echo $cat_filter ? htmlspecialchars($cat_filter) : 'Travel Stories &<br><em class="not-italic text-amber-400">Guides</em>'; ?>
            </h1>
            <p class="text-white/90 text-lg md:text-xl max-w-2xl mx-auto mb-8 drop-shadow">Guides, tips and stories from Chhatrapati Sambhajinagar.</p>
            <a href="#blogs-grid" class="inline-flex items-center gap-2 px-8 py-4 border-2 border-white/60 rounded-full text-white font-semibold backdrop-blur-sm bg-white/10 hover:bg-amber-500 hover:border-amber-500 hover:scale-105 transition-all shadow-lg hover:shadow-amber-500/40">
                <span>Read Blogs</span>
                <span class="material-symbols-outlined">arrow_downward</span>
            </a>
        </div>
    </div>
</section>

<?php if ($featured): ?>
<!-- Featured Article strip below hero -->
<div data-reveal="scale" class="bg-white py-8 border-b border-slate-100">
    <div class="max-w-[1140px] mx-auto px-5 text-slate-900">
        <a href="<?php echo blogSlug($featured); ?>" class="group flex flex-col md:flex-row gap-6 items-center bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl p-5 transition-all shadow-sm">
            <div class="w-full md:w-64 h-40 rounded-xl overflow-hidden shrink-0">
                <img loading="lazy" width="800" height="600" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     src="<?php echo htmlspecialchars(get_working_image_url($featured['image'] ?? '')); ?>"
                     alt="<?php echo htmlspecialchars($featured['title']); ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80&auto=format'"/>
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
// `$all_blogs_for_filter` already fetched and cached above.
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
        <article data-reveal class="flex flex-col group h-full relative bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <!-- Entire Card Link -->
            <a href="<?php echo blogSlug($blog); ?>" class="absolute inset-0 z-10" aria-label="<?php echo htmlspecialchars($blog['title']); ?>"></a>

            <div class="relative rounded-2xl overflow-hidden mb-5 aspect-video shadow-lg" style="transform: translateZ(0); -webkit-transform: translateZ(0); -webkit-mask-image: -webkit-radial-gradient(white, black); -webkit-backface-visibility: hidden; backface-visibility: hidden; isolation: isolate;">
                <?php
                    $imgSrc = $blog['image'] ?? '';
                    $srcset = '';
                    $sizesAttr = 'sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"';
                    if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                        $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                        $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                        $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                        $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                        $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                        if (file_exists($p400) && file_exists($p700)) {
                            $imgFinal = get_working_image_url($imgSrc);
                            $srcset = 'srcset="'.$v400.' 400w, '.$v700.' 700w, '.$imgFinal.' 800w"';
                        }
                    }
                ?>
                <img loading="lazy" decoding="async" width="800" height="600" class="w-full h-full object-cover rounded-2xl transition-transform duration-500 group-hover:scale-110"
                     src="<?php echo htmlspecialchars(get_working_image_url($imgSrc)); ?>" <?php echo $srcset; ?> <?php echo $sizesAttr; ?>
                     alt="<?php echo htmlspecialchars($blog['title']); ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80&auto=format'"/>
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
                    <?php echo htmlspecialchars($blog['meta_description'] ?: ($blog['excerpt'] ?: '')); ?>
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

    <?php endif; ?>

    <?php if ($total_blogs > $limit): $totalPages = ceil($total_blogs / $limit); ?>
    <div class="flex justify-center mt-12 gap-2">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page-1; ?><?php echo $cat_filter?'&category='.urlencode($cat_filter):''; ?><?php echo $search_filter?'&search='.urlencode($search_filter):''; ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:border-primary hover:text-primary transition-all shadow-sm">Prev</a>
        <?php endif; ?>
        <span class="px-4 py-2 text-slate-500 font-semibold bg-slate-50 rounded-xl border border-slate-100 shadow-sm">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page+1; ?><?php echo $cat_filter?'&category='.urlencode($cat_filter):''; ?><?php echo $search_filter?'&search='.urlencode($search_filter):''; ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:border-primary hover:text-primary transition-all shadow-sm">Next</a>
        <?php endif; ?>
    </div>
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


