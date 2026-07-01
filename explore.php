<?php
require_once 'php/config.php';
$page_title  = "Explore Chhatrapati Sambhajinagar (Aurangabad) | CSNExplore";
$current_page = "explore.php";

$page_meta = [
    'description' => 'Explore the rich history, culture, and ancient marvels of Chhatrapati Sambhajinagar (Aurangabad). Discover Ajanta, Ellora, Daulatabad Fort, and Bibi Ka Maqbara.',
    'canonical'   => 'https://csnexplore.com/explore',
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/Logo-light-optimized.webp',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Explore', 'url' => '/explore'],
    ],
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "@id": "https://csnexplore.com/explore",
  "name": "Explore Chhatrapati Sambhajinagar (Aurangabad) | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '"
}
</script>';

$extra_styles = "
    /* ── Explore page specific styles ── */
    .glass-panel { background:rgba(255,255,255,0.07); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.12); }
    .hero-parallax { background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover; }
    /* Disable fixed parallax on mobile — causes blank backgrounds on iOS */
    @media (max-width: 768px) {
        .hero-parallax { background-attachment: scroll !important; }
    }
    .card-zoom-image { transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1); }
    .group:hover .card-zoom-image { transform: scale(1.08); }

    /* ── FIX 1: Ensure all data-reveal elements are visible even if JS is slow ── */
    /* Override the global opacity:0 for explore page specific elements by default-revealing
       critical text, then letting JS handle the animated version */
    .explore-section-text[data-reveal],
    .explore-section-text [data-reveal] {
        opacity: 1 !important;
        transform: none !important;
        filter: none !important;
    }

    /* ── FIX 2: Landmark grid — mobile fix for masonry rows ── */
    @media (max-width: 767px) {
        .landmark-grid {
            grid-template-columns: 1fr !important;
            auto-rows: auto !important;
            grid-auto-rows: auto !important;
        }
        .landmark-grid > div {
            min-height: 260px !important;
            grid-column: span 1 !important;
            grid-row: span 1 !important;
        }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
        .landmark-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            auto-rows: 220px !important;
            grid-auto-rows: 220px !important;
        }
        .landmark-grid .md\\:col-span-8 { grid-column: span 2 !important; }
        .landmark-grid .md\\:col-span-4 { grid-column: span 2 !important; }
        .landmark-grid .md\\:col-span-6 { grid-column: span 1 !important; }
    }

    /* ── FIX 3: Card text always visible (not hidden behind translate) ── */
    .landmark-card-content {
        transform: none !important;
        transition: transform 0.3s ease !important;
    }
    .group:hover .landmark-card-content {
        transform: translateY(-4px) !important;
    }

    /* ── FIX 4: Icon containers — ensure proper size and alignment ── */
    .vibe-icon-box {
        width: 4rem;
        height: 4rem;
        flex-shrink: 0;
    }
    .vibe-icon-box .material-symbols-outlined {
        font-size: 2rem !important;
        line-height: 1 !important;
    }

    /* ── FIX 5: CTA arrow icon alignment ── */
    .cta-arrow-icon {
        font-size: 1.125rem !important;
        vertical-align: middle;
        line-height: 1;
    }

    /* ── FIX 6: Page bottom padding for mobile nav ── */
    @media (max-width: 767px) {
        main.bg-slate-50 {
            padding-bottom: 80px;
        }
        /* Ensure section text doesn't get clipped */
        .explore-hero-text h1 {
            font-size: clamp(1.8rem, 7vw, 4rem) !important;
            line-height: 1.15 !important;
        }
        .explore-hero-text p {
            font-size: 1rem !important;
        }
    }

    /* ── FIX 7: Breadcrumb icon alignment ── */
    .breadcrumb-icon {
        font-size: 1rem !important;
        vertical-align: middle;
        line-height: 1;
    }

    /* ── FIX 8: Hero badge text visibility ── */
    .hero-badge {
        background: rgba(236,91,19,0.25) !important;
        border: 1px solid rgba(236,91,19,0.5) !important;
        color: #ff8c5c !important;
        backdrop-filter: blur(8px);
    }

    /* ── FIX 9: History section tag pills ── */
    .history-tag {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        display: inline-block;
    }

    /* ── FIX 10: Vibe cards on mobile — image card layout ── */
    @media (max-width: 767px) {
        .vibe-grid {
            grid-template-columns: 1fr !important;
        }
        .vibe-card .h-48 {
            height: 11rem !important;
        }
    }
    /* Vibe card image zoom on hover */
    .vibe-card:hover .card-zoom-image {
        transform: scale(1.06);
    }

    /* ── FIX 11: CTA section buttons on mobile ── */
    @media (max-width: 479px) {
        .cta-btn-group {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .cta-btn-group a {
            text-align: center !important;
            justify-content: center !important;
        }
    }

    /* ── Hidden Gems strip: 2-col on mobile, 4-col on desktop ── */
    @media (max-width: 479px) {
        .grid.grid-cols-2.md\\:grid-cols-4 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    /* Hidden gems card hover zoom */
    .group:hover .card-zoom-image {
        transform: scale(1.07);
    }
";
require 'header.php';
?>

<main class="bg-slate-50">
<!-- Hero Section -->
<section class="relative min-h-[60vh] md:min-h-[70vh] flex flex-col justify-end overflow-hidden" style="padding-top:92px;">
    <div class="absolute inset-0 z-0">
        <!-- Background image (parallax on desktop, scroll on mobile) -->
        <div class="w-full h-full hero-parallax" style="background-image: url('<?php echo BASE_PATH; ?>/images/hero_csn.webp');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-[#0f172a]"></div>
    </div>
    <!-- Breadcrumb — sits right below the fixed header -->
    <div class="relative z-20 w-full" style="padding-top:1.25rem;">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-1.5 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined breadcrumb-icon">home</span>Home
            </a>
            <span class="material-symbols-outlined breadcrumb-icon" style="opacity:0.4;">chevron_right</span>
            <span class="text-white font-semibold">Explore</span>
        </div>
    </div>
    <!-- Hero text — always visible, reveal animation is progressive enhancement -->
    <div class="relative z-10 text-center px-5 max-w-[1140px] mx-auto w-full py-14 md:py-20 explore-hero-text" data-reveal>
        <div class="max-w-4xl mx-auto">
            <span class="hero-badge inline-block px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-widest mb-5">City of Gates</span>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-serif font-black mb-5 text-white leading-tight drop-shadow-2xl">
                Explore <span class="text-primary italic">Chhatrapati Sambhajinagar</span>
            </h1>
            <p class="text-base md:text-xl text-white/80 max-w-2xl mx-auto leading-relaxed drop-shadow-md">
                A journey through time. Uncover the secrets of the ancient caves, majestic forts, and rich Mughal history in the tourism capital of Maharashtra.
            </p>
        </div>
    </div>
</section>

<!-- History & Significance -->
<section class="py-20 bg-white border-b border-slate-100">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div data-reveal="left" class="space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-12 h-1 bg-primary rounded-full flex-shrink-0"></span>
                    <span class="text-primary font-bold text-sm uppercase tracking-widest">Heritage</span>
                </div>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-slate-900 leading-tight">The Historic Heart of India</h2>
                <p class="text-slate-600 text-base md:text-lg leading-relaxed">
                    Historically known as <strong class="text-slate-800">Aurangabad</strong>, and recently renamed to <strong class="text-slate-800">Chhatrapati Sambhajinagar</strong>, the city stands as a testament to India's magnificent past. It was once a prominent hub of the Mughal Empire under Aurangzeb, earning it the title "City of Gates" due to the 52 gates built during its golden era.
                </p>
                <p class="text-slate-600 text-base md:text-lg leading-relaxed">
                    Today, it acts as the gateway to the UNESCO World Heritage Sites of the Ajanta and Ellora Caves, drawing historians, pilgrims, and explorers from all corners of the globe. The city beautifully balances its profound historical significance with an evolving modern identity.
                </p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <span class="history-tag">UNESCO Heritage</span>
                    <span class="history-tag">City of 52 Gates</span>
                    <span class="history-tag">Tourism Capital of Maharashtra</span>
                </div>
            </div>
            <div data-reveal="right" class="relative mt-8 md:mt-0">
                <div class="absolute -inset-4 bg-primary/10 rounded-[2rem] transform rotate-3 pointer-events-none"></div>
                <div class="relative rounded-2xl overflow-hidden shadow-2xl" style="aspect-ratio:4/5;">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/panchakki.webp" alt="Panchakki — Historic Water Mill, Chhatrapati Sambhajinagar" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Must Visit Landmarks (Masonry-style Grid) -->
<section class="py-20 md:py-24 bg-slate-50">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="text-center mb-12 md:mb-16" data-reveal>
            <span class="text-primary font-bold text-sm uppercase tracking-widest block mb-3">Discover</span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-slate-900">Iconic Landmarks</h2>
            <p class="text-slate-500 max-w-2xl mx-auto mt-4 text-base md:text-lg">From monolithic rock-cut temples to majestic forts, explore the most breathtaking destinations in and around the city.</p>
        </div>

        <!-- Landmark Grid: 12-col masonry on desktop, single-col on mobile -->
        <div class="landmark-grid grid grid-cols-1 md:grid-cols-12 gap-5 md:auto-rows-[250px]">
            
            <!-- Ellora Caves (Large — spans 8 cols × 2 rows on desktop) -->
            <div data-reveal="scale" class="md:col-span-8 md:row-span-2 group relative overflow-hidden rounded-3xl shadow-lg cursor-pointer" style="min-height:280px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/ellora_caves.webp" alt="Ellora Caves — Kailasa Temple" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                <div class="landmark-card-content absolute bottom-0 left-0 right-0 p-6 md:p-8">
                    <span class="inline-block px-3 py-1 bg-primary text-white text-xs font-bold rounded mb-2 md:mb-3">UNESCO Site</span>
                    <h3 class="text-xl md:text-3xl font-serif font-bold text-white mb-1 md:mb-2">Ellora Caves (Kailasa Temple)</h3>
                    <p class="text-white/80 text-sm md:text-base line-clamp-2">The Kailasa temple is the world's largest monolithic structure carved from a single piece of rock. A true architectural marvel representing Hindu, Buddhist, and Jain faiths.</p>
                </div>
            </div>

            <!-- Bibi Ka Maqbara (4 cols × 2 rows) -->
            <div data-reveal="scale" data-delay="100" class="md:col-span-4 md:row-span-2 group relative overflow-hidden rounded-3xl shadow-lg cursor-pointer" style="min-height:260px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/bibi.webp" alt="Bibi Ka Maqbara — Taj of the Deccan" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                <div class="landmark-card-content absolute bottom-0 left-0 right-0 p-5 md:p-6">
                    <h3 class="text-xl md:text-2xl font-serif font-bold text-white mb-1 md:mb-2">Bibi Ka Maqbara</h3>
                    <p class="text-white/80 text-sm line-clamp-3">Often called the "Taj of the Deccan", this magnificent mausoleum was built by Aurangzeb's son in memory of his mother, Dilras Banu Begum.</p>
                </div>
            </div>

            <!-- Ajanta Caves (6 cols × 2 rows) -->
            <div data-reveal="scale" data-delay="200" class="md:col-span-6 md:row-span-2 group relative overflow-hidden rounded-3xl shadow-lg cursor-pointer" style="min-height:260px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/ajanta.webp" alt="Ajanta Caves — Buddhist Rock-cut Monuments" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                <div class="landmark-card-content absolute bottom-0 left-0 right-0 p-6 md:p-8">
                    <span class="inline-block px-3 py-1 bg-primary text-white text-xs font-bold rounded mb-2 md:mb-3">UNESCO Site</span>
                    <h3 class="text-xl md:text-3xl font-serif font-bold text-white mb-1 md:mb-2">Ajanta Caves</h3>
                    <p class="text-white/80 text-sm md:text-base line-clamp-2">Famous for its exquisite murals and rock-cut Buddhist cave monuments dating from the 2nd century BCE to about 480 CE.</p>
                </div>
            </div>

            <!-- Daulatabad Fort (6 cols × 2 rows) -->
            <div data-reveal="scale" data-delay="300" class="md:col-span-6 md:row-span-2 group relative overflow-hidden rounded-3xl shadow-lg cursor-pointer" style="min-height:260px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/daulatabad.webp" alt="Daulatabad Fort — 12th Century Hill Fortress" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                <div class="landmark-card-content absolute bottom-0 left-0 right-0 p-6 md:p-8">
                    <h3 class="text-xl md:text-3xl font-serif font-bold text-white mb-1 md:mb-2">Daulatabad Fort</h3>
                    <p class="text-white/80 text-sm md:text-base line-clamp-2">An impregnable 12th-century hill fortress built on a conical hill, known for its strategic location and complex defense mechanisms.</p>
                </div>
            </div>
            
        </div>
        
        <div class="text-center mt-10 md:mt-12" data-reveal>
            <a href="<?php echo BASE_PATH; ?>/attractions" class="inline-flex items-center gap-2 bg-slate-900 text-white px-8 py-4 rounded-full font-bold hover:bg-primary transition-colors shadow-xl">
                View All Attractions <span class="material-symbols-outlined cta-arrow-icon">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<!-- The Vibe of the City -->
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="text-center mb-10 md:mb-12" data-reveal>
            <span class="text-primary font-bold text-sm uppercase tracking-widest block mb-3">Experience</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900">The Vibe of the City</h2>
        </div>
        <div class="vibe-grid grid md:grid-cols-3 gap-6 md:gap-8">

            <!-- Mughlai Cuisine -->
            <div data-reveal="left" class="vibe-card rounded-3xl bg-slate-50 border border-slate-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/north-indian-thali.webp" alt="Mughlai Cuisine — North Indian Thali" class="w-full h-full object-cover card-zoom-image">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-3 left-4">
                        <span class="inline-flex items-center gap-1.5 bg-primary/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-sm">
                            <span class="material-symbols-outlined" style="font-size:14px;line-height:1;">restaurant</span>Food
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <h4 class="text-lg md:text-xl font-bold text-slate-900 mb-2">Mughlai Cuisine</h4>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed">The city is renowned for its rich Mughlai food. Do not miss the famous 'Naan Qalia', a traditional slow-cooked meat dish served with soft tandoori naan.</p>
                </div>
            </div>

            <!-- Himroo & Paithani -->
            <div data-reveal="up" data-delay="100" class="vibe-card rounded-3xl bg-slate-50 border border-slate-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/valley_of_saints.webp" alt="Valley of Saints — Chhatrapati Sambhajinagar" class="w-full h-full object-cover card-zoom-image">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-3 left-4">
                        <span class="inline-flex items-center gap-1.5 bg-primary/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-sm">
                            <span class="material-symbols-outlined" style="font-size:14px;line-height:1;">shopping_bag</span>Shopping
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <h4 class="text-lg md:text-xl font-bold text-slate-900 mb-2">Himroo &amp; Paithani</h4>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed">Aurangabad is a center for traditional textiles. Shop for exquisite Himroo shawls and authentic Paithani sarees, woven with gold and silver threads.</p>
                </div>
            </div>

            <!-- Ellora Festival -->
            <div data-reveal="right" data-delay="200" class="vibe-card rounded-3xl bg-slate-50 border border-slate-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/grishneshwar-temple.webp" alt="Grishneshwar Temple — Chhatrapati Sambhajinagar" class="w-full h-full object-cover card-zoom-image">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-3 left-4">
                        <span class="inline-flex items-center gap-1.5 bg-primary/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-sm">
                            <span class="material-symbols-outlined" style="font-size:14px;line-height:1;">festival</span>Culture
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <h4 class="text-lg md:text-xl font-bold text-slate-900 mb-2">Ellora Festival</h4>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed">Experience the vibrant Ellora-Aurangabad Festival, an annual celebration of Indian classical music and dance set against the backdrop of the majestic caves.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- More to Explore — hidden gems strip -->
<section class="py-14 md:py-16 bg-slate-50 border-t border-slate-100">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="flex items-end justify-between mb-8 flex-wrap gap-3" data-reveal>
            <div>
                <span class="text-primary font-bold text-sm uppercase tracking-widest block mb-2">Hidden Gems</span>
                <h2 class="text-2xl md:text-3xl font-serif font-bold text-slate-900">More to Explore</h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/attractions" class="inline-flex items-center gap-1.5 text-primary font-bold text-sm hover:underline">
                See all attractions <span class="material-symbols-outlined" style="font-size:16px;">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <!-- Siddharth Lake -->
            <div data-reveal="scale" class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer" style="min-height:180px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/siddharth_lake.webp" alt="Siddharth Lake Garden" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-white font-bold text-sm leading-tight">Siddharth Lake Garden</p>
                    <p class="text-white/60 text-xs mt-0.5">Scenic park &amp; lake</p>
                </div>
            </div>

            <!-- Buddha Vihar -->
            <div data-reveal="scale" data-delay="80" class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer" style="min-height:180px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/buddha_vihar.webp" alt="Buddha Vihar" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-white font-bold text-sm leading-tight">Buddha Vihar</p>
                    <p class="text-white/60 text-xs mt-0.5">Buddhist meditation center</p>
                </div>
            </div>

            <!-- Maharashtra Museum -->
            <div data-reveal="scale" data-delay="160" class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer" style="min-height:180px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/maharashtra_museum.webp" alt="Maharashtra State Museum" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-white font-bold text-sm leading-tight">State Museum</p>
                    <p class="text-white/60 text-xs mt-0.5">History &amp; artefacts</p>
                </div>
            </div>

            <!-- CIDCO Garden -->
            <div data-reveal="scale" data-delay="240" class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer" style="min-height:180px;">
                <img src="<?php echo BASE_PATH; ?>/images/uploads/cidco_garden.webp" alt="CIDCO Garden" class="absolute inset-0 w-full h-full object-cover card-zoom-image">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-white font-bold text-sm leading-tight">CIDCO Garden</p>
                    <p class="text-white/60 text-xs mt-0.5">Green recreational space</p>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="py-20 md:py-24 relative overflow-hidden bg-[#0f172a]">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <!-- Real background image with dark overlay -->
        <img src="<?php echo BASE_PATH; ?>/images/uploads/travel-gate.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(236,91,19,0.18)_0%,rgba(15,23,42,0.95)_70%)]"></div>
        <!-- Glowing orbs -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/20 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px] translate-y-1/3 -translate-x-1/3"></div>
    </div>
    
    <div class="max-w-[900px] mx-auto px-5 relative z-10 text-center" data-reveal>
        <span class="inline-block px-4 py-1.5 rounded-full text-white text-xs font-bold uppercase tracking-widest mb-6 border border-white/25" style="background:rgba(255,255,255,0.1);">Start Planning</span>
        <h2 class="text-3xl md:text-5xl lg:text-6xl font-serif font-black text-white mb-5 md:mb-6 leading-tight">Ready to experience the magic?</h2>
        <p class="text-base md:text-xl text-white/75 mb-8 md:mb-10 leading-relaxed max-w-2xl mx-auto">
            From luxury stays to seamless car rentals and guided tours, CSNExplore helps you organize your perfect trip to Chhatrapati Sambhajinagar.
        </p>
        <div class="cta-btn-group flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo BASE_PATH; ?>/hotels" class="bg-primary text-white px-8 py-4 rounded-full font-bold text-base md:text-lg hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(236,91,19,0.4)] transition-all text-center">
                Book a Hotel
            </a>
            <a href="<?php echo BASE_PATH; ?>/suggestor" class="border border-white/25 text-white px-8 py-4 rounded-full font-bold text-base md:text-lg hover:bg-white/15 transition-colors text-center" style="background:rgba(255,255,255,0.08);">
                Use AI Trip Planner
            </a>
        </div>
    </div>
</section>

</main>

<?php require 'footer.php'; ?>
