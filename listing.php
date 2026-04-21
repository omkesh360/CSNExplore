<?php
// listing.php – Unified listing page for all categories
require_once 'php/config.php';

$type = $_GET['type'] ?? 'stays';
$allowed = ['stays','cars','bikes','attractions','restaurants','buses'];
if (!in_array($type, $allowed)) $type = 'stays';

// ── Fetch from DB ─────────────────────────────────────────────────────────────
$db = getDB();
$search     = trim($_GET['search'] ?? '');
$filterType = trim($_GET['filter_type'] ?? '');

$priceCol = ['stays'=>'price_per_night','cars'=>'price_per_day','bikes'=>'price_per_day',
             'restaurants'=>'price_per_person','attractions'=>'entry_fee','buses'=>'price'];
$pc = $priceCol[$type];

$where  = ['is_active = 1'];
$params = [];
if ($filterType) { $where[] = 'type = ?'; $params[] = $filterType; }
if ($search) { $where[] = 'name LIKE ?'; $params[] = '%' . $search . '%'; }

$sql = "SELECT * FROM $type WHERE " . implode(' AND ', $where) . " ORDER BY display_order ASC, rating DESC, id ASC";
$items = $db->fetchAll($sql, $params);

// Decode JSON fields
foreach ($items as &$item) {
    foreach (['amenities','features','gallery','menu_highlights'] as $f) {
        if (isset($item[$f]) && is_string($item[$f])) {
            $item[$f] = json_decode($item[$f], true) ?: [];
        }
    }
}
unset($item);

$config = [
  'stays' => [
    'title'    => 'Hotel Stays | CSNExplore',
    'heading'  => 'Hotels in Chhatrapati Sambhajinagar',
    'icon'     => 'bed',
    'label'    => 'Stays',
    'unit'     => '/ night',
    'cta'      => 'Check Availability',
    'hero_bg'  => BASE_PATH . '/images/hotel-hero-section%20(4).webp',
    'hero_h1'  => 'Find Your Perfect Stay',
    'hero_sub' => 'Hotels, resorts & homestays in Chhatrapati Sambhajinagar.',
    'filters'  => ['Luxury Hotel','Heritage Hotel','Budget Hotel','Resort','Homestay','Hostel','Guesthouse','Business Hotel'],
    'filter_label' => 'Property Type',
    'promo'    => 'First hotel booking with CSNExplore',
  ],
  'cars' => [
    'title'    => 'Car Rentals | CSNExplore',
    'heading'  => 'Cars in Chhatrapati Sambhajinagar',
    'icon'     => 'directions_car',
    'label'    => 'Car Rentals',
    'unit'     => '/ day',
    'cta'      => 'Check Availability',
    'hero_bg'  => BASE_PATH . '/images/car-rental-hero-section%20(3).webp',
    'hero_h1'  => 'Rent a Car Your Way',
    'hero_sub' => 'Self-drive or with driver — explore Maharashtra at your pace.',
    'filters'  => ['Sedan','SUV','Compact SUV','Premium SUV','MUV','Hatchback'],
    'filter_label' => 'Vehicle Type',
    'promo'    => 'First car rental with CSNExplore',
  ],
  'bikes' => [
    'title'    => 'Bike Rentals | CSNExplore',
    'heading'  => 'Bikes in Chhatrapati Sambhajinagar',
    'icon'     => 'motorcycle',
    'label'    => 'Bike Rentals',
    'unit'     => '/ day',
    'cta'      => 'Check Availability',
    'hero_bg'  => BASE_PATH . '/images/bike%20rentals-hero-section%20(6).webp',
    'hero_h1'  => 'Ride the Open Road',
    'hero_sub' => 'Scooters, cruisers & sports bikes for every trail.',
    'filters'  => ['Scooter','Cruiser','Sports Bike','Street Bike','Commuter','Naked Bike','Adventure','Electric Bike','Bicycle'],
    'filter_label' => 'Bike Type',
    'promo'    => 'Free cancellation on bike rentals',
  ],
  'attractions' => [
    'title'    => 'Attractions | CSNExplore',
    'heading'  => 'Attractions near Chhatrapati Sambhajinagar',
    'icon'     => 'confirmation_number',
    'label'    => 'Attractions',
    'unit'     => ' entry',
    'cta'      => 'Check Availability',
    'hero_bg'  => BASE_PATH . '/images/attractions-hero-section%20(7).webp',
    'hero_h1'  => 'Explore Ancient Marvels',
    'hero_sub' => 'Forts, caves, temples & UNESCO heritage sites await.',
    'filters'  => ['UNESCO Heritage','Historical Monument','Historical Fort','Heritage Site','Religious Site','Nature & Wildlife','Garden & Zoo','Historical Caves','Museum','Garden'],
    'filter_label' => 'Category',
    'promo'    => 'Verified guides for Ajanta & Ellora',
  ],
  'restaurants' => [
    'title'    => 'Restaurants | CSNExplore',
    'heading'  => 'Restaurants in Chhatrapati Sambhajinagar',
    'icon'     => 'restaurant',
    'label'    => 'Restaurants',
    'unit'     => ' for two',
    'cta'      => 'Check Availability',
    'hero_bg'  => BASE_PATH . '/images/dine-hero-section%20(1).webp',
    'hero_h1'  => 'Dine Out Tonight',
    'hero_sub' => 'Best restaurants, cafes & local eateries in the city.',
    'filters'  => ['Traditional','North Indian','South Indian','Biryani Specialist','Multi-cuisine','Vegetarian','Street Food','Fast Food','Fine Dining','Barbecue','Italian','Chinese','Cafe','Seafood','Dessert'],
    'filter_label' => 'Cuisine Type',
    'promo'    => 'Best dining deals in the city',
  ],
  'buses' => [
    'title'    => 'Bus Routes | CSNExplore',
    'heading'  => 'Bus Routes from Chhatrapati Sambhajinagar',
    'icon'     => 'directions_bus',
    'label'    => 'Buses',
    'unit'     => '',
    'cta'      => 'Check Availability',
    'hero_bg'  => BASE_PATH . '/images/bus-hero-section%20(2).webp',
    'hero_h1'  => 'Travel by Bus',
    'hero_sub' => 'Intercity buses on request — call or WhatsApp to book.',
    'filters'  => ['AC Semi-Luxury','AC Sleeper','Volvo AC','AC Seater','Non-AC Seater'],
    'filter_label' => 'Bus Type',
    'promo'    => 'Group discounts available on buses',
  ],
];

$c = $config[$type];
$page_title    = $c['title'];
$current_page  = 'listing.php';
$listing_type  = $type;

// Compute price range for slider
$prices = array_filter(array_column($items, $pc));
$price_min = $prices ? (int)min($prices) : 0;
$price_max = $prices ? (int)max($prices) : 10000;
// Round nicely
$slider_min = (int)(floor($price_min / 100) * 100);
$slider_max = (int)(ceil($price_max / 100) * 100) ?: 10000;

function listingSlug($type, $item) {
    $name = $item['name'] ?? $item['operator'] ?? 'item';
    return BASE_PATH . '/listing-detail/' . generateSlug($type, $item['id'], $name) . '.html';
}

$page_meta = [
    'description' => "Find the best " . strtolower($c['label']) . " in Chhatrapati Sambhajinagar. " . $c['hero_sub'],
    'canonical' => "https://csnexplore.com/listing/" . htmlspecialchars($type),
    'image' => "https://csnexplore.com" . $c['hero_bg'],
    'type' => 'website'
];

$itemListElements = [];
$position = 1;
foreach ($items as $itm) {
    if ($position > 20) break; // limit to top 20 for schema size
    $name = $itm['name'] ?? $itm['operator'] ?? 'item';
    $itemListElements[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "url" => "https://csnexplore.com/listing-detail/" . generateSlug($type, $itm['id'], $name) . ".html"
    ];
}

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "' . $c['title'] . '",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '",
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": ' . json_encode($itemListElements) . '
  }
}
</script>';

$extra_styles = "
  .glassy { background:rgba(255,255,255,0.08); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); border:1px solid rgba(255,255,255,0.15); box-shadow:0 8px 32px rgba(0,0,0,0.1); }
  .glassy:hover { background:rgba(255,255,255,0.12); box-shadow:0 12px 40px rgba(0,0,0,0.15); }
  .hide-scrollbar::-webkit-scrollbar{display:none} .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
  body { background-color:#f8f6f6; }
  .glass-filter { background:rgba(255,255,255,0.06); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.12); }
  .glass-card { background:rgba(255,255,255,0.07); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border:1px solid rgba(255,255,255,0.1); }
  .glass-button { background:rgba(236,91,19,0.85); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.2); }
  .glass-button:hover { background:rgba(236,91,19,0.95); }
  
  /* Smooth Morph Animation Styles */
  #listings-grid { position: relative; transition: height 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
  .listing-card-anim { transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); backface-visibility: hidden; transform-origin: center; opacity: 1; transform: scale(1) translateY(0); }
  .listing-card-anim.fade-out { opacity: 0; transform: scale(0.95) translateY(10px); pointer-events: none; }
  .listing-card-anim.fade-in { opacity: 1; transform: scale(1) translateY(0); }

  /* Sidebar transition */
  #sidebar-filters { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
  /* Desktop: width-based collapse */
  @media (min-width: 1024px) {
    #sidebar-filters.collapsed { width: 0; margin-right: 0; opacity: 0; transform: translateX(-20px); pointer-events: none; }
  }
  /* Mobile/Tablet: height-based collapse */
  @media (max-width: 1023px) {
    #sidebar-filters { width: 100% !important; transform: none !important; max-height: 2000px; }
    #sidebar-filters.collapsed { max-height: 0 !important; opacity: 0 !important; margin-bottom: 0 !important; pointer-events: none !important; }
    #sidebar-filters:not(.collapsed) { max-height: 2000px !important; opacity: 1 !important; margin-bottom: 12px !important; }
  }
  .grid-container-anim { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

  /* Adaptive card footer when filters are open */
  .grid-filtered .card-footer { flex-direction: column; align-items: flex-start; gap: 12px; }
  .grid-filtered .card-footer a { width: 100%; text-align: center; }
  .card-footer { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
";
require_once 'header.php';

$category_nav = [
  ['type'=>'stays',       'icon'=>'bed',                'label'=>'Stays'],
  ['type'=>'cars',        'icon'=>'directions_car',     'label'=>'Cars'],
  ['type'=>'bikes',       'icon'=>'motorcycle',         'label'=>'Bikes'],
  ['type'=>'attractions', 'icon'=>'confirmation_number','label'=>'Attractions'],
  ['type'=>'restaurants', 'icon'=>'restaurant',         'label'=>'Dine'],
  ['type'=>'buses',       'icon'=>'directions_bus',     'label'=>'Buses'],
];
?>
<!-- Category Sub-Nav is in header.php for listing pages -->

<!-- Hero Banner with breadcrumb at top -->
<div class="relative h-52 md:h-72 overflow-hidden">
    <img src="<?php echo htmlspecialchars($c['hero_bg']); ?>"
         alt="<?php echo htmlspecialchars($c['label']); ?>"
         class="w-full h-full object-cover"/>
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
    <!-- Breadcrumb at very top -->
    <div class="absolute top-0 left-0 right-0 pt-5">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold"><?php echo htmlspecialchars($c['label']); ?></span>
        </div>
    </div>
    <!-- Title at bottom -->
    <div class="absolute bottom-0 left-0 right-0 pb-6">
        <div class="max-w-[1140px] mx-auto px-5">
            <h1 class="text-white text-2xl md:text-4xl font-serif font-black flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl"><?php echo htmlspecialchars($c['icon']); ?></span>
                <?php echo htmlspecialchars($c['hero_h1']); ?>
            </h1>
            <p class="text-white/60 text-sm mt-1"><?php echo htmlspecialchars($c['hero_sub']); ?></p>
        </div>
    </div>
</div>

<main class="min-h-screen" style="background:#f8f6f6">
<div class="max-w-[1140px] mx-auto px-3 sm:px-5 py-4 sm:py-8" style="width:100%;box-sizing:border-box;overflow-x:hidden">
    <!-- Page Title & Search Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
      <div>
        <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-900 leading-tight"><?php echo htmlspecialchars($c['heading']); ?></h1>
        <p class="text-slate-500 text-sm font-medium"><?php echo count($items); ?> result<?php echo count($items) !== 1 ? 's' : ''; ?> found</p>
      </div>
      <form method="GET" action="<?php echo BASE_PATH; ?>/listing/<?php echo htmlspecialchars($type); ?>" class="flex items-center gap-2 w-full sm:w-auto">
        <div class="relative flex-1 sm:w-64">
           <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
           <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                  placeholder="Search <?php echo htmlspecialchars($c['label']); ?>..." list="location-list"
                  class="w-full border border-slate-200 rounded-xl pl-12 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 bg-white text-slate-900 transition-all text-ellipsis whitespace-nowrap overflow-hidden"/>
        </div>
        <button type="submit" class="px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-orange-600 transition-all shadow active:scale-95">Search</button>
      </form>
    </div>

    <!-- Filter & Sort Bar (Spans full width above content) -->
    <div class="mb-3 flex flex-col sm:flex-row items-center justify-between bg-white px-3 sm:px-4 py-2.5 sm:py-3 rounded-2xl border border-slate-100 shadow-sm gap-2 sm:gap-3">
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <button onclick="document.getElementById('sidebar-filters').classList.toggle('collapsed'); document.getElementById('listings-wrapper').classList.toggle('grid-filtered')" 
                class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all shadow active:scale-95 group">
          <span class="material-symbols-outlined text-base group-hover:rotate-12 transition-transform">tune</span>
          Filters
          <span id="active-filter-badge" class="hidden ml-1 size-4 bg-primary text-white text-[9px] rounded-full flex items-center justify-center border-2 border-slate-900">0</span>
        </button>
        <button onclick="resetFilters()" class="px-3 py-2.5 text-slate-400 hover:text-primary transition-colors text-sm font-bold">Clear</button>
        <!-- Sort inline on mobile -->
        <div class="flex items-center gap-1.5 flex-1 sm:hidden bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
           <span class="material-symbols-outlined text-slate-400 text-base">sort</span>
           <select onchange="applyFilters(this.value)" class="bg-transparent text-xs font-bold text-slate-700 outline-none cursor-pointer py-1 w-full">
             <option value="default">Featured</option>
             <option value="price-low">Price ↑</option>
             <option value="price-high">Price ↓</option>
             <option value="rating">Top Rated</option>
           </select>
        </div>
      </div>

      <div class="hidden sm:flex items-center gap-4 w-full sm:w-auto">
        <div class="flex items-center gap-2 flex-1 sm:flex-none bg-slate-50 px-4 py-1.5 rounded-2xl border border-slate-200">
           <span class="material-symbols-outlined text-slate-400 text-lg">sort</span>
           <select onchange="applyFilters(this.value)" class="bg-transparent text-sm font-bold text-slate-700 outline-none cursor-pointer py-1.5 min-w-[120px]">
             <option value="default">Sort by: Featured</option>
             <option value="price-low">Price: Low to High</option>
             <option value="price-high">Price: High to Low</option>
             <option value="rating">Top Rated</option>
           </select>
        </div>
      </div>
    </div>

    <div id="listings-wrapper" class="flex flex-col lg:flex-row gap-0 lg:gap-8 items-start grid-container-anim collapsed-active" style="width:100%;min-width:0">
      <!-- Sidebar (Hidden by default, shown via toggle) -->
      <aside id="sidebar-filters" class="collapsed w-full lg:w-72 shrink-0 lg:sticky lg:top-24" style="min-width:0">
      <div class="bg-white/80 p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-lg font-bold text-slate-900">Filters</h3>
          <button onclick="resetFilters()" class="text-sm text-primary font-semibold hover:underline">Reset</button>
        </div>

        <!-- Type filters -->
        <div class="mb-6">
          <p class="text-xs font-bold text-slate-500 mb-3 flex items-center gap-2 uppercase tracking-wider">
            <span class="material-symbols-outlined text-base">category</span><?php echo htmlspecialchars($c['filter_label']); ?>
          </p>
          <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
            <?php foreach ($c['filters'] as $f): ?>
            <label class="flex items-center gap-3 cursor-pointer group">
              <input type="checkbox" value="<?php echo htmlspecialchars($f); ?>"
                     class="type-filter rounded text-primary focus:ring-primary size-4 border-slate-300 cursor-pointer"/>
              <span class="text-sm font-medium text-slate-700 group-hover:text-primary transition-colors select-none"><?php echo htmlspecialchars($f); ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Price Range -->
        <div class="mb-6">
          <p class="text-xs font-bold text-slate-500 mb-3 flex items-center gap-2 uppercase tracking-wider">
            <span class="material-symbols-outlined text-base">payments</span>Price Range
          </p>
          <div class="flex justify-between text-xs font-bold text-slate-700 mb-2">
            <span>₹<span id="price-min-label"><?php echo $slider_min; ?></span></span>
            <span>₹<span id="price-max-label"><?php echo $slider_max; ?></span></span>
          </div>
          <input type="range" id="price-min-range" min="<?php echo $slider_min; ?>" max="<?php echo $slider_max; ?>"
                 value="<?php echo $slider_min; ?>" step="100"
                 class="w-full accent-primary cursor-pointer" oninput="onPriceMin(this.value)"/>
          <input type="range" id="price-max-range" min="<?php echo $slider_min; ?>" max="<?php echo $slider_max; ?>"
                 value="<?php echo $slider_max; ?>" step="100"
                 class="w-full accent-primary cursor-pointer mt-1" oninput="onPriceMax(this.value)"/>
        </div>

        <!-- Rating -->
        <div>
          <p class="text-xs font-bold text-slate-500 mb-3 flex items-center gap-2 uppercase tracking-wider">
            <span class="material-symbols-outlined text-base">star</span>Min Rating
          </p>
          <div class="space-y-2">
            <?php foreach ([['val'=>'0','label'=>'All ratings'],['val'=>'8','label'=>'8.0+ Very Good'],['val'=>'8.5','label'=>'8.5+ Excellent'],['val'=>'9','label'=>'9.0+ Outstanding']] as $r): ?>
            <label class="flex items-center gap-3 cursor-pointer">
              <input type="radio" name="rating-filter" value="<?php echo $r['val']; ?>"
                     class="text-primary focus:ring-primary size-4 border-slate-300 cursor-pointer"
                     <?php echo $r['val']==='0'?'checked':''; ?>/>
              <span class="text-sm font-medium text-slate-700 select-none flex items-center gap-1">
                <?php echo $r['label']; ?>
                <?php if($r['val']!=='0'): ?><span class="material-symbols-outlined text-sm text-amber-400">star</span><?php endif; ?>
              </span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Active filter count badge -->
      <div id="active-filter-bar" class="hidden bg-white border border-slate-100 rounded-2xl px-4 py-3 flex items-center justify-between">
        <span class="text-sm font-semibold text-primary"><span id="active-filter-count">0</span> filter(s) active</span>
        <button onclick="resetFilters()" class="text-xs text-slate-500 hover:text-primary font-semibold">Clear all</button>
      </div>

      <!-- Apply Button -->
      <div class="mt-4 flex gap-2">
        <button onclick="applyFilters()" class="flex-1 py-3 bg-primary text-white font-bold rounded-xl hover:bg-orange-600 transition-all text-sm flex items-center justify-center gap-2 shadow-sm">
          <span class="material-symbols-outlined text-base">filter_alt</span> Apply Filters
        </button>
        <button onclick="resetFilters()" class="px-4 py-3 border border-slate-200 text-slate-500 font-semibold rounded-xl hover:bg-slate-50 transition-all text-sm">
          Reset
        </button>
      </div>
    </aside>


      <!-- Main content -->
      <div class="flex-1 min-w-0" style="width:100%;overflow-x:hidden">
        <!-- Cards grid -->
        <div id="listings-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 lg:gap-6" style="width:100%">
        <?php if (empty($items)): ?>
        <div class="col-span-3 text-center py-16 text-slate-400">
            <span class="material-symbols-outlined text-5xl mb-3 block">search_off</span>
            <p class="text-lg font-semibold">No listings found</p>
            <p class="text-sm mt-1">Try adjusting your search or filters</p>
        </div>
        <?php else: foreach ($items as $i => $item):
            // Normalise price field
            $price_val = $item[$pc] ?? 0;
            $price_fmt = $price_val > 0 ? '₹' . number_format($price_val) : 'Free';
            // Subtitle: buses show route, others show location
            if ($type === 'buses') {
                $subtitle = htmlspecialchars(($item['from_location'] ?? '') . ' → ' . ($item['to_location'] ?? ''));
                $sub2     = htmlspecialchars(($item['bus_type'] ?? '') . ' · ' . ($item['duration'] ?? ''));
                $item_type = $item['bus_type'] ?? '';
            } else {
                $subtitle = htmlspecialchars($item['location'] ?? '');
                $sub2     = htmlspecialchars($item['type'] ?? '');
                $item_type = $item['type'] ?? '';
            }
            $badge_colors = ['bg-primary','bg-amber-500','bg-green-500','bg-blue-500','bg-purple-500'];
            $badge_color  = !empty($item['badge']) ? $badge_colors[crc32($item['badge']) % count($badge_colors)] : '';
            $hidden_class = '';
        ?>
        <div data-reveal class="listing-card-anim group bg-white rounded-2xl overflow-hidden flex flex-col hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 relative border border-slate-100"
             data-type="<?php echo htmlspecialchars($item_type); ?>"
             data-price="<?php echo (int)$price_val; ?>"
             data-rating="<?php echo number_format((float)($item['rating'] ?? 0), 1); ?>">
          
          <!-- Entire Card Link -->
          <a href="<?php echo listingSlug($type, $item); ?>" class="absolute inset-0 z-10" aria-label="View Details"></a>

          <div class="relative h-52 overflow-hidden <?php 
              $imgSrc = $item['image'] ?? '';
              if(stripos($imgSrc, '.png') !== false) echo 'bg-[#ecf5ff]'; 
          ?>">
            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                 src="<?php 
                    echo (strpos($imgSrc, 'http') === 0) ? htmlspecialchars($imgSrc) : BASE_PATH . '/' . ltrim(htmlspecialchars($imgSrc), '/'); 
                 ?>"
                 alt="<?php echo htmlspecialchars($item['name'] ?? $item['operator'] ?? ''); ?>"
                 loading="lazy"
                 onerror="this.onerror=null;if(!this.src.includes('unsplash.com'))this.src='<?php
                    $fallbacks = [
                        'bikes'       => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80',
                        'cars'        => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&q=80',
                        'stays'       => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80',
                        'restaurants' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80',
                        'attractions' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80',
                        'buses'       => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&q=80',
                    ];
                    echo $fallbacks[$type] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80';
                 ?>'"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            

            <div class="absolute top-3 left-3 flex items-center gap-1 bg-black/50 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-full">
              <span class="material-symbols-outlined text-amber-400 text-sm">star</span>
              <?php echo number_format((float)($item['rating'] ?? 0), 1); ?>
            </div>
            <?php if (!empty($item['badge'])): ?>
            <div class="absolute bottom-3 left-3">
              <span class="px-3 py-1 <?php echo $badge_color; ?> text-white text-xs font-bold rounded-full shadow-lg">
                <?php echo htmlspecialchars($item['badge']); ?>
              </span>
            </div>
            <?php endif; ?>
          </div>
          <div class="p-5 flex flex-col flex-1">
            <div class="mb-1">
              <?php if ($sub2): ?>
              <p class="text-xs text-primary font-bold uppercase tracking-wide mb-1"><?php echo $sub2; ?></p>
              <?php endif; ?>
              <h3 class="text-base font-bold leading-tight group-hover:text-primary transition-colors line-clamp-2">
                <?php echo htmlspecialchars($item['name'] ?? $item['operator'] ?? ''); ?>
              </h3>
            </div>
            <div class="flex items-center gap-1 text-slate-400 text-xs mt-1.5 mb-4">
              <span class="material-symbols-outlined text-sm text-primary/70">location_on</span>
              <span class="line-clamp-1"><?php echo $subtitle; ?></span>
            </div>
            <?php if ($type === 'restaurants'): ?>
            <div class="space-y-1.5 text-xs text-slate-600 mb-4">
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm" style="color:#ec5b13">verified</span><span class="font-semibold">Verified</span></div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm" style="color:#ec5b13">cancel</span><span>Free cancellation</span></div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm" style="color:#ec5b13">info</span><span>No hidden charges</span></div>
            </div>
            <?php endif; ?>
            <div class="card-footer mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
              <div>
                <?php if ($price_val > 0): ?><span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">from</span><?php endif; ?>
                <span class="text-xl font-black text-primary"><?php echo $price_fmt; ?></span>
                <?php if ($c['unit'] && $price_val > 0): ?>
                <span class="text-xs text-slate-400 font-medium"><?php echo htmlspecialchars($c['unit']); ?></span>
                <?php endif; ?>
              </div>
              <a href="<?php echo listingSlug($type, $item); ?>"
                 class="px-4 py-2 bg-primary text-white hover:bg-orange-600 rounded-xl text-sm font-bold transition-all shadow-sm shadow-primary/20">
                <?php echo htmlspecialchars($c['cta']); ?>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>

      <style>.listing-hidden{display:none!important;}</style>
    </div>
  </div>
</div>
</main>

<?php require 'footer.php'; ?>

<!-- Filter JS -->
<script>
var _allCards = null;
var _priceMin = <?php echo $slider_min; ?>;
var _priceMax = <?php echo $slider_max; ?>;

function onPriceMin(v) {
    v = parseInt(v);
    var maxR = document.getElementById('price-max-range');
    if (v > parseInt(maxR.value)) { maxR.value = v; _priceMax = v; document.getElementById('price-max-label').textContent = v; }
    _priceMin = v;
    document.getElementById('price-min-label').textContent = v;
    applyFilters();
}
function onPriceMax(v) {
    v = parseInt(v);
    var minR = document.getElementById('price-min-range');
    if (v < parseInt(minR.value)) { minR.value = v; _priceMin = v; document.getElementById('price-min-label').textContent = v; }
    _priceMax = v;
    document.getElementById('price-max-label').textContent = v;
    applyFilters();
}

function applyFilters(sortBy = 'default') {
    var checkedTypes = Array.from(document.querySelectorAll('.type-filter:checked')).map(function(el){ return el.value.toLowerCase(); });
    var minRating = parseFloat(document.querySelector('input[name="rating-filter"]:checked')?.value || '0');
    var grid = document.getElementById('listings-grid');
    var cards = Array.from(grid.querySelectorAll('.listing-card-anim'));
    
    // Add fade-out to all cards first for smooth transition
    cards.forEach(card => card.classList.add('fade-out'));

    setTimeout(() => {
        var shown = 0;
        cards.forEach(function(card) {
            var cType   = (card.dataset.type || '').toLowerCase();
            var cPrice  = parseInt(card.dataset.price || '0');
            var cRating = parseFloat(card.dataset.rating || '0');

            var typeOk   = checkedTypes.length === 0 || checkedTypes.includes(cType);
            var priceOk  = cPrice === 0 || (cPrice >= _priceMin && cPrice <= _priceMax);
            var ratingOk = cRating >= minRating;

            var pass = typeOk && priceOk && ratingOk;
            card.classList.remove('listing-hidden');
            card.style.display = pass ? '' : 'none';
            if (pass) {
                shown++;
                card.classList.remove('fade-out');
                card.classList.add('fade-in');
            }
        });

        // Sorting (with re-appending)
        if (sortBy !== 'default') {
            cards.sort(function(a, b) {
                if (sortBy === 'price-low') return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                if (sortBy === 'price-high') return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                if (sortBy === 'rating') return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                return 0;
            });
            // Re-order DOM elements
            cards.forEach(function(c) { grid.appendChild(c); });
        }

        // Update result count
        var countEl = document.querySelector('.text-slate-500');
        if (countEl) countEl.textContent = shown + ' result' + (shown !== 1 ? 's' : '') + ' found';

        // Update active filter bar
        var activeCount = checkedTypes.length + (minRating > 0 ? 1 : 0) +
            (_priceMin > <?php echo $slider_min; ?> || _priceMax < <?php echo $slider_max; ?> ? 1 : 0);
        
        var badge = document.getElementById('active-filter-badge');
        if (badge) {
            badge.textContent = activeCount;
            badge.classList.toggle('hidden', activeCount === 0);
            badge.classList.toggle('flex', activeCount > 0);
        }

        var bar = document.getElementById('active-filter-bar');
        document.getElementById('active-filter-count').textContent = activeCount;
        if (activeCount > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
        else { bar.classList.add('hidden'); bar.classList.remove('flex'); }

        // filters applied
    }, 300);
}

function resetFilters() {
    document.querySelectorAll('.type-filter').forEach(function(el){ el.checked = false; });
    document.querySelector('input[name="rating-filter"][value="0"]').checked = true;
    var minR = document.getElementById('price-min-range');
    var maxR = document.getElementById('price-max-range');
    if (minR) { minR.value = minR.min; _priceMin = parseInt(minR.min); document.getElementById('price-min-label').textContent = minR.min; }
    if (maxR) { maxR.value = maxR.max; _priceMax = parseInt(maxR.max); document.getElementById('price-max-label').textContent = maxR.max; }
    // Restore load-more hidden state
    var grid = document.getElementById('listings-grid');
    var cards = Array.from(grid.querySelectorAll('[data-type]'));
    cards.forEach(function(card, i) {
        card.style.display = '';
        if (i >= _shown) card.classList.add('listing-hidden');
    });
    document.getElementById('active-filter-bar').classList.add('hidden');
    document.getElementById('active-filter-bar').classList.remove('flex');
    var countEl = document.querySelector('.text-slate-500');
    if (countEl) countEl.textContent = cards.length + ' result' + (cards.length !== 1 ? 's' : '') + ' found';
}
</script>

<!-- Booking Modal -->
<div id="booking-modal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 relative">
    <button onclick="closeBooking()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700">
      <span class="material-symbols-outlined">close</span>
    </button>
    <h2 class="text-2xl font-serif font-black mb-1">Book Now</h2>
    <p id="modal-listing-name" class="text-primary font-semibold text-sm mb-4"></p>
    <!-- Need help? -->
    <div class="flex flex-col gap-2 mb-5 pb-4 border-b border-slate-100">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Need help?</p>
      <div class="flex gap-2">
        <a href="tel:<?php echo CONTACT_PHONE; ?>" class="flex-1 flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold py-2.5 px-3 rounded-xl text-sm transition-colors">
          <span class="material-symbols-outlined text-base text-primary">call</span><?php echo CONTACT_PHONE; ?>
        </a>
        <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>" class="flex-1 flex items-center gap-2 bg-green-50 hover:bg-green-100 text-green-700 font-bold py-2.5 px-3 rounded-xl text-sm transition-colors">
          <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
          WhatsApp
        </a>
      </div>
    </div>
    <form id="booking-form" class="space-y-4">
      <input type="hidden" id="modal-listing-id"/>
      <input type="hidden" id="modal-service-type"/>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Full Name *</label>
          <input type="text" id="b-name" required placeholder="Your name"
                 class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
        </div>
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Phone *</label>
          <input type="tel" id="b-phone" required placeholder="+91 XXXXX XXXXX"
                 class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
        </div>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Email</label>
        <input type="email" id="b-email" placeholder="your@email.com"
               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Date</label>
          <input type="date" id="b-date" min="<?php echo date('Y-m-d'); ?>"
                 class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
        </div>
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Guests</label>
          <input type="number" id="b-guests" min="1" max="20" value="1"
                 class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
        </div>
      </div>
      <div>
        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Notes</label>
        <textarea id="b-notes" rows="2" placeholder="Any special requests..."
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 resize-none"></textarea>
      </div>
      <div id="booking-success" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-base">check_circle</span> Booking request sent! We'll contact you shortly.
      </div>
      <div id="booking-error" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm"></div>
      <button type="submit" class="w-full bg-primary text-white font-black py-4 rounded-2xl hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">
        Confirm Booking Request
      </button>
    </form>
  </div>
</div>

<!-- Login Required Modal -->
<div id="login-required-modal" class="fixed inset-0 z-[300] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
  <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center relative">
    <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700">
      <span class="material-symbols-outlined">close</span>
    </button>
    <span class="material-symbols-outlined text-5xl text-primary mb-3 block">lock</span>
    <h3 class="text-xl font-serif font-black mb-2">Login Required</h3>
    <p class="text-slate-500 text-sm mb-6">Please login or create an account to make a booking.</p>
    <div class="flex gap-3">
      <a id="login-redirect-btn" href="<?php echo BASE_PATH; ?>/login" class="flex-1 bg-primary text-white font-bold py-3 rounded-xl text-sm hover:bg-orange-600 transition-all">Login</a>
      <a id="register-redirect-btn" href="<?php echo BASE_PATH; ?>/register" class="flex-1 border-2 border-primary text-primary font-bold py-3 rounded-xl text-sm hover:bg-primary/5 transition-all">Register</a>
    </div>
  </div>
</div>

<script>
function openBooking(id, name, type) {
    // Check if user is logged in
    var token = localStorage.getItem('csn_token');
    var user  = JSON.parse(localStorage.getItem('csn_user') || 'null');
    if (!token || !user) {
        // Store intended action and show login modal
        var currentUrl = window.location.href;
        document.getElementById('login-redirect-btn').href = 'login?redirect=' + encodeURIComponent(currentUrl);
        document.getElementById('register-redirect-btn').href = 'register?redirect=' + encodeURIComponent(currentUrl);
        var lm = document.getElementById('login-required-modal');
        lm.classList.remove('hidden');
        lm.classList.add('flex');
        return;
    }
    document.getElementById('modal-listing-id').value = id;
    document.getElementById('modal-listing-name').textContent = name;
    document.getElementById('modal-service-type').value = type;
    document.getElementById('booking-success').classList.add('hidden');
    document.getElementById('booking-error').classList.add('hidden');
    document.getElementById('booking-form').reset();
    document.getElementById('modal-listing-id').value = id;
    document.getElementById('modal-service-type').value = type;
    // Pre-fill from user profile
    if (user.name) document.getElementById('b-name').value = user.name;
    if (user.phone) document.getElementById('b-phone').value = user.phone;
    if (user.email) document.getElementById('b-email').value = user.email;
    var m = document.getElementById('booking-modal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeBooking() {
    var m = document.getElementById('booking-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}
function closeLoginModal() {
    var m = document.getElementById('login-required-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}
document.getElementById('booking-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Sending...';
    var payload = {
        full_name: document.getElementById('b-name').value,
        phone: document.getElementById('b-phone').value,
        email: document.getElementById('b-email').value,
        booking_date: document.getElementById('b-date').value,
        number_of_people: parseInt(document.getElementById('b-guests').value) || 1,
        service_type: document.getElementById('modal-service-type').value,
        listing_id: parseInt(document.getElementById('modal-listing-id').value),
        listing_name: document.getElementById('modal-listing-name').textContent,
        notes: document.getElementById('b-notes').value,
    };
    try {
        var res = await fetch('<?php echo BASE_PATH; ?>/php/api/bookings.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload)
        });
        var data = await res.json();
        if (res.ok) {
            document.getElementById('booking-success').classList.remove('hidden');
            setTimeout(closeBooking, 3000);
        } else {
            document.getElementById('booking-error').textContent = data.error || 'Something went wrong.';
            document.getElementById('booking-error').classList.remove('hidden');
        }
    } catch(err) {
        document.getElementById('booking-error').textContent = 'Network error. Please try again.';
        document.getElementById('booking-error').classList.remove('hidden');
    }
    btn.disabled = false; btn.textContent = 'Confirm Booking Request';
});
document.getElementById('booking-modal').addEventListener('click', function(e) {
    if (e.target === this) closeBooking();
});
document.getElementById('login-required-modal').addEventListener('click', function(e) {
    if (e.target === this) closeLoginModal();
});
</script>
