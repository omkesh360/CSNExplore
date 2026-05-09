<?php
// near-attractions.php – Landing pages for "near [Attraction]" searches
$location = $_GET['attraction'] ?? 'ajanta-caves';
$current_page = "landing";
require_once 'php/config.php';

$attractions = [
    'ajanta-caves' => [
        'name' => 'Ajanta Caves',
        'display_name' => 'Near Ajanta Caves',
        'distance' => '100 km',
        'time' => '2-3 hours',
        'description' => 'Find hotels, car rentals, bikes, and restaurants near the UNESCO-listed Ajanta Caves in Chhatrapati Sambhajinagar.',
        'hero_image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80&auto=format',
        'meta' => 'Hotels near Ajanta Caves, car rental for Ajanta tour, things to do near Ajanta',
    ],
    'ellora-caves' => [
        'name' => 'Ellora Caves',
        'display_name' => 'Near Ellora Caves',
        'distance' => '30 km',
        'time' => '45 minutes',
        'description' => 'Discover accommodations, transportation, and dining options near the magnificent Ellora Caves complex.',
        'hero_image' => 'https://images.unsplash.com/photo-1494496195356-cbb2edc47986?w=800&q=80&auto=format',
        'meta' => 'Hotels near Ellora Caves, restaurants near Ellora, Ellora Caves tour guide',
    ],
    'bibi-ka-maqbara' => [
        'name' => 'Bibi Ka Maqbara',
        'display_name' => 'Near Bibi Ka Maqbara',
        'distance' => '6 km',
        'time' => '15 minutes',
        'description' => 'Book hotels, cars, and find dining options near Bibi Ka Maqbara, the beautiful mausoleum in Aurangabad.',
        'hero_image' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80&auto=format',
        'meta' => 'Hotels near Bibi Ka Maqbara, car rental Aurangabad, restaurants near monument',
    ],
    'daulatabad-fort' => [
        'name' => 'Daulatabad Fort',
        'display_name' => 'Near Daulatabad Fort',
        'distance' => '15 km',
        'time' => '30 minutes',
        'description' => 'Find accommodations and transportation near the historic Daulatabad Fort in Sambhajinagar.',
        'hero_image' => 'https://images.unsplash.com/photo-1518235506717-e1ed3306a326?w=800&q=80&auto=format',
        'meta' => 'Hotels near Daulatabad Fort, car hire Aurangabad, things to do near fort',
    ],
    'panchakki' => [
        'name' => 'Panchakki (Water Mill)',
        'display_name' => 'Near Panchakki',
        'distance' => '8 km',
        'time' => '20 minutes',
        'description' => 'Explore hotels, car rentals, and restaurants near Panchakki, the historic water mill monument.',
        'hero_image' => 'https://images.unsplash.com/photo-1501426614569-8821bcb91f10?w=800&q=80&auto=format',
        'meta' => 'Hotels near Panchakki, bike rental Aurangabad, restaurants near Panchakki',
    ],
];

if (!isset($attractions[$location])) $location = 'ajanta-caves';
$data = $attractions[$location];
$page_title = "Best Hotels & Rentals {$data['display_name']} | CSNExplore";
$meta_description = $data['description'];

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Attractions', 'url' => '/listing/attractions'],
    ['name' => $data['name'], 'url' => '/listing/attractions'],
    ['name' => 'Nearby', 'url' => '/near-attractions?attraction=' . $location],
];

// Schema markup for LocalBusiness near Attraction
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Hotels ' . $data['display_name'],
            'url' => SITE_URL . '/listing/stays?filter=' . $location,
            'description' => 'Find comfortable accommodations near ' . $data['name']
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Car Rentals ' . $data['display_name'],
            'url' => SITE_URL . '/listing/cars',
            'description' => 'Rent a car for touring ' . $data['name']
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => 'Bike Rentals ' . $data['display_name'],
            'url' => SITE_URL . '/listing/bikes',
            'description' => 'Rent a bike to explore ' . $data['name']
        ],
        [
            '@type' => 'ListItem',
            'position' => 4,
            'name' => 'Restaurants ' . $data['display_name'],
            'url' => SITE_URL . '/listing/restaurants',
            'description' => 'Dine near ' . $data['name']
        ]
    ]
];

$extra_head = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

require 'header.php';
?>

<main style="background: #f8f6f6;">

<!-- Hero Section -->
<section class="relative h-[400px] flex items-center justify-center overflow-hidden">
    <img loading="lazy" width="800" height="600" class="absolute inset-0 w-full h-full object-cover"
         src="<?php echo $data['hero_image']; ?>" alt="<?php echo $data['name']; ?>"/>
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 to-black/70"></div>
    
    <div class="relative z-10 text-center text-white px-5">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Stay & Explore <?php echo $data['display_name']; ?></h1>
        <p class="text-xl text-white/90 mb-6">⏱️ <?php echo $data['time']; ?> from Aurangabad City</p>
        <p class="text-lg text-white/80 max-w-2xl mx-auto"><?php echo $data['description']; ?></p>
    </div>
</section>

<!-- Main Content -->
<section class="max-w-5xl mx-auto px-5 py-16">
    
    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-12">
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <span class="material-symbols-outlined text-4xl text-primary mb-2">bed</span>
            <h3 class="font-bold mb-2">Hotels</h3>
            <p class="text-sm text-gray-600">Comfortable stay near <?php echo $data['name']; ?></p>
            <a href="<?php echo BASE_PATH; ?>/listing/stays" class="text-primary hover:underline text-sm font-semibold mt-3 inline-block">Browse Hotels →</a>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <span class="material-symbols-outlined text-4xl text-primary mb-2">directions_car</span>
            <h3 class="font-bold mb-2">Car Rentals</h3>
            <p class="text-sm text-gray-600">Self-drive or with driver</p>
            <a href="<?php echo BASE_PATH; ?>/listing/cars" class="text-primary hover:underline text-sm font-semibold mt-3 inline-block">Rent a Car →</a>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <span class="material-symbols-outlined text-4xl text-primary mb-2">motorcycle</span>
            <h3 class="font-bold mb-2">Bike Rentals</h3>
            <p class="text-sm text-gray-600">Adventure at ₹300-800/day</p>
            <a href="<?php echo BASE_PATH; ?>/listing/bikes" class="text-primary hover:underline text-sm font-semibold mt-3 inline-block">Rent a Bike →</a>
        </div>
        
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <span class="material-symbols-outlined text-4xl text-primary mb-2">restaurant</span>
            <h3 class="font-bold mb-2">Restaurants</h3>
            <p class="text-sm text-gray-600">Authentic local cuisine</p>
            <a href="<?php echo BASE_PATH; ?>/listing/restaurants" class="text-primary hover:underline text-sm font-semibold mt-3 inline-block">Find Food →</a>
        </div>
    </div>

    <!-- Detailed Info -->
    <div class="bg-white rounded-lg p-8 shadow-lg mb-12">
        <h2 class="text-3xl font-bold mb-6">Guide to Visiting <?php echo $data['name']; ?></h2>
        
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4 text-primary">📍 Location & Distance</h3>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="text-primary font-bold">→</span>
                        <span><strong>From Aurangabad:</strong> <?php echo $data['distance']; ?> (<?php echo $data['time']; ?>)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-primary font-bold">→</span>
                        <span><strong>Best Season:</strong> October to February</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-primary font-bold">→</span>
                        <span><strong>Entry Fee:</strong> Variable (₹250-500)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-primary font-bold">→</span>
                        <span><strong>Best Time:</strong> Early morning or late afternoon</span>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-4 text-primary">💡 What to Bring</h3>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">check_circle</span>
                        <span>Comfortable walking shoes</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">check_circle</span>
                        <span>Water bottle (2-3 liters)</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">check_circle</span>
                        <span>Sunscreen & hat</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">check_circle</span>
                        <span>Camera for photos</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- More Attractions Links -->
    <div class="bg-gradient-to-r from-primary/10 to-orange-50 rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6">Other Nearby Attractions</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php foreach ($attractions as $key => $attr): ?>
                <?php if ($key !== $location): ?>
                <a href="<?php echo BASE_PATH; ?>/near-attractions?attraction=<?php echo $key; ?>"
                   class="bg-white p-4 rounded-lg hover:shadow-lg transition-all text-center">
                    <h4 class="font-semibold text-gray-800 hover:text-primary"><?php echo $attr['name']; ?></h4>
                    <p class="text-sm text-gray-600"><?php echo $attr['distance']; ?> away</p>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

</section>

</main>

<?php require 'footer.php'; ?>
