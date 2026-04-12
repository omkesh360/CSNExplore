<?php
// index.php – CSNExplore Home Page
$page_title = "CSNExplore – Hotels, Bikes, Cars & Attractions in Chhatrapati Sambhajinagar";
$current_page = "home";
require_once 'php/config.php';
$db = getDB();


// Fetch homepage settings
$hp_settings_row = $db->fetchOne("SELECT content FROM about_contact WHERE section = 'homepage'");
$hp_settings = [];
if ($hp_settings_row && !empty($hp_settings_row['content'])) {
    $decoded = json_decode($hp_settings_row['content'], true);
    if (is_array($decoded)) $hp_settings = $decoded;
}
// Defaults
$hp_defaults = [
    'title_attractions' => 'Ancient Marvels',
    'title_bikes'       => 'Quick Bike Rentals',
    'title_restaurants' => 'Taste the City',
    'title_buses'       => 'Travel Your Way',
    'title_blogs'       => 'Travel Insights',
    'title_cars'        => 'Self Drive Cars',
    'title_stays'       => 'Premium Stays',
    'show_attractions'  => true,
    'show_bikes'        => true,
    'show_restaurants'  => true,
    'show_buses'        => true,
    'show_blogs'        => true,
    'show_cars'         => true,
    'show_stays'        => true,
    'hero_subtext'      => 'Stays, cars, bikes, restaurants, attractions and buses — all in one place.',
    'city_intro'        => '',
    'stat1_label'       => '500+ Hotels',
    'stat2_label'       => '50+ Attractions',
    'stat3_label'       => '200+ Restaurants',
    'stat4_label'       => '10K+ Happy Travelers',
    'count_attractions' => 4,
    'count_bikes'       => 4,
    'count_restaurants' => 4,
    'count_buses'       => 2,
    'count_blogs'       => 3,
    'count_cars'        => 4,
    'count_stays'       => 4,
    'layout_attractions'=> '4-col',
    'layout_bikes'      => '4-col',
    'layout_restaurants'=> '4-col',
    'layout_buses'      => '2-col',
    'layout_blogs'      => '3-col',
    'layout_cars'       => '4-col',
    'layout_stays'      => '4-col',
    'section_order'     => ['stays','cars','bikes','attractions','restaurants','buses','blogs'],
    'picks_attractions' => [],
    'picks_bikes'       => [],
    'picks_restaurants' => [],
    'picks_buses'       => [],
    'picks_blogs'       => [],
    'picks_cars'        => [],
    'picks_stays'       => [],
];
foreach ($hp_defaults as $k => $v) {
    if (!isset($hp_settings[$k]) || $hp_settings[$k] === '') {
        $hp_settings[$k] = $v;
    }
}
// Ensure section_order is always a valid array
if (!is_array($hp_settings['section_order']) || count($hp_settings['section_order']) < 5) {
    $hp_settings['section_order'] = ['stays','cars','bikes','attractions','restaurants','buses','blogs'];
}

// Helper: layout string → Tailwind grid/flex class
function hp_grid_class($layout) {
    $map = [
        '3-col' => 'grid grid-cols-1 md:grid-cols-3 gap-5',
        '4-col' => 'grid grid-cols-2 md:grid-cols-4 gap-5',
        '2-col' => 'grid grid-cols-1 md:grid-cols-2 gap-4',
        'list'  => 'flex flex-col gap-3',
        'scroll'=> 'flex gap-5 overflow-x-auto hide-scrollbar pb-3 snap-x snap-mandatory',
    ];
    return $map[$layout] ?? $map['3-col'];
}
// Card wrapper class for scroll layout
function hp_card_wrap($layout) {
    return $layout === 'scroll' ? 'flex-shrink-0 w-72 snap-start' : '';
}

// Fetch real data from DB — use picks if set, otherwise use saved counts
function hp_fetch_picks($db, $table, $picks, $where_active, $fallback_sql) {
    if (!empty($picks) && is_array($picks)) {
        $ids = implode(',', array_map('intval', $picks));
        $rows = $db->fetchAll("SELECT * FROM {$table} WHERE id IN ({$ids}) AND {$where_active}");
        // Preserve pick order
        $indexed = [];
        foreach ($rows as $r) $indexed[$r['id']] = $r;
        $ordered = [];
        foreach ($picks as $pid) { if (isset($indexed[$pid])) $ordered[] = $indexed[$pid]; }
        return $ordered;
    }
    return $db->fetchAll($fallback_sql);
}

$hp_attractions = hp_fetch_picks($db, 'attractions', $hp_settings['picks_attractions'], 'is_active=1',
    "SELECT * FROM attractions WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_attractions']);
$hp_bikes = hp_fetch_picks($db, 'bikes', $hp_settings['picks_bikes'], 'is_active=1',
    "SELECT * FROM bikes WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_bikes']);
$hp_restaurants = hp_fetch_picks($db, 'restaurants', $hp_settings['picks_restaurants'], 'is_active=1',
    "SELECT * FROM restaurants WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_restaurants']);
$hp_buses = hp_fetch_picks($db, 'buses', $hp_settings['picks_buses'] ?? [], 'is_active=1',
    "SELECT * FROM buses WHERE is_active=1 ORDER BY display_order ASC LIMIT " . (int)$hp_settings['count_buses']);
$hp_blogs = hp_fetch_picks($db, 'blogs', $hp_settings['picks_blogs'] ?? [], "status='published'",
    "SELECT * FROM blogs WHERE status='published' ORDER BY created_at DESC LIMIT " . (int)$hp_settings['count_blogs']);
$hp_cars = hp_fetch_picks($db, 'cars', $hp_settings['picks_cars'] ?? [], 'is_active=1',
    "SELECT * FROM cars WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_cars']);
$hp_stays = hp_fetch_picks($db, 'stays', $hp_settings['picks_stays'] ?? [], 'is_active=1',
    "SELECT * FROM stays WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_stays']);
?>
<?php
$page_meta = [
    'description' => 'Your premium gateway to the wonders of Chhatrapati Sambhajinagar, Maharashtra. Book hotels, cars, bikes, and explore attractions easily.',
    'canonical'   => 'https://csnexplore.com/',
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/travelhub.png'
];
$extra_head = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" media="print" onload="this.media=\'all\'"/><noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css"/></noscript><script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "CSNExplore",
  "url": "https://csnexplore.com",
  "logo": "https://csnexplore.com/images/travelhub.png",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+91-8600968888",
    "contactType": "customer service"
  }
}
</script>';
$extra_styles = "
        .hide-scrollbar::-webkit-scrollbar { display:none; }
        .hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .card-hover:hover { box-shadow:0 0 30px rgba(236,91,19,0.15); }
        /* Mobile card widths: 85vw shows 1.15 cards hinting scroll; buses almost full */
        :root { 
          --card-w-attractions: 82vw; 
          --card-w-bikes: 82vw; 
          --card-w-restaurants: 82vw; 
          --card-w-buses: 92vw; 
          --card-w-blogs: 82vw; 
          --card-w-cars: 82vw; 
          --card-w-stays: 82vw; 
        }
        @media (min-width: 480px) { 
          :root { 
            --card-w-attractions: 48vw; --card-w-bikes: 48vw; --card-w-restaurants: 48vw;
            --card-w-buses: 70vw; --card-w-blogs: 52vw; --card-w-cars: 48vw; --card-w-stays: 48vw; 
          } 
        }
        @media (min-width: 768px) { 
          :root { 
            --card-w-attractions: calc(33.333% - 14px); --card-w-bikes: calc(33.333% - 14px);
            --card-w-restaurants: calc(33.333% - 14px); --card-w-buses: calc(50% - 10px);
            --card-w-blogs: calc(33.333% - 14px); --card-w-cars: calc(33.333% - 14px); --card-w-stays: calc(33.333% - 14px); 
          } 
        }
        @media (min-width: 1024px) { 
          :root { 
            --card-w-attractions: calc(25% - 15px); --card-w-bikes: calc(25% - 15px);
            --card-w-restaurants: calc(25% - 15px); --card-w-buses: calc(50% - 10px);
            --card-w-blogs: calc(33.333% - 14px); --card-w-cars: calc(25% - 15px); --card-w-stays: calc(25% - 15px); 
          } 
        }
        #hero-label, #hero-pre, #hero-highlight, #hero-post, #hero-desc { transition: opacity 0.25s ease; }
        .search-box { 
            background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-top: 1px solid rgba(255,255,255,0.3);
            border-left: 1px solid rgba(255,255,255,0.2);
            border-radius: 32px; padding: 28px 32px;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.6), inset 0 0 0 1px rgba(255,255,255,0.05);
        }
        #search-tabs-scroll {
            display: flex; gap: 8px; justify-content: center; margin: 0 auto 24px auto;
            background: rgba(0,0,0,0.2); border-radius: 99px;
            border: 1px solid rgba(255,255,255,0.05); padding: 6px; width: fit-content;
        }
        .tab-btn { 
            display:flex; align-items:center; gap:8px; padding:10px 24px; 
            border-radius:99px; font-size:14px; font-weight:700; 
            color:rgba(255,255,255,0.6); cursor:pointer; 
            transition:all 0.5s cubic-bezier(0.22, 1, 0.36, 1); 
            border: none; background: transparent; white-space:nowrap;
        }
        .tab-btn:hover { color:#fff; background:rgba(255,255,255,0.08); }
        .tab-btn.active { 
            color:#fff; background: linear-gradient(135deg, #ec5b13, #ff7a2e);
            box-shadow: 0 8px 16px rgba(236,91,19,0.3);
        }
        .tab-btn .material-symbols-outlined { font-size:20px; transition:transform 0.3s; }
        .tab-btn.active .material-symbols-outlined { animation:iconPop 0.5s; }
        @keyframes iconPop {
            0%, 100% { transform:scale(1) rotate(0deg); }
            50% { transform:scale(1.2) rotate(-10deg); }
        }
        .search-row { 
            display:flex; align-items:stretch; width:100%; 
            background: rgba(0,0,0,0.25); border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.08); padding: 6px;
        }
        .search-field, .date-field { 
            display:flex; align-items:center; gap:12px; 
            background: transparent; border:none; border-right: 1px solid rgba(255,255,255,0.1); 
            border-radius: 12px; padding: 0 20px; flex:1; min-width:0; height:60px; 
            transition:background 0.3s; position: relative;
        }
        .search-field:hover, .date-field:hover { background: rgba(255,255,255,0.05); }
        .search-field:nth-last-child(2), .date-field:nth-last-child(2) { border-right: none; }
        
        .search-field .material-symbols-outlined, .date-field .material-symbols-outlined { color: #ec5b13; font-size:24px; flex-shrink:0; }
        .search-field input, .date-field input { background:transparent; border:none; outline:none; color:#fff; font-size:16px; font-weight:600; width:100%; min-width:0; box-shadow:none; -webkit-appearance:none; text-overflow: ellipsis; }
        .search-field input::placeholder, .date-field input::placeholder { color:rgba(255,255,255,0.5); font-weight:400; text-overflow: ellipsis; }
        
        .search-btn { 
            background: #fff; color: #111; font-weight:800; font-size:16px; 
            padding:0 32px; border-radius:14px; border:none; cursor:pointer; 
            display:flex; align-items:center; gap:8px; transition:all 0.3s; white-space:nowrap; flex-shrink:0; height:60px; 
        }
        .search-btn:hover { background: #ec5b13; color: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(236,91,19,0.3); }
        .search-btn:hover .material-symbols-outlined { color: #fff; }
        .search-btn .material-symbols-outlined { font-size:22px; color: #ec5b13; transition: color 0.3s; }
        
        @media(max-width:768px){
          .search-box { padding:20px 16px; border-radius:24px; }
          .search-row { 
            display: flex; flex-direction: column; 
            background: transparent; border: none; padding: 0; gap: 12px;
          }
          .search-field, .date-field { 
            display: flex !important; width: 100% !important; min-height: 72px !important; flex: none !important;
            padding: 0 24px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.15);
            background: rgba(0,0,0,0.3); align-items: center;
          }
          .search-field input, .date-field input { 
            font-size: 16px; font-weight: 500; height: 100%; width: 100%;
          }
          .search-btn { 
            width: 100%; min-height: 72px; padding: 0 24px; border-radius: 20px; 
            margin-top: 4px; font-size: 18px; font-weight: 800; justify-content: center;
          }
          
          #search-tabs-scroll { 
            display: grid !important; grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important; padding: 0 !important; margin-bottom: 24px !important; width: 100% !important;
            background: transparent !important; border: none !important; border-radius: 0 !important; box-shadow: none !important;
          }

          .tab-btn { 
            display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: center !important;
            padding: 0 4px !important; font-size: 14px !important; min-height: 54px !important; width: 100% !important;
            border-radius: 99px !important; background: rgba(0,0,0,0.3) !important; border: 1px solid rgba(255,255,255,0.1) !important;
          }
          .tab-btn .material-symbols-outlined { display: none !important; }
          .tab-btn span:not(.material-symbols-outlined) { font-weight: 700 !important; opacity: 0.8; }
          .tab-btn.active {
            background: linear-gradient(135deg, #ec5b13, #ff7a2e) !important; border-color: transparent !important;
          }
          .tab-btn.active span:not(.material-symbols-outlined) { opacity: 1; }
          
          /* Extra compact fix for tiny phones */
          @media(max-width:360px){
            .search-field, .date-field, .search-btn { height: 60px !important; min-height: 60px !important; }
            .tab-btn { font-size: 12px !important; min-height: 48px !important; }
          }
        }
        .search-panel { 
            display:none; 
            opacity:0;
            transform:translateY(10px);
            transition:opacity 0.5s cubic-bezier(0.22, 1, 0.36, 1), transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .search-panel.active { 
            display:flex; flex-direction:column; gap:8px; 
            animation:panelFadeIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        @keyframes panelFadeIn {
            from { opacity:0; transform:translateY(10px); }
            to { opacity:1; transform:translateY(0); }
        }
        .flatpickr-calendar { background:#1c1410 !important; border:1px solid rgba(236,91,19,0.3) !important; border-radius:16px !important; box-shadow:0 20px 60px rgba(0,0,0,0.6) !important; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background:#ec5b13 !important; border-color:#ec5b13 !important; }
        .flatpickr-day.inRange { background:rgba(236,91,19,0.2) !important; border-color:transparent !important; }
        .flatpickr-day:hover { background:rgba(236,91,19,0.3) !important; }
        .flatpickr-months .flatpickr-month, .flatpickr-current-month { color:#fff !important; }
        .flatpickr-weekday { color:rgba(255,255,255,0.5) !important; }
        .flatpickr-day { color:#fff !important; }
        .flatpickr-day.flatpickr-disabled { color:rgba(255,255,255,0.2) !important; }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { fill:#fff !important; }
        #hero-bg { will-change: transform; }
        .particle { position:absolute; border-radius:50%; pointer-events:none; animation:particleDrift linear infinite; }
        @keyframes particleDrift { 0% { transform:translateY(0) translateX(0) scale(1); opacity:0; } 10% { opacity:1; } 90% { opacity:0.6; } 100% { transform:translateY(-120vh) translateX(30px) scale(0.5); opacity:0; } }
        .stat-num { display:inline-block; }
        .wave-divider { line-height:0; overflow:hidden; }
        .gradient-text { background:linear-gradient(135deg,#ec5b13,#ff8c42); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .glow-badge { box-shadow:0 0 20px rgba(236,91,19,0.4); }
        
        /* Inheriting scroll-triggered animations from header.php */
        /* Stagger animation delays */
        [data-reveal]:nth-child(1) { transition-delay: 0ms; }
        [data-reveal]:nth-child(2) { transition-delay: 100ms; }
        [data-reveal]:nth-child(3) { transition-delay: 200ms; }
        [data-reveal]:nth-child(4) { transition-delay: 300ms; }
        [data-reveal]:nth-child(5) { transition-delay: 400ms; }
        [data-reveal]:nth-child(6) { transition-delay: 500ms; }
        
        /* Enhanced card hover effects */
        .card-hover { 
            transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1); 
            position: relative;
        }
        .card-hover::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(236,91,19,0.1), rgba(255,140,66,0.1));
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }
        .card-hover:hover::before { opacity: 1; }
        .card-hover:hover { 
            box-shadow: 0 20px 60px rgba(236,91,19,0.2), 0 0 0 1px rgba(236,91,19,0.1); 
            transform: translateY(-4px) scale(1.01);
        }
        
        /* Section fade-in animation */
        section { 
            animation: sectionFadeIn 1.2s cubic-bezier(0.22, 1, 0.36, 1) forwards; 
        }
        @keyframes sectionFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Service button stagger animation */
        .service-btn-stagger > *:nth-child(1) { animation-delay: 0ms; }
        .service-btn-stagger > *:nth-child(2) { animation-delay: 100ms; }
        .service-btn-stagger > *:nth-child(3) { animation-delay: 200ms; }
        .service-btn-stagger > *:nth-child(4) { animation-delay: 300ms; }
        .service-btn-stagger > *:nth-child(5) { animation-delay: 400ms; }
        .service-btn-stagger > *:nth-child(6) { animation-delay: 500ms; }
    ";
require 'header.php';
?>

<main>
<!-- Hero -->
<section class="relative min-h-[100svh] md:min-h-[85vh] flex flex-col items-center justify-center overflow-hidden pt-20 md:pt-24 pb-8 md:pb-12 w-full">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(0,0,0,0)_0%,rgba(0,0,0,0.8)_100%)] z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-transparent to-[#0a0705] z-10"></div>
        <div id="hero-bg" class="w-full h-full bg-cover bg-center transition-all duration-1000"
             style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDfTDZo8LglfsdX1vCy-PfHltcZor3jl-l4xxrXMYSU-zLgoKXxY-ouUImyR0WZq69V0y63PE1wDL2_EfqYwWhgQOHPVDJVHhyGGB7H8kZNyboNAXVxWDvlFW_Z_QRXuTKMBuuk7a9HgI3Gde3PidzWIcOhtgs4QAHX2DHA2V6QUaFo6mYDZzEhvq1Y7FwjBSsjTNmfwco23Zfvdb8laeVoTMZHDGMoMrH3yPn4aQDHZ9AJE-WXiuWGVG-c0BegSoJwB1zEXVVWIUie')">
        </div>
        <!-- Floating orbs -->
        <div class="orb w-96 h-96 bg-primary/20 top-1/4 -left-24 z-[5]" style="animation-delay:0s"></div>
        <div class="orb w-64 h-64 bg-orange-400/10 bottom-1/3 right-10 z-[5]" style="animation-delay:3s"></div>
        <!-- Particles container -->
        <div id="particles" class="absolute inset-0 z-[6] overflow-hidden"></div>    </div>
    <div class="relative z-20 text-center px-4 w-full max-w-[1140px] mx-auto pt-4 md:pt-8 pb-4 md:pb-6">
        <p id="hero-label" class="mobile-hide text-primary font-bold text-[10px] md:text-xs uppercase tracking-widest mb-2 md:mb-3">Chhatrapati Sambhajinagar</p>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl text-white mb-3 md:mb-4 leading-tight font-black px-2">
            <span id="hero-pre">Explore </span><span class="text-primary" id="hero-highlight">Your City</span><span id="hero-post"> Your Way</span>
        </h1>
        <p id="hero-desc" class="text-white/70 text-sm sm:text-base md:text-lg mb-6 md:mb-8 lg:mb-10 max-w-2xl mx-auto px-4"><?php echo htmlspecialchars($hp_settings['hero_subtext']); ?></p>

        <!-- Modern Tabs Section -->
        <div class="search-box max-w-4xl mx-auto w-full">
            <div id="search-tabs-scroll" class="grid grid-cols-3 md:flex md:flex-wrap gap-2 md:gap-3 mb-6 pb-4 justify-center items-center">
                <?php
                $tabs = [
                    ['id' => 'stays',       'icon' => 'bed',                  'label' => 'Stays'],
                    ['id' => 'cars',        'icon' => 'directions_car',        'label' => 'Cars'],
                    ['id' => 'bikes',       'icon' => 'motorcycle',            'label' => 'Bikes'],
                    ['id' => 'attractions', 'icon' => 'confirmation_number',   'label' => 'Attractions'],
                    ['id' => 'dine',        'icon' => 'restaurant',            'label' => 'Dine'],
                    ['id' => 'buses',       'icon' => 'directions_bus',        'label' => 'Buses'],
                ];
                foreach ($tabs as $i => $tab): ?>
                    <button class="tab-btn <?php echo $i === 0 ? 'active' : ''; ?>" data-tab="<?php echo $tab['id']; ?>" onclick="switchTab('<?php echo $tab['id']; ?>')">
                        <span class="material-symbols-outlined"><?php echo $tab['icon']; ?></span>
                        <span><?php echo $tab['label']; ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- CARS panel -->
            <div id="panel-cars" class="search-panel active">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="cars-pickup" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="cars-drop" type="text" placeholder="Drop location"/></div>
                    <div class="date-field" onclick="document.getElementById('cars-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="cars-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('cars')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- STAYS panel -->
            <div id="panel-stays" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="stays-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkin').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkin" type="text" placeholder="Check-in" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkout').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkout" type="text" placeholder="Check-out" readonly/></div>
                    <button class="search-btn" onclick="doSearch('stays')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BIKES panel -->
            <div id="panel-bikes" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="bikes-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="bikes-date" type="text" placeholder="From date" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-return').focus()"><span class="material-symbols-outlined">event_available</span><input id="bikes-return" type="text" placeholder="Return date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('bikes')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- ATTRACTIONS panel -->
            <div id="panel-attractions" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="attractions-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('attractions-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="attractions-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('attractions')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- DINE panel -->
            <div id="panel-dine" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="dine-location" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="date-field" onclick="document.getElementById('dine-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="dine-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('dine')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BUSES panel -->
            <div id="panel-buses" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="buses-from" type="text" placeholder="Chhatrapati Sambhajinagar" value="Chhatrapati Sambhajinagar"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="buses-to" type="text" placeholder="Destination city"/></div>
                    <div class="date-field" onclick="document.getElementById('buses-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="buses-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('buses')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function switchTab(tab, fromAuto) {
    document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
    var btn = document.querySelector('[data-tab="'+tab+'"]');
    btn.classList.add('active');
    
    document.querySelectorAll('.search-panel').forEach(function(p){ p.classList.remove('active'); });
    document.getElementById('panel-'+tab).classList.add('active');
    var heroData = {
        stays:       { img: 'images/hotel-hero-section%20(4).webp', label:'Find Your Stay',       pre:'Discover ',   highlight:'Perfect Hotels',    post:' Near You',    desc:'The best hotels, guesthouses and homestays in Chhatrapati Sambhajinagar.' },
        cars:        { img: 'images/car-rental-hero-section%20(3).webp', label:'Rent a Car',            pre:'Drive in ',   highlight:'Premium Style',     post:' Today',       desc:'Luxury sedans, SUVs and hatchbacks with professional chauffeurs at your service.' },
        bikes:       { img: 'images/bike%20rentals-hero-section%20(6).webp', label:'Rent a Bike',           pre:'Ride ',       highlight:'The Open Road',     post:' Your Way',    desc:'Scooters, cruisers and sports bikes — ride the city your way, anytime.' },
        attractions: { img: 'images/attractions-hero-section%20(7).webp', label:'Discover Attractions',  pre:'Explore ',    highlight:'Ancient Marvels',   post:' Around You',  desc:'Ellora, Ajanta, Bibi Ka Maqbara and more — heritage wonders await you.' },
        dine:        { img: 'images/dine-hero-section%20(1).webp', label:'Taste the City',        pre:'Savour ',     highlight:'Local Flavours',    post:' Tonight',     desc:'From Mughlai feasts to street food — find the best restaurants near you.' },
        buses:       { img: 'images/bus-hero-section%20(2).webp', label:'Book a Bus',            pre:'Travel ',     highlight:'Your Way',          post:' Comfortably', desc:'AC sleepers, Volvo coaches and MSRTC buses to and from Sambhajinagar.' }
    };
    var d = heroData[tab];
    
    // Update background image
    if (d.img) document.getElementById('hero-bg').style.backgroundImage = "url('" + d.img + "')";

    ['hero-label','hero-pre','hero-highlight','hero-post','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='0'; });
    setTimeout(function(){
        document.getElementById('hero-label').textContent = d.label;
        document.getElementById('hero-pre').textContent = d.pre;
        document.getElementById('hero-highlight').textContent = d.highlight;
        document.getElementById('hero-post').textContent = d.post;
        document.getElementById('hero-desc').textContent = d.desc;
        ['hero-label','hero-pre','hero-highlight','hero-post','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='1'; });
    }, 250);
    
    // Stop auto-rotation when user manually selects a tab
    if (!fromAuto) {
        if (window.heroInterval) {
            clearInterval(window.heroInterval);
            window.heroInterval = null;
        }
    }
}

// Auto-rotation functionality
var tabsList = ['stays', 'cars', 'bikes', 'attractions', 'dine', 'buses'];
var currentTabIndex = 0;
function autoSwitch() {
    currentTabIndex = (currentTabIndex + 1) % tabsList.length;
    switchTab(tabsList[currentTabIndex], true);
}
window.heroInterval = setInterval(autoSwitch, 3000);

// Initialize with the first background (cars)
document.getElementById('hero-bg').style.backgroundImage = "url('images/car-rental-hero-section%20(3).webp')";

var searchUrls = { stays:'<?php echo BASE_PATH; ?>/listing/stays', cars:'<?php echo BASE_PATH; ?>/listing/cars', bikes:'<?php echo BASE_PATH; ?>/listing/bikes', attractions:'<?php echo BASE_PATH; ?>/listing/attractions', dine:'<?php echo BASE_PATH; ?>/listing/restaurants', buses:'<?php echo BASE_PATH; ?>/listing/buses' };
function doSearch(tab) {
    window.location.href = searchUrls[tab];
}
document.addEventListener('DOMContentLoaded', function() {    var today = new Date(), tomorrow = new Date(today);
    tomorrow.setDate(today.getDate()+1);
    var opts = { dateFormat:'d M Y', minDate:'today', disableMobile:false };
    var fpCI = flatpickr('#stays-checkin', Object.assign({},opts,{ defaultDate:today, onChange:function(s){ if(s[0]){ var n=new Date(s[0]); n.setDate(n.getDate()+1); fpCO.set('minDate',n); if(!fpCO.selectedDates[0]||fpCO.selectedDates[0]<=s[0]) fpCO.setDate(n); } } }));
    var fpCO = flatpickr('#stays-checkout', Object.assign({},opts,{ defaultDate:tomorrow }));
    flatpickr('#cars-date', Object.assign({},opts,{ defaultDate:today }));
    var fpBD = flatpickr('#bikes-date', Object.assign({},opts,{ defaultDate:today, onChange:function(s){ if(s[0]){ var n=new Date(s[0]); n.setDate(n.getDate()+1); fpBR.set('minDate',n); if(!fpBR.selectedDates[0]||fpBR.selectedDates[0]<=s[0]) fpBR.setDate(n); } } }));
    var fpBR = flatpickr('#bikes-return', Object.assign({},opts,{ defaultDate:tomorrow }));
    flatpickr('#attractions-date', Object.assign({},opts,{ defaultDate:today }));
    flatpickr('#dine-date', Object.assign({},opts,{ defaultDate:today }));
    flatpickr('#buses-date', Object.assign({},opts,{ defaultDate:today }));
});

// Banner Text Auto-Rotation
document.addEventListener('DOMContentLoaded', function() {
    var bannerData = [
        {
            tracking: "Explore Your Way",
            heading: "Experience the essence of <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>Maharashtra.</span>",
            quote: "\"Chhatrapati Sambhajinagar is more than a city; it's a living museum of ancient artistry.\""
        },
        {
            tracking: "Uncover Hidden Gems",
            heading: "Journey through <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>Time.</span>",
            quote: "\"From majestic forts to silent caves, every stone here tells a forgotten tale.\""
        },
        {
            tracking: "Adventure Awaits",
            heading: "Feel the pulse of <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>The Deccan.</span>",
            quote: "\"Taste the vibrant culture and escape into the ultimate local adventure.\""
        },
        {
            tracking: "Your Premium Guide",
            heading: "Travel seamlessly <span class='bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1'>Everywhere.</span>",
            quote: "\"Premium stays, fast rides, and flawless itineraries, all curated just for you.\""
        }
    ];
    var bannerIndex = 0;
    var container = document.getElementById('banner-text-container');
    
    if (container) {
        setInterval(function() {
            bannerIndex = (bannerIndex + 1) % bannerData.length;
            
            // Fade out
            container.style.opacity = '0';
            container.style.transform = 'translateY(10px)';
            
            setTimeout(function() {
                var d = bannerData[bannerIndex];
                document.getElementById('banner-tracking').innerHTML = d.tracking;
                document.getElementById('banner-heading').innerHTML = d.heading;
                document.getElementById('banner-quote').innerHTML = d.quote;
                
                // Fade in
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 500); // Matches transition duration
        }, 3000); // changes every 3 seconds
    }
});
</script>


<!-- Banner Section -->
<section class="py-16 bg-white overflow-hidden">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="flex flex-col lg:flex-row items-center gap-12 mb-16">
            <div class="flex-1 transition-all duration-500" id="banner-text-container" style="opacity: 1; transform: translateY(0);" data-reveal data-reveal="left">
                <p id="banner-tracking" class="mobile-hide text-primary font-bold text-xs uppercase tracking-widest mb-2">Explore Your Way</p>
                <h2 id="banner-heading" class="font-serif text-3xl md:text-5xl text-slate-900 leading-tight mb-6">Experience the essence of <span class="bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1">Maharashtra.</span></h2>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                        <p id="banner-quote" class="text-slate-600 text-sm italic">"Chhatrapati Sambhajinagar is more than a city; it's a living museum of ancient artistry."</p>
                    </div>
                </div>
            </div>
            
            <!-- Desktop: Image Cards -->
            <div class="flex-1 hidden lg:grid grid-cols-2 gap-4">
                <!-- Car Rentals Card -->
                <div data-reveal data-reveal="right" class="group relative overflow-hidden rounded-2xl h-40 sm:h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                    <img alt="Car Rentals" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&q=80"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-5">
                        <h4 class="text-white text-sm sm:text-lg font-bold mb-1">Car Rentals</h4>
                        <a href="<?php echo BASE_PATH; ?>/listing/cars" class="text-white/80 text-xs hover:text-white transition-colors flex items-center gap-1">Browse Cars <span class="material-symbols-outlined text-[10px]">arrow_forward</span></a>
                    </div>
                </div>
                <!-- Bike Rentals Card -->
                <div data-reveal data-reveal="right" class="group relative overflow-hidden rounded-2xl h-40 sm:h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                    <img alt="Bike Rentals" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=800&q=80"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-5">
                        <h4 class="text-white text-sm sm:text-lg font-bold mb-1">Bike Rentals</h4>
                        <a href="<?php echo BASE_PATH; ?>/listing/bikes" class="text-white/80 text-xs hover:text-white transition-colors flex items-center gap-1">Browse Bikes <span class="material-symbols-outlined text-[10px]">arrow_forward</span></a>
                    </div>
                </div>
            </div>
            
            <!-- Service Cards removed on mobile/tablet to reduce redundancy -->
        </div>

        <!-- Highlight Cards (Inside Banner Section) - Hidden on Mobile -->
        <div class="hidden md:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php 
            $highlights = [
                ['icon'=>'hotel', 'label'=>'500+ Hotels', 'sub' => 'Premium Stays'],
                ['icon'=>'directions_car', 'label'=>'50+ Rentals', 'sub' => 'Cars & Bikes'],
                ['icon'=>'restaurant', 'label'=>'200+ Eateries', 'sub' => 'Best Dining'],
                ['icon'=>'map', 'label'=>'50+ Wonders', 'sub' => 'Heritage Sites'],
                ['icon'=>'groups', 'label'=>'10K+ Travelers', 'sub' => 'Happy Guests']
            ];
            foreach($highlights as $h): ?>
            <div data-reveal class="bg-white p-6 rounded-2xl shadow-xl shadow-black/5 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 group flex flex-col items-center text-center">
                <div class="size-11 rounded-xl bg-orange-50 flex items-center justify-center mb-4 group-hover:bg-primary transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-primary group-hover:text-white transition-colors text-2xl"><?php echo $h['icon']; ?></span>
                </div>
                <h4 class="text-slate-900 font-bold text-sm leading-tight italic"><?php echo $h['label']; ?></h4>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1.5"><?php echo $h['sub']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($hp_settings['city_intro'])): ?>
<!-- City Intro -->
<div class="bg-white py-10">
    <div class="max-w-[1140px] mx-auto px-5">
        <p class="text-slate-600 text-base md:text-lg leading-relaxed text-center max-w-3xl mx-auto">
            <?php echo nl2br(htmlspecialchars($hp_settings['city_intro'])); ?>
        </p>
    </div>
</div>
<?php endif; ?>


<?php
// Base counts — if more items than this are shown, switch to horizontal scroll
$_sec_base_counts = [
    'attractions' => 4,
    'bikes'       => 4,
    'restaurants' => 6,
    'buses'       => 2,
    'blogs'       => 3,
    'cars'        => 4,
    'stays'       => 4,
];

// ── Render sections in saved order ───────────────────────────────────────────
$_sec_bg_toggle = false;
foreach ($hp_settings['section_order'] as $_sec_key):
    if (empty($hp_settings['show_' . $_sec_key])) continue;

    // --- Inject Personalized Trip Assistant above Restaurants ---
    if ($_sec_key === 'restaurants'):
        $_ta_bg = $_sec_bg_toggle ? 'bg-white' : 'bg-slate-50';
        // We flip it so the sections alternate cleanly
        $_sec_bg_toggle = !$_sec_bg_toggle;
?>
<section class="py-24 <?php echo $_ta_bg; ?> relative overflow-hidden group">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/60 z-10"></div>
        <div class="w-full h-full opacity-40 bg-cover bg-center scale-110 group-hover:scale-100 transition-transform duration-1000" style="background-image:url('https://images.unsplash.com/photo-1506012787146-f92b2d7d6d96?w=1600&q=80')"></div>
    </div>
    <div class="max-w-[1140px] mx-auto px-5 relative z-10" data-reveal>
        <div class="bg-gradient-to-br from-[#1a1c29] to-[#0f111a] rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center gap-8 border border-white/10">
            <!-- decorative background glows -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/20 blur-[80px] rounded-full pointer-events-none"></div>
            
            <div class="flex-1 text-center md:text-left z-10">
                <div class="inline-flex items-center gap-1.5 bg-white/10 border border-white/20 backdrop-blur-md px-3 py-1 rounded-full text-white/90 text-xs font-bold uppercase tracking-widest mb-4">
                    <span class="material-symbols-outlined text-[14px] text-primary">auto_awesome</span> Plan your trip now
                </div>
                <h3 class="font-serif text-3xl md:text-5xl text-white font-black leading-tight mb-4 text-balance">
                    Not sure where to go? <span class="bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1 pr-2">Let us plan it.</span>
                </h3>
                <p class="text-white/70 text-sm md:text-base max-w-lg mx-auto md:mx-0 mb-6 group-hover:text-white/90 transition-colors">
                    Talk to our local trip experts. We'll craft a customized, fully personalized itinerary for your entire trip—including cars, hotels, and ancient site guides—at no extra cost.
                </p>
                <a href="<?php echo BASE_PATH; ?>/suggestor" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-orange-500 text-white font-black rounded-xl text-sm shadow-[0_8px_30px_rgb(236,91,19,0.3)] hover:shadow-[0_8px_30px_rgb(236,91,19,0.5)] hover:-translate-y-1 transition-all">
                    Get Free Trip Plan <span class="material-symbols-outlined text-[18px]">support_agent</span>
                </a>
            </div>
            
            <!-- Graphic layout (Dynamic Modern Stacked Image Animation) -->
            <div class="flex flex-shrink-0 relative w-64 h-72 md:w-72 md:h-80 lg:w-80 lg:h-96 z-10 items-center justify-center perspective-[1000px]">
                <style>
                    .stack-card {
                        position: absolute;
                        width: 100%;
                        height: 100%;
                        border-radius: 1.5rem;
                        background-size: cover;
                        background-position: center;
                        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
                        border: 3px solid rgba(255,255,255,0.15);
                        animation: stackCycle 12s infinite cubic-bezier(0.22, 1, 0.36, 1);
                    }
                    <?php
                    $attr_imgs = [];
                    foreach($hp_attractions as $a) {
                        if(!empty($a['image'])) {
                            $img = (strpos($a['image'], 'http') === 0) ? $a['image'] : BASE_PATH . '/' . ltrim($a['image'], '/');
                            $attr_imgs[] = htmlspecialchars($img);
                        }
                    }
                    if (count($attr_imgs) < 4) {
                        $attr_imgs = array_pad($attr_imgs, 4, 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80');
                    }
                    shuffle($attr_imgs);
                    ?>
                    /* 4 cards, 3 seconds each = 12s total duration */
                    .stack-card:nth-child(1) { animation-delay: 0s; background-image: url('<?php echo $attr_imgs[0]; ?>'); z-index: 4; }
                    .stack-card:nth-child(2) { animation-delay: 3s; background-image: url('<?php echo $attr_imgs[1]; ?>'); z-index: 3; }
                    .stack-card:nth-child(3) { animation-delay: 6s; background-image: url('<?php echo $attr_imgs[2]; ?>'); z-index: 2; }
                    .stack-card:nth-child(4) { animation-delay: 9s; background-image: url('<?php echo $attr_imgs[3]; ?>'); z-index: 1; }

                    @keyframes stackCycle {
                        0%, 22% { opacity: 1; transform: translateY(0) scale(1) rotate(0deg); z-index: 4; }
                        25% { opacity: 0; transform: translateY(-40px) scale(1.05) rotate(3deg); z-index: 4; }
                        26% { opacity: 0; transform: translateY(40px) scale(0.85); z-index: 1; }
                        28%, 47% { opacity: 1; transform: translateY(20px) scale(0.9) rotate(-2deg); z-index: 1; }
                        50%, 72% { opacity: 1; transform: translateY(10px) scale(0.95) rotate(2deg); z-index: 2; }
                        75%, 97% { opacity: 1; transform: translateY(5px) scale(0.98) rotate(-1deg); z-index: 3; }
                        100% { opacity: 1; transform: translateY(0) scale(1) rotate(0deg); z-index: 4; }
                    }
                </style>
                <div class="relative w-full h-full">
                    <div class="stack-card"></div>
                    <div class="stack-card"></div>
                    <div class="stack-card"></div>
                    <div class="stack-card"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php
    $_layout = $hp_settings['layout_' . $_sec_key];
    // If more items than base count → force horizontal scroll
    $_sec_items_map = ['attractions'=>$hp_attractions,'bikes'=>$hp_bikes,'restaurants'=>$hp_restaurants,'buses'=>$hp_buses,'blogs'=>$hp_blogs,'cars'=>$hp_cars,'stays'=>$hp_stays];
    $_sec_item_count = count($_sec_items_map[$_sec_key] ?? []);
    $_base = $_sec_base_counts[$_sec_key] ?? 4;
    if ($_sec_item_count > $_base) {
        $_layout = 'scroll';
    }
    $_grid   = hp_grid_class($_layout);
    $_bg     = $_sec_bg_toggle ? 'bg-white' : 'bg-slate-50';
    $_sec_bg_toggle = !$_sec_bg_toggle;
?>
<section class="py-12 <?php echo $_bg; ?>">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="flex items-end justify-between mb-6" data-reveal>
            <?php if ($_sec_key === 'blogs'): ?>
            <div>
                <p class="mobile-hide text-primary font-bold text-xs uppercase tracking-widest mb-1">Our Travel Journals</p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($hp_settings['title_blogs']); ?></h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/blogs" class="text-sm font-bold text-primary hover:underline">Read more &rarr;</a>
            <?php else: ?>
            <div>
                <p class="mobile-hide text-primary font-bold text-xs uppercase tracking-widest mb-1"><?php
                    $sec_subtitles = ['attractions'=>'Heritage & Culture','bikes'=>'Two-Wheeler Rentals','restaurants'=>'Food & Dining','buses'=>'Travel Your Way','cars'=>'Self-Drive & Taxis','stays'=>'Hotels & Resorts'];
                    echo $sec_subtitles[$_sec_key] ?? 'Explore';
                ?></p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($hp_settings['title_' . $_sec_key]); ?></h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/listing/<?php echo $_sec_key; ?>" class="text-sm font-bold text-primary hover:underline">See all &rarr;</a>
            <?php endif; ?>
        </div>
        <?php if ($_sec_key !== 'blogs'): ?>
        <!-- Mobile scroll hint -->
        <p class="text-slate-400 text-xs flex items-center gap-1 mt-1 mb-5 md:hidden">
          <span class="material-symbols-outlined text-sm">swipe</span> Swipe to explore
        </p>
        <?php endif; ?>
        <?php
        // ── Visible-cards-per-section config ─────────────────────────────────
        $_vis = ['attractions'=>4,'bikes'=>4,'restaurants'=>4,'buses'=>2,'blogs'=>3];
        $_vis_count = $_vis[$_sec_key] ?? 4;
        // Mobile: fixed 80vw so ~1.1 cards visible. Desktop: percentage of container.
        $_card_w = 'var(--card-w-' . $_sec_key . ')';

        if ($_sec_key === 'attractions'):
            $render_fn = function($a) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('attractions', $a['id'], $a['name']) . '.html';
                $imgSrc = $a['image'] ?? '';
                $img = (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80';
                $name=htmlspecialchars($a['name']);
                $tag=htmlspecialchars($a['type']??'Attraction');
                $price=$a['entry_fee']>0 ? '&#8377;'.number_format($a['entry_fee']) : 'Free';
                $rating=number_format((float)($a['rating']??0),1);
                return '<a href="'.$slug.'" class="group relative overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest relative z-20">'.$tag.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1 relative z-20">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between relative z-20">'
                    .'<p class="font-black text-slate-900 text-sm">'.$price.' <span class="text-xs text-slate-400 font-normal">entry</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Details</span>'
                    .'</div></div></a>';
            };
            $items = $hp_attractions;
        elseif ($_sec_key === 'bikes'):
            $render_fn = function($b) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('bikes', $b['id'], $b['name']) . '.html';
                $imgSrc = $b['image'] ?? '';
                $img = (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80';
                $name=htmlspecialchars($b['name']);
                $type=htmlspecialchars($b['type']); $price=number_format($b['price_per_day']);
                $rating=number_format((float)($b['rating']??0),1);
                return '<a href="'.$slug.'" class="group overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest">'.$type.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">/day</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_bikes;
        elseif ($_sec_key === 'restaurants'):
            $render_fn = function($r) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('restaurants', $r['id'], $r['name']) . '.html';
                $imgSrc = $r['image'] ?? '';
                $img = (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80';
                $name=htmlspecialchars($r['name']);
                $cuisine=htmlspecialchars($r['cuisine']??$r['type']); $price=number_format($r['price_per_person']??0);
                $rating=number_format((float)($r['rating']??0),1);
                return '<a href="'.$slug.'" class="group relative overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest relative z-20">'.$cuisine.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-2 line-clamp-1 relative z-20">'.$name.'</h5>'
                    .'<div class="flex items-center gap-1 text-slate-500 text-xs mb-3 relative z-20"><span style="font-family:Material Symbols Outlined;font-size:14px">location_on</span><span class="line-clamp-1">'.htmlspecialchars($r['location']??'').'</span></div>'
                    .'<div class="space-y-1.5 text-xs text-slate-600 mb-3 relative z-20">'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">verified</span><span class="font-semibold">Verified</span></div>'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">cancel</span><span>Free cancellation</span></div>'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">info</span><span>No hidden charges</span></div>'
                    .'</div>'
                    .'<div class="flex items-center justify-between gap-3 relative z-20 border-t border-slate-100 pt-3">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">for two</span></p>'
                    .'<span class="bg-primary text-white px-4 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all whitespace-nowrap">Check Details</span>'
                    .'</div></div></a>';
            };
            $items = $hp_restaurants;
        elseif ($_sec_key === 'cars'):
            $render_fn = function($c) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('cars', $c['id'], $c['name']) . '.html';
                $imgSrc = $c['image'] ?? '';
                $img = (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&q=80';
                $name=htmlspecialchars($c['name']);
                $type=htmlspecialchars($c['type']??'Sedan'); $price=number_format($c['price_per_day']??0);
                $rating=number_format((float)($c['rating']??0),1);
                return '<a href="'.$slug.'" class="group overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest">'.$type.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h5>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">/day</span></p>'
                    .'<span class="bg-primary text-white px-3 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_cars;
        elseif ($_sec_key === 'stays'):
            $render_fn = function($s) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('stays', $s['id'], $s['name']) . '.html';
                $imgSrc = $s['image'] ?? '';
                $img = (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80';
                $name=htmlspecialchars($s['name']);
                $type=htmlspecialchars($s['type']??'Hotel'); $price=number_format($s['price_per_night']??0);
                $rating=number_format((float)($s['rating']??0),1);
                return '<a href="'.$slug.'" class="group relative overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative"><img alt="'.$name.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-primary text-[10px] font-bold uppercase tracking-widest relative z-20">'.$type.'</span>'
                    .'<h5 class="font-serif text-base text-slate-900 mt-1 mb-2 line-clamp-1 relative z-20">'.$name.'</h5>'
                    .'<div class="flex items-center gap-1 text-slate-500 text-xs mb-3 relative z-20"><span style="font-family:Material Symbols Outlined;font-size:14px">location_on</span><span class="line-clamp-1">'.htmlspecialchars($s['location']??'').'</span></div>'
                    .'<div class="flex items-center justify-between gap-3 relative z-20 border-t border-slate-100 pt-3">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-400 font-normal">/night</span></p>'
                    .'<span class="bg-primary text-white px-4 py-1.5 rounded-full font-bold text-xs group-hover:bg-orange-600 transition-all whitespace-nowrap">Check Details</span>'
                    .'</div></div></a>';
            };
            $items = $hp_stays;
        elseif ($_sec_key === 'buses'):
            $render_fn = function($bus) {
                $op=htmlspecialchars($bus['operator']); $bt=htmlspecialchars($bus['bus_type']);
                $route=htmlspecialchars($bus['from_location']).' → '.htmlspecialchars($bus['to_location']);
                $price=number_format($bus['price']);
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('buses', $bus['id'], $bus['operator']) . '.html';
                return '<a href="'.$slug.'" class="glass-dark p-5 rounded-2xl flex items-center justify-between gap-4 card-hover flex-shrink-0 group relative overflow-hidden" style="width:VAR_W">'
                    .'<div class="flex items-center gap-4 min-w-0 relative z-20">'
                    .'<div class="w-12 h-12 bg-primary/15 rounded-xl flex items-center justify-center shrink-0">'
                    .'<span class="material-symbols-outlined text-primary text-2xl">directions_bus</span></div>'
                    .'<div class="min-w-0"><p class="text-white font-bold text-sm truncate">'.$op.' <span class="text-[10px] font-normal text-white/50 bg-white/10 px-2 py-0.5 rounded ml-1">'.$bt.'</span></p>'
                    .'<p class="text-white/50 text-xs mt-0.5 truncate">'.$route.'</p></div></div>'
                    .'<div class="flex items-center gap-3 shrink-0 relative z-20">'
                    .'<p class="text-primary font-black text-lg">&#8377;'.$price.'</p>'
                    .'<span class="bg-primary text-white px-4 py-2 rounded-xl font-bold text-xs group-hover:bg-orange-600 transition-all">Check Details</span>'
                    .'</div></a>';
            };
            $items = $hp_buses;
        else:
            $render_fn = function($blog) {
                $rt=max(3,intval(strlen(strip_tags($blog['content']??''))/1000));
                $t=strtolower(trim($blog['title']));
                $t=preg_replace('/[^a-z0-9\s-]/','',$t);
                $t=preg_replace('/[\s-]+/','-',$t);
                $slug = BASE_PATH . '/blogs/'.$blog['id'].'-'.substr(trim($t,'-'),0,60) . '.html';
                $imgSrc = $blog['image'] ?? '';
                $img = (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/');
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=600&q=80';
                $title=htmlspecialchars($blog['title']);
                $cat=htmlspecialchars($blog['category']??'Travel');
                return '<a href="'.$slug.'" class="group cursor-pointer flex-shrink-0 hover:-translate-y-1 transition-all duration-300" style="width:VAR_W">'
                    .'<div class="rounded-2xl overflow-hidden aspect-[16/10] mb-3 shadow-md relative">'
                    .'<img alt="'.$title.'" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="'.$img.'"/>'
                    .'<div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">'
                    .'<span class="bg-white text-black px-4 py-1.5 rounded-full font-bold text-xs">READ POST</span></div></div>'
                    .'<div class="flex items-center gap-3 mb-2">'
                    .'<span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase">'.$cat.'</span>'
                    .'<span class="text-slate-400 text-xs flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span>'.$rt.' min</span>'
                    .'</div><h4 class="font-serif text-base text-slate-900 group-hover:text-primary transition-colors line-clamp-2">'.$title.'</h4></a>';
            };
            $items = $hp_blogs;
        endif;
        ?>
        <div class="relative overflow-x-auto overflow-y-hidden hide-scrollbar snap-x snap-mandatory" id="carousel-wrap-<?php echo $_sec_key; ?>" style="scroll-behavior: auto !important;">
            <div id="carousel-track-<?php echo $_sec_key; ?>" class="flex gap-6 pb-4">
                <?php
                if (!empty($items)) {
                    // Render 3× for seamless infinite loop
                    for ($__r = 0; $__r < 3; $__r++) {
                        foreach ($items as $__item) {
                            echo str_replace('VAR_W', $_card_w, $render_fn($__item));
                        }
                    }
                } else {
                    echo '<p class="text-slate-400 py-8">No items yet.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</section>
<?php endforeach; ?>
</main>

<?php require 'footer.php'; ?>

<!-- Infinite carousel with seamless loop -->
<script>
(function(){
    var sections = ['stays','cars','bikes','attractions','restaurants','buses','blogs'];
    sections.forEach(function(s) {
        var el = document.getElementById('carousel-wrap-' + s);
        var track = document.getElementById('carousel-track-' + s);
        if(!el || !track) return;
        
        var isPaused = false;
        var isDown = false;
        var speed = 0.5;
        var currentX = 0;
        
        function getSetWidth() { return track.scrollWidth / 3; }
        var oneSetWidth = getSetWidth();
        
        currentX = oneSetWidth;
        el.scrollLeft = currentX;
        
        function loop() {
            if (!isPaused && !isDown) {
                currentX += speed;
                if (currentX >= oneSetWidth * 2) {
                    currentX = oneSetWidth;
                }
                el.scrollLeft = currentX;
            }
            requestAnimationFrame(loop);
        }
        
        // Desktop Drag
        var startX, scrollL;
        el.addEventListener('mousedown', function(e) {
            isDown = true;
            startX = e.pageX - el.offsetLeft;
            scrollL = el.scrollLeft;
            el.style.cursor = 'grabbing';
            el.style.scrollSnapType = 'none';
        });
        window.addEventListener('mouseup', function() { 
            if(!isDown) return;
            isDown = false;
            currentX = el.scrollLeft; // Sync position
            el.style.cursor = 'grab';
            el.style.scrollSnapType = 'x mandatory';
        });
        el.addEventListener('mousemove', function(e) {
            if(!isDown) return;
            e.preventDefault();
            var x = e.pageX - el.offsetLeft;
            var walk = (x - startX) * 1.5;
            el.scrollLeft = scrollL - walk;
        });

        // Touch Hover/Pause
        el.addEventListener('mouseenter', function() { isPaused = true; });
        el.addEventListener('mouseleave', function() { isPaused = false; });
        el.addEventListener('touchstart', function() { isPaused = true; }, {passive: true});
        el.addEventListener('touchend', function() { 
            setTimeout(function(){ isPaused = false; currentX = el.scrollLeft; }, 1000); 
        }, {passive: true});

        // Initialize
        setTimeout(function() {
            oneSetWidth = getSetWidth();
            currentX = oneSetWidth;
            el.scrollLeft = currentX;
            loop();
        }, 500);
        
        window.addEventListener('resize', function() {
            oneSetWidth = getSetWidth();
        });
    });
})();
</script>





</body>
</html>
