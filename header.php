<?php
// Include W3Speedster header optimization
if (file_exists(__DIR__ . '/W3speedster/header_opt.php')) {
    // require_once __DIR__ . '/W3speedster/header_opt.php'; // Temporarily disabled to test speed issues
}

// Advanced HTML Minification
if (!function_exists('sanitize_output')) {
    function sanitize_output($buffer) {
        // Strip HTML comments (except IE conditionals)
        $buffer = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $buffer);
        // Minify HTML
        $buffer = preg_replace('/>\s+</', '><', $buffer);
        
        // Automated Lazy Loading: Add loading="lazy" to all images unless they have loading="eager" or fetchpriority="high"
        $buffer = preg_replace_callback('/<img\s+([^>]+)>/i', function($matches) {
            $attrs = $matches[1];
            if (stripos($attrs, 'loading=') === false && stripos($attrs, 'fetchpriority="high"') === false) {
                return '<img loading="lazy" ' . $attrs . '>';
            }
            return $matches[0];
        }, $buffer);

        $reqUri = $_SERVER['REQUEST_URI'] ?? '';
        if (($reqUri === '/' || $reqUri === '/index.php' || strpos($reqUri, '/?') === 0) && !isset($_GET['nocache'])) {
            $cache_dir = __DIR__ . '/cache';
            if (!is_dir($cache_dir)) @mkdir($cache_dir, 0755, true);
            @file_put_contents($cache_dir . '/homepage.html', $buffer);
        }
        
        return $buffer;
    }
}
ob_start("sanitize_output");

require_once 'php/config.php';
$current_page = $current_page ?? '';
$page_title = $page_title ?? 'CSNExplore – Chhatrapati Sambhajinagar';
$nav_links = [
    ['href' => BASE_PATH . '/index', 'label' => 'Home'],
    ['href' => BASE_PATH . '/explore', 'label' => 'Explore'],
    ['href' => BASE_PATH . '/suggestor', 'label' => 'Trip Planner'],
    ['href' => BASE_PATH . '/about', 'label' => 'About'],
    ['href' => BASE_PATH . '/contact', 'label' => 'Contact'],
    ['href' => BASE_PATH . '/blogs', 'label' => 'Blog'],
];

$listing_nav = [
    ['href' => BASE_PATH . '/hotels', 'icon' => 'bed', 'label' => 'Stays', 'type' => 'stays'],
    ['href' => BASE_PATH . '/car-rentals', 'icon' => 'directions_car', 'label' => 'Cars', 'type' => 'cars'],
    ['href' => BASE_PATH . '/bike-rentals', 'icon' => 'motorcycle', 'label' => 'Bikes', 'type' => 'bikes'],
    ['href' => BASE_PATH . '/attractions', 'icon' => 'confirmation_number', 'label' => 'Attractions', 'type' => 'attractions'],
    ['href' => BASE_PATH . '/restaurants', 'icon' => 'restaurant', 'label' => 'Dine', 'type' => 'restaurants'],
    ['href' => BASE_PATH . '/bus', 'icon' => 'directions_bus', 'label' => 'Buses', 'type' => 'buses'],
];

$is_listing_page = ($current_page === 'listing' || $current_page === 'listing-detail' || isset($listing_type));
$active_listing_type = $listing_type ?? '';
?>
<?php
// Maintenance Mode Check
if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE === true) {
    if (!isset($_GET['admin_bypass'])) {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 3600');
        die('<!DOCTYPE html><html><head><title>Under Maintenance</title><style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8fafc;color:#333;text-align:center;} h1{color:#ec5b13;}</style></head><body><div><h1>We\'ll be right back!</h1><p>Our website is currently undergoing scheduled maintenance.<br>Thank you for your patience.</p></div></body></html>');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- BFCACHE WHITE PAGE FIX: runs immediately, before any deferred JS -->
    <script>window.addEventListener('pageshow',function(e){if(e.persisted){document.body.style.cssText='opacity:1!important;visibility:visible!important;transition:none!important';document.querySelectorAll('[data-reveal],[data-reveal-children],[data-animate],.card-reveal').forEach(function(el){el.classList.add('revealed');el.style.cssText='opacity:1!important;transform:none!important;filter:none!important;transition:none!important';});var pb=document.getElementById('page-loading-bar');if(pb)pb.remove();}},{passive:true});</script>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="format-detection" content="telephone=no" />
    <!-- ── Favicon: all sizes generated from fevicon.png ── -->
    <!-- iOS Apple Touch Icons (all standard sizes) -->
    <link rel="apple-touch-icon" sizes="57x57"   href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60"   href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72"   href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76"   href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-180x180.png">
    <link rel="apple-touch-icon"                 href="<?php echo BASE_PATH; ?>/images/fevicon/apple-touch-icon.png">
    <!-- Standard browser favicons (PNG + ICO fallback) -->
    <link rel="icon" type="image/png" sizes="16x16"  href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-16x16.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32"  href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="48x48"  href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-48x48.png?v=2">
    <link rel="icon" type="image/png" sizes="96x96"  href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-96x96.png?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo BASE_PATH; ?>/images/fevicon/android-chrome-192x192.png?v=2">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo BASE_PATH; ?>/images/fevicon/android-chrome-512x512.png?v=2">
    <link rel="shortcut icon" href="<?php echo BASE_PATH; ?>/images/fevicon/favicon.ico?v=2" type="image/x-icon">
    <!-- Web App Manifest (PWA) -->
    <link rel="manifest" href="<?php echo BASE_PATH; ?>/manifest.json">
    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="CSNExplore Latest Updates" href="<?php echo BASE_PATH; ?>/rss.php">
    <!-- Windows Tile meta tags -->
    <meta name="msapplication-TileColor"          content="#ec5b13">
    <meta name="msapplication-TileImage"          content="<?php echo BASE_PATH; ?>/images/fevicon/ms-icon-144x144.png">
    <meta name="msapplication-square70x70logo"    content="<?php echo BASE_PATH; ?>/images/fevicon/ms-icon-70x70.png">
    <meta name="msapplication-square150x150logo"  content="<?php echo BASE_PATH; ?>/images/fevicon/ms-icon-150x150.png">
    <meta name="msapplication-square310x310logo"  content="<?php echo BASE_PATH; ?>/images/fevicon/ms-icon-310x310.png">
    <meta name="msapplication-config"             content="none">
    <!-- Theme colors for browser chrome -->
    <meta name="theme-color" content="#ec5b13">
    <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="CSNExplore">
    <meta name="publisher" content="CSNExplore">
    <!-- Extended OG Tags -->
    <meta property="og:locale:alternate" content="hi_IN" />
    <meta property="article:publisher" content="https://www.facebook.com/csnexplore" />
    <?php
    // ── Dynamic SEO Meta Tags — powered by php/seo-meta.php & php/seo-optimizer.php ─────────────────
    require_once __DIR__ . '/php/seo-meta.php';
    require_once __DIR__ . '/php/seo-optimizer.php';

    $page_meta = $page_meta ?? [];

    // Build seo_meta context from whatever the page has set
    $seo_type = $page_meta['seo_type'] ?? match($current_page ?? '') {
        'home'         => 'home',
        'listing.php'  => $listing_type ?? 'stays',
        'blogs.php'    => 'blogs',
        'blog-detail'  => 'blog',
        'about.php'    => 'about',
        'contact.php'  => 'contact',
        default        => 'home',
    };

    $seo_ctx = [
        'type'        => $seo_type,
        'item'        => $page_meta['item'] ?? [],
        'breadcrumbs' => $page_meta['breadcrumbs'] ?? [],
        'faqs'        => $page_meta['faqs'] ?? [],
        'price'       => $page_meta['price'] ?? '',
        'price_unit'  => $page_meta['price_unit'] ?? '',
        'canonical'   => $page_meta['canonical'] ?? '',
    ];

    $seo = seo_meta($seo_ctx);

    // Allow pages to override title/description directly
    if (!empty($page_meta['description'])) $seo['description'] = $page_meta['description'];
    if (!empty($page_meta['canonical']))   $seo['canonical']   = $page_meta['canonical'];
    if (!empty($page_meta['image']))       $seo['img_abs']     = $page_meta['image'];

    $meta_description = $seo['description'];
    $meta_canonical   = $seo['canonical'];
    $meta_image       = $seo['img_abs'];
    $meta_type        = $page_meta['type'] ?? 'website';

    echo seo_render($seo, $meta_type);

    // Geographic targeting
    echo '<meta name="geo.region" content="IN-MH">' . "\n";
    echo '<meta name="geo.placename" content="Chhatrapati Sambhajinagar">' . "\n";
    echo '<meta name="geo.position" content="19.8762;75.3433">' . "\n";
    echo '<meta name="ICBM" content="19.8762, 75.3433">' . "\n";
    ?>
    <title><?php echo htmlspecialchars($seo['title'] ?? $page_title); ?></title>
    <!-- Google Analytics loaded via env-based GA4 block at end of <head> to avoid duplication -->
    <!-- ═══ PERFORMANCE OPTIMIZED - Core Web Vitals ═══ -->
    <!-- Preconnect for critical external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    
    <!-- Tailwind JIT disabled for performance. Styles are in style.min.css -->


    
    <!-- Fonts: Preload and load async with display=optional to prevent render blocking AND prevent CLS FOUT -->
    <link rel="preload" as="style" crossorigin="anonymous" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=optional" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=optional" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=optional" /></noscript>
    
    <!-- Material Symbols: Preload and load async to prevent render blocking -->
    <link rel="preload" as="style" crossorigin="anonymous" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" /></noscript>
    
    <!-- Main & Responsive CSS: INLINED for Instantaneous FCP (<1s) -->
    <style>
        <?php 
        if (file_exists(__DIR__ . '/style.min.css')) echo file_get_contents(__DIR__ . '/style.min.css'); 
        if (file_exists(__DIR__ . '/mobile-responsive.min.css')) echo file_get_contents(__DIR__ . '/mobile-responsive.min.css');
        ?>
        

    </style>
    
    <!-- Animations CSS: ASYNC LOAD to prevent render blocking (minified only) -->
    <?php
    // Cache filemtime() calls per process to avoid repeated disk stat() I/O
    static $_assetVersions = [];
    if (empty($_assetVersions)) {
        $_assetVersions = [
            'anim_css' => '?v=' . @filemtime(__DIR__ . '/animations.min.css'),
            'anim_js'  => '?v=' . @filemtime(__DIR__ . '/animations.min.js'),
        ];
    }
    $animVer = $_assetVersions['anim_css'];
    ?>
    <link rel="preload" href="<?php echo BASE_PATH; ?>/animations.min.css<?php echo $animVer; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'"/>
    <noscript><link rel="stylesheet" href="<?php echo BASE_PATH; ?>/animations.min.css<?php echo $animVer; ?>"></noscript>
    
    <!-- Custom CSS Extension -->
    <style>
        <?php if (file_exists(__DIR__ . '/custom.css')) echo file_get_contents(__DIR__ . '/custom.css'); ?>
    </style>
    
    <style>
        /* ═══ CRITICAL INLINE CSS - Above the Fold ═══ */
        /* This CSS is inlined to prevent render blocking */
        
        /* Base styles */
        body { 
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            background: #fff; 
            color: #0f172a;
            line-height: 1.5;
        }
        /* Performance: Delay rendering of off-screen sections */
        section:not(.homepage-hero) { content-visibility: auto; contain-intrinsic-size: 0 600px; }
        *, ::before, ::after { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
            border: 0 solid #e5e7eb;
        }
        a { color: inherit; text-decoration: inherit; }
        button, input { font-family: inherit; font-size: 100%; margin: 0; padding: 0; background: transparent; }
        
        /* Material Icons fallback */
        .material-symbols-outlined { 
            font-variation-settings: "FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24; 
            font-family: "Material Symbols Outlined", sans-serif; 
            font-style: normal; 
            display: inline-block; 
            line-height: 1; 
            letter-spacing: normal; 
            text-transform: none; 
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        
        /* Hero section - Critical for LCP */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding-top: 5rem;
        }
        
        /* Prevent layout shift */
        img { 
            max-width: 100%; 
            height: auto;
            display: block;
        }
        
        /* Loading state */
        .loading { opacity: 0; transition: opacity 0.3s ease-in; }
        .loaded  { opacity: 1; }

        /* ── FLASH FIX: glass-card solid fallback before backdrop-filter CSS loads ── */
        .glass-card,.glass,.glass-section,.glass-glow{background:#fff;border:1px solid #e2e8f0}
        
        /* ── Logo Swap for Pill Mode ── */
        .logo-scrolled { display: none !important; }
        #site-header.pill-mode .logo-main { display: none !important; }
        #site-header.pill-mode .logo-scrolled { display: block !important; }
    </style>
    <style>
        /* ── Global Enhancements ── */
        html { scroll-behavior: smooth; }
        
        /* Modern Thin Scrollbar for the entire site */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* ── Override for header buttons - keep text white on hover ── */
        .hdr-call-btn:hover,
        .hdr-call-btn:hover *,
        .hdr-wa-btn:hover,
        .hdr-wa-btn:hover *,
        #hdr-login-btn:hover {
          color: #ffffff !important;
        }
        /* ── Universal fix: any <a> or <button> with text-white stays white on hover ── */
        a.text-white:hover,
        a.text-white:hover *,
        button.text-white:hover,
        button.text-white:hover * {
          color: #ffffff !important;
        }

        .glass-dark { background:#000000; backdrop-filter:blur(20px); border-b:1px solid rgba(255,255,255,0.05); }
        .header-solid { background:#000000 !important; }
        body {
            background:#fff; color:#0f172a; font-family:Inter,sans-serif; font-display: swap;
            overflow-x:hidden; max-width:100vw;
        }
        body.page-fade-out { opacity:0 !important; transition:opacity 0.35s ease !important; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; font-display: swap; }

        /* ── Marquee ── */
        @keyframes marquee { 
            0% { transform:translate3d(0,0,0); } 
            100% { transform:translate3d(-50%,0,0); } 
        }
        .animate-marquee {
            display:flex !important; 
            flex-shrink: 0 !important;
            animation: marquee 20s linear infinite !important;
            will-change: transform;
        }
        .animate-marquee:hover { animation-play-state:paused; }
        
        /* Global Scroll-triggered Animations handled in animations.css */
        /* ── Marquee bar – fixed at very top ── */
        #marquee-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 70;
            overflow: hidden;
            height: 28px;
            display: flex;
            align-items: center;
            transition: opacity 0.35s ease, height 0.35s ease;
        }
        #marquee-bar.hidden-bar {
            opacity: 0;
            height: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            pointer-events: none;
        }
        /* ══ Site Header – ALWAYS STICKY (position:fixed is permanent) ══ */
        #site-header {
            position: fixed !important;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 100%;
            border-radius: 0;
            background: #000;
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            box-shadow: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            z-index: 60 !important;
            transition:
                width          0.5s cubic-bezier(0.32,0,0.15,1),
                max-width      0.5s cubic-bezier(0.32,0,0.15,1),
                top            0.5s cubic-bezier(0.32,0,0.15,1),
                border-radius  0.5s cubic-bezier(0.32,0,0.15,1),
                background     0.5s cubic-bezier(0.32,0,0.15,1),
                box-shadow     0.5s cubic-bezier(0.32,0,0.15,1),
                backdrop-filter 0.5s ease;
        }
        /* ── Pill mode: floating pill with premium glass effect ── */
        #site-header.pill-mode {
            position: fixed !important;
            top: 14px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            width: calc(100% - 32px) !important;
            max-width: 1120px !important;
            border-radius: 9999px !important;
            background: rgba(10, 7, 5, 0.65) !important;
            backdrop-filter: blur(24px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(200%) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            box-shadow: 
                0 12px 40px -10px rgba(0,0,0,0.4),
                0 4px 20px -5px rgba(236,91,19,0.15),
                inset 0 1px 0 rgba(255,255,255,0.1) !important;
            z-index: 9000 !important;
        }
        #site-header nav { height: 64px; min-height: 64px; }
        #site-header.pill-mode nav { height: 60px !important; min-height: 60px !important; }
        /* Call/WA buttons – pill mode shrink to icon only */
        #site-header.pill-mode .hdr-call-text,
        #site-header.pill-mode .hdr-wa-text { display: none !important; }
        #site-header.pill-mode .hdr-call-btn { padding: 0 !important; width: 34px !important; height: 34px !important; border-radius: 50% !important; }
        #site-header.pill-mode .hdr-wa-btn  { padding: 0 !important; width: 34px !important; height: 34px !important; border-radius: 50% !important; }
        .hdr-call-btn, .hdr-wa-btn { position: relative; overflow: hidden; }
        .hdr-call-btn::before, .hdr-wa-btn::before { display: none !important; }
        /* ── Global mobile fixes ── */
        * { box-sizing: border-box; }
        @media (max-width: 640px) {
            .max-w-\[1140px\] { padding-left: 12px !important; padding-right: 12px !important; }
            h1.font-serif, h2.font-serif { font-size: 1.5rem !important; line-height: 1.15 !important; }
            .py-16 { padding-top: 2rem !important; padding-bottom: 2rem !important; }
            .py-12 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
            .mb-16 { margin-bottom: 2rem !important; }
            .mb-12 { margin-bottom: 1.5rem !important; }
            .gap-12 { gap: 1.5rem !important; }
            
            /* Hide category icons on mobile */
            #mob-menu .material-symbols-outlined {
                display: none !important;
            }
        }
        @media (max-width: 360px) {
            h1.font-serif, h2.font-serif { font-size: 1.35rem !important; }
            #marquee-bar span { font-size: 8px !important; letter-spacing: normal !important; padding-left: 8px !important; padding-right: 8px !important; }
        }
        /* ── Listing page mobile layout ── */
        @media (max-width: 1023px) {
            #sidebar-filters { width: 100% !important; transform: none !important; }
            #sidebar-filters.collapsed { max-height: 0 !important; padding: 0 !important; margin: 0 !important; opacity: 0 !important; overflow: hidden !important; pointer-events: none !important; }
            #sidebar-filters:not(.collapsed) { max-height: 2000px !important; opacity: 1 !important; }
            #listings-wrapper { gap: 0 !important; }
        }
        @media (max-width: 640px) {
            #listings-grid { grid-template-columns: 1fr !important; gap: 12px !important; }
            .listing-card-anim { width: 100% !important; }
        }
        @media (min-width: 641px) and (max-width: 1023px) {
            #listings-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
        }
        <?php if (!empty($extra_styles)) echo $extra_styles; ?>
    </style>
    <?php

    // Include WebSite + SearchAction schema ONLY on the homepage (per Google guidelines)
    if ($seo_type === 'home') {
        echo SEOOptimizer::renderSchema(SEOOptimizer::generateWebSiteSchema());
    }
    
    // Always include Organization schema
    echo SEOOptimizer::renderSchema(SEOOptimizer::generateOrganizationSchema());
    
    // LocalBusiness schema — always present for local SEO
    echo SEOOptimizer::renderSchema(SEOOptimizer::generateLocalBusinessSchema());
    
    // ItemList schema — exclusively for homepage SEO
    if ($seo_type === 'home') {
        echo SEOOptimizer::renderSchema(SEOOptimizer::generateItemListSchema());
        echo SEOOptimizer::renderSchema(SEOOptimizer::generateHowToSchema());
    }
    

    
    ?>
    <!-- Google Analytics GA4 (DEFERRED - Load after user interaction) -->
    <script>
    // PERFORMANCE OPTIMIZED: Load GA only after user interaction or 5 seconds
    (function() {
        var gaLoaded = false;
        function loadGA() {
            if (gaLoaded) return;
            gaLoaded = true;
            var script = document.createElement('script');
            script.src = "https://www.googletagmanager.com/gtag/js?id=G-58P4JE1SYS";
            script.async = true;
            document.head.appendChild(script);
            script.onload = function() {
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-58P4JE1SYS', {
                    'send_page_view': true,
                    'anonymize_ip': true
                });
            };
            // Remove event listeners after loading
            ['scroll','mousemove','touchstart','keydown','click'].forEach(function(e) {
                window.removeEventListener(e, loadGA);
            });
        }
        // Load on first user interaction
        ['scroll','mousemove','touchstart','keydown','click'].forEach(function(e) {
            window.addEventListener(e, loadGA, {once: true, passive: true});
        });
        // Only load on explicit user interaction for 0 TBT
    })();
    </script>
    <!-- CSRF Protection Fetch Interceptor -->
    <script>
    (function() {
        var originalFetch = window.fetch;
        window.fetch = function(url, options) {
            options = options || {};
            var method = (options.method || 'GET').toUpperCase();
            if (['POST', 'PUT', 'DELETE'].indexOf(method) !== -1) {
                var isRelative = !url.match(/^(?:https?:)?\/\//i);
                var isSameOrigin = typeof url === 'string' && url.indexOf(window.location.origin) === 0;
                if (isRelative || isSameOrigin) {
                    options.headers = options.headers || {};
                    var matches = document.cookie.match(/(?:^|; )csrf_token=([^;]*)/);
                    var csrfToken = matches ? decodeURIComponent(matches[1]) : '';
                    if (csrfToken) {
                        if (options.headers instanceof Headers) {
                            options.headers.set('X-CSRF-Token', csrfToken);
                        } else if (Array.isArray(options.headers)) {
                            options.headers.push(['X-CSRF-Token', csrfToken]);
                        } else {
                            options.headers['X-CSRF-Token'] = csrfToken;
                        }
                    }
                }
            }
            return originalFetch(url, options);
        };
    })();
    </script>
    <!-- Microsoft Clarity Tracking -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "xn92b2mf6k");
    </script>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-white font-display text-slate-900">




<!-- ── Scroll Progress Bar ───────────────────────────────── -->
<div id="csn-scroll-bar"></div>
<script>
(function(){
  var bar = document.getElementById('csn-scroll-bar');
  if(bar){
    window.addEventListener('scroll', function(){
      var doc = document.documentElement;
      var scrolled = doc.scrollTop || document.body.scrollTop;
      var total = doc.scrollHeight - doc.clientHeight;
      bar.style.width = total > 0 ? (scrolled/total*100)+'%' : '0%';
    }, { passive: true });
  }
})();
</script>

<!-- Top Announcement Marquee -->
<div id="marquee-bar" class="bg-primary text-white overflow-hidden whitespace-nowrap border-b border-primary/20" style="background-color:#ec5b13;display:flex;align-items:center;height:28px;">
    <div class="relative flex" style="width:100%;overflow:hidden;">
        <div class="animate-marquee flex items-center" style="display:flex;flex-shrink:0;">
            <?php 
            $marquee_items = [
                "Discover The Wonders of Chhatrapati Sambhajinagar",
                "Book Premium Stays, Car Rentals & Local Tours",
                "Special Offers Available For First Time Visitors!",
                "Verified Local Guides for Ajanta & Ellora Caves",
                "24/7 Support for all your Travel Needs"
            ];
            // Double items for seamless loop (animation moves -50%)
            $loop_items = array_merge($marquee_items, $marquee_items);
            foreach($loop_items as $text): ?>
                <span class="flex items-center mx-8" style="flex-shrink:0;">
                    <span class="material-symbols-outlined text-white text-sm mr-2">stars</span>
                    <span class="text-[10px] font-bold text-white tracking-wider uppercase" style="white-space:nowrap;"><?php echo $text; ?></span>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<header id="site-header" class="w-full">
    <nav class="max-w-[1140px] mx-auto px-4 sm:px-5 flex items-center justify-between" style="height:64px;min-height:64px">
        <a href="<?php echo BASE_PATH; ?>/" class="flex items-center shrink-0">
            <?php if (defined('WHITE_LABEL_MODE') && WHITE_LABEL_MODE === true): ?>
                <span class="text-white font-bold text-xl tracking-wide">TravelPortal</span>
            <?php else: ?>
                <img fetchpriority="high" loading="eager" width="120" height="36" src="<?php echo BASE_PATH; ?>/images/csnexplore-logo.webp" alt="CSNExplore" class="h-8 sm:h-9 object-contain logo-main"/>
                <img fetchpriority="high" loading="eager" width="120" height="36" src="<?php echo BASE_PATH; ?>/images/Logo-light-optimized.webp" alt="CSNExplore" class="h-8 sm:h-9 object-contain logo-scrolled"/>
            <?php endif; ?>
        </a>
        <div class="hidden xl:flex items-center gap-0" itemscope itemtype="https://schema.org/SiteNavigationElement">
            <?php foreach ($nav_links as $link):
                $is_active = (trim($link['href'],'/') === trim($current_page,'/') || ($current_page==='home' && ($link['href'] === BASE_PATH.'/' || strpos($link['href'],'/index')!==false)));
            ?>
            <a itemprop="url" href="<?php echo $link['href']; ?>" title="<?php echo htmlspecialchars($link['label']); ?> in Chhatrapati Sambhajinagar"
               class="text-[13px] font-bold px-3.5 py-2 rounded-full transition-colors duration-200 <?php echo $is_active ? 'text-white bg-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white'; ?> whitespace-nowrap">
                <span itemprop="name"><?php echo $link['label']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="hidden lg:flex items-center gap-1.5">
                <a href="tel:<?php echo CONTACT_PHONE; ?>"
                   class="hdr-call-btn flex items-center justify-center gap-1.5 bg-slate-800 text-white h-9 px-4 rounded-full hover:bg-slate-700 hover:!text-white transition-all border border-slate-700 text-sm font-bold">
                    <span class="material-symbols-outlined text-[17px] text-primary">call</span>
                    <span class="hdr-call-text"><?php echo CONTACT_PHONE; ?></span>
                </a>
                <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>" target="_blank"
                   class="hdr-wa-btn flex items-center justify-center gap-1.5 bg-[#128C7E] text-white h-9 px-3 rounded-full hover:bg-[#075E54] hover:!text-white transition-all text-sm font-bold">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    <span class="hdr-wa-text">WhatsApp</span>
                </a>
            </div>
            <div class="h-5 w-px bg-white/10 mx-0.5 hidden lg:block"></div>
            <a href="<?php echo BASE_PATH; ?>/login" id="hdr-login-btn"
               class="text-white text-[13px] font-bold px-3 py-1.5 hover:bg-white/10 hover:!text-white rounded-full transition-all">Login</a>
            <div id="hdr-user-menu" class="hidden relative">
                <button id="hdr-user-btn" class="size-9 flex items-center justify-center hover:bg-white/10 rounded-full transition-all">
                    <span class="material-symbols-outlined text-[22px] text-primary" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">account_circle</span>
                </button>
                <div id="hdr-dropdown" class="hidden absolute right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-[200]" style="min-width: 180px;">
                    <div class="px-4 py-2 border-b border-slate-100 mb-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">My Account</p>
                    </div>
                    <a href="<?php echo BASE_PATH; ?>/my-booking" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors font-bold text-left whitespace-nowrap">
                        <span class="material-symbols-outlined text-slate-600 text-[18px]">calendar_today</span> My Bookings
                    </a>
                    <button id="hdr-logout-btn" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors font-bold text-left whitespace-nowrap">
                        <span class="material-symbols-outlined text-red-500 text-[18px]">logout</span> Sign Out
                    </button>
                </div>
            </div>
            <button id="mob-btn" class="md:hidden size-9 flex items-center justify-center rounded-full text-white active:bg-white/10 transition-colors ml-0.5">
                <span class="material-symbols-outlined text-xl">menu</span>
            </button>
        </div>
    </nav>
</header>

<!-- ═══ MOBILE MENU — outside header, full viewport overlay ═══ -->
<style>
@keyframes mobMenuIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
#mob-menu.mob-open { animation: mobMenuIn 0.22s cubic-bezier(0.32,0,0.15,1) forwards; }
#mob-menu { z-index: 9999 !important; }
</style>
<div id="mob-menu" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:#0a0705;overflow-y:auto;flex-direction:column;opacity:0">
    <!-- header row -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,0.1)">
        <?php if (defined('WHITE_LABEL_MODE') && WHITE_LABEL_MODE === true): ?>
            <span style="color:#fff;font-weight:bold;font-size:18px;">TravelPortal</span>
        <?php else: ?>
            <img loading="lazy" width="120" height="36" src="<?php echo BASE_PATH; ?>/images/Logo-light-optimized.webp" alt="CSNExplore" style="height:28px;object-fit:contain"/>
        <?php endif; ?>
        <button id="mob-close" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.08);border:none;border-radius:50%;cursor:pointer;color:#fff">
            <span class="material-symbols-outlined" style="font-size:20px">close</span>
        </button>
    </div>
    <!-- nav links -->
    <div style="padding:14px 12px 8px;display:flex;flex-direction:column;gap:2px">
        <p style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:rgba(255,255,255,0.35);margin:0 0 6px 10px">Navigation</p>
        <?php foreach ($nav_links as $link): ?>
        <a href="<?php echo $link['href']; ?>" title="<?php echo htmlspecialchars($link['label']); ?>" style="display:flex;align-items:center;padding:11px 14px;border-radius:12px;font-size:14px;font-weight:600;color:#fff;text-decoration:none;background:rgba(255,255,255,0.04)"><?php echo $link['label']; ?></a>
        <?php endforeach; ?>
    </div>
    <!-- listing categories (Always show on mobile) -->
    <div style="padding:0 12px 12px">
        <p style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:rgba(255,255,255,0.35);margin:0 0 8px 10px">Categories</p>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px">
            <?php foreach ($listing_nav as $link): ?>
            <a href="<?php echo $link['href']; ?>" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;padding:10px 6px;border-radius:12px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#fff;text-decoration:none;font-size:10px;font-weight:700">
                <span class="material-symbols-outlined" style="font-size:18px;color:#ec5b13"><?php echo $link['icon']; ?></span>
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- spacer -->
    <div style="flex:1;min-height:12px"></div>
    <!-- bottom actions -->
    <div style="padding:12px 12px 28px;border-top:1px solid rgba(255,255,255,0.1);display:flex;flex-direction:column;gap:8px">
        <!-- auth -->
        <div id="mob-auth-login" style="display:flex;gap:6px">
            <a href="<?php echo BASE_PATH; ?>/login" style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:11px;background:#ec5b13;color:#fff;font-weight:700;border-radius:12px;font-size:13px;text-decoration:none">
                <span class="material-symbols-outlined" style="font-size:15px">login</span> Sign In
            </a>
            <a href="<?php echo BASE_PATH; ?>/register" style="flex:1;display:flex;align-items:center;justify-content:center;padding:11px;background:rgba(255,255,255,0.08);color:#fff;font-weight:700;border-radius:12px;font-size:13px;border:1px solid rgba(255,255,255,0.15);text-decoration:none">Register</a>
        </div>
        <div id="mob-auth-user" style="display:none;flex-direction:column;gap:6px">
            <a href="<?php echo BASE_PATH; ?>/my-booking" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;color:#fff;font-weight:700;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:12px;font-size:13px;text-decoration:none">
                <span class="material-symbols-outlined" style="font-size:15px">calendar_today</span> My Bookings
            </a>
            <button id="mob-logout-btn" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;color:#f87171;font-weight:700;background:rgba(248,113,113,0.12);border:1px solid rgba(248,113,113,0.2);border-radius:12px;font-size:13px;cursor:pointer">
                <span class="material-symbols-outlined" style="font-size:15px">logout</span> Sign Out
            </button>
        </div>
        <!-- call -->
        <a href="tel:<?php echo CONTACT_PHONE; ?>" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;background:#1e293b;color:#fff;font-weight:700;border-radius:12px;border:1px solid #334155;font-size:13px;text-decoration:none">
            <span class="material-symbols-outlined" style="font-size:15px;color:#ec5b13">call</span> <?php echo CONTACT_PHONE; ?>
        </a>
        <!-- whatsapp -->
        <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;background:#25D366;color:#fff;font-weight:700;border-radius:12px;font-size:13px;text-decoration:none">
            <svg style="width:14px;height:14px;fill:currentColor;flex-shrink:0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            WhatsApp Us
        </a>
    </div>
</div>

<script>
    var _mob = document.getElementById('mob-menu');
    function openMob() {
        _mob.style.display = 'flex';
        _mob.style.opacity = '0';
        // Trigger animation on next frame
        requestAnimationFrame(function() {
            _mob.classList.add('mob-open');
            document.body.style.overflow = 'hidden';
        });
    }
    function closeMob() {
        _mob.style.opacity = '0';
        _mob.style.transition = 'opacity 0.18s ease';
        setTimeout(function() {
            _mob.style.display = 'none';
            _mob.style.opacity = '';
            _mob.style.transition = '';
            _mob.classList.remove('mob-open');
            document.body.style.overflow = '';
        }, 180);
    }
    document.getElementById('mob-btn').addEventListener('click', openMob);
    document.getElementById('mob-close').addEventListener('click', closeMob);

        // ══ Scroll → Pill Header (bulletproof sticky) ══
        (function(){
            var h  = document.getElementById('site-header');
            var mb = document.getElementById('marquee-bar');
            var ticking = false;
            var isPill = false;

            // Use a hardcoded marquee height to avoid forced reflow from getBoundingClientRect()
            // Marquee bar is always 28px (10px padding-y × 2 + ~8px text). Adjust if changed.
            var MH = 28;

            // Normal state: marquee visible, header full-width below marquee
            function setNormal(){
                isPill = false;
                if (mb) mb.classList.remove('hidden-bar');
                h.classList.remove('pill-mode');
                // Force inline style so it sits right below the marquee bar
                h.style.setProperty('position', 'fixed', 'important');
                h.style.setProperty('top', MH + 'px', 'important');
                h.style.setProperty('left', '50%', 'important');
                h.style.setProperty('transform', 'translateX(-50%)', 'important');
                h.style.setProperty('width', '100%', 'important');
                h.style.setProperty('max-width', '100%', 'important');
                h.style.setProperty('border-radius', '0', 'important');
                h.style.setProperty('z-index', '60', 'important');
            }

            // Pill state: marquee hidden, header shrinks to floating pill at top:14px
            function setPill(){
                isPill = true;
                if (mb) mb.classList.add('hidden-bar');
                h.classList.add('pill-mode');
                // Force all critical styles inline to guarantee visibility
                h.style.setProperty('position', 'fixed', 'important');
                h.style.setProperty('top', '14px', 'important');
                h.style.setProperty('left', '50%', 'important');
                h.style.setProperty('transform', 'translateX(-50%)', 'important');
                h.style.setProperty('width', 'calc(100% - 32px)', 'important');
                h.style.setProperty('max-width', '1120px', 'important');
                h.style.setProperty('border-radius', '9999px', 'important');
                h.style.setProperty('z-index', '9000', 'important');
            }

            function update(){
                if (window.scrollY > 40) {
                    setPill();
                } else {
                    setNormal();
                }
                ticking = false;
            }

            // Init
            update();

            window.addEventListener('scroll', function(){
                if (!ticking) {
                    requestAnimationFrame(update);
                    ticking = true;
                }
            }, { passive: true });

            window.addEventListener('resize', function(){
                update();
            }, { passive: true });

            window.addEventListener('load', function(){
                update();
            });
        })();

        // Auth
        (function(){
            var tok=localStorage.getItem('csn_token'), usr=JSON.parse(localStorage.getItem('csn_user')||'null');
            if(tok&&usr){
                var lb=document.getElementById('hdr-login-btn'); if(lb)lb.style.display='none';
                var um=document.getElementById('hdr-user-menu'); if(um)um.classList.remove('hidden');
                var mal=document.getElementById('mob-auth-login'); if(mal)mal.style.display='none';
                var mau=document.getElementById('mob-auth-user');  if(mau)mau.style.display='flex';
            }
            var ub=document.getElementById('hdr-user-btn'),dd=document.getElementById('hdr-dropdown');
            if(ub&&dd){ ub.addEventListener('click',function(e){e.stopPropagation();dd.classList.toggle('hidden');}); document.addEventListener('click',function(){dd.classList.add('hidden');}); }
            function logout(){ ['csn_token','csn_user','csn_admin_token','csn_admin_user'].forEach(function(k){localStorage.removeItem(k);}); location.reload(); }
            var dl=document.getElementById('hdr-logout-btn'); if(dl)dl.addEventListener('click',logout);
            var ml=document.getElementById('mob-logout-btn'); if(ml)ml.addEventListener('click',logout);
        })();
    </script>

