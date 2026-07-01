<?php
// HOME PAGE - index.php
$cache_file = __DIR__ . '/cache/homepage.html';
if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600 && !isset($_GET['nocache'])) {
    require_once 'php/cache-headers.php';
    applyCacheHeaders('page');
    readfile($cache_file);
    exit;
}
require_once 'php/redirects.php'; // 301 handler â€” must be before any output
$page_title = "CSNExplore â€“ Hotels, Cars, Bikes & Attractions in Chhatrapati Sambhajinagar (Aurangabad)";
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
    'hero_subtext'      => 'Stays, cars, bikes, restaurants, attractions and buses â€” all in one place.',
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
    'hp_hero_headline' => 'Explore Chhatrapati Sambhajinagar Your Way: Aurangabad Scooter Rentals, Stays & Tours',
    'hp_hero_subtext' => 'Book premium stays, self-drive cars, bike rentals, top restaurants, local attractions, and outstation busesâ€”all in one place.',
    
    'hp_hiw_title' => 'How CSNExplore Works',
    'hp_hiw_subtext' => 'Your journey to discovering the wonders of Chhatrapati Sambhajinagar starts here, in three simple steps.',
    'hp_hiw_s1_icon' => 'search',
    'hp_hiw_s1_title' => 'Discover',
    'hp_hiw_s1_desc' => 'Search verified hotels, rentals, attractions, and experiences in minutes.',
    'hp_hiw_s2_icon' => 'shield',
    'hp_hiw_s2_title' => 'Book Securely',
    'hp_hiw_s2_desc' => 'Enjoy transparent pricing, instant confirmation, flexible cancellation, and secure payments.',
    'hp_hiw_s3_icon' => 'explore',
    'hp_hiw_s3_title' => 'Explore Freely',
    'hp_hiw_s3_desc' => 'Check in, collect your vehicle, and experience Chhatrapati Sambhajinagar with confidence.',
    
    'hp_wcu_title' => 'Why Choose CSNExplore',
    'hp_wcu_subtext' => 'We are a local, verified, and premium booking portal dedicated exclusively to Chhatrapati Sambhajinagar.',
    'hp_wcu_f1_icon' => 'verified_user',
    'hp_wcu_f1_title' => '100% Verified Listings',
    'hp_wcu_f1_desc' => 'Every hotel, vehicle, guide, and experience is carefully verified to ensure trusted quality, safety, and reliability.',
    'hp_wcu_f2_icon' => 'sell',
    'hp_wcu_f2_title' => 'Best Price Guarantee',
    'hp_wcu_f2_desc' => 'Book directly with local partners for transparent pricing, exclusive deals, and zero hidden charges.',
    'hp_wcu_f3_icon' => 'support_agent',
    'hp_wcu_f3_title' => '24/7 Local Support',
    'hp_wcu_f3_desc' => 'Receive fast assistance from our local support team before, during, and after your trip.',
    
    'hp_testi_title' => 'What Our Travelers Say',
    'hp_testi_r1_text' => 'CSNExplore made our trip to Ajanta and Ellora completely hassle-free. We rented a Swift for 2 days and the process was buttery smooth. The driver was polite and knew all the local food spots!',
    'hp_testi_r1_name' => 'Rahul Sharma',
    'hp_testi_r1_loc' => 'Travelled from Mumbai',
    'hp_testi_r2_text' => 'Booked a premium stay near the station. The rates on CSNExplore were genuinely cheaper than other major OTAs. Highly recommended for anyone visiting Chhatrapati Sambhajinagar.',
    'hp_testi_r2_name' => 'Ananya Desai',
    'hp_testi_r2_loc' => 'Travelled from Pune',
    'hp_testi_r3_text' => 'The bike rental service was a lifesaver! Rented a scooter to roam around the city. Transparent pricing and the vehicle was in great condition. Will definitely use again.',
    'hp_testi_r3_name' => 'Vikram Singh',
    'hp_testi_r3_loc' => 'Travelled from Delhi',
    
    'hp_itin_title' => 'Curated Itineraries',
    'hp_itin_subtext' => 'Expertly crafted travel plans designed to help you make the most of your time in Chhatrapati Sambhajinagar.',
    'hp_itin_i1_img' => 'https://images.unsplash.com/photo-1610017122171-ec331c1e55b6?w=800&q=80',
    'hp_itin_i1_dur' => '2 Days',
    'hp_itin_i1_title' => 'The Cave Explorer\'s Trail',
    'hp_itin_i1_desc' => 'Cover the magnificent Ajanta and Ellora caves in a packed two-day weekend itinerary.',
    'hp_itin_i1_locs' => 'Ajanta &middot; Ellora',
    'hp_itin_i2_img' => 'https://images.unsplash.com/photo-1658406560875-9c5c24e61b78?w=800&q=80',
    'hp_itin_i2_dur' => '1 Day',
    'hp_itin_i2_title' => 'Forts & Heritage Run',
    'hp_itin_i2_desc' => 'An adventurous day exploring Daulatabad Fort and the historic 52 gates of the city.',
    'hp_itin_i2_locs' => 'Daulatabad Fort &middot; City Gates',
    'hp_itin_i3_img' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80',
    'hp_itin_i3_dur' => 'Evening',
    'hp_itin_i3_title' => 'Mughlai Culinary Walk',
    'hp_itin_i3_desc' => 'Taste the authentic Naan Qalia and street food delights in the old city streets.',
    'hp_itin_i3_locs' => 'Buddi Lane &middot; Nirala Bazaar',
    
    'hp_news_title' => 'Stay ',
    'hp_news_subtext' => 'Subscribe to our newsletter for exclusive deals, hidden travel spots, and the latest updates on Chhatrapati Sambhajinagar tourism.',
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

// Helper: layout string â†’ Tailwind grid/flex class
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

// Fetch real data from DB â€” use picks if set, otherwise use saved counts
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

// Only fetch columns actually used by homepage cards (avoid SELECT *)
// This reduces data transferred from MySQL by ~70% on busy pages
$_card_cols = [
    'stays'       => 'id,name,type,location,price_per_night,rating,reviews,badge,image,slug,is_active,display_order',
    'cars'        => 'id,name,type,location,price_per_day,rating,reviews,badge,image,slug,fuel_type,seats,is_active,display_order',
    'bikes'       => 'id,name,type,location,price_per_day,rating,reviews,badge,image,slug,cc,is_active,display_order',
    'restaurants' => 'id,name,type,cuisine,location,price_per_person,rating,reviews,badge,image,slug,is_active,display_order',
    'attractions' => 'id,name,type,location,entry_fee,rating,reviews,badge,image,slug,is_active,display_order',
    'buses'       => 'id,operator,bus_type,from_location,to_location,departure_time,price,rating,badge,image,slug,is_active,display_order',
    'blogs'       => 'id,title,author,image,category,read_time,tags,meta_description,created_at,status',
];

$hp_attractions = hp_fetch_picks($db, 'attractions', $hp_settings['picks_attractions'], 'is_active=1',
    "SELECT {$_card_cols['attractions']} FROM attractions WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_attractions']);
$hp_bikes = hp_fetch_picks($db, 'bikes', $hp_settings['picks_bikes'], 'is_active=1',
    "SELECT {$_card_cols['bikes']} FROM bikes WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_bikes']);
$hp_restaurants = hp_fetch_picks($db, 'restaurants', $hp_settings['picks_restaurants'], 'is_active=1',
    "SELECT {$_card_cols['restaurants']} FROM restaurants WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_restaurants']);
$hp_buses = hp_fetch_picks($db, 'buses', $hp_settings['picks_buses'] ?? [], 'is_active=1',
    "SELECT {$_card_cols['buses']} FROM buses WHERE is_active=1 ORDER BY display_order ASC LIMIT " . (int)$hp_settings['count_buses']);
$hp_blogs = hp_fetch_picks($db, 'blogs', $hp_settings['picks_blogs'] ?? [], "status='published'",
    "SELECT {$_card_cols['blogs']} FROM blogs WHERE status='published' ORDER BY created_at DESC LIMIT " . (int)$hp_settings['count_blogs']);
$hp_cars = hp_fetch_picks($db, 'cars', $hp_settings['picks_cars'] ?? [], 'is_active=1',
    "SELECT {$_card_cols['cars']} FROM cars WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_cars']);
$hp_stays = hp_fetch_picks($db, 'stays', $hp_settings['picks_stays'] ?? [], 'is_active=1',
    "SELECT {$_card_cols['stays']} FROM stays WHERE is_active=1 ORDER BY display_order ASC, rating DESC LIMIT " . (int)$hp_settings['count_stays']);
?>
<?php
$page_meta = [
    'description' => 'CSNExplore â€“ your premium travel portal for Chhatrapati Sambhajinagar (Aurangabad). Book hotels, car & bike rentals, explore Ajanta & Ellora Caves attractions, restaurants and more.',
    'canonical'   => 'https://csnexplore.com/',
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/Logo-light-optimized.webp',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
    ],
];
$extra_head = '<link rel="preload" as="image" imagesrcset="' . BASE_PATH . '/images/hotel-hero-section-mobile.webp 768w, ' . BASE_PATH . '/images/hotel-hero-section-4.webp 1920w" imagesizes="100vw" fetchpriority="high">';
$extra_styles = "
        .hide-scrollbar::-webkit-scrollbar { display:none; }
        .hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .card-hover::before { box-shadow:0 0 30px rgba(236,91,19,0.15); }
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
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.03) 100%);
            backdrop-filter: blur(40px) saturate(200%); -webkit-backdrop-filter: blur(40px) saturate(200%); 
            border: 1px solid rgba(255,255,255,0.15); 
            border-top: 1px solid rgba(255,255,255,0.3);
            border-left: 1px solid rgba(255,255,255,0.2);
            border-radius: 36px; padding: 32px 36px;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.8), 0 0 50px rgba(236,91,19,0.15), inset 0 0 0 1px rgba(255,255,255,0.1);
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
            transition: background 0.5s cubic-bezier(0.22, 1, 0.36, 1); 
            border: none; background: transparent; white-space:nowrap;
        }
        .tab-btn:hover { color:#fff; background:rgba(255,255,255,0.08); }
        .tab-btn.active { 
            color:#fff; background: #ec5b13;
            box-shadow: 0 4px 15px rgba(236,91,19,0.4);
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
            background: transparent; border:none; border-right: 1px solid rgba(255,255,255,0.1); 
            border-radius: 12px; flex:1; min-width:0; height:64px; 
            transition:background 0.3s; position: relative;
        }
        .search-field:hover, .date-field:hover { background: rgba(255,255,255,0.05); }
        .search-field:nth-last-child(2), .date-field:nth-last-child(2) { border-right: none; }
        
        .search-field .material-symbols-outlined, .date-field .material-symbols-outlined { 
            position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
            color: #ec5b13; font-size:24px; pointer-events: none;
        }
        .search-field input, .date-field input { 
            background:transparent; border:none; outline:none; color:#fff; font-size:16px; font-weight:600; 
            width:100%; height:100%; padding: 0 20px 0 56px; margin:0; box-shadow:none; -webkit-appearance:none; text-overflow: ellipsis; 
            white-space: nowrap; overflow: hidden;
        }
        .search-field input::placeholder, .date-field input::placeholder { color:rgba(255,255,255,0.5); font-weight:400; }
        
        .search-btn { 
            background: #ec5b13; color: #fff; font-weight:800; font-size:16px; 
            padding:0 32px; border-radius:14px; border:none; cursor:pointer; 
            display:flex; align-items:center; justify-content:center; gap:8px; transition: all 0.3s ease; white-space:nowrap; flex-shrink:0; height:64px; position:relative;
            box-shadow: 0 4px 15px rgba(236,91,19,0.3);
        }
        .search-btn:hover { background: #ea580c; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(236,91,19,0.5); }
        .search-btn .material-symbols-outlined { font-size:22px; color: #fff; transition: transform 0.3s ease; position: static; transform: none; }
        .search-btn span:not(.material-symbols-outlined) { position: relative; transition: color 0.3s ease; }
        .search-btn:hover .material-symbols-outlined { transform: scale(1.1); }
        
        @media(max-width:768px){
          .search-box { padding:20px 16px; border-radius:24px; }
          .search-row { 
            display: flex; flex-direction: column; 
            background: transparent; border: none; padding: 0; gap: 12px;
          }
          .search-field, .date-field { 
            display: block !important; width: 100% !important; min-height: 70px !important; flex: none !important;
            border-radius: 20px; border: 1px solid rgba(255,255,255,0.15);
            background: rgba(0,0,0,0.3);
          }
          .search-field .material-symbols-outlined, .date-field .material-symbols-outlined { left: 24px; }
          .search-field input, .date-field input { 
            font-size: 16px; font-weight: 500; height: 100%; width: 100%; padding: 0 24px 0 64px;
          }
          .search-btn { 
            width: 100%; min-height: 64px; padding: 0 24px; border-radius: 20px; 
            margin-top: 8px; font-size: 17px; font-weight: 800; justify-content: center;
            background: #c2410c !important; color: #fff !important;
            box-shadow: 0 10px 25px rgba(236,91,19,0.3) !important;
          }
          .search-btn .material-symbols-outlined { color: #fff !important; }
          
          #search-tabs-scroll { 
            display: flex !important; flex-wrap: wrap !important;
            gap: 10px !important; padding: 0 !important; margin-bottom: 32px !important; width: 100% !important;
            background: transparent !important; border: none !important; border-radius: 0 !important; box-shadow: none !important;
            justify-content: center !important;
          }

          .tab-btn { 
            display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: center !important;
            padding: 12px 16px !important; font-size: 14px !important; min-height: 54px !important; flex: 1 1 calc(33.333% - 10px) !important;
            min-width: 90px !important;
            border-radius: 16px !important; background: rgba(0,0,0,0.4) !important; border: 1px solid rgba(255,255,255,0.12) !important;
          }
          .tab-btn .material-symbols-outlined { display: none !important; } /* Hide icons on mobile */
          .tab-btn span:not(.material-symbols-outlined) { font-weight: 700 !important; }
          .tab-btn.active {
            background: #ec5b13 !important; border-color: transparent !important;
            filter: drop-shadow(0 6px 12px rgba(236,91,19,0.4)) !important;
          }
          
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
        @keyframes heroZoom {
            0% { transform: scale3d(1, 1, 1); }
            100% { transform: scale3d(1.15, 1.15, 1); }
        }
        #hero-bg-1, #hero-bg-2 { 
            will-change: opacity, transform; 
            background-color: #0a0705;
            animation: heroZoom 4s ease-in-out infinite alternate !important;
        }
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
            transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1); 
            position: relative;
            will-change: transform;
        }
        .card-hover::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(236,91,19,0.1), rgba(255,140,66,0.1));
            opacity: 0;
            transition: opacity 0.4s ease;
            box-shadow: 0 0 30px rgba(236,91,19,0.15);
            pointer-events: none;
        }
        .card-hover:hover::before { opacity: 1; }
        .card-hover:hover { 
            box-shadow: 0 25px 50px -12px rgba(236,91,19,0.3), 0 0 0 1px rgba(236,91,19,0.15); 
            transform: translateY(-6px) scale(1.02);
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
<section class="homepage-hero relative min-h-[100svh] md:min-h-[85vh] flex flex-col items-center justify-start md:justify-center overflow-hidden pt-32 md:pt-24 pb-8 md:pb-12 w-full bg-[#0a0705]">
    <div class="absolute inset-0 z-0 bg-[#0a0705]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(0,0,0,0)_0%,rgba(0,0,0,0.8)_100%)] z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-transparent to-[#0a0705] z-10"></div>
        <!-- Dual image setup for smooth crossfade -->
        <img id="hero-bg-1" 
             src="<?php echo BASE_PATH; ?>/images/hotel-hero-section-4.webp" 
             srcset="<?php echo BASE_PATH; ?>/images/hotel-hero-section-mobile.webp 768w, <?php echo BASE_PATH; ?>/images/hotel-hero-section-4.webp 1920w"
             sizes="(max-width: 768px) 100vw, 1920px"
             width="1920" height="1080"
             fetchpriority="high"
             loading="eager"
             decoding="sync"
             class="w-full h-full object-cover bg-[#0a0705] absolute inset-0 transition-opacity duration-500"
             style="opacity: 1;"
             alt="CSNExplore hero background - Chhatrapati Sambhajinagar">
        <img id="hero-bg-2" 
             src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
             class="w-full h-full object-cover bg-transparent absolute inset-0 transition-opacity duration-500"
             style="opacity: 0;"
             alt="">
    </div>
    <div class="relative z-20 text-center px-4 w-full max-w-[1140px] mx-auto pt-24 md:pt-32 pb-8 md:pb-12">
        <div class="h-[200px] sm:h-[220px] md:h-[240px] lg:h-[260px] flex flex-col justify-end mb-8 md:mb-10 lg:mb-12">
            <p id="hero-label" class="mobile-hide text-orange-500 font-bold text-[10px] md:text-xs uppercase tracking-widest mb-2 md:mb-3">Chhatrapati Sambhajinagar</p>
            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl text-white mb-4 md:mb-6 leading-[1.1] font-black px-4" style="text-shadow: 0 4px 30px rgba(0,0,0,0.6);">
                <?php echo htmlspecialchars($hp_settings['hp_hero_headline'] ?? 'Explore Chhatrapati Sambhajinagar Your Way: Aurangabad Scooter Rentals, Stays & Tours'); ?>
            </h1>
            <p id="hero-desc" class="text-white/70 text-sm sm:text-base md:text-lg max-w-2xl mx-auto px-6 leading-relaxed mb-0"><?php echo htmlspecialchars($hp_settings['hp_hero_subtext'] ?? 'Book premium stays, self-drive cars, bike rentals, top restaurants, local attractions, and outstation busesâ€”all in one place.'); ?></p>
        </div>

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

            <!-- STAYS panel (Default) -->
            <div id="panel-stays" class="search-panel active">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="stays-location" type="text" placeholder="Where are you going?" value="" list="location-list"/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkin').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkin" type="text" placeholder="Check-in" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('stays-checkout').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="stays-checkout" type="text" placeholder="Check-out" readonly/></div>
                    <button class="search-btn" onclick="doSearch('stays')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- CARS panel -->
            <div id="panel-cars" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="cars-pickup" type="text" placeholder="Pick-up location" value="" list="location-list"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="cars-drop" type="text" placeholder="Drop location" list="location-list"/></div>
                    <div class="date-field" onclick="document.getElementById('cars-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="cars-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('cars')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BIKES panel -->
            <div id="panel-bikes" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="bikes-location" type="text" placeholder="Pick-up location" value="" list="location-list"/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="bikes-date" type="text" placeholder="From date" readonly/></div>
                    <div class="date-field" onclick="document.getElementById('bikes-return').focus()"><span class="material-symbols-outlined">event_available</span><input id="bikes-return" type="text" placeholder="Return date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('bikes')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- ATTRACTIONS panel -->
            <div id="panel-attractions" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="attractions-location" type="text" placeholder="Search attractions" value="" list="location-list"/></div>
                    <div class="date-field" onclick="document.getElementById('attractions-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="attractions-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('attractions')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- DINE panel -->
            <div id="panel-dine" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="dine-location" type="text" placeholder="Search restaurants" value="" list="location-list"/></div>
                    <div class="date-field" onclick="document.getElementById('dine-date').focus()"><span class="material-symbols-outlined">calendar_month</span><input id="dine-date" type="text" placeholder="Select date" readonly/></div>
                    <button class="search-btn" onclick="doSearch('dine')"><span class="material-symbols-outlined">search</span>Search</button>
                </div>
            </div>
            <!-- BUSES panel -->
            <div id="panel-buses" class="search-panel">
                <div class="search-row">
                    <div class="search-field"><span class="material-symbols-outlined">trip_origin</span><input id="buses-from" type="text" placeholder="From city" value="" list="location-list"/></div>
                    <div class="search-field"><span class="material-symbols-outlined">location_on</span><input id="buses-to" type="text" placeholder="To city" list="location-list"/></div>
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
        stays:       { img: '<?php echo BASE_PATH; ?>/images/hotel-hero-section-4.webp', label:'Find Your Stay',       pre:'Discover ',   highlight:'Perfect Hotels',    post:' Near You',    desc:'The best hotels, guesthouses and homestays in Chhatrapati Sambhajinagar.' },
        cars:        { img: '<?php echo BASE_PATH; ?>/images/car-rental-hero-section%20(3).webp', label:'Rent a Car',            pre:'Drive in ',   highlight:'Premium Style',     post:' Today',       desc:'Luxury sedans, SUVs and hatchbacks with professional chauffeurs at your service.' },
        bikes:       { img: '<?php echo BASE_PATH; ?>/images/bike%20rentals-hero-section%20(6).webp', label:'Rent a Bike',           pre:'Ride ',       highlight:'The Open Road',     post:' Your Way',    desc:'Scooters, cruisers and sports bikes â€” ride the city your way, anytime.' },
        attractions: { img: '<?php echo BASE_PATH; ?>/images/attractions-hero-section%20(7).webp', label:'Discover Attractions',  pre:'Explore ',    highlight:'Ancient Marvels',   post:' Around You',  desc:'Ellora, Ajanta, Bibi Ka Maqbara and more â€” heritage wonders await you.' },
        dine:        { img: '<?php echo BASE_PATH; ?>/images/dine-hero-section%20(1).webp', label:'Taste the City',        pre:'Savour ',     highlight:'Local Flavours',    post:' Tonight',     desc:'From Mughlai feasts to street food â€” find the best restaurants near you.' },
        buses:       { img: '<?php echo BASE_PATH; ?>/images/bus-hero-section%20(2).webp', label:'Book a Bus',            pre:'Travel ',     highlight:'Your Way',          post:' Comfortably', desc:'AC sleepers, Volvo coaches and MSRTC buses to and from Sambhajinagar.' }
    };
    var d = heroData[tab];
    
    // Update background image with smooth crossfade (dual image technique)
    if (d.img) {
        var bg1 = document.getElementById('hero-bg-1');
        var bg2 = document.getElementById('hero-bg-2');
        
        // Determine which image is currently visible
        var visibleBg = (bg1.style.opacity === '1' || bg1.style.opacity === '') ? bg1 : bg2;
        var hiddenBg = visibleBg === bg1 ? bg2 : bg1;
        
        // Preload new image in hidden layer
        hiddenBg.src = d.img;
        
        // Wait for image to load, then crossfade
        hiddenBg.onload = function() {
            // Fade in the new image
            hiddenBg.style.opacity = '1';
            // Fade out the old image
            visibleBg.style.opacity = '0';
        };
        
        // Fallback if image already cached (onload won't fire)
        if (hiddenBg.complete) {
            hiddenBg.style.opacity = '1';
            visibleBg.style.opacity = '0';
        }
    }

    ['hero-label','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='0'; });
    setTimeout(function(){
        document.getElementById('hero-label').textContent = d.label;
        document.getElementById('hero-desc').textContent = d.desc;
        ['hero-label','hero-desc'].forEach(function(id){ document.getElementById(id).style.opacity='1'; });
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
// DISABLED: Auto-rotation causes severe Cumulative Layout Shift (CLS) on mobile and hurts LCP.
// window.heroInterval = setInterval(autoSwitch, 4000);

// Stop auto-rotation immediately when user interacts with the search box
document.addEventListener('DOMContentLoaded', function() {
    var sbox = document.querySelector('.search-box');
    if(sbox) {
        var stopRot = function() {
            if (window.heroInterval) {
                clearInterval(window.heroInterval);
                window.heroInterval = null;
            }
        };
        sbox.addEventListener('focusin', stopRot);
        sbox.addEventListener('click', stopRot);
        sbox.addEventListener('keydown', stopRot);
    }
});

// Initialize with the first background (stays - default tab)
// hero-bg srcs are already set via HTML â€” no need to reset them

// Fix bfcache: restore hero text when navigating back
window.addEventListener('pageshow', function(e) {
    ['hero-label','hero-desc'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.style.opacity = '1';
    });
    // Also make sure body is visible
    document.body.style.opacity = '1';
    // Restart auto-rotation if it was cleared
    // if (!window.heroInterval) {
    //     window.heroInterval = setInterval(autoSwitch, 4000);
    // }
});

var searchUrls = { stays:'<?php echo BASE_PATH; ?>/hotels', cars:'<?php echo BASE_PATH; ?>/car-rentals', bikes:'<?php echo BASE_PATH; ?>/bike-rentals', attractions:'<?php echo BASE_PATH; ?>/attractions', dine:'<?php echo BASE_PATH; ?>/restaurants', buses:'<?php echo BASE_PATH; ?>/bus' };
function doSearch(tab) {
    window.location.href = searchUrls[tab];
}
// Initialize flatpickr after script loads
function initFlatpickr() {
    if (typeof flatpickr === 'undefined') {
        setTimeout(initFlatpickr, 50);
        return;
    }
    var today = new Date(), tomorrow = new Date(today);
    tomorrow.setDate(today.getDate()+1);
    var opts = { dateFormat:'d M Y', minDate:'today', disableMobile:false };
    var fpCI = flatpickr('#stays-checkin', Object.assign({},opts,{ defaultDate:today, onChange:function(s){ if(s[0]){ var n=new Date(s[0]); n.setDate(n.getDate()+1); fpCO.set('minDate',n); if(!fpCO.selectedDates[0]||fpCO.selectedDates[0]<=s[0]) fpCO.setDate(n); } }, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    var fpCO = flatpickr('#stays-checkout', Object.assign({},opts,{ defaultDate:tomorrow, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    flatpickr('#cars-date', Object.assign({},opts,{ defaultDate:today, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    var fpBD = flatpickr('#bikes-date', Object.assign({},opts,{ defaultDate:today, onChange:function(s){ if(s[0]){ var n=new Date(s[0]); n.setDate(n.getDate()+1); fpBR.set('minDate',n); if(!fpBR.selectedDates[0]||fpBR.selectedDates[0]<=s[0]) fpBR.setDate(n); } }, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    var fpBR = flatpickr('#bikes-return', Object.assign({},opts,{ defaultDate:tomorrow, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    flatpickr('#attractions-date', Object.assign({},opts,{ defaultDate:today, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    flatpickr('#dine-date', Object.assign({},opts,{ defaultDate:today, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
    flatpickr('#buses-date', Object.assign({},opts,{ defaultDate:today, onReady: function(s, d, inst) { if(inst.mobileInput) inst.mobileInput.removeAttribute('tabindex'); } }));
}
document.addEventListener('DOMContentLoaded', initFlatpickr);

// Banner Text Auto-Rotation
document.addEventListener('DOMContentLoaded', function() {
    var bannerData = [
        {
            tracking: "Explore Your Way",
            heading: "Experience the essence of <span class='text-primary italic px-1'>Maharashtra.</span>",
            quote: "\"Chhatrapati Sambhajinagar is more than a city; it's a living museum of ancient artistry.\""
        },
        {
            tracking: "Uncover Hidden Gems",
            heading: "Journey through <span class='text-primary italic px-1'>Time.</span>",
            quote: "\"From majestic forts to silent caves, every stone here tells a forgotten tale.\""
        },
        {
            tracking: "Adventure Awaits",
            heading: "Feel the pulse of <span class='text-primary italic px-1'>The Deccan.</span>",
            quote: "\"Taste the vibrant culture and escape into the ultimate local adventure.\""
        },
        {
            tracking: "Your Premium Guide",
            heading: "Travel seamlessly <span class='text-primary italic px-1'>Everywhere.</span>",
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
                <p id="banner-tracking" class="mobile-hide text-orange-500 font-bold text-xs uppercase tracking-widest mb-2">Explore Your Way</p>
                <h2 id="banner-heading" class="font-serif text-3xl md:text-5xl text-slate-900 leading-tight mb-6">Experience the essence of <span class="text-primary italic px-1">Maharashtra.</span></h2>
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
                    <img alt="Car Rentals" loading="lazy" width="800" height="533" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=600&q=80&auto=format"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-5">
                        <h3 class="text-white text-sm sm:text-lg font-bold mb-1">Car Rentals</h3>
                        <a href="<?php echo BASE_PATH; ?>/listing/cars" class="text-white/80 text-xs hover:text-white transition-colors flex items-center gap-1">Browse Cars <span class="material-symbols-outlined text-[10px]">arrow_forward</span></a>
                    </div>
                </div>
                <!-- Bike Rentals Card -->
                <div data-reveal data-reveal="right" class="group relative overflow-hidden rounded-2xl h-40 sm:h-64 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                    <img alt="Bike Rentals" loading="lazy" width="800" height="533" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=600&q=80&auto=format"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-5">
                        <h3 class="text-white text-sm sm:text-lg font-bold mb-1">Bike Rentals</h3>
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
            <div data-reveal class="bg-white p-6 rounded-2xl shadow-xl shadow-black/5 hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-400 ease-out border border-slate-100 group flex flex-col items-center text-center">
                <div class="size-11 rounded-xl bg-orange-50 flex items-center justify-center mb-4 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-primary transition-colors text-2xl"><?php echo $h['icon']; ?></span>
                </div>
                <h3 class="text-slate-900 font-bold text-sm leading-tight italic"><?php echo $h['label']; ?></h3>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-1.5"><?php echo $h['sub']; ?></p>
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
// Base counts â€” if more items than this are shown, switch to horizontal scroll
$_sec_base_counts = [
    'attractions' => 4,
    'bikes'       => 4,
    'restaurants' => 6,
    'buses'       => 2,
    'blogs'       => 3,
    'cars'        => 4,
    'stays'       => 4,
];

// â”€â”€ Render sections in saved order â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
        <div class="w-full h-full opacity-40 bg-cover bg-center scale-110 group-hover:scale-100 transition-transform duration-1000" style="background-image:url('https://images.unsplash.com/photo-1506012787146-f92b2d7d6d96?auto=format&w=800&q=80')"></div>
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
                <h2 class="font-serif text-3xl md:text-5xl text-white font-black leading-tight mb-4">
                    Not sure where to go? <span class="bg-gradient-to-r from-primary to-orange-400 bg-clip-text text-transparent italic px-1 pr-2">Let us plan it.</span>
                </h2>
                <p class="text-white/70 text-sm md:text-base max-w-lg mx-auto md:mx-0 mb-6 group-hover:text-white/90 transition-colors">
                    Talk to our local trip experts. We'll craft a customized, fully personalized itinerary for your entire tripâ€”including cars, hotels, and ancient site guidesâ€”at no extra cost.
                </p>
                <a href="<?php echo BASE_PATH; ?>/suggestor" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-orange-500 text-white font-black rounded-xl text-sm shadow-[0_8px_30px_rgb(236,91,19,0.3)] hover:shadow-[0_8px_30px_rgb(236,91,19,0.5)] hover:-translate-y-1 hover:text-white transition-all">
                    Trip Plan <span class="material-symbols-outlined text-[18px]">support_agent</span>
                </a>
            </div>
            
            <!-- Graphic layout â€” JS-driven smooth card stack -->
            <div class="flex flex-shrink-0 relative w-64 h-72 md:w-72 md:h-80 lg:w-80 lg:h-96 z-10 items-center justify-center" id="trip-stack-wrap">
                <style>
                    #trip-stack-wrap .stack-card {
                        position: absolute;
                        inset: 0;
                        border-radius: 1.5rem;
                        background-size: cover;
                        background-position: center;
                        border: 2px solid rgba(255,255,255,0.22);
                        /* Use filter:drop-shadow instead of box-shadow â€” GPU composited, no CLS */
                        will-change: transform, opacity;
                        transition: transform 0.6s cubic-bezier(0.22,1,0.36,1),
                                    opacity   0.6s cubic-bezier(0.22,1,0.36,1);
                    }
                    <?php
                    $attr_imgs = [];
                    foreach($hp_attractions as $a) {
                        if (!empty($a['image'])) {
                            $img = (strpos($a['image'], 'http') === 0)
                                ? $a['image']
                                : BASE_PATH . '/' . ltrim($a['image'], '/');
                            $attr_imgs[] = htmlspecialchars($img);
                        }
                    }
                    $fallbacks = [
                        'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80&auto=format',
                        'https://images.unsplash.com/photo-1506012787146-f92b2d7d6d96?w=600&q=80&auto=format',
                        'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=600&q=80&auto=format',
                        'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600&q=80&auto=format',
                    ];
                    while (count($attr_imgs) < 4) {
                        $attr_imgs[] = $fallbacks[count($attr_imgs) % count($fallbacks)];
                    }
                    shuffle($attr_imgs);
                    ?>
                    #trip-stack-wrap .stack-card:nth-child(1){background-image:url('<?php echo $attr_imgs[0]; ?>');}
                    #trip-stack-wrap .stack-card:nth-child(2){background-image:url('<?php echo $attr_imgs[1]; ?>');}
                    #trip-stack-wrap .stack-card:nth-child(3){background-image:url('<?php echo $attr_imgs[2]; ?>');}
                    #trip-stack-wrap .stack-card:nth-child(4){background-image:url('<?php echo $attr_imgs[3]; ?>');}
                </style>
                <div class="stack-card"></div>
                <div class="stack-card"></div>
                <div class="stack-card"></div>
                <div class="stack-card"></div>
            </div>
        </div>
    </div>
</section>
<script>
(function(){
    var wrap  = document.getElementById('trip-stack-wrap');
    if (!wrap) return;
    var cards = wrap.querySelectorAll('.stack-card');
    if (!cards.length) return;
    var n = cards.length, cur = 0;

    /* 4-layer depth states â€” use filter:drop-shadow (GPU composited) instead of box-shadow */
    var S = [
        { z:4, o:1,    t:'translateY(0px) scale(1) rotate(0deg)',        f:'drop-shadow(0 28px 30px rgba(0,0,0,0.5)) drop-shadow(0 0 20px rgba(236,91,19,0.15))' },
        { z:3, o:0.75, t:'translateY(12px) scale(0.93) rotate(-2.5deg)', f:'drop-shadow(0 10px 18px rgba(0,0,0,0.28))' },
        { z:2, o:0.45, t:'translateY(22px) scale(0.86) rotate(3deg)',    f:'drop-shadow(0 4px 8px rgba(0,0,0,0.15))' },
        { z:1, o:0,    t:'translateY(32px) scale(0.80) rotate(-1.5deg)', f:'none' },
    ];

    function set(card, s, anim) {
        if (!anim) card.style.transition = 'none';
        card.style.zIndex    = s.z;
        card.style.opacity   = s.o;
        card.style.transform = s.t;
        card.style.filter    = s.f; /* GPU-composited: no CLS */
    }

    /* Snap to initial positions without animation */
    cards.forEach(function(c,i){ set(c, S[i%n], false); });
    void wrap.offsetWidth; /* flush */
    cards.forEach(function(c){
        c.style.transition = 'transform .55s cubic-bezier(.22,1,.36,1),'
                           + 'opacity .55s cubic-bezier(.22,1,.36,1)';
    });

    /* Advance every 4 seconds to reduce CPU usage and TBT */
    setInterval(function(){
        cur = (cur+1)%n;
        cards.forEach(function(c,i){ set(c, S[(i-cur+n)%n], true); });
    }, 4000);
})();
</script>
<?php endif; ?>
<?php
    $_layout = $hp_settings['layout_' . $_sec_key];
    // If more items than base count â†’ force horizontal scroll
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
<section class="py-12 <?php echo $_bg; ?>" style="content-visibility:auto;contain-intrinsic-size:0 500px;">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="flex items-end justify-between mb-6" data-reveal>
            <?php if ($_sec_key === 'blogs'): ?>
            <div>
                <p class="mobile-hide text-orange-600 font-bold text-xs uppercase tracking-widest mb-1">Our Travel Journals</p>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900">Travel Journals & Itineraries</h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/blogs" class="text-sm font-bold text-[#c2410c] hover:underline">Read more &rarr;</a>
            <?php else: ?>
            <div>
                <p class="mobile-hide text-orange-600 font-bold text-xs uppercase tracking-widest mb-1"><?php
                    $sec_subtitles = ['attractions'=>'Heritage & Culture','bikes'=>'Two-Wheeler Rentals','restaurants'=>'Food & Dining','buses'=>'Travel Your Way','cars'=>'Self-Drive & Taxis','stays'=>'Hotels & Resorts'];
                    echo $sec_subtitles[$_sec_key] ?? 'Explore';
                ?></p>
                <?php 
                $seo_titles = [
                    'stays' => 'Premium Stays & Hotels in Chhatrapati Sambhajinagar',
                    'cars' => 'Self-Drive Car Rentals & Taxis (Aurangabad)',
                    'bikes' => 'Quick Bike & Scooter Rentals in Sambhajinagar',
                    'attractions' => 'Top Heritage Sites & Caves Tour',
                    'restaurants' => 'Taste the City: Pure Veg, Jain & Maharashtrian',
                    'buses' => 'Outstation Buses & Travel Options'
                ];
                $display_title = $seo_titles[$_sec_key] ?? $hp_settings['title_' . $_sec_key];
                ?>
                <h2 class="font-serif text-2xl md:text-3xl text-slate-900"><?php echo htmlspecialchars($display_title); ?></h2>
            </div>
            <a href="<?php echo BASE_PATH; ?>/listing/<?php echo $_sec_key; ?>" aria-label="See all <?php echo htmlspecialchars($display_title); ?>" class="text-sm font-bold text-[#c2410c] hover:underline flex items-center gap-1 group">
                See all <span class="hidden md:inline lowercase"><?php echo htmlspecialchars($sec_subtitles[$_sec_key] ?? $_sec_key); ?></span> 
                <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
            <?php endif; ?>
        </div>
        <?php if ($_sec_key !== 'blogs'): ?>
        <!-- Mobile scroll hint -->
        <p class="text-slate-500 text-xs flex items-center gap-1 mt-1 mb-5 md:hidden">
          <span class="material-symbols-outlined text-sm">swipe</span> Swipe to explore
        </p>
        <?php endif; ?>
        <?php
        // â”€â”€ Visible-cards-per-section config â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $_vis = ['attractions'=>4,'bikes'=>4,'restaurants'=>4,'buses'=>2,'blogs'=>3];
        $_vis_count = $_vis[$_sec_key] ?? 4;
        // Mobile: fixed 80vw so ~1.1 cards visible. Desktop: percentage of container.
        $_card_w = 'var(--card-w-' . $_sec_key . ')';

        if ($_sec_key === 'attractions'):
            $render_fn = function($a) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('attractions', $a['id'], $a['name']);
                $imgSrc = $a['image'] ?? '';
                $img = htmlspecialchars(get_working_image_url($imgSrc));
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80&auto=format';
                $name=htmlspecialchars($a['name'] ?? '');
                $tag=htmlspecialchars($a['type'] ?? 'Attraction');
                $price=$a['entry_fee']>0 ? '&#8377;'.number_format($a['entry_fee']) : 'Free';
                $rating=number_format((float)($a['rating']??0),1);
                // Build responsive srcset for local images
                $srcset = '';
                $sizesAttr = 'sizes="(max-width:480px) 82vw,(max-width:768px) 48vw,(max-width:1024px) 33vw,280px"';
                if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                    $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                    $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                    $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                    $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                    $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                    if (file_exists($p400) && file_exists($p700)) {
                        $srcset = 'srcset="'.$v400.' 400w,'.$v700.' 700w,'.$img.' 800w"';
                    }
                }
                return '<a href="'.$slug.'" class="group relative rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-400 ease-out flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative rounded-t-2xl"><img width="800" height="600" alt="'.SEOOptimizer::generateAltText('attractions', $name).'" loading="lazy" decoding="async" '.$srcset.' '.$sizesAttr.' class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-[#c2410c] text-[10px] font-bold uppercase tracking-widest relative z-20">'.$tag.'</span>'
                    .'<h3 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1 relative z-20">'.$name.'</h3>'
                    .'<div class="flex items-center justify-between relative z-20">'
                    .'<p class="font-black text-slate-900 text-sm">'.$price.' <span class="text-xs text-slate-500 font-normal">entry</span></p>'
                    .'<span class="bg-[#c2410c] text-white px-3 py-1.5 rounded-full font-bold text-xs transition-all" aria-label="Check details for '.$name.' in Chhatrapati Sambhajinagar">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_attractions;
        elseif ($_sec_key === 'bikes'):
            $render_fn = function($b) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('bikes', $b['id'], $b['name']);
                $imgSrc = $b['image'] ?? '';
                $img = htmlspecialchars(get_working_image_url($imgSrc));
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80&auto=format';
                $name=htmlspecialchars($b['name'] ?? '');
                $type=htmlspecialchars($b['type'] ?? ''); $price=number_format($b['price_per_day'] ?? 0);
                $rating=number_format((float)($b['rating']??0),1);
                $srcset = '';
                $sizesAttr = 'sizes="(max-width:480px) 82vw,(max-width:768px) 48vw,(max-width:1024px) 33vw,280px"';
                if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                    $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                    $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                    $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                    $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                    $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                    if (file_exists($p400) && file_exists($p700)) {
                        $srcset = 'srcset="'.$v400.' 400w,'.$v700.' 700w,'.$img.' 800w"';
                    }
                }
                return '<a href="'.$slug.'" class="group rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-400 ease-out flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative rounded-t-2xl"><img width="800" height="600" alt="'.SEOOptimizer::generateAltText('bikes', $name).'" loading="lazy" decoding="async" '.$srcset.' '.$sizesAttr.' class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-[#c2410c] text-[10px] font-bold uppercase tracking-widest">'.$type.'</span>'
                    .'<h3 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h3>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-500 font-normal">/day</span></p>'
                    .'<span class="bg-[#c2410c] text-white px-3 py-1.5 rounded-full font-bold text-xs transition-all" aria-label="Check availability for '.$name.' bike rental">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_bikes;
        elseif ($_sec_key === 'restaurants'):
            $render_fn = function($r) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('restaurants', $r['id'], $r['name']);
                $imgSrc = $r['image'] ?? '';
                $img = htmlspecialchars(get_working_image_url($imgSrc));
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80&auto=format';
                $name=htmlspecialchars($r['name'] ?? '');
                $cuisine=htmlspecialchars($r['cuisine'] ?? $r['type'] ?? ''); $price=number_format($r['price_per_person'] ?? 0);
                $rating=number_format((float)($r['rating']??0),1);
                $srcset = '';
                $sizesAttr = 'sizes="(max-width:480px) 82vw,(max-width:768px) 48vw,(max-width:1024px) 33vw,280px"';
                if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                    $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                    $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                    $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                    $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                    $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                    if (file_exists($p400) && file_exists($p700)) {
                        $srcset = 'srcset="'.$v400.' 400w,'.$v700.' 700w,'.$img.' 800w"';
                    }
                }
                return '<a href="'.$slug.'" class="group relative rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-400 ease-out flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative rounded-t-2xl"><img width="800" height="600" alt="'.SEOOptimizer::generateAltText('restaurants', $name).'" loading="lazy" decoding="async" '.$srcset.' '.$sizesAttr.' class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-[#c2410c] text-[10px] font-bold uppercase tracking-widest relative z-20">'.$cuisine.'</span>'
                    .'<h3 class="font-serif text-base text-slate-900 mt-1 mb-2 line-clamp-1 relative z-20">'.$name.'</h3>'
                    .'<div class="flex items-center gap-1 text-slate-500 text-xs mb-3 relative z-20"><span style="font-family:Material Symbols Outlined;font-size:14px">location_on</span><span class="line-clamp-1">'.htmlspecialchars($r['location']??'').'</span></div>'
                    .'<div class="space-y-1.5 text-xs text-slate-600 mb-3 relative z-20">'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">verified</span><span class="font-semibold">Verified</span></div>'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">cancel</span><span>Free cancellation</span></div>'
                    .'<div class="flex items-center gap-2"><span style="font-family:Material Symbols Outlined;font-size:14px;color:#ec5b13">info</span><span>No hidden charges</span></div>'
                    .'</div>'
                    .'<div class="flex items-center justify-between gap-3 relative z-20 border-t border-slate-100 pt-3">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-500 font-normal">for two</span></p>'
                    .'<span class="bg-[#c2410c] text-white px-4 py-1.5 rounded-full font-bold text-xs  transition-all whitespace-nowrap" aria-label="Check details for '.$name.'">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_restaurants;
        elseif ($_sec_key === 'cars'):
            $render_fn = function($c) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('cars', $c['id'], $c['name']);
                $imgSrc = $c['image'] ?? '';
                $img = htmlspecialchars(get_working_image_url($imgSrc));
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&q=80&auto=format';
                $name=htmlspecialchars($c['name']??'');
                $type=htmlspecialchars($c['type']??'Sedan'); $price=number_format($c['price_per_day']??0);
                $rating=number_format((float)($c['rating']??0),1);
                $srcset = '';
                $sizesAttr = 'sizes="(max-width:480px) 82vw,(max-width:768px) 48vw,(max-width:1024px) 33vw,280px"';
                if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                    $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                    $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                    $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                    $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                    $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                    if (file_exists($p400) && file_exists($p700)) {
                        $srcset = 'srcset="'.$v400.' 400w,'.$v700.' 700w,'.$img.' 800w"';
                    }
                }
                return '<a href="'.$slug.'" class="group rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-400 ease-out flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative rounded-t-2xl"><img width="800" height="600" alt="'.SEOOptimizer::generateAltText('cars', $name).'" loading="lazy" decoding="async" '.$srcset.' '.$sizesAttr.' class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-[#c2410c] text-[10px] font-bold uppercase tracking-widest">'.$type.'</span>'
                    .'<h3 class="font-serif text-base text-slate-900 mt-1 mb-3 line-clamp-1">'.$name.'</h3>'
                    .'<div class="flex items-center justify-between">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-500 font-normal">/day</span></p>'
                    .'<span class="bg-[#c2410c] text-white px-3 py-1.5 rounded-full font-bold text-xs transition-all" aria-label="Check availability for '.$name.' self drive car rental">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_cars;
        elseif ($_sec_key === 'stays'):
            $render_fn = function($s) {
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('stays', $s['id'], $s['name']);
                $imgSrc = $s['image'] ?? '';
                $img = htmlspecialchars(get_working_image_url($imgSrc));
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80&auto=format';
                $name=htmlspecialchars($s['name']??'');
                $type=htmlspecialchars($s['type']??'Hotel'); $price=number_format($s['price_per_night']??0);
                $rating=number_format((float)($s['rating']??0),1);
                $srcset = '';
                $sizesAttr = 'sizes="(max-width:480px) 82vw,(max-width:768px) 48vw,(max-width:1024px) 33vw,280px"';
                if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                    $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                    $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                    $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                    $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                    $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                    if (file_exists($p400) && file_exists($p700)) {
                        $srcset = 'srcset="'.$v400.' 400w,'.$v700.' 700w,'.$img.' 800w"';
                    }
                }
                return '<a href="'.$slug.'" class="group relative rounded-2xl bg-white border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-400 ease-out flex-shrink-0" style="width:VAR_W">'
                    .'<div class="h-44 overflow-hidden relative rounded-t-2xl"><img width="800" height="600" alt="'.SEOOptimizer::generateAltText('stays', $name).'" loading="lazy" decoding="async" '.$srcset.' '.$sizesAttr.' class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" src="'.$img.'"/>'
                    .'<div class="absolute top-2.5 right-2.5 flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-0.5 rounded-full z-20"><span style="font-family:Material Symbols Outlined;font-size:12px;color:#fbbf24">star</span>'.$rating.'</div></div>'
                    .'<div class="p-4"><span class="text-[#c2410c] text-[10px] font-bold uppercase tracking-widest relative z-20">'.$type.'</span>'
                    .'<h3 class="font-serif text-base text-slate-900 mt-1 mb-2 line-clamp-1 relative z-20">'.$name.'</h3>'
                    .'<div class="flex items-center gap-1 text-slate-500 text-xs mb-3 relative z-20"><span style="font-family:Material Symbols Outlined;font-size:14px">location_on</span><span class="line-clamp-1">'.htmlspecialchars($s['location']??'').'</span></div>'
                    .'<div class="flex items-center justify-between gap-3 relative z-20 border-t border-slate-100 pt-3">'
                    .'<p class="font-black text-slate-900 text-sm">&#8377;'.$price.' <span class="text-xs text-slate-500 font-normal">/night</span></p>'
                    .'<span class="bg-[#c2410c] text-white px-4 py-1.5 rounded-full font-bold text-xs transition-all whitespace-nowrap" aria-label="Check details for '.$name.' in Chhatrapati Sambhajinagar">Check Availability</span>'
                    .'</div></div></a>';
            };
            $items = $hp_stays;
        elseif ($_sec_key === 'buses'):
            $render_fn = function($bus) {
                $op=htmlspecialchars($bus['operator']??''); $bt=htmlspecialchars($bus['bus_type']??'');
                $route=htmlspecialchars($bus['from_location']??'').' â†’ '.htmlspecialchars($bus['to_location']??'');
                $price=number_format($bus['price']);
                $slug = BASE_PATH . '/listing-detail/' . generateSlug('buses', $bus['id'], $bus['operator']);
                return '<a href="'.$slug.'" class="glass-dark p-5 rounded-2xl flex items-center justify-between gap-4 card-hover flex-shrink-0 group relative overflow-hidden" style="width:VAR_W">'
                    .'<div class="flex items-center gap-4 min-w-0 relative z-20">'
                    .'<div class="w-12 h-12 bg-primary/15 rounded-xl flex items-center justify-center shrink-0">'
                    .'<span class="material-symbols-outlined text-primary text-2xl">directions_bus</span></div>'
                    .'<div class="min-w-0"><p class="text-white font-bold text-sm truncate">'.$op.' <span class="text-[10px] font-normal text-white/50 bg-white/10 px-2 py-0.5 rounded ml-1">'.$bt.'</span></p>'
                    .'<p class="text-white/50 text-xs mt-0.5 truncate">'.$route.'</p></div></div>'
                    .'<div class="flex items-center gap-3 shrink-0 relative z-20">'
                    .'<p class="text-primary font-black text-lg">&#8377;'.$price.'</p>'
                    .'<span class="bg-[#c2410c] text-white px-4 py-2 rounded-xl font-bold text-xs transition-all" aria-label="Check availability for '.$op.' bus to '.htmlspecialchars($bus['to_location']).'">Check Availability</span>'
                    .'</div></a>';
            };
            $items = $hp_buses;
        else:
            $render_fn = function($blog) {
                $rt=max(3,intval(strlen(strip_tags($blog['content']??''))/1000));
                $t=strtolower(trim($blog['title']));
                $t=preg_replace('/[^a-z0-9\s-]/','',$t);
                $t=preg_replace('/[\s-]+/','-',$t);
                $slug = BASE_PATH . '/blogs/'.$blog['id'].'-'.substr(trim($t,'-'),0,60);
                $imgSrc = $blog['image'] ?? '';
                $img = htmlspecialchars(get_working_image_url($imgSrc));
                if (!$imgSrc) $img = 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=600&q=80&auto=format';
                $title=htmlspecialchars($blog['title']);
                $cat=htmlspecialchars($blog['category']??'Travel');
                $desc=htmlspecialchars($blog['meta_description'] ?? strip_tags(substr($blog['content']??'', 0, 150)) . '...');
                $date=date('M d, Y', strtotime($blog['created_at']));
                $author=htmlspecialchars($blog['author'] ?? 'Admin');
                $srcset = '';
                $sizesAttr = 'sizes="(max-width:480px) 82vw,(max-width:768px) 52vw,(max-width:1024px) 33vw,280px"';
                if (strpos($imgSrc, 'http') !== 0 && $imgSrc) {
                    $base = pathinfo($imgSrc, PATHINFO_FILENAME);
                    $v400 = BASE_PATH.'/images/uploads/variants/'.$base.'-400w.webp';
                    $v700 = BASE_PATH.'/images/uploads/variants/'.$base.'-700w.webp';
                    $p400 = __DIR__.'/images/uploads/variants/'.$base.'-400w.webp';
                    $p700 = __DIR__.'/images/uploads/variants/'.$base.'-700w.webp';
                    if (file_exists($p400) && file_exists($p700)) {
                        $srcset = 'srcset="'.$v400.' 400w,'.$v700.' 700w,'.$img.' 800w"';
                    }
                }
                return '<div class="flex flex-col group h-full relative bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-400 ease-out flex-shrink-0" style="width:VAR_W">'
                    .'<a href="'.$slug.'" class="absolute inset-0 z-10" aria-label="'.$title.'"></a>'
                    .'<div class="relative rounded-2xl overflow-hidden mb-5 aspect-video shadow-lg" style="transform: translateZ(0); -webkit-transform: translateZ(0); -webkit-mask-image: -webkit-radial-gradient(white, black); -webkit-backface-visibility: hidden; backface-visibility: hidden; isolation: isolate;">'
                    .'<img width="800" height="600" alt="'.SEOOptimizer::generateAltText('blogs', $title).'" loading="lazy" decoding="async" '.$srcset.' '.$sizesAttr.' class="w-full h-full object-cover rounded-2xl transition-transform duration-500 group-hover:scale-110" src="'.$img.'"/>'
                    .'<div class="absolute top-4 left-4">'
                    .'<span class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-primary uppercase relative z-20">'.$cat.'</span>'
                    .'</div></div>'
                    .'<div class="flex flex-col flex-grow">'
                    .'<div class="flex items-center gap-4 mb-3 text-slate-500 text-xs font-semibold">'
                    .'<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span>'.$rt.' min read</span>'
                    .'<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">calendar_today</span>'.$date.'</span>'
                    .'</div>'
                    .'<h4 class="text-xl font-serif font-bold leading-snug mb-3 group-hover:text-primary transition-colors text-slate-900 line-clamp-2">'.$title.'</h4>'
                    .'<p class="text-slate-600 text-sm line-clamp-2 mb-6">'.$desc.'</p>'
                    .'<div class="mt-auto pt-4 border-t border-slate-100 flex justify-between items-center text-slate-900 relative z-20">'
                    .'<span class="text-primary font-bold text-sm flex items-center gap-1 group/btn">Read More<span class="material-symbols-outlined text-base transition-transform group-hover/btn:translate-x-1">chevron_right</span></span>'
                    .'<span class="text-xs text-slate-400">'.$author.'</span>'
                    .'</div></div></div>';
            };
            $items = $hp_blogs;
        endif;
        ?>
        <?php if ($_sec_key === 'stays'): ?>
        <!-- Premium Carousel Slider for Stays -->
        <div class="relative overflow-hidden group/slider -mx-5 px-5" id="carousel-wrap-stays" style="scroll-behavior: auto !important;">
            <div class="flex gap-6 pb-6 overflow-x-auto snap-x snap-mandatory hide-scrollbar" id="carousel-track-stays" style="scroll-padding-left: 20px;">
                <?php
                if (!empty($items)) {
                    foreach ($items as $__item) {
                        echo str_replace('VAR_W', $_card_w, $render_fn($__item));
                    }
                } else {
                    echo '<p class="text-slate-400 py-8">No items yet.</p>';
                }
                ?>
            </div>
            <!-- Slider Controls -->
            <button onclick="document.getElementById('carousel-track-stays').scrollBy({left:-350, behavior:'smooth'})" class="absolute left-6 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-md shadow-xl rounded-full p-3 text-slate-800 opacity-0 group-hover/slider:opacity-100 transition-all hover:bg-white hover:scale-110 hidden md:block z-30 border border-slate-200" aria-label="Previous">
                <span class="material-symbols-outlined font-bold">arrow_back_ios_new</span>
            </button>
            <button onclick="document.getElementById('carousel-track-stays').scrollBy({left:350, behavior:'smooth'})" class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-md shadow-xl rounded-full p-3 text-slate-800 opacity-0 group-hover/slider:opacity-100 transition-all hover:bg-white hover:scale-110 hidden md:block z-30 border border-slate-200" aria-label="Next">
                <span class="material-symbols-outlined font-bold">arrow_forward_ios</span>
            </button>
        </div>
        <?php else: ?>
        <!-- Default Carousel -->
        <div class="relative overflow-x-auto overflow-y-hidden hide-scrollbar snap-x snap-mandatory" id="carousel-wrap-<?php echo $_sec_key; ?>" style="scroll-behavior: auto !important;">
            <div id="carousel-track-<?php echo $_sec_key; ?>" class="flex gap-6 pb-4">
                <?php
                if (!empty($items)) {
                    // Render 1x initially to keep HTML payload tiny. JS will clone for infinite loop.
                    foreach ($items as $__item) {
                        echo str_replace('VAR_W', $_card_w, $render_fn($__item));
                    }
                } else {
                    echo '<p class="text-slate-400 py-8">No items yet.</p>';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endforeach; ?>
<!-- How CSNExplore Works Section â€” Premium Redesign -->
<style>
/* HIW Section Styles */
.hiw-section {
    background: linear-gradient(135deg, #0f0c0a 0%, #1a1208 40%, #0f0c0a 100%);
    position: relative;
    overflow: hidden;
    padding: 96px 0;
}
.hiw-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 10% 20%, rgba(236,91,19,0.18) 0%, transparent 60%),
        radial-gradient(ellipse 50% 40% at 90% 80%, rgba(245,158,11,0.12) 0%, transparent 55%),
        radial-gradient(ellipse 30% 30% at 50% 50%, rgba(236,91,19,0.06) 0%, transparent 70%);
    pointer-events: none;
}
.hiw-orb-1 {
    position: absolute;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(236,91,19,0.12) 0%, transparent 70%);
    top: -100px; left: -100px;
    animation: hiwFloat1 8s ease-in-out infinite;
    pointer-events: none;
}
.hiw-orb-2 {
    position: absolute;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
    bottom: -80px; right: -60px;
    animation: hiwFloat2 10s ease-in-out infinite;
    pointer-events: none;
}
@keyframes hiwFloat1 {
    0%, 100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(30px, 20px) scale(1.08); }
}
@keyframes hiwFloat2 {
    0%, 100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(-20px, -30px) scale(1.05); }
}
.hiw-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(236,91,19,0.15);
    border: 1px solid rgba(236,91,19,0.3);
    color: #f97316;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 6px 16px;
    border-radius: 100px;
    margin-bottom: 20px;
    backdrop-filter: blur(8px);
}
.hiw-title {
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 800;
    color: #ffffff;
    line-height: 1.15;
    margin-bottom: 16px;
    letter-spacing: -0.02em;
}
.hiw-title span {
    background: linear-gradient(135deg, #f97316, #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hiw-subtitle {
    color: rgba(255,255,255,0.5);
    font-size: 15px;
    line-height: 1.7;
    max-width: 560px;
    margin: 0 auto;
}
/* Desktop connector line */
.hiw-connector-wrap {
    position: absolute;
    top: 44px;
    left: calc(16.666% + 40px);
    right: calc(16.666% + 40px);
    height: 2px;
    pointer-events: none;
    z-index: 0;
    display: none;
}
@media (min-width: 768px) {
    .hiw-connector-wrap { display: block; }
    .hiw-connector-wrap.hiw-row2 { top: 44px; }
}
.hiw-connector-line {
    width: 100%;
    height: 100%;
    border-top: 2px dashed rgba(236,91,19,0.25);
    position: relative;
}
.hiw-connector-line::after {
    content: '';
    position: absolute;
    top: -1px;
    left: 0;
    height: 2px;
    width: 0%;
    background: linear-gradient(90deg, #f97316, #f59e0b);
    animation: hiwConnectorFill 2s ease-out 0.5s forwards;
}
@keyframes hiwConnectorFill {
    to { width: 100%; }
}

/* Step Card */
.hiw-step-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0;
    flex-shrink: 0;
    width: 85vw;
    max-width: 320px;
    scroll-snap-align: center;
    height: 100%;
    min-height: 280px;
}
.hiw-icon-wrap {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 20px;
    flex-shrink: 0;
    transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
    cursor: default;
    background: linear-gradient(135deg, rgba(249,115,22,0.15) 0%, rgba(245,158,11,0.1) 100%);
    border: 1px solid rgba(249,115,22,0.25);
    box-shadow: 0 0 0 0 rgba(249,115,22,0);
}
.hiw-step-card:hover .hiw-icon-wrap {
    transform: scale(1.12) translateY(-4px);
    box-shadow: 0 0 40px rgba(249,115,22,0.35), 0 20px 40px rgba(0,0,0,0.3);
    border-color: rgba(249,115,22,0.5);
}
.hiw-icon-inner {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f97316, #f59e0b);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(249,115,22,0.5);
}
.hiw-step-num {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f97316, #f59e0b);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(249,115,22,0.5);
    border: 2px solid rgba(15,12,10,0.8);
}
.hiw-card-body {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 24px 20px;
    backdrop-filter: blur(12px);
    transition: all 0.4s ease;
    width: 100%;
    flex-grow: 1;
}
.hiw-step-card:hover .hiw-card-body {
    background: rgba(255,255,255,0.07);
    border-color: rgba(249,115,22,0.2);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(249,115,22,0.1);
    transform: translateY(-4px);
}
.hiw-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 8px;
    letter-spacing: -0.01em;
}
.hiw-card-desc {
    font-size: 12.5px;
    color: rgba(255,255,255,0.5);
    line-height: 1.75;
}

/* Mobile vertical line */
.hiw-mobile-line {
    position: absolute;
    left: 43px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, rgba(249,115,22,0.4), rgba(249,115,22,0.1));
    pointer-events: none;
}

/* WCU Section Premium */
.wcu-section {
    background: #ffffff;
    padding: 96px 0;
    position: relative;
    overflow: hidden;
}
.wcu-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 60% at 80% 50%, rgba(236,91,19,0.04) 0%, transparent 60%);
    pointer-events: none;
}
.wcu-card {
    background: #ffffff;
    border-radius: 28px;
    padding: 36px 32px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 24px rgba(0,0,0,0.04);
    transition: all 0.4s cubic-bezier(0.22,1,0.36,1);
    position: relative;
    overflow: hidden;
}
.wcu-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 28px;
    background: linear-gradient(135deg, rgba(249,115,22,0) 0%, rgba(249,115,22,0) 100%);
    transition: background 0.4s ease;
    pointer-events: none;
}
.wcu-card:hover {
    box-shadow: 0 20px 60px rgba(236,91,19,0.12), 0 4px 20px rgba(0,0,0,0.06);
    transform: translateY(-8px);
    border-color: rgba(249,115,22,0.15);
}
.wcu-card:hover::before {
    background: linear-gradient(135deg, rgba(249,115,22,0.03) 0%, rgba(245,158,11,0.02) 100%);
}
.wcu-icon-ring {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
}
.wcu-card:hover .wcu-icon-ring {
    transform: scale(1.12) rotate(-5deg);
}
.wcu-number {
    position: absolute;
    top: 28px;
    right: 28px;
    font-size: 56px;
    font-weight: 900;
    color: rgba(0,0,0,0.03);
    line-height: 1;
    font-family: 'Playfair Display', serif;
    pointer-events: none;
}
.wcu-title-text {
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 12px;
    letter-spacing: -0.02em;
}
.wcu-desc-text {
    font-size: 13.5px;
    color: #64748b;
    line-height: 1.75;
}

/* Testimonials Premium */
.testi-section {
    background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
    border-top: 1px solid #f1f5f9;
}
.testi-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #fff7ed, #fff3e0);
    border: 1px solid rgba(249,115,22,0.2);
    color: #ea580c;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 100px;
    margin-bottom: 16px;
}
.testi-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 32px 28px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    transition: all 0.4s cubic-bezier(0.22,1,0.36,1);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
    width: 85vw;
    max-width: 350px;
    scroll-snap-align: center;
    display: flex;
    flex-direction: column;
}
.testi-card::before {
    content: '"';
    position: absolute;
    top: 16px;
    right: 24px;
    font-size: 96px;
    font-family: 'Playfair Display', Georgia, serif;
    color: rgba(249,115,22,0.08);
    line-height: 1;
    font-weight: 900;
    pointer-events: none;
}
.testi-card:hover {
    box-shadow: 0 20px 50px rgba(236,91,19,0.1), 0 4px 16px rgba(0,0,0,0.06);
    transform: translateY(-6px);
    border-color: rgba(249,115,22,0.12);
}
.testi-stars {
    display: flex;
    gap: 2px;
    margin-bottom: 16px;
}
.testi-star {
    color: #f59e0b;
    font-size: 16px;
}
.testi-text {
    color: #475569;
    font-size: 13.5px;
    line-height: 1.8;
    margin-bottom: 24px;
    font-style: italic;
}
.testi-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f97316, #f59e0b);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 16px;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(249,115,22,0.3);
}
.testi-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}
.testi-loc {
    font-size: 12px;
    color: #94a3b8;
}
</style>

<style>
/* How CSNExplore Works Grid Styles */
.hiw-grid-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    position: relative;
    z-index: 10;
}
@media (min-width: 768px) {
    .hiw-grid-container {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .hiw-grid-container {
        /* Three cards on top, three at bottom as requested */
        grid-template-columns: repeat(3, 1fr);
    }
}
.hiw-grid-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 32px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.hiw-grid-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 24px;
    background: linear-gradient(135deg, rgba(249,115,22,0.03) 0%, rgba(245,158,11,0.02) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}
.hiw-grid-card:hover {
    box-shadow: 0 20px 40px rgba(236,91,19,0.08), 0 4px 12px rgba(0,0,0,0.04);
    transform: translateY(-6px);
    border-color: rgba(249,115,22,0.15);
}
.hiw-grid-card:hover::before {
    opacity: 1;
}
.hiw-grid-icon {
    width: 72px;
    height: 72px;
    border-radius: 20px;
    background: linear-gradient(135deg, #fff7ed, #fffbeb);
    border: 1px solid rgba(249,115,22,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    color: #ea580c;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.hiw-grid-card:hover .hiw-grid-icon {
    transform: scale(1.1) rotate(-5deg);
    background: linear-gradient(135deg, #f97316, #f59e0b);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 10px 20px rgba(236,91,19,0.25);
}
.hiw-grid-number {
    position: absolute;
    top: -10px;
    right: -10px;
    font-size: 80px;
    font-weight: 900;
    line-height: 1;
    color: rgba(0,0,0,0.03);
    z-index: 0;
    pointer-events: none;
    font-family: 'Playfair Display', serif;
    transition: color 0.4s;
}
.hiw-grid-card:hover .hiw-grid-number {
    color: rgba(249,115,22,0.05);
}
</style>

<section class="py-24 bg-slate-50 relative overflow-hidden" data-reveal>
    <div style="max-width:1140px; margin:0 auto; padding:0 20px; position:relative; z-index:10;">
        <div style="text-align:center; margin-bottom:56px;">
            <div style="display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#fff7ed,#fffbeb); border:1px solid rgba(249,115,22,0.2); color:#ea580c; font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; padding:6px 16px; border-radius:100px; margin-bottom:16px;">
                <span class="material-symbols-outlined" style="font-size:14px;">route</span> Our Journey
            </div>
            <h2 class="font-serif text-3xl md:text-5xl font-bold text-slate-900 mb-4">How <span class="text-orange-500">CSNExplore</span> Works</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Follow a seamless journey from dreaming about your destination to sharing unforgettable memories.</p>
        </div>

        <div class="hiw-grid-container">
            <?php
            $hiw_steps = [
                ['icon'=>'travel_explore', 'num'=>'01', 'title'=>'Discover Places', 'desc'=>'Browse verified destinations, heritage sites, and top-rated local attractions.'],
                ['icon'=>'event_note',     'num'=>'02', 'title'=>'Plan Itinerary',  'desc'=>'Get custom travel plans tailored to your budget and specific timeline.'],
                ['icon'=>'compare_arrows', 'num'=>'03', 'title'=>'Compare Options', 'desc'=>'Instantly compare prices across hotels, cabs, and bike rental services.'],
                ['icon'=>'local_taxi',     'num'=>'04', 'title'=>'Book Instantly',  'desc'=>'Reserve everything you need in one unified portal without hidden fees.'],
                ['icon'=>'support_agent',  'num'=>'05', 'title'=>'Expert Support',  'desc'=>'Connect with our local experts who ensure your journey is flawless.'],
                ['icon'=>'luggage',        'num'=>'06', 'title'=>'Enjoy the Trip',  'desc'=>'Travel with absolute confidence alongside our trusted local partners.'],
            ];
            foreach ($hiw_steps as $step):
            ?>
            <div class="hiw-grid-card group">
                <div class="hiw-grid-number"><?php echo $step['num']; ?></div>
                <div class="hiw-grid-icon">
                    <span class="material-symbols-outlined text-[32px] group-hover:text-white transition-colors duration-300" style="font-variation-settings:'FILL' 1;"><?php echo $step['icon']; ?></span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3 relative z-10"><?php echo htmlspecialchars($step['title']); ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed relative z-10"><?php echo htmlspecialchars($step['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section â€” Holographic Glass Grid -->
<style>
.holo-grid { display: grid; grid-template-columns: 1fr; gap: 24px; position: relative; z-index: 10; padding-bottom: 40px; }
@media (min-width: 768px) { .holo-grid { grid-template-columns: repeat(3, 1fr); } }
.holo-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 32px;
    padding: 40px;
    position: relative;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    transform-style: preserve-3d;
}
.holo-card::before {
    content: ''; position: absolute; inset: 0; background: radial-gradient(800px circle at var(--mouse-x) var(--mouse-y), rgba(249,115,22,0.1), transparent 40%); opacity: 0; transition: opacity 0.5s;
}
.holo-card:hover { transform: translateY(-8px); border-color: rgba(249, 115, 22, 0.3); box-shadow: 0 20px 40px rgba(0,0,0,0.2), 0 0 30px rgba(249, 115, 22, 0.1); }
.holo-card:hover::before { opacity: 1; }
.holo-icon-ring {
    width: 72px; height: 72px; border-radius: 20px; display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, rgba(249,115,22,0.15), rgba(249,115,22,0.05));
    border: 1px solid rgba(249,115,22,0.2); margin-bottom: 32px;
}
.holo-number { position: absolute; top: -10px; right: -10px; font-size: 140px; font-weight: 900; line-height: 1; color: rgba(255,255,255,0.03); z-index: -1; pointer-events: none; font-family: 'Playfair Display', serif; }
</style>
<section class="py-24 relative overflow-hidden bg-slate-900" data-reveal>
    <!-- Background Accents -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-orange-600/10 rounded-full blur-[100px] mix-blend-screen pointer-events-none"></div>
    </div>
    
    <div class="max-w-[1140px] mx-auto px-5 relative z-10">
        <div style="text-align:center; margin-bottom:60px;">
            <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fdba74; font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; padding:6px 16px; border-radius:100px; margin-bottom:16px; backdrop-filter: blur(8px);">
                <span class="material-symbols-outlined" style="font-size:14px;">star</span>
                Why Us
            </div>
            <h2 class="font-serif text-3xl md:text-5xl font-bold text-white mb-4"><?php echo htmlspecialchars($hp_settings['hp_wcu_title'] ?? 'Why Choose CSNExplore'); ?></h2>
            <p class="text-slate-400 text-sm md:text-base max-w-xl mx-auto"><?php echo htmlspecialchars($hp_settings['hp_wcu_subtext'] ?? 'We are a local, verified, and premium booking portal dedicated exclusively to Chhatrapati Sambhajinagar.'); ?></p>
        </div>

        <div class="holo-grid" id="holo-grid-container">
            <!-- Feature 1 -->
            <div class="holo-card">
                <div class="holo-number">01</div>
                <div class="holo-icon-ring">
                    <span class="material-symbols-outlined text-orange-400 text-3xl"><?php echo htmlspecialchars($hp_settings['hp_wcu_f1_icon'] ?? 'verified_user'); ?></span>
                </div>
                <h3 class="text-white text-xl font-bold mb-3"><?php echo htmlspecialchars($hp_settings['hp_wcu_f1_title'] ?? '100% Verified Listings'); ?></h3>
                <p class="text-slate-400 text-sm leading-relaxed"><?php echo htmlspecialchars($hp_settings['hp_wcu_f1_desc'] ?? 'Every hotel, vehicle, guide, and experience is carefully verified to ensure trusted quality, safety, and reliability.'); ?></p>
            </div>

            <!-- Feature 2 -->
            <div class="holo-card">
                <div class="holo-number">02</div>
                <div class="holo-icon-ring" style="background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05)); border-color: rgba(59,130,246,0.2);">
                    <span class="material-symbols-outlined text-blue-400 text-3xl"><?php echo htmlspecialchars($hp_settings['hp_wcu_f2_icon'] ?? 'sell'); ?></span>
                </div>
                <h3 class="text-white text-xl font-bold mb-3"><?php echo htmlspecialchars($hp_settings['hp_wcu_f2_title'] ?? 'Best Price Guarantee'); ?></h3>
                <p class="text-slate-400 text-sm leading-relaxed"><?php echo htmlspecialchars($hp_settings['hp_wcu_f2_desc'] ?? 'Book directly with local partners for transparent pricing, exclusive deals, and zero hidden charges.'); ?></p>
            </div>

            <!-- Feature 3 -->
            <div class="holo-card">
                <div class="holo-number">03</div>
                <div class="holo-icon-ring" style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05)); border-color: rgba(16,185,129,0.2);">
                    <span class="material-symbols-outlined text-emerald-400 text-3xl"><?php echo htmlspecialchars($hp_settings['hp_wcu_f3_icon'] ?? 'support_agent'); ?></span>
                </div>
                <h3 class="text-white text-xl font-bold mb-3"><?php echo htmlspecialchars($hp_settings['hp_wcu_f3_title'] ?? '24/7 Local Support'); ?></h3>
                <p class="text-slate-400 text-sm leading-relaxed"><?php echo htmlspecialchars($hp_settings['hp_wcu_f3_desc'] ?? 'Receive fast assistance from our local support team before, during, and after your trip.'); ?></p>
            </div>
        </div>
    </div>
</section>
<script>
// Mouse tracking for holo cards glow effect
document.getElementById('holo-grid-container')?.addEventListener('mousemove', function(e) {
    for(const card of document.querySelectorAll('.holo-card')) {
        const rect = card.getBoundingClientRect(), x = e.clientX - rect.left, y = e.clientY - rect.top;
        card.style.setProperty('--mouse-x', `${x}px`);
        card.style.setProperty('--mouse-y', `${y}px`);
    }
});
</script>

<!-- Testimonials Section â€” Cinematic Glass Wall -->
<style>
.testi-wall { display: grid; grid-template-columns: 1fr; gap: 24px; position: relative; z-index: 10; padding-bottom: 40px; }
@media (min-width: 768px) { .testi-wall { grid-template-columns: repeat(3, 1fr); align-items: start; } }
.testi-glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 1);
    border-radius: 32px;
    padding: 32px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.03);
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.testi-glass-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
.testi-quote-mark { font-size: 80px; font-family: 'Playfair Display', serif; color: rgba(249,115,22,0.15); line-height: 0; position: absolute; top: 50px; left: 24px; }
</style>
<section class="py-24 relative overflow-hidden bg-slate-50" data-reveal>
    <div class="absolute inset-0 z-0">
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-100 rounded-full blur-[100px] opacity-50 pointer-events-none"></div>
    </div>
    
    <div class="max-w-[1140px] mx-auto px-5 relative z-10">
        <div style="text-align:center; margin-bottom:60px;">
            <div style="display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#fff7ed,#fffbeb); border:1px solid rgba(249,115,22,0.2); color:#ea580c; font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; padding:6px 16px; border-radius:100px; margin-bottom:16px;">
                <span class="material-symbols-outlined" style="font-size:14px; font-variation-settings:'FILL' 1;">star</span>
                Testimonials
            </div>
            <h2 class="font-serif text-3xl md:text-5xl font-bold text-slate-900 mb-4"><?php echo htmlspecialchars($hp_settings['hp_testi_title'] ?? 'What Our Travelers Say'); ?></h2>
        </div>
        
        <div class="testi-wall">
            <!-- Review 1 -->
            <div class="testi-glass-card" style="transform: translateY(20px);">
                <div class="testi-quote-mark">"</div>
                <div class="flex items-center gap-1 mb-6">
                    <?php for($s=0;$s<5;$s++): ?><span class="material-symbols-outlined text-orange-400 text-xl" style="font-variation-settings:'FILL' 1;">star</span><?php endfor; ?>
                </div>
                <p class="text-slate-700 text-[15px] leading-relaxed mb-8 relative z-10 font-medium">"<?php echo htmlspecialchars($hp_settings['hp_testi_r1_text'] ?? 'CSNExplore made our trip to Ajanta and Ellora completely hassle-free. We rented a Swift for 2 days and the process was buttery smooth. The driver was polite and knew all the local food spots!'); ?>"</p>
                <div class="flex items-center gap-4 border-t border-slate-200/50 pt-5">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-amber-400 text-white flex items-center justify-center font-bold text-lg shadow-inner"><?php echo substr($hp_settings['hp_testi_r1_name'] ?? 'Rahul Sharma', 0, 1); ?></div>
                    <div>
                        <div class="font-bold text-slate-900"><?php echo htmlspecialchars($hp_settings['hp_testi_r1_name'] ?? 'Rahul Sharma'); ?></div>
                        <div class="text-xs text-slate-500 font-medium uppercase tracking-wider"><?php echo htmlspecialchars($hp_settings['hp_testi_r1_loc'] ?? 'Travelled from Mumbai'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="testi-glass-card" style="transform: translateY(-10px);">
                <div class="testi-quote-mark">"</div>
                <div class="flex items-center gap-1 mb-6">
                    <?php for($s=0;$s<5;$s++): ?><span class="material-symbols-outlined text-orange-400 text-xl" style="font-variation-settings:'FILL' 1;">star</span><?php endfor; ?>
                </div>
                <p class="text-slate-700 text-[15px] leading-relaxed mb-8 relative z-10 font-medium">"<?php echo htmlspecialchars($hp_settings['hp_testi_r2_text'] ?? 'Booked a premium stay near the station. The rates on CSNExplore were genuinely cheaper than other major OTAs. Highly recommended for anyone visiting Chhatrapati Sambhajinagar.'); ?>"</p>
                <div class="flex items-center gap-4 border-t border-slate-200/50 pt-5">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-cyan-400 text-white flex items-center justify-center font-bold text-lg shadow-inner"><?php echo substr($hp_settings['hp_testi_r2_name'] ?? 'Ananya Desai', 0, 1); ?></div>
                    <div>
                        <div class="font-bold text-slate-900"><?php echo htmlspecialchars($hp_settings['hp_testi_r2_name'] ?? 'Ananya Desai'); ?></div>
                        <div class="text-xs text-slate-500 font-medium uppercase tracking-wider"><?php echo htmlspecialchars($hp_settings['hp_testi_r2_loc'] ?? 'Travelled from Pune'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="testi-glass-card" style="transform: translateY(30px);">
                <div class="testi-quote-mark">"</div>
                <div class="flex items-center gap-1 mb-6">
                    <?php for($s=0;$s<5;$s++): ?><span class="material-symbols-outlined text-orange-400 text-xl" style="font-variation-settings:'FILL' 1;">star</span><?php endfor; ?>
                </div>
                <p class="text-slate-700 text-[15px] leading-relaxed mb-8 relative z-10 font-medium">"<?php echo htmlspecialchars($hp_settings['hp_testi_r3_text'] ?? 'The bike rental service was a lifesaver! Rented a scooter to roam around the city. Transparent pricing and the vehicle was in great condition. Will definitely use again.'); ?>"</p>
                <div class="flex items-center gap-4 border-t border-slate-200/50 pt-5">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-400 text-white flex items-center justify-center font-bold text-lg shadow-inner"><?php echo substr($hp_settings['hp_testi_r3_name'] ?? 'Vikram Singh', 0, 1); ?></div>
                    <div>
                        <div class="font-bold text-slate-900"><?php echo htmlspecialchars($hp_settings['hp_testi_r3_name'] ?? 'Vikram Singh'); ?></div>
                        <div class="text-xs text-slate-500 font-medium uppercase tracking-wider"><?php echo htmlspecialchars($hp_settings['hp_testi_r3_loc'] ?? 'Travelled from Delhi'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Curated Itineraries Section â€” Minimalist Carousel Redesign -->
<section class="py-24 relative overflow-hidden bg-white" data-reveal>
    <div class="max-w-[1140px] mx-auto px-5 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <h2 class="font-serif text-3xl md:text-5xl text-slate-900 font-bold leading-tight mb-4"><?php echo htmlspecialchars($hp_settings['hp_itin_title'] ?? 'Curated Itineraries'); ?></h2>
                <p class="text-slate-500 text-sm md:text-base max-w-xl"><?php echo htmlspecialchars($hp_settings['hp_itin_subtext'] ?? 'Expertly crafted travel plans designed to help you make the most of your time in Chhatrapati Sambhajinagar.'); ?></p>
            </div>
            <a href="<?php echo BASE_PATH; ?>/suggestor" class="inline-flex items-center gap-2 text-primary font-bold hover:text-orange-600 transition-colors">
                View all plans <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </a>
        </div>
        
        <div id="itin-scroll" style="display:flex; overflow-x:auto; scroll-snap-type:x mandatory; gap:24px; padding-bottom:32px; align-items:stretch; scroll-behavior: smooth;" class="hide-scrollbar">
            <!-- Itinerary 1 -->
            <a href="<?php echo BASE_PATH; ?>/suggestor" class="group flex-shrink-0 w-[85vw] md:w-[400px] lg:w-[450px] scroll-snap-align-center bg-slate-50 rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-4 hover:scale-[1.03] hover:border-orange-200">
                <div class="relative h-[240px] overflow-hidden">
                    <img src="<?php echo BASE_PATH; ?>/images/hotel-hero-section-4.webp" alt="Ajanta Ellora Tour" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full shadow-sm">
                        <?php echo htmlspecialchars($hp_settings['hp_itin_i1_dur'] ?? '2 Days'); ?>
                    </div>
                </div>
                <div class="p-8">
                    <div class="text-orange-500 text-sm font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Ajanta Ellora Tour
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 font-serif"><?php echo htmlspecialchars($hp_settings['hp_itin_i1_title'] ?? "The Cave Explorer's Trail"); ?></h3>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2"><?php echo htmlspecialchars($hp_settings['hp_itin_i1_desc'] ?? 'Cover the magnificent Ajanta and Ellora caves in a packed two-day weekend itinerary.'); ?></p>
                    <div class="flex items-center text-slate-400 text-xs font-medium uppercase tracking-wider gap-1">
                        <span class="material-symbols-outlined text-base">map</span> Ajanta &middot; Ellora
                    </div>
                </div>
            </a>
            
            <!-- Itinerary 2 -->
            <a href="<?php echo BASE_PATH; ?>/suggestor" class="group flex-shrink-0 w-[85vw] md:w-[400px] lg:w-[450px] scroll-snap-align-center bg-slate-50 rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-4 hover:scale-[1.03] hover:border-orange-200">
                <div class="relative h-[240px] overflow-hidden">
                    <img src="<?php echo BASE_PATH; ?>/images/bike%20rentals-hero-section%20(6).webp" alt="Historical Forts" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full shadow-sm">
                        <?php echo htmlspecialchars($hp_settings['hp_itin_i2_dur'] ?? '1 Day'); ?>
                    </div>
                </div>
                <div class="p-8">
                    <div class="text-blue-500 text-sm font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Historical Forts
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 font-serif"><?php echo htmlspecialchars($hp_settings['hp_itin_i2_title'] ?? 'Forts & Heritage Run'); ?></h3>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2"><?php echo htmlspecialchars($hp_settings['hp_itin_i2_desc'] ?? 'An adventurous day exploring Daulatabad Fort and the historic 52 gates of the city.'); ?></p>
                    <div class="flex items-center text-slate-400 text-xs font-medium uppercase tracking-wider gap-1">
                        <span class="material-symbols-outlined text-base">map</span> Daulatabad Fort &middot; City Gates
                    </div>
                </div>
            </a>
            
            <!-- Itinerary 3 -->
            <a href="<?php echo BASE_PATH; ?>/suggestor" class="group flex-shrink-0 w-[85vw] md:w-[400px] lg:w-[450px] scroll-snap-align-center bg-slate-50 rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-4 hover:scale-[1.03] hover:border-orange-200">
                <div class="relative h-[240px] overflow-hidden">
                    <img src="<?php echo BASE_PATH; ?>/images/car-rental-hero-section%20(3).webp" alt="Culinary Tour" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full shadow-sm">
                        <?php echo htmlspecialchars($hp_settings['hp_itin_i3_dur'] ?? 'Evening'); ?>
                    </div>
                </div>
                <div class="p-8">
                    <div class="text-purple-500 text-sm font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Culinary Tour
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 font-serif"><?php echo htmlspecialchars($hp_settings['hp_itin_i3_title'] ?? 'Mughlai Culinary Walk'); ?></h3>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2"><?php echo htmlspecialchars($hp_settings['hp_itin_i3_desc'] ?? 'Taste the authentic Naan Qalia and street food delights in the old city streets.'); ?></p>
                    <div class="flex items-center text-slate-400 text-xs font-medium uppercase tracking-wider gap-1">
                        <span class="material-symbols-outlined text-base">map</span> Nirala Bazaar &middot; Old City
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>



</main>

<!-- About Chhatrapati Sambhajinagar Tourism (SEO Content) â€” Clean Bento Box Redesign -->
<section class="py-24 relative overflow-hidden bg-slate-50 border-t border-slate-200" data-reveal>
    <div class="max-w-[1140px] mx-auto px-5 relative z-10">
        
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            
            <!-- Bento Item 1: Main Title & Intro -->
            <div class="md:col-span-2 lg:col-span-3 bg-white rounded-3xl p-8 md:p-12 relative overflow-hidden border border-slate-200 shadow-[0_8px_30px_rgba(0,0,0,0.04)] flex flex-col justify-center transition-all duration-500 hover:-translate-y-3 hover:shadow-[0_20px_50px_rgba(236,91,19,0.15)] group">
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 border border-slate-200 mb-6">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        <span class="text-slate-600 text-xs font-bold uppercase tracking-widest">Premium Portal</span>
                    </div>
                    <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6 text-slate-900">
                        The Ultimate <br>
                        <span class="text-orange-600">Aurangabad Tourism</span> <br>
                        & Rental Portal
                    </h2>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed max-w-2xl font-medium">
                        Welcome to CSNExplore, your premium gateway to the historic wonders of Maharashtra. Whether you are planning a comprehensive budget tour or exploring the city over a long weekend, our platform brings everything into one unified dashboard.
                    </p>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed max-w-2xl font-medium mt-3">
                        From finding the best budget car rentals to booking verified luxury stays, we ensure your journey to Ajanta and Ellora Caves is entirely seamless and worry-free.
                    </p>
                </div>
            </div>

            <!-- Bento Item 2: Highlight Stat -->
            <div class="bg-gradient-to-br from-orange-500 to-amber-500 rounded-3xl p-8 flex flex-col justify-center items-center text-center shadow-[0_8px_30px_rgba(249,115,22,0.2)] transition-all duration-500 hover:scale-105 hover:shadow-[0_20px_40px_rgba(249,115,22,0.4)]">
                <span class="material-symbols-outlined text-white text-5xl mb-4">verified</span>
                <h3 class="text-white font-bold text-4xl mb-2">100+</h3>
                <p class="text-white/90 text-sm font-bold uppercase tracking-wider">Verified<br>Partners</p>
                <p class="text-white/70 text-xs mt-3">Trusted local hotels, rentals, and guides.</p>
            </div>

            <!-- Bento Item 3: Tags / Popular Searches -->
            <div class="md:col-span-3 lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)]">
                <h3 class="text-slate-900 font-bold text-lg mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-500 bg-orange-50 p-2 rounded-xl">trending_up</span>
                    Popular Searches
                </h3>
                <div class="flex flex-wrap gap-2.5">
                    <?php
                    $tags = [
                        'Hotels CSNExplore', 'Jain Hotels', 'Safe Stays', 
                        'Car Rentals', 'Cab Booking', 'Self Drive Cars',
                        'Bike Rental', 'Ajanta Caves Tour', 'Daulatabad Fort'
                    ];
                    foreach ($tags as $tag) {
                        echo '<span class="bg-slate-50 hover:bg-orange-50 hover:text-orange-600 transition-colors border border-slate-200 text-slate-700 px-4 py-2 rounded-full text-xs font-bold cursor-default shadow-sm">'.htmlspecialchars($tag).'</span>';
                    }
                    ?>
                </div>
            </div>

            <!-- Bento Item 4: Safety & Support -->
            <div class="md:col-span-3 lg:col-span-2 bg-white rounded-3xl p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-200 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-16 h-16 shrink-0 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100">
                    <span class="material-symbols-outlined text-blue-500 text-3xl">security</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900">Safe & Reliable Travel</h3>
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">
                        Curated options for solo female travelers. Enjoy 24/7 local support and detailed guides to uncover hidden gems across the Deccan plateau.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Extreme SEO FAQ Section for Exact Match Queries â€” Clean Light Redesign -->
<section class="max-w-4xl mx-auto px-6 py-16 mb-12" data-reveal>
    <div class="text-center mb-12">
        <h2 class="font-serif text-3xl md:text-4xl font-bold text-slate-900 mb-4">
            Frequently Asked Questions
        </h2>
        <p class="text-slate-500">Everything you need to know about visiting Chhatrapati Sambhajinagar.</p>
    </div>
    
    <style>
        .faq-clean details > summary { list-style: none; outline: none; }
        .faq-clean details > summary::-webkit-details-marker { display: none; }
        .faq-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }
        .faq-item:hover { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border-color: #cbd5e1; }
    </style>
    
    <div class="faq-clean">
        <!-- FAQ 1 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>How do I reach Chhatrapati Sambhajinagar (Aurangabad)?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    Chhatrapati Sambhajinagar is well-connected by air, train, and road. The city has its own airport (IXU) with daily flights from Mumbai, Delhi, and Hyderabad. By train, it connects to major cities, or you can take a bus/cab from Pune (approx. 5-6 hours) or Mumbai. CSNExplore offers reliable airport transfers and outstation cabs to your hotel upon arrival.
                </p>
            </div>
        </details>
        <!-- FAQ 2 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>Which days are the Ajanta and Ellora Caves closed?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    This is crucial for your itinerary: Ajanta Caves are closed every Monday, and Ellora Caves are closed every Tuesday. Never schedule Ajanta on a Monday or Ellora on a Tuesday. The CSNExplore travel dashboard automatically factors these closure days into our curated 2-day and 3-day itinerary packages.
                </p>
            </div>
        </details>
        <!-- FAQ 3 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>What are the timings and entry fees for Ajanta and Ellora Caves?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    Ajanta is open from 9:00 AM to 5:00 PM, while Ellora opens earlier from 6:00 AM to 6:00 PM. For Indian and SAARC tourists, tickets are â‚¹40 offline (â‚¹35 if booked online via ASI). For foreign tourists, it is â‚¹600 (â‚¹550 online). Children under 15 enter for free. You can easily rent an Activa or book a cab through CSNExplore to reach the caves right when they open.
                </p>
            </div>
        </details>
        <!-- FAQ 4 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>How far are the Ajanta and Ellora Caves from the city center?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    Ellora is about 30 km away (a 45-minute drive), making it easy to combine with Daulatabad Fort and the Grishneshwar Jyotirlinga. Ajanta is much furtherâ€”about 100 km away, taking 2.5 to 3 hours by road. Because of the distance, we strongly recommend giving Ajanta its own full day. Book a comfortable AC cab through CSNExplore for the long drive to Ajanta.
                </p>
            </div>
        </details>
        <!-- FAQ 5 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>What is the best time of year to visit Chhatrapati Sambhajinagar?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    The most comfortable time is Winter (October to March) when temperatures range from 10Â°C to 25Â°C. Monsoon (July to September) is also popular, as the hills turn lush green and waterfalls cascade over the caves. Summer (April to June) gets exceedingly hot (up to 45Â°C), so if you visit then, start your cave tours right at sunrise.
                </p>
            </div>
        </details>
        <!-- FAQ 6 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>What should I wear and pack for the cave tours?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    Wear slip-on shoes or sandals, as you must remove your footwear before entering several of the cave temples (especially the ones with active shrines or delicate floors). Pack a hat, sunglasses, plenty of water, and an umbrella if visiting in summer. Flash photography is strictly prohibited inside the caves to protect the ancient frescoes.
                </p>
            </div>
        </details>
        <!-- FAQ 7 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>What are the top attractions inside the city besides the caves?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    Inside the city, you must visit Bibi Ka Maqbara (the 'Taj of the Deccan'), the Aurangabad Caves, and the historic Panchakki water mill. A quick scooter rental from CSNExplore is the cheapest and fastest way to cover these local city monuments in a single afternoon.
                </p>
            </div>
        </details>
        <!-- FAQ 8 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>What local food is Chhatrapati Sambhajinagar famous for?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    The city is famous for its Mughal and Hyderabadi influences. You must try Naan Qalia (a rich spiced mutton curry), traditional Maharashtrian Thalis, and local street food in Nirala Bazaar. Ask your CSNExplore cab driver for their favorite local dining spots!
                </p>
            </div>
        </details>
        <!-- FAQ 9 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>What is the best thing to buy in Aurangabad?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    The region is renowned for its textiles. The top buys are authentic Paithani silk sarees (woven with real gold zari) and unique Himroo brocade shawls. Bidri pottery and metalwork are also excellent souvenirs.
                </p>
            </div>
        </details>
        <!-- FAQ 10 -->
        <details class="group faq-item px-6 py-5 cursor-pointer">
            <summary class="font-bold text-[15px] md:text-lg text-slate-900 flex justify-between items-center select-none">
                <span>How do I plan a budget trip to Chhatrapati Sambhajinagar?</span>
                <span class="shrink-0 ml-4 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-orange-500 group-open:rotate-45 transition-transform duration-300">
                    <span class="material-symbols-outlined text-xl">add</span>
                </span>
            </summary>
            <div class="pt-4 mt-3 border-t border-slate-100">
                <p class="text-slate-600 text-sm md:text-base leading-relaxed">
                    To keep costs low, book your stay at backpacker hostels or dormitories near the central bus stand via CSNExplore. Instead of hiring a guide for the whole day, download ASI audio guides, eat at local Maharashtrian dhabas, and use CSNExplore to rent an Activa for local city sights or share a cab ride to the caves.
                </p>
            </div>
        </details>
    </div>
</section>

<!-- Detailed FAQPage JSON-LD Schema for CSNExplore -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How do I reach Chhatrapati Sambhajinagar (Aurangabad)?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Chhatrapati Sambhajinagar is well-connected by air, train, and road. The city has its own airport (IXU) with daily flights from Mumbai, Delhi, and Hyderabad. By train, it connects to major cities, or you can take a bus/cab from Pune (approx. 5-6 hours) or Mumbai. CSNExplore offers reliable airport transfers and outstation cabs to your hotel upon arrival."
      }
    },
    {
      "@type": "Question",
      "name": "Which days are the Ajanta and Ellora Caves closed?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "This is crucial for your itinerary: Ajanta Caves are closed every Monday, and Ellora Caves are closed every Tuesday. Never schedule Ajanta on a Monday or Ellora on a Tuesday. The CSNExplore travel dashboard automatically factors these closure days into our curated 2-day and 3-day itinerary packages."
      }
    },
    {
      "@type": "Question",
      "name": "What are the timings and entry fees for Ajanta and Ellora Caves?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ajanta is open from 9:00 AM to 5:00 PM, while Ellora opens earlier from 6:00 AM to 6:00 PM. For Indian and SAARC tourists, tickets are â‚¹40 offline (â‚¹35 if booked online via ASI). For foreign tourists, it is â‚¹600 (â‚¹550 online). Children under 15 enter for free. You can easily rent an Activa or book a cab through CSNExplore to reach the caves right when they open."
      }
    },
    {
      "@type": "Question",
      "name": "How far are the Ajanta and Ellora Caves from the city center?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ellora is about 30 km away (a 45-minute drive), making it easy to combine with Daulatabad Fort and the Grishneshwar Jyotirlinga. Ajanta is much furtherâ€”about 100 km away, taking 2.5 to 3 hours by road. Because of the distance, we strongly recommend giving Ajanta its own full day. Book a comfortable AC cab through CSNExplore for the long drive to Ajanta."
      }
    },
    {
      "@type": "Question",
      "name": "What is the best time of year to visit Chhatrapati Sambhajinagar?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The most comfortable time is Winter (October to March) when temperatures range from 10Â°C to 25Â°C. Monsoon (July to September) is also popular, as the hills turn lush green and waterfalls cascade over the caves. Summer (April to June) gets exceedingly hot (up to 45Â°C), so if you visit then, start your cave tours right at sunrise."
      }
    },
    {
      "@type": "Question",
      "name": "What should I wear and pack for the cave tours?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Wear slip-on shoes or sandals, as you must remove your footwear before entering several of the cave temples (especially the ones with active shrines or delicate floors). Pack a hat, sunglasses, plenty of water, and an umbrella if visiting in summer. Flash photography is strictly prohibited inside the caves to protect the ancient frescoes."
      }
    },
    {
      "@type": "Question",
      "name": "What are the top attractions inside the city besides the caves?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Inside the city, you must visit Bibi Ka Maqbara (the 'Taj of the Deccan'), the Aurangabad Caves, and the historic Panchakki water mill. A quick scooter rental from CSNExplore is the cheapest and fastest way to cover these local city monuments in a single afternoon."
      }
    },
    {
      "@type": "Question",
      "name": "What local food is Chhatrapati Sambhajinagar famous for?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The city is famous for its Mughal and Hyderabadi influences. You must try Naan Qalia (a rich spiced mutton curry), traditional Maharashtrian Thalis, and local street food in Nirala Bazaar. Ask your CSNExplore cab driver for their favorite local dining spots!"
      }
    },
    {
      "@type": "Question",
      "name": "What is the best thing to buy in Aurangabad?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The region is renowned for its textiles. The top buys are authentic Paithani silk sarees (woven with real gold zari) and unique Himroo brocade shawls. Bidri pottery and metalwork are also excellent souvenirs."
      }
    },
    {
      "@type": "Question",
      "name": "How do I plan a budget trip to Chhatrapati Sambhajinagar?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "To keep costs low, book your stay at backpacker hostels or dormitories near the central bus stand via CSNExplore. Instead of hiring a guide for the whole day, download ASI audio guides, eat at local Maharashtrian dhabas, and use CSNExplore to rent an Activa for local city sights or share a cab ride to the caves."
      }
    }
  ]
}
</script>

<?php require 'footer.php'; ?>

<!-- Infinite carousel with seamless loop -->
<script>
(function(){
    var sections = ['stays','cars','bikes','attractions','restaurants','buses','blogs'];
    sections.forEach(function(s) {
        var el = document.getElementById('carousel-wrap-' + s);
        var track = document.getElementById('carousel-track-' + s);
        if(!el || !track) return;
        
        // Clone nodes 2x for infinite loop effect (keeps initial HTML payload small)
        var originalChildren = Array.from(track.children);
        if (originalChildren.length > 0) {
            for(var i=0; i<2; i++) {
                originalChildren.forEach(function(child) {
                    track.appendChild(child.cloneNode(true));
                });
            }
        }
        
        var isPaused = false;
        var isDown = false;
        var speed = 0.5;
        var currentX = 0;
        
        function getSetWidth() { return track.scrollWidth / 3; }
        var oneSetWidth = getSetWidth();
        
        currentX = oneSetWidth;
        el.scrollLeft = currentX;
        
        var isVisible = false;
        function loop() {
            if (!isPaused && !isDown && isVisible) {
                currentX += speed;
                if (currentX >= oneSetWidth * 2) {
                    currentX = oneSetWidth;
                }
                el.scrollLeft = currentX;
            }
            if (isVisible) requestAnimationFrame(loop);
        }
        
        var observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                isVisible = true;
                loop();
            } else {
                isVisible = false;
            }
        }, { threshold: 0 });
        observer.observe(el);
        
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

        // Initialize (loop is now triggered by IntersectionObserver)
        setTimeout(function() {
            oneSetWidth = getSetWidth();
            currentX = oneSetWidth;
            el.scrollLeft = currentX;
        }, 500);
        
        window.addEventListener('resize', function() {
            oneSetWidth = getSetWidth();
        });
    });
})();
</script>

<!-- Flatpickr: Load on-demand only when user focuses a date field (saves 20KB on initial load) -->
<script>
(function(){
    var _fp_loaded = false;
    var _fp_loading = false;
    function loadFlatpickr() {
        if (_fp_loaded) { if(typeof initFlatpickr==='function') initFlatpickr(); return; }
        if (_fp_loading) return;
        _fp_loading = true;
        // Load CSS
        var css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/dark.css';
        document.head.appendChild(css);
        // Load JS
        var js = document.createElement('script');
        js.src = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js';
        js.onload = function() { _fp_loaded = true; if(typeof initFlatpickr==='function') initFlatpickr(); };
        document.head.appendChild(js);
    }
    // Attach to all date inputs â€” load only on first focus
    document.querySelectorAll('.date-field input').forEach(function(el) {
        el.addEventListener('focus', loadFlatpickr, { once: true, passive: true });
        el.addEventListener('touchstart', loadFlatpickr, { once: true, passive: true });
    });
    // Also preload after 8 seconds idle (guarantees it loads eventually)
    setTimeout(loadFlatpickr, 15000);
})();
</script>

</body>
</html>


