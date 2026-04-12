<?php
/**
 * Regenerate all blog HTML pages with animations
 */
require_once __DIR__ . '/config.php';

$db = getDB();

echo "Starting blog regeneration with animations...\n\n";

// Get all published blogs
$blogs = $db->fetchAll("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC");

$count = 0;

foreach ($blogs as $blog) {
    $html = generateBlogHTML($blog);
    
    // Generate slug
    $title = strtolower(trim($blog['title']));
    $title = preg_replace('/[^a-z0-9\s-]/', '', $title);
    $title = preg_replace('/[\s-]+/', '-', $title);
    $slug = $blog['id'] . '-' . substr(trim($title, '-'), 0, 60);
    
    $filename = __DIR__ . '/../blogs/' . $slug . '.html';
    
    // Ensure directory exists
    $dir = dirname($filename);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Write file
    file_put_contents($filename, $html);
    $count++;
    
    if ($count % 50 == 0) {
        echo "  Generated {$count} blogs...\n";
    }
}

echo "\n✅ Successfully generated {$count} blog pages with animations!\n";

/**
 * Generate HTML for a blog page with animations
 */
function generateBlogHTML($blog) {
    $title = htmlspecialchars($blog['title']);
    $content = $blog['content'];
    $image = $blog['image'] ?? '';
    $category = htmlspecialchars($blog['category'] ?? 'Travel');
    $author = htmlspecialchars($blog['author'] ?? 'CSNExplore Team');
    $date = date('F j, Y', strtotime($blog['created_at']));
    $reading_time = max(3, intval(strlen(strip_tags($content)) / 1000));
    
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="en" style="scroll-behavior:smooth">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo $title; ?> | CSNExplore Blog</title>
<meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($content), 0, 160)); ?>">
<link rel="canonical" href="https://csnexplore.com/blogs/<?php echo $blog['id']; ?>-<?php echo substr(preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $blog['title']))), 0, 60); ?>.html" />
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link rel="stylesheet" href="../mobile-responsive.css"/>
<link rel="stylesheet" href="../animations.css"/>
<script>tailwind.config={darkMode:"class",theme:{extend:{colors:{primary:"#ec5b13"},fontFamily:{display:["Inter","sans-serif"],serif:["Playfair Display","serif"]}}}}</script>
<style>
body{opacity:0;will-change:opacity;}
body.page-ready{animation:pageFadeIn 0.2s ease forwards;}
@keyframes pageFadeIn{from{opacity:0;}to{opacity:1;}}
.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;}
.blog-content{max-width:720px;margin:0 auto;}
.blog-content h2{font-size:1.875rem;font-weight:700;margin:2rem 0 1rem;color:#1e293b;}
.blog-content h3{font-size:1.5rem;font-weight:700;margin:1.5rem 0 0.75rem;color:#334155;}
.blog-content p{margin:1rem 0;line-height:1.75;color:#475569;}
.blog-content ul,.blog-content ol{margin:1rem 0;padding-left:2rem;color:#475569;}
.blog-content li{margin:0.5rem 0;}
.blog-content img{border-radius:0.75rem;margin:2rem 0;width:100%;height:auto;}
.blog-content a{color:#ec5b13;text-decoration:underline;}
.blog-content blockquote{border-left:4px solid #ec5b13;padding-left:1.5rem;margin:2rem 0;font-style:italic;color:#64748b;}
</style>
</head>
<body class="bg-white font-display text-slate-900">

<!-- Simple Header -->
<header class="fixed top-0 left-0 right-0 z-50 bg-black border-b border-white/10">
    <nav class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="../" class="flex items-center">
            <img src="../images/travelhub.png" alt="CSNExplore" class="h-8 object-contain"/>
        </a>
        <div class="flex items-center gap-4">
            <a href="../blogs" class="text-white text-sm font-bold hover:text-primary transition">All Blogs</a>
            <a href="../" class="text-white text-sm font-bold hover:text-primary transition">Home</a>
        </div>
    </nav>
</header>

<!-- Hero Section -->
<div class="relative h-[60vh] min-h-[400px] mt-16 overflow-hidden">
    <?php if ($image): ?>
        <div class="absolute inset-0 img-zoom-container" data-animate="zoom-in">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo $title; ?>" class="w-full h-full object-cover img-zoom"/>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
    <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800"></div>
    <?php endif; ?>
    
    <div class="relative h-full flex items-end">
        <div class="max-w-4xl mx-auto px-4 pb-12 w-full">
            <div data-animate="fade-in-up">
                <div class="flex items-center gap-4 mb-4">
                    <span class="bg-primary/90 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold uppercase"><?php echo $category; ?></span>
                    <span class="text-white/80 text-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">schedule</span>
                        <?php echo $reading_time; ?> min read
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4 leading-tight"><?php echo $title; ?></h1>
                <div class="flex items-center gap-4 text-white/70 text-sm">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">person</span>
                        <?php echo $author; ?>
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">calendar_today</span>
                        <?php echo $date; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blog Content -->
<article class="py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="blog-content" data-animate="fade-in-up" data-animate-delay="1">
            <?php echo $content; ?>
        </div>
        
        <!-- Share Section -->
        <div class="mt-12 pt-8 border-t border-gray-200" data-animate="fade-in-up" data-animate-delay="2">
            <h3 class="text-lg font-bold mb-4">Share this article</h3>
            <div class="flex gap-3">
                <button onclick="shareArticle()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-orange-600 transition hover-lift">
                    <span class="material-symbols-outlined text-base">share</span>
                    Share
                </button>
                <a href="https://wa.me/?text=<?php echo urlencode($title . ' - ' . 'https://csnexplore.com/blogs/' . $blog['id']); ?>" 
                   target="_blank" 
                   class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition hover-lift">
                    <span class="material-symbols-outlined text-base">chat</span>
                    WhatsApp
                </a>
            </div>
        </div>
        
        <!-- Back to Blogs -->
        <div class="mt-8" data-animate="fade-in-up" data-animate-delay="3">
            <a href="../blogs" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                <span class="material-symbols-outlined">arrow_back</span>
                Back to all blogs
            </a>
        </div>
    </div>
</article>

<!-- Simple Footer -->
<footer class="bg-slate-900 text-white py-8">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p class="text-sm text-white/50">© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
        <div class="mt-4 flex justify-center gap-4 text-sm">
            <a href="../" class="hover:text-primary transition">Home</a>
            <a href="../blogs" class="hover:text-primary transition">Blogs</a>
            <a href="../about" class="hover:text-primary transition">About</a>
            <a href="../contact" class="hover:text-primary transition">Contact</a>
        </div>
    </div>
</footer>

<script src="../animations.js"></script>
<script>
document.body.classList.add('page-ready');

function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: <?php echo json_encode($title); ?>,
            text: 'Check out this article from CSNExplore',
            url: window.location.href
        }).catch(() => {});
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}
</script>
</body>
</html>
    <?php
    return ob_get_clean();
}
?>
