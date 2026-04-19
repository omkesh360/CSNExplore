<?php
require_once 'php/config.php';
$current_page = $current_page ?? '';
$page_title = $page_title ?? 'CSNExplore – Chhatrapati Sambhajinagar';
$nav_links = [
    ['href' => BASE_PATH . '/index', 'label' => 'Home'],
    ['href' => BASE_PATH . '/suggestor', 'label' => 'Trip Planner'],
    ['href' => BASE_PATH . '/about', 'label' => 'About Us'],
    ['href' => BASE_PATH . '/contact', 'label' => 'Contact Us'],
    ['href' => BASE_PATH . '/blogs', 'label' => 'Our Blogs'],
];

$listing_nav = [
    ['href' => BASE_PATH . '/listing?type=stays', 'icon' => 'bed', 'label' => 'Stays', 'type' => 'stays'],
    ['href' => BASE_PATH . '/listing?type=cars', 'icon' => 'directions_car', 'label' => 'Cars', 'type' => 'cars'],
    ['href' => BASE_PATH . '/listing?type=bikes', 'icon' => 'motorcycle', 'label' => 'Bikes', 'type' => 'bikes'],
    ['href' => BASE_PATH . '/listing?type=attractions', 'icon' => 'confirmation_number', 'label' => 'Attractions', 'type' => 'attractions'],
    ['href' => BASE_PATH . '/listing?type=restaurants', 'icon' => 'restaurant', 'label' => 'Dine', 'type' => 'restaurants'],
    ['href' => BASE_PATH . '/listing?type=buses', 'icon' => 'directions_bus', 'label' => 'Buses', 'type' => 'buses'],
];

$is_listing_page = ($current_page === 'listing' || $current_page === 'listing-detail' || isset($listing_type));
$active_listing_type = $listing_type ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="format-detection" content="telephone=no" />
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_PATH; ?>/images/fevicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo BASE_PATH; ?>/images/fevicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_PATH; ?>/images/fevicon/favicon-16x16.png">
    <link rel="shortcut icon" href="<?php echo BASE_PATH; ?>/images/fevicon/favicon.ico" type="image/x-icon">
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="<?php echo BASE_PATH; ?>/images/fevicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ec5b13">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="CSNExplore">
    <meta name="publisher" content="CSNExplore">
    <meta name="keywords" content="Chhatrapati Sambhajinagar, Aurangabad, hotels, cars, bikes, attractions, restaurants, tourism, CSNExplore, travel guide, heritage sites, Ajanta, Ellora">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Preconnect for critical external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <!-- Fonts: synchronous to prevent invisible text on page load -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <!-- Tailwind CDN: config MUST be set AFTER CDN loads; sync to avoid FOUC -->
    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: { "primary": "#ec5b13", "whatsapp": "#25D366", "background-dark": "#0a0705" },
                    fontFamily: { "display": ["Inter", "sans-serif"], "serif": ["Playfair Display", "serif"] }
                }
            }
        }
    </script>
    <!-- Site CSS -->
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/mobile-responsive.css?v=2026041203"/>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/animations.css?v=20260412"/>
    <style>
        /* ── Global Enhancements ── */
        html { scroll-behavior: smooth; }
        ::-moz-selection { background: rgba(236,91,19,0.2); color: #ec5b13; }
        ::selection { background: rgba(236,91,19,0.2); color: #ec5b13; }
        
        /* Modern Thin Scrollbar for the entire site */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .glass-dark { background:#000000; backdrop-filter:blur(20px); border-b:1px solid rgba(255,255,255,0.05); }
        .header-solid { background:#000000 !important; }
        /* Force base visibility — prevents transparent/invisible text on all pages */
        body { background:#fff; color:#0f172a; font-family:Inter,sans-serif; animation: pageFadeIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
        @keyframes pageFadeIn { from { opacity: 0; } to { opacity: 1; } }
        body.page-fade-out { opacity: 0 !important; transition: opacity 0.4s ease-in-out; }
        .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; font-family:'Material Symbols Outlined'; font-style:normal; display:inline-block; line-height:1; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { display: flex; width: max-content; animation: marquee 120s linear infinite; }
        .animate-marquee:hover { animation-play-state: paused; }
        
        /* Global Scroll-triggered Animations */
        [data-reveal] { 
            opacity: 0; 
            transform: translateY(40px); 
            transition: opacity 1s cubic-bezier(0.22, 1, 0.36, 1), transform 1s cubic-bezier(0.22, 1, 0.36, 1); 
        }
        [data-reveal].revealed { opacity: 1; transform: translateY(0) scale(1) translateX(0); }
        
        [data-reveal='left'] { transform: translateX(-40px) translateY(0); }
        [data-reveal='right'] { transform: translateX(40px) translateY(0); }
        [data-reveal='scale'] { transform: scale(0.95) translateY(0); }
        [data-reveal='fade'] { transform: translateY(0); }
        
        /* Staggered transition delays for children if needed */
        [data-reveal-stagger] > *:nth-child(1) { transition-delay: 50ms; }
        [data-reveal-stagger] > *:nth-child(2) { transition-delay: 100ms; }
        [data-reveal-stagger] > *:nth-child(3) { transition-delay: 150ms; }
        [data-reveal-stagger] > *:nth-child(4) { transition-delay: 200ms; }
        [data-reveal-stagger] > *:nth-child(5) { transition-delay: 250ms; }
        [data-reveal-stagger] > *:nth-child(6) { transition-delay: 300ms; }
        /* ── Marquee bar – fixed at very top ── */
        #marquee-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 70;
            overflow: hidden;
            max-height: 40px;
            transition: opacity 0.35s ease, max-height 0.35s ease, padding 0.35s ease;
        }
        #marquee-bar.hidden-bar {
            opacity: 0;
            max-height: 0;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
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
                border-radius  0.5s cubic-bezier(0.32,0,0.15,1),
                background     0.5s cubic-bezier(0.32,0,0.15,1),
                box-shadow     0.5s cubic-bezier(0.32,0,0.15,1),
                backdrop-filter 0.5s ease;
        }
        /* ── Pill mode: floating pill, always visible ── */
        #site-header.pill-mode {
            position: fixed !important;
            top: 14px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            width: calc(100% - 32px) !important;
            max-width: 1120px !important;
            border-radius: 9999px !important;
            background: rgba(8,5,3,0.95) !important;
            backdrop-filter: blur(28px) !important;
            -webkit-backdrop-filter: blur(28px) !important;
            border: 1px solid rgba(255,255,255,0.10) !important;
            box-shadow: 0 4px 32px rgba(0,0,0,0.45), 0 1px 0 rgba(255,255,255,0.04) inset !important;
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
        #site-header-placeholder { display: block; background: #000; }
        /* ── Global mobile fixes ── */
        body { overflow-x: hidden; max-width: 100vw; }
        * { box-sizing: border-box; }
        @media (max-width: 640px) {
            .max-w-\[1140px\] { padding-left: 12px !important; padding-right: 12px !important; }
            section { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
            h1.font-serif, h2.font-serif { font-size: 1.5rem !important; line-height: 1.15 !important; }
            .py-16 { padding-top: 2rem !important; padding-bottom: 2rem !important; }
            .py-12 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
            .mb-16 { margin-bottom: 2rem !important; }
            .mb-12 { margin-bottom: 1.5rem !important; }
            .gap-12 { gap: 1.5rem !important; }
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
    // ── Dynamic SEO Meta Tags [A4.1] ─────────────────────────────────────────
    $page_meta = $page_meta ?? [];
    $meta_description = $page_meta['description'] ?? 'Discover the best hotels, bikes, cars & attractions in Chhatrapati Sambhajinagar with CSNExplore — your premium travel partner.';
    $meta_canonical   = $page_meta['canonical'] ?? 'https://csnexplore.com';
    $meta_image       = $page_meta['image'] ?? 'https://csnexplore.com/images/og-image.jpg';
    $meta_type        = $page_meta['type'] ?? 'website';
    ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($meta_canonical); ?>">
    <meta property="og:type" content="<?php echo htmlspecialchars($meta_type); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($meta_image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($meta_canonical); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($meta_image); ?>">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "<?php echo htmlspecialchars($page_title); ?>",
      "url": "<?php echo htmlspecialchars($meta_canonical); ?>",
      "description": "<?php echo htmlspecialchars($meta_description); ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://csnexplore.com/listing/stays?search={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    <?php if (!empty($extra_head)) echo $extra_head; ?>
    <!-- ── Google Analytics 4 [B3.4] ──────────────────────────────────────── -->
    <!-- IMPORTANT: Replace G-XXXXXXXXXX with your real GA4 Measurement ID    -->
    <!-- Get it from: analytics.google.com → Admin → Data Streams             -->
    <?php if (getenv('GA4_ID') || defined('GA4_ID')): ?>
    <?php $ga4 = getenv('GA4_ID') ?: (defined('GA4_ID') ? GA4_ID : ''); ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($ga4); ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo htmlspecialchars($ga4); ?>');</script>
    <?php endif; ?>
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
<div id="marquee-bar" class="bg-primary text-white py-0.5 overflow-hidden whitespace-nowrap border-b border-primary/20" style="background-color:#ec5b13">
    <div class="overflow-hidden flex-1 relative h-full">
        <div class="animate-marquee whitespace-nowrap flex items-center h-full">
            <?php 
            $marquee_items = [
                "Discover The Wonders of Chhatrapati Sambhajinagar",
                "Book Premium Stays, Car Rentals & Local Tours",
                "Special Offers Available For First Time Visitors!",
                "Verified Local Guides for Ajanta & Ellora Caves",
                "24/7 Support for all your Travel Needs"
            ];
            // Triple items to ensure seamless loop
            $loop_items = array_merge($marquee_items, $marquee_items, $marquee_items);
            foreach($loop_items as $text): ?>
                <span class="flex items-center mx-8">
                    <span class="material-symbols-outlined text-white text-sm mr-2">stars</span>
                    <span class="text-[10px] font-bold text-white tracking-wider uppercase"><?php echo $text; ?></span>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="site-header-placeholder" class="w-full"></div>
<header id="site-header" class="w-full">
    <nav class="max-w-[1140px] mx-auto px-4 sm:px-5 flex items-center justify-between" style="height:64px;min-height:64px">
        <a href="<?php echo BASE_PATH; ?>/" class="flex items-center shrink-0">
            <img src="<?php echo BASE_PATH; ?>/images/travelhub.png" alt="CSNExplore" class="h-8 sm:h-9 object-contain"/>
        </a>
        <div class="hidden md:flex items-center gap-0.5">
            <?php foreach (($is_listing_page ? $listing_nav : $nav_links) as $link):
                $is_active = ($is_listing_page
                    ? ($link['type'] === $active_listing_type)
                    : (trim($link['href'],'/') === trim($current_page,'/') || ($current_page==='home' && ($link['href'] === BASE_PATH.'/' || strpos($link['href'],'/index')!==false))));
            ?>
            <a href="<?php echo $link['href']; ?>"
               class="text-sm font-semibold px-4 py-2 rounded-full transition-colors duration-200 <?php echo $is_active ? 'text-white bg-white/10' : 'text-white/65 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo $link['label']; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="hidden lg:flex items-center gap-1.5">
                <a href="tel:+918600968888"
                   class="hdr-call-btn flex items-center justify-center gap-1.5 bg-slate-800 text-white h-9 px-4 rounded-full hover:bg-slate-700 transition-all border border-slate-700 text-sm font-bold">
                    <span class="material-symbols-outlined text-[17px] text-primary">call</span>
                    <span class="hdr-call-text">+91 86009 68888</span>
                </a>
                <a href="https://wa.me/918600968888" target="_blank"
                   class="hdr-wa-btn flex items-center justify-center gap-1.5 bg-[#25D366] text-white h-9 px-3 rounded-full hover:bg-[#1ebe5d] transition-all text-sm font-bold">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    <span class="hdr-wa-text">WhatsApp</span>
                </a>
            </div>
            <div class="h-5 w-px bg-white/10 mx-0.5 hidden lg:block"></div>
            <a href="<?php echo BASE_PATH; ?>/login" id="hdr-login-btn"
               class="text-white text-[13px] font-bold px-3 py-1.5 hover:bg-white/10 rounded-full transition-all">Login</a>
            <div id="hdr-user-menu" class="hidden relative">
                <button id="hdr-user-btn" class="size-9 flex items-center justify-center hover:bg-white/10 rounded-full transition-all">
                    <span class="material-symbols-outlined text-[22px] text-primary" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">account_circle</span>
                </button>
                <div id="hdr-dropdown" class="hidden absolute right-0 top-full mt-2 w-44 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-[200]">
                    <div class="px-4 py-2 border-b border-slate-100 mb-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">My Account</p>
                    </div>
                    <a href="<?php echo BASE_PATH; ?>/my-booking" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors font-bold text-left">
                        <span class="material-symbols-outlined text-slate-600 text-[18px]">calendar_today</span> My Bookings
                    </a>
                    <button id="hdr-logout-btn" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors font-bold text-left">
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
        <img src="<?php echo BASE_PATH; ?>/images/travelhub.png" alt="CSNExplore" style="height:28px;object-fit:contain"/>
        <button id="mob-close" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.08);border:none;border-radius:50%;cursor:pointer;color:#fff">
            <span class="material-symbols-outlined" style="font-size:20px">close</span>
        </button>
    </div>
    <!-- nav links -->
    <div style="padding:14px 12px 8px;display:flex;flex-direction:column;gap:2px">
        <p style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:rgba(255,255,255,0.35);margin:0 0 6px 10px">Navigation</p>
        <?php foreach ($nav_links as $link): ?>
        <a href="<?php echo $link['href']; ?>" style="display:flex;align-items:center;padding:11px 14px;border-radius:12px;font-size:14px;font-weight:600;color:#fff;text-decoration:none;background:rgba(255,255,255,0.04)"><?php echo $link['label']; ?></a>
        <?php endforeach; ?>
    </div>
    <?php if ($is_listing_page): ?>
    <!-- listing categories -->
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
    <?php endif; ?>
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
        <div id="mob-auth-user" style="display:none">
            <button id="mob-logout-btn" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;color:#f87171;font-weight:700;background:rgba(248,113,113,0.12);border:1px solid rgba(248,113,113,0.2);border-radius:12px;font-size:13px;cursor:pointer">
                <span class="material-symbols-outlined" style="font-size:15px">logout</span> Sign Out
            </button>
        </div>
        <!-- call -->
        <a href="tel:+918600968888" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;background:#1e293b;color:#fff;font-weight:700;border-radius:12px;border:1px solid #334155;font-size:13px;text-decoration:none">
            <span class="material-symbols-outlined" style="font-size:15px;color:#ec5b13">call</span> +91 86009 68888
        </a>
        <!-- whatsapp -->
        <a href="https://wa.me/918600968888" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;background:#25D366;color:#fff;font-weight:700;border-radius:12px;font-size:13px;text-decoration:none">
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
            var ph = document.getElementById('site-header-placeholder');
            var mb = document.getElementById('marquee-bar');
            var ticking = false;
            var MH = 0; // marquee height
            var isPill = false;

            // Measure marquee height without transition interference
            function measureMarquee(){
                if (!mb) return 0;
                var old = mb.style.transition;
                mb.style.transition = 'none';
                mb.classList.remove('hidden-bar');
                var v = mb.getBoundingClientRect().height || mb.offsetHeight;
                mb.style.transition = old;
                return v;
            }

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
                if (ph) ph.style.height = (MH + 64) + 'px';
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
                if (ph) ph.style.height = (MH + 64) + 'px';
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
            MH = measureMarquee();
            update();

            window.addEventListener('scroll', function(){
                if (!ticking) {
                    requestAnimationFrame(update);
                    ticking = true;
                }
            }, { passive: true });

            window.addEventListener('resize', function(){
                MH = measureMarquee();
                update();
            }, { passive: true });

            window.addEventListener('load', function(){
                MH = measureMarquee();
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
                var mau=document.getElementById('mob-auth-user');  if(mau)mau.style.display='block';
            }
            var ub=document.getElementById('hdr-user-btn'),dd=document.getElementById('hdr-dropdown');
            if(ub&&dd){ ub.addEventListener('click',function(e){e.stopPropagation();dd.classList.toggle('hidden');}); document.addEventListener('click',function(){dd.classList.add('hidden');}); }
            function logout(){ ['csn_token','csn_user','csn_admin_token','csn_admin_user'].forEach(function(k){localStorage.removeItem(k);}); location.reload(); }
            var dl=document.getElementById('hdr-logout-btn'); if(dl)dl.addEventListener('click',logout);
            var ml=document.getElementById('mob-logout-btn'); if(ml)ml.addEventListener('click',logout);
        })();
    </script>

