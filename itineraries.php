<?php
// itineraries.php – Pre-planned trip itineraries with schema
$trip_type = $_GET['type'] ?? '2-day-aurangabad';
$current_page = "itineraries";
require_once 'php/config.php';

$itineraries = [
    '2-day-aurangabad' => [
        'title' => '2-Day Aurangabad Itinerary | CSNExplore',
        'name' => '2-Day Complete Aurangabad Experience',
        'duration' => '2 days',
        'price' => '₹5,000-₹8,000',
        'difficulty' => 'Moderate',
        'best_for' => 'First-time visitors with limited time',
        'description' => 'Explore the best of Chhatrapati Sambhajinagar in just 2 days. Visit Ajanta Caves, Ellora Caves, and city attractions.',
        'days' => [
            [
                'number' => 1,
                'title' => 'Ajanta Caves Exploration',
                'morning' => 'Early 6 AM departure from city center. 100km journey to Ajanta Caves.',
                'activities' => ['Guided tour of 30 ancient caves', 'Admire Buddhist paintings and sculptures', 'Lunch at local restaurant'],
                'evening' => 'Return to city. Evening exploration of Bibi Ka Maqbara monument.',
                'meals' => 'Breakfast (hotel), Lunch (Ajanta area), Dinner (city restaurant)',
                'accommodation' => '4-star hotel in Aurangabad',
                'cost_estimate' => '₹2,500-₹3,500'
            ],
            [
                'number' => 2,
                'title' => 'Ellora Caves & City Attractions',
                'morning' => 'Leisurely breakfast. 30km drive to Ellora Caves.',
                'activities' => ['Explore 34 ancient caves', 'Visit Kailash Temple (rock-carved marvel)', 'Photography stops'],
                'afternoon' => 'Return to city. Visit Daulatabad Fort and Panchakki.',
                'evening' => 'Shopping at local markets. Farewell dinner.',
                'meals' => 'Breakfast (hotel), Lunch (Ellora area), Dinner (city)',
                'accommodation' => 'Optional second night',
                'cost_estimate' => '₹2,500-₹3,500'
            ]
        ]
    ],
    '3-day-caves-tour' => [
        'title' => '3-Day Heritage Caves Tour | CSNExplore',
        'name' => '3-Day UNESCO Caves Heritage Tour',
        'duration' => '3 days',
        'price' => '₹8,000-₹12,000',
        'difficulty' => 'Moderate to Challenging',
        'best_for' => 'History enthusiasts and culture seekers',
        'description' => 'Deep dive into UNESCO World Heritage Sites. Comprehensive tour of Ajanta and Ellora with expert guides.',
        'days' => [
            [
                'number' => 1,
                'title' => 'Arrival & Bibi Ka Maqbara',
                'morning' => 'Arrival in Aurangabad. Hotel check-in.',
                'activities' => ['Rest and refresh', 'Visit Bibi Ka Maqbara', 'Local orientation'],
                'evening' => 'Sunset at Panchakki (water mill monument).',
                'meals' => 'Lunch (hotel), Dinner (traditional restaurant)',
                'accommodation' => '4-star hotel',
                'cost_estimate' => '₹2,000-₹3,000'
            ],
            [
                'number' => 2,
                'title' => 'Ajanta Caves Full Day',
                'morning' => 'Early start. 100km drive to Ajanta with expert guide.',
                'activities' => ['Cave 1: Painted monastery', 'Cave 16: Chaitya hall', 'Caves 4-5: Sculptures', 'Lunch at site restaurant'],
                'afternoon' => 'Continued cave exploration. Photography time.',
                'evening' => 'Return to city. Rest.',
                'meals' => 'Breakfast (hotel), Lunch (site), Dinner (city)',
                'accommodation' => '4-star hotel',
                'cost_estimate' => '₹3,000-₹4,500'
            ],
            [
                'number' => 3,
                'title' => 'Ellora & Departure',
                'morning' => 'Drive to Ellora Caves (30km). Guided tour with historian.',
                'activities' => ['Kailash Temple exploration', 'Buddhist caves section', 'Hindu temple caves', 'Jain caves complex'],
                'afternoon' => 'Return to Aurangabad. Lunch. Shopping.',
                'evening' => 'Departure.',
                'meals' => 'Breakfast (hotel), Lunch (Ellora)', 'accommodation' => 'N/A',
                'cost_estimate' => '₹3,000-₹4,500'
            ]
        ]
    ],
    '4-day-adventure-tour' => [
        'title' => '4-Day Adventure & Heritage Tour | CSNExplore',
        'name' => '4-Day Adventure & Heritage Combination',
        'duration' => '4 days',
        'price' => '₹12,000-₹18,000',
        'difficulty' => 'Moderate',
        'best_for' => 'Adventure seekers with cultural interest',
        'description' => 'Combine heritage site exploration with adventure activities. Includes cave tours, biking, and local experiences.',
        'days' => [
            [
                'number' => 1,
                'title' => 'Arrival & City Exploration',
                'morning' => 'Arrival. Hotel check-in.',
                'activities' => ['Visit Daulatabad Fort', 'Panoramic city views', 'Local market exploration'],
                'evening' => 'Sunset point visit. Traditional dinner.',
                'meals' => 'Lunch (hotel), Dinner (traditional)',
                'accommodation' => '4-star hotel',
                'cost_estimate' => '₹2,000-₹3,000'
            ],
            [
                'number' => 2,
                'title' => 'Ajanta Caves Tour',
                'morning' => 'Early departure for Ajanta (100km, 2.5 hours).',
                'activities' => ['Full day guided tour of 30 caves', 'Paintings & sculptures', 'Lunch included'],
                'evening' => 'Return. Relaxation.',
                'meals' => 'Breakfast (hotel), Lunch (Ajanta), Dinner (city)',
                'accommodation' => '4-star hotel',
                'cost_estimate' => '₹3,500-₹4,500'
            ],
            [
                'number' => 3,
                'title' => 'Bike Adventure Day',
                'morning' => 'Bike rental. Guided bike tour to Ellora Caves.',
                'activities' => ['Scenic 30km bike ride', 'Cave exploration', 'Lunch at traditional dhaba'],
                'evening' => 'Bike return. City exploration.',
                'meals' => 'Breakfast (hotel), Lunch (Ellora), Dinner (restaurant)',
                'accommodation' => '4-star hotel',
                'cost_estimate' => '₹3,000-₹4,000'
            ],
            [
                'number' => 4,
                'title' => 'Departure Day',
                'morning' => 'Final shopping. Local artisan visit.',
                'activities' => ['Souvenir shopping', 'Panchakki revisit', 'Lunch'],
                'afternoon' => 'Departure.',
                'meals' => 'Breakfast (hotel), Lunch (city)',
                'accommodation' => 'N/A',
                'cost_estimate' => '₹1,500-₹2,500'
            ]
        ]
    ],
    '1-day-express' => [
        'title' => '1-Day Express Aurangabad Tour | CSNExplore',
        'name' => '1-Day Express Tour',
        'duration' => '1 day',
        'price' => '₹2,500-₹4,000',
        'difficulty' => 'Easy',
        'best_for' => 'Travelers with very limited time',
        'description' => 'Quick tour covering Ellora Caves and city attractions. Perfect if you only have one day in Aurangabad.',
        'days' => [
            [
                'number' => 1,
                'title' => 'Full Day Express Tour',
                'morning' => '6 AM hotel pickup. First stop: Daulatabad Fort (15 min).',
                'activities' => ['Daulatabad Fort quick tour (45 min)', 'Drive to Ellora (15 min)', 'Ellora Caves guided tour (2 hours)'],
                'afternoon' => 'Lunch at local restaurant. Visit Kailash Temple.',
                'evening' => 'Drive through city. Bibi Ka Maqbara visit. Panchakki sunset visit.',
                'meals' => 'Breakfast (breakfast pack), Lunch (restaurant), Snacks (included)',
                'accommodation' => 'Day tour - no overnight',
                'cost_estimate' => '₹2,500-₹4,000'
            ]
        ]
    ]
];

if (!isset($itineraries[$trip_type])) $trip_type = '2-day-aurangabad';
$itinerary = $itineraries[$trip_type];
$page_title = $itinerary['title'];
$meta_description = $itinerary['description'];

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Trip Planner', 'url' => '/suggestor'],
    ['name' => 'Itineraries', 'url' => '/itineraries'],
    ['name' => $itinerary['name'], 'url' => '/itineraries?type=' . $trip_type],
];

// TouristTrip Schema
$trip_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'TouristTrip',
    'name' => $itinerary['name'],
    'description' => $itinerary['description'],
    'duration' => $itinerary['duration'],
    'touristType' => $itinerary['best_for'],
    'itinerary' => [
        '@type' => 'ItemList',
        'itemListElement' => array_map(function($day, $idx) {
            return [
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'name' => 'Day ' . $day['number'] . ': ' . $day['title'],
                'description' => $day['activities'][0] ?? ''
            ];
        }, $itinerary['days'], array_keys($itinerary['days']))
    ]
];

$extra_head = '<script type="application/ld+json">' . json_encode($trip_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

$page_meta = [
    'description' => $itinerary['description'],
    'canonical'   => 'https://csnexplore.com/itineraries?type=' . $trip_type,
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/Logo-light-optimized.webp',
    'breadcrumbs' => $breadcrumbs,
];

require 'header.php';
?>

<main style="background: #f8f6f6;">

<!-- Hero Section -->
<section class="relative h-[350px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-primary to-orange-500"></div>
    <div class="relative z-10 text-center text-white px-5">
        <h1 class="text-4xl md:text-5xl font-bold mb-3"><?php echo $itinerary['name']; ?></h1>
        <p class="text-xl text-white/90">⏱️ <?php echo $itinerary['duration']; ?> | 💰 <?php echo $itinerary['price']; ?> | ⭐ <?php echo $itinerary['difficulty']; ?></p>
    </div>
</section>

<!-- Quick Info -->
<section class="max-w-5xl mx-auto px-5 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8 grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="text-3xl font-bold text-primary mb-2"><?php echo $itinerary['duration']; ?></div>
            <div class="text-gray-600">Duration</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-primary mb-2"><?php echo $itinerary['price']; ?></div>
            <div class="text-gray-600">Budget</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-primary mb-2"><?php echo $itinerary['difficulty']; ?></div>
            <div class="text-gray-600">Difficulty</div>
        </div>
        <div class="text-center">
            <div class="text-sm text-primary font-bold mb-2 leading-tight"><?php echo $itinerary['best_for']; ?></div>
            <div class="text-gray-600">Best For</div>
        </div>
    </div>
</section>

<!-- Itinerary Days -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <div class="space-y-6">
        <?php foreach ($itinerary['days'] as $day): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Day Header -->
            <div class="bg-gradient-to-r from-primary to-orange-500 text-white p-6">
                <h2 class="text-2xl font-bold">Day <?php echo $day['number']; ?></h2>
                <p class="text-lg text-white/90"><?php echo $day['title']; ?></p>
            </div>
            
            <!-- Day Content -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Timeline -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-800">⏰ Timeline</h3>
                        <div class="space-y-4">
                            <div class="border-l-4 border-primary pl-4">
                                <p class="font-semibold text-gray-800">Morning</p>
                                <p class="text-gray-600"><?php echo $day['morning']; ?></p>
                            </div>
                            <div class="border-l-4 border-orange-500 pl-4">
                                <p class="font-semibold text-gray-800">Activities</p>
                                <ul class="text-gray-600 space-y-1">
                                    <?php foreach ($day['activities'] as $activity): ?>
                                    <li>✓ <?php echo $activity; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php if (isset($day['afternoon'])): ?>
                            <div class="border-l-4 border-blue-500 pl-4">
                                <p class="font-semibold text-gray-800">Afternoon</p>
                                <p class="text-gray-600"><?php echo $day['afternoon']; ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="border-l-4 border-purple-500 pl-4">
                                <p class="font-semibold text-gray-800">Evening</p>
                                <p class="text-gray-600"><?php echo $day['evening']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-800">📋 Details</h3>
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-semibold text-gray-800 mb-2">🍽️ Meals</p>
                                <p class="text-gray-600 text-sm"><?php echo is_array($day['meals']) ? implode(', ', $day['meals']) : $day['meals']; ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-semibold text-gray-800 mb-2">🏨 Accommodation</p>
                                <p class="text-gray-600 text-sm"><?php echo $day['accommodation']; ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-semibold text-gray-800 mb-2">💰 Estimated Cost</p>
                                <p class="text-gray-600 text-sm"><?php echo $day['cost_estimate']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Booking Section -->
    <div class="bg-gradient-to-r from-primary to-orange-500 text-white rounded-lg p-8 mt-12 text-center">
        <h2 class="text-2xl font-bold mb-3">Ready to Explore?</h2>
        <p class="mb-6 text-lg">Book all components of your trip on CSNExplore</p>
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="<?php echo BASE_PATH; ?>/listing/stays" class="btn-white inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">bed</span> Book Hotels
            </a>
            <a href="<?php echo BASE_PATH; ?>/listing/cars" class="btn-white inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">directions_car</span> Rent a Car
            </a>
            <a href="<?php echo BASE_PATH; ?>/listing/attractions" class="btn-white inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">confirmation_number</span> Book Attractions
            </a>
        </div>
    </div>

    <!-- Other Itineraries -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Other Popular Itineraries</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($itineraries as $key => $itin): ?>
                <?php if ($key !== $trip_type): ?>
                <a href="<?php echo BASE_PATH; ?>/itineraries?type=<?php echo $key; ?>"
                   class="bg-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all border-l-4 border-primary">
                    <h3 class="font-bold text-lg text-gray-800 mb-2"><?php echo $itin['name']; ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo $itin['description']; ?></p>
                    <div class="flex justify-between text-sm">
                        <span class="text-primary font-semibold">⏱️ <?php echo $itin['duration']; ?></span>
                        <span class="text-orange-500 font-semibold">💰 <?php echo $itin['price']; ?></span>
                    </div>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

</main>

<?php require 'footer.php'; ?>
