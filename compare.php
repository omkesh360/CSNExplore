<?php
// compare.php – Comparison pages for high-value keywords
$type = $_GET['type'] ?? 'hotels-vs-homestays';
$current_page = "compare";
require_once 'php/config.php';

$comparisons = [
    'hotels-vs-homestays' => [
        'title' => 'Hotels vs Homestays in Chhatrapati Sambhajinagar | CSNExplore',
        'description' => 'Compare hotels and homestays in Aurangabad. Learn pros, cons, pricing, and which is best for your budget and travel style.',
        'h1' => 'Hotels vs Homestays in Chhatrapati Sambhajinagar',
        'intro' => 'Choosing between a hotel and homestay in Chhatrapati Sambhajinagar? Both offer unique advantages. Here\'s a detailed comparison to help you decide.',
        'comparison' => [
            ['aspect' => 'Cost', 'hotels' => 'Hotels typically cost ₹2,000-₹5,000+ per night', 'homestays' => 'Homestays cost ₹1,500-₹3,000 per night (20-30% cheaper)', 'winner' => 'Homestays'],
            ['aspect' => 'Comfort', 'hotels' => 'High comfort with modern amenities, WiFi, AC, hot water', 'homestays' => 'Cozy and comfortable, but amenities vary by property', 'winner' => 'Hotels'],
            ['aspect' => 'Experience', 'hotels' => 'Professional service but less local interaction', 'homestays' => 'Authentic local experience, cultural immersion', 'winner' => 'Homestays'],
            ['aspect' => 'Facilities', 'hotels' => 'Restaurant, room service, 24/7 reception, housekeeping', 'homestays' => 'Kitchen access, communal areas, self-service options', 'winner' => 'Hotels'],
            ['aspect' => 'Best For', 'hotels' => 'Business travelers, luxury seekers, families wanting services', 'homestays' => 'Backpackers, budget travelers, culture seekers', 'winner' => 'Depends on you'],
        ],
        'faqs' => [
            ['q' => 'Are homestays safe in Aurangabad?', 'a' => 'Yes, homestays in Aurangabad are generally safe. Check reviews and choose verified properties on CSNExplore. Host ratings help ensure quality and safety.'],
            ['q' => 'Do homestays have WiFi?', 'a' => 'Most modern homestays provide WiFi, but it\'s best to confirm before booking. Hotels typically guarantee high-speed internet.'],
            ['q' => 'Can I cook at homestays?', 'a' => 'Many homestays have shared kitchens or allow guest cooking. Hotels don\'t allow cooking in rooms but provide restaurants and room service.'],
        ]
    ],
    'budget-vs-luxury-hotels' => [
        'title' => 'Budget vs Luxury Hotels in Aurangabad | CSNExplore',
        'description' => 'Compare budget and luxury hotels in Aurangabad. Find the best hotel for your travel style, budget, and preferences.',
        'h1' => 'Budget vs Luxury Hotels in Chhatrapati Sambhajinagar',
        'intro' => 'Whether you\'re looking for a comfortable budget hotel or a premium luxury experience, Aurangabad offers options for every budget. Here\'s how to choose.',
        'comparison' => [
            ['aspect' => 'Price Range', 'budget' => '₹800-₹2,000 per night', 'luxury' => '₹5,000-₹15,000+ per night', 'winner' => 'Budget (for savings)'],
            ['aspect' => 'Room Quality', 'budget' => 'Clean and basic, functional furniture', 'luxury' => 'Spacious, elegant, premium furnishings', 'winner' => 'Luxury'],
            ['aspect' => 'Amenities', 'budget' => 'AC, WiFi, basic TV, basic bathroom', 'luxury' => 'Spa, pool, gym, restaurant, room service, butler', 'winner' => 'Luxury'],
            ['aspect' => 'Dining', 'budget' => 'Basic breakfast, limited dining options', 'luxury' => 'Multi-cuisine restaurants, room service 24/7', 'winner' => 'Luxury'],
            ['aspect' => 'Location', 'budget' => 'Usually near city center but less premium', 'luxury' => 'Prime locations near attractions', 'winner' => 'Luxury'],
            ['aspect' => 'Best For', 'budget' => 'Backpackers, families wanting value for money', 'luxury' => 'Honeymoons, anniversaries, corporate events', 'winner' => 'Depends on occasion'],
        ],
        'faqs' => [
            ['q' => 'Are budget hotels comfortable?', 'a' => 'Yes! CSNExplore-listed budget hotels are well-maintained and comfortable. They focus on essentials like clean rooms, WiFi, and AC.'],
            ['q' => 'Do luxury hotels offer discounts?', 'a' => 'Yes, luxury hotels often offer seasonal discounts, group rates, and special packages. Check CSNExplore for current deals.'],
            ['q' => 'What\'s the best budget hotel in Aurangabad?', 'a' => 'Several budget hotels have excellent reviews on CSNExplore. Filter by ratings and read guest reviews to find the best match for your needs.'],
        ]
    ],
    'car-vs-bike-rental' => [
        'title' => 'Car Rental vs Bike Rental in Aurangabad | CSNExplore',
        'description' => 'Compare car rentals and bike rentals in Chhatrapati Sambhajinagar. Which is best for Ajanta Caves tour?',
        'h1' => 'Car Rental vs Bike Rental in Chhatrapati Sambhajinagar',
        'intro' => 'Planning to explore Aurangabad and nearby attractions like Ajanta Caves? Compare car rentals and bike rentals to find the best option for your trip.',
        'comparison' => [
            ['aspect' => 'Cost', 'car' => '₹1,000-₹2,500 per day', 'bike' => '₹300-₹800 per day', 'winner' => 'Bikes (4-5x cheaper)'],
            ['aspect' => 'Comfort', 'car' => 'Comfortable for multiple passengers, AC, weather protection', 'bike' => 'Direct experience but no weather protection', 'winner' => 'Cars'],
            ['aspect' => 'Fuel', 'car' => '₹300-₹500 per day (you pay)', 'bike' => '₹150-₹250 per day (you pay)', 'winner' => 'Bikes'],
            ['aspect' => 'Group Travel', 'car' => 'Best for 2-6 people, cheaper per person', 'bike' => 'Only 1-2 people per bike', 'winner' => 'Cars (for groups)'],
            ['aspect' => 'Ajanta Caves Tour', 'car' => 'Ideal - 100km journey, comfortable for 2-6 people', 'bike' => 'Good for adventurous solo/couple travel', 'winner' => 'Cars (convenience)'],
            ['aspect' => 'Best For', 'car' => 'Families, groups, comfort-seekers, long distances', 'bike' => 'Solo travelers, adventurers, budget travelers', 'winner' => 'Depends on trip'],
        ],
        'faqs' => [
            ['q' => 'Is bike rental safe in Aurangabad?', 'a' => 'Yes, CSNExplore partners only with reliable bike rental services. All bikes are well-maintained, helmets provided, and 24/7 support available.'],
            ['q' => 'Can I drive a bike to Ajanta Caves?', 'a' => 'Yes, but it\'s a 100km journey. A car is more comfortable for this long-distance trip. However, many adventure seekers rent bikes and enjoy the ride!'],
            ['q' => 'Do car rentals include insurance?', 'a' => 'Yes, comprehensive insurance is included in all CSNExplore car rentals. Damage waiver covers accidental damage.'],
        ]
    ],
    'ellora-vs-ajanta-caves' => [
        'title' => 'Ellora Caves vs Ajanta Caves | Which to Visit? CSNExplore',
        'description' => 'Compare Ajanta and Ellora Caves near Aurangabad. Learn differences, distances, what to see, and which is better for your trip.',
        'h1' => 'Ajanta Caves vs Ellora Caves - A Complete Comparison',
        'intro' => 'Both Ajanta and Ellora Caves are UNESCO World Heritage Sites near Aurangabad. Here\'s a detailed comparison to help you decide which to visit or if you should visit both.',
        'comparison' => [
            ['aspect' => 'Distance', 'ajanta' => '100km from Aurangabad (2-3 hours)', 'ellora' => '30km from Aurangabad (45 mins)', 'winner' => 'Ellora (closer)'],
            ['aspect' => 'Age & History', 'ajanta' => 'Built 200 BC - 600 AD (Ancient Buddhist caves)', 'ellora' => 'Built 600-1000 AD (Buddhist, Hindu, Jain caves)', 'winner' => 'Ajanta (older)'],
            ['aspect' => 'Art & Paintings', 'ajanta' => 'Famous for detailed paintings and frescoes', 'ellora' => 'Famous for rock sculpture and architecture', 'winner' => 'Ajanta (art), Ellora (sculpture)'],
            ['aspect' => 'Number of Caves', 'ajanta' => '30 caves (main excavations)', 'ellora' => '34 caves (Hindu, Buddhist, Jain)', 'winner' => 'Ellora (more variety)'],
            ['aspect' => 'Time Needed', 'ajanta' => '3-4 hours with guide (full exploration)', 'ellora' => '2-3 hours with guide', 'winner' => 'Ellora (quicker)'],
            ['aspect' => 'Best For', 'ajanta' => 'Art and history enthusiasts', 'ellora' => 'Architecture lovers, time-limited visitors', 'winner' => 'Depends on interest'],
        ],
        'faqs' => [
            ['q' => 'Can I visit both Ajanta and Ellora in one day?', 'a' => 'Technically yes, but it\'s rushed. Better to spend one day per site. Many visitors do both in 2 days or dedicate 1 day to Ellora (closer) and another to Ajanta.'],
            ['q' => 'Which is better - Ajanta or Ellora?', 'a' => 'Both are exceptional. Ajanta is better for paintings and ancient history. Ellora is better for variety (Hindu, Buddhist, Jain) and sculpture.'],
            ['q' => 'Do I need a guide?', 'a' => 'Highly recommended! Guides provide historical context, point out details, and explain architecture. CSNExplore offers verified, English-speaking guides.'],
        ]
    ],
    'tour-packages-vs-diy' => [
        'title' => 'Tour Packages vs DIY Travel | Aurangabad Comparison',
        'description' => 'Compare organized tour packages vs DIY independent travel in Chhatrapati Sambhajinagar. Pros, cons, cost, and best choice.',
        'h1' => 'Tour Packages vs DIY Travel in Chhatrapati Sambhajinagar',
        'intro' => 'Plan your Aurangabad trip? Compare guided tour packages with DIY independent travel to choose what suits your style and budget.',
        'comparison' => [
            ['aspect' => 'Cost', 'package' => '₹5,000-₹15,000+ per day (all-inclusive)', 'diy' => '₹2,000-₹5,000 per day (self-arranged)', 'winner' => 'DIY (budget-friendly)'],
            ['aspect' => 'Flexibility', 'package' => 'Fixed itinerary, limited freedom', 'diy' => 'Complete flexibility, your own pace', 'winner' => 'DIY'],
            ['aspect' => 'Convenience', 'package' => 'Everything arranged, no planning needed', 'diy' => 'You arrange everything yourself', 'winner' => 'Package'],
            ['aspect' => 'Social', 'package' => 'Meet other travelers on the tour', 'diy' => 'Independent or partner travel', 'winner' => 'Package (social)'],
            ['aspect' => 'Expertise', 'package' => 'Professional guides, expert knowledge', 'diy' => 'Research yourself, trial and error', 'winner' => 'Package'],
            ['aspect' => 'Best For', 'package' => 'First-time visitors, busy professionals, group travel', 'diy' => 'Budget travelers, experienced explorers, solo travel', 'winner' => 'Depends on you'],
        ],
        'faqs' => [
            ['q' => 'Are tour packages worth it?', 'a' => 'If you have limited time and want hassle-free travel, yes. If you prefer flexibility and lower costs, DIY travel might be better.'],
            ['q' => 'Can I book DIY travel components on CSNExplore?', 'a' => 'Yes! Book hotels, car rentals, attraction tickets, and restaurant reservations individually to create your own itinerary.'],
            ['q' => 'Which is safer - tour packages or DIY?', 'a' => 'Both are safe. Tour packages offer structured safety, while DIY travelers can book through verified platforms like CSNExplore for security.'],
        ]
    ],
    'online-vs-onsite-booking' => [
        'title' => 'Online Booking vs On-Site Booking in Aurangabad | CSNExplore',
        'description' => 'Should you book online before arriving or book on-site? Compare prices, availability, and flexibility.',
        'h1' => 'Online Booking vs On-Site Booking in Aurangabad',
        'intro' => 'One of the key decisions is whether to pre-book your accommodation and services online or wait to book after arriving in Aurangabad.',
        'comparison' => [
            ['aspect' => 'Price', 'online' => 'Usually 10-20% cheaper with advance discounts', 'onsite' => 'Can negotiate but limited inventory', 'winner' => 'Online'],
            ['aspect' => 'Availability', 'online' => 'High availability, wide choices, book weeks ahead', 'onsite' => 'Limited options, subject to walk-in availability', 'winner' => 'Online'],
            ['aspect' => 'Flexibility', 'online' => 'Cancellation policies vary (non-refundable common)', 'onsite' => 'More flexible to negotiate and change', 'winner' => 'On-Site'],
            ['aspect' => 'Peace of Mind', 'online' => 'Everything confirmed before arrival', 'onsite' => 'Uncertainty until you arrive', 'winner' => 'Online'],
            ['aspect' => 'Transparency', 'online' => 'Clear pricing, all fees visible upfront', 'onsite' => 'Risk of hidden charges or upsells', 'winner' => 'Online'],
            ['aspect' => 'Best For', 'online' => 'Peak season, certainty-seekers, group travel', 'onsite' => 'Off-season, budget flexibility, spontaneous travelers', 'winner' => 'Depends'],
        ],
        'faqs' => [
            ['q' => 'Can I get better deals by booking on-site?', 'a' => 'Possibly in off-season, but online pre-booking typically offers better rates and guarantees availability. CSNExplore offers competitive online rates.'],
            ['q' => 'Is it safe to book online?', 'a' => 'Yes, CSNExplore is secure. All bookings are protected, and you\'ll receive confirmations via email and SMS.'],
            ['q' => 'What if I need to cancel my online booking?', 'a' => 'Most CSNExplore bookings allow cancellation up to 48 hours before arrival with full refund. Check specific cancellation policies.'],
        ]
    ],
    'solo-vs-group-travel' => [
        'title' => 'Solo Travel vs Group Travel in Aurangabad | CSNExplore',
        'description' => 'Compare traveling alone versus with a group. Understand costs, safety, experiences, and social aspects.',
        'h1' => 'Solo Travel vs Group Travel in Aurangabad',
        'intro' => 'Deciding between traveling solo or with a group? Each offers unique advantages and challenges. Let\'s compare.',
        'comparison' => [
            ['aspect' => 'Cost Per Person', 'solo' => 'Higher per night (single room premium)', 'group' => 'Lower per person (shared rooms, group discounts)', 'winner' => 'Group'],
            ['aspect' => 'Flexibility', 'solo' => 'Complete freedom, your schedule', 'group' => 'Group consensus required, compromises needed', 'winner' => 'Solo'],
            ['aspect' => 'Safety', 'solo' => 'Need extra precautions, limited protection', 'group' => 'Safer with companions, shared responsibility', 'winner' => 'Group'],
            ['aspect' => 'Local Interaction', 'solo' => 'More engaging with locals, authentic', 'group' => 'Limited local interaction (group-focused)', 'winner' => 'Solo'],
            ['aspect' => 'Accommodation', 'solo' => 'Private rooms more expensive', 'group' => 'Dorms or shared rooms reduce cost', 'winner' => 'Group'],
            ['aspect' => 'Best For', 'solo' => 'Self-discovery, adventurers, experienced travelers', 'group' => 'Bonding, first-time visitors, shared experiences', 'winner' => 'Depends'],
        ],
        'faqs' => [
            ['q' => 'Is solo travel safe for females in Aurangabad?', 'a' => 'Yes, Aurangabad is relatively safe. Stay in recommended areas, use CSNExplore verified services, and follow common travel safety tips.'],
            ['q' => 'How can solo travelers meet other travelers?', 'a' => 'Stay in hostels, join group tours on CSNExplore, or attend local events. Many solo travelers connect through travel groups.'],
            ['q' => 'Is group travel more economical?', 'a' => 'Often yes. Group discounts, shared rooms, and split transportation costs make group travel cheaper per person.'],
        ]
    ],
    'monsoon-vs-winter-visit' => [
        'title' => 'Best Time to Visit: Monsoon vs Winter in Aurangabad',
        'description' => 'Compare monsoon season (Jun-Aug) vs winter (Oct-Feb) in Aurangabad. Weather, crowds, prices, and activities.',
        'h1' => 'Monsoon vs Winter - Best Season to Visit Aurangabad',
        'intro' => 'Aurangabad has distinct seasons. Monsoon brings budget deals and green landscapes, while winter offers perfect weather and peak activities.',
        'comparison' => [
            ['aspect' => 'Weather', 'monsoon' => 'Rainy, 20-25°C, high humidity', 'winter' => 'Sunny, 10-20°C, dry, pleasant', 'winner' => 'Winter'],
            ['aspect' => 'Tourist Crowds', 'monsoon' => 'Very few tourists, peaceful', 'winter' => 'Peak season crowds, festivals', 'winner' => 'Monsoon (if crowd-averse)'],
            ['aspect' => 'Hotel Rates', 'monsoon' => '30-50% cheaper than winter', 'winter' => 'Premium prices, early booking needed', 'winner' => 'Monsoon'],
            ['aspect' => 'Cave Exploration', 'monsoon' => 'Slippery, less recommended', 'winter' => 'Ideal conditions, safe walks', 'winner' => 'Winter'],
            ['aspect' => 'Photography', 'monsoon' => 'Green landscapes, moody shots', 'winter' => 'Clear skies, vibrant colors', 'winner' => 'Winter (or personal preference)'],
            ['aspect' => 'Best For', 'monsoon' => 'Budget travelers, off-season explorers', 'winter' => 'Comfort-seekers, families, peak experience', 'winner' => 'Winter'],
        ],
        'faqs' => [
            ['q' => 'Can I visit Aurangabad during monsoon?', 'a' => 'Yes, but caves may be less accessible. Wear waterproof gear. Some cave paths become slippery, so guided tours are recommended.'],
            ['q' => 'When is the best time to visit?', 'a' => 'October to February (winter) offers the best weather, ideal activities, and vibrant local festivals.'],
            ['q' => 'Are monsoon deals worth it?', 'a' => 'If budget is priority and weather doesn\'t bother you, monsoon (June-August) offers 30-50% savings on accommodations.'],
        ]
    ],
    'luxury-vs-budget-experience' => [
        'title' => 'Luxury vs Budget Travel Experience in Aurangabad',
        'description' => 'Compare luxury travel (high spending) vs budget travel (low spending) experiences in Aurangabad.',
        'h1' => 'Luxury vs Budget Travel Experience in Aurangabad',
        'intro' => 'Think expensive means better? Not always. Compare luxury vs budget travel experiences and what each offers.',
        'comparison' => [
            ['aspect' => '5-Day Total Cost', 'luxury' => '₹50,000-₹100,000+ per person', 'budget' => '₹12,000-₹20,000 per person', 'winner' => 'Budget'],
            ['aspect' => 'Accommodation', 'luxury' => '5-star premium hotels, spas', 'budget' => 'Budget hotels, hostels, homestays', 'winner' => 'Luxury (amenities)'],
            ['aspect' => 'Food', 'luxury' => 'Fine dining, upscale restaurants', 'budget' => 'Street food, local eateries, authentic', 'winner' => 'Neutral (different experiences)'],
            ['aspect' => 'Transportation', 'luxury' => 'Private AC cars, premium services', 'budget' => 'Public transport, shared rides', 'winner' => 'Luxury (comfort)'],
            ['aspect' => 'Social Connections', 'luxury' => 'Limited, isolated groups', 'budget' => 'High, meet fellow travelers', 'winner' => 'Budget'],
            ['aspect' => 'Best For', 'luxury' => 'Relaxation, special occasions, corporate', 'budget' => 'Adventure, cultural immersion, backpacking', 'winner' => 'Depends on priorities'],
        ],
        'faqs' => [
            ['q' => 'Can I have an amazing experience on a budget?', 'a' => 'Absolutely! Budget travel offers authentic experiences, local interactions, and adventure. Many travelers prefer budget travel for deeper connections.'],
            ['q' => 'Is luxury travel always better?', 'a' => 'No. Luxury offers comfort and convenience, but budget travel often provides better cultural immersion and cost-effectiveness.'],
            ['q' => 'How can I mix luxury and budget?', 'a' => 'Mix and match! Budget accommodation, mid-range dining, and occasional luxury experiences (spa, nice dinner) offer great balance.'],
        ]
    ],
    'adventure-vs-cultural-tourism' => [
        'title' => 'Adventure Activities vs Cultural Sightseeing in Aurangabad',
        'description' => 'Compare adventure travel (biking, trekking) with cultural tourism (heritage sites, museums).',
        'h1' => 'Adventure Travel vs Cultural Tourism in Aurangabad',
        'intro' => 'Aurangabad offers both adrenaline-pumping activities and rich cultural heritage. Which matches your travel style?',
        'comparison' => [
            ['aspect' => 'Activities', 'adventure' => 'Biking tours, rock climbing, caving, trekking', 'cultural' => 'Cave tours, museum visits, historical sites', 'winner' => 'Neutral'],
            ['aspect' => 'Physical Demand', 'adventure' => 'High fitness required, challenging', 'cultural' => 'Moderate walking, all ages welcome', 'winner' => 'Cultural (accessibility)'],
            ['aspect' => 'Cost', 'adventure' => '₹2,000-₹4,000 per activity', 'cultural' => '₹500-₹1,500 per site entrance', 'winner' => 'Cultural'],
            ['aspect' => 'Learning Experience', 'adventure' => 'Physical challenges, personal growth', 'cultural' => 'Historical knowledge, deep understanding', 'winner' => 'Cultural (education)'],
            ['aspect' => 'Thrill Level', 'adventure' => '⭐⭐⭐⭐⭐ High', 'cultural' => '⭐⭐ Moderate', 'winner' => 'Adventure'],
            ['aspect' => 'Best For', 'adventure' => 'Young, active, thrill-seekers', 'cultural' => 'History lovers, families, all ages', 'winner' => 'Depends'],
        ],
        'faqs' => [
            ['q' => 'Can I do both adventure and cultural activities?', 'a' => 'Yes! Many visitors combine both. Our 4-day adventure tour includes cave visits AND biking adventures.'],
            ['q' => 'Is adventure travel safe?', 'a' => 'Yes, with proper precautions. CSNExplore partners with certified operators with safety equipment and insurance.'],
            ['q' => 'What\'s the best cultural site in Aurangabad?', 'a' => 'Ajanta Caves for paintings, Ellora Caves for architecture, and Bibi Ka Maqbara for Mughal heritage. Visit all three!'],
        ]
    ],
    'indian-vs-international-tourists' => [
        'title' => 'Travel Experience: Indian vs International Tourists in Aurangabad',
        'description' => 'Compare travel experiences, accommodations, and services for Indian vs international tourists.',
        'h1' => 'Indian Tourists vs International Tourists - Service Comparison',
        'intro' => 'Aurangabad welcomes both Indian and international travelers. Compare experiences and what to expect.',
        'comparison' => [
            ['aspect' => 'Accommodation Prices', 'indian' => '₹1,500-₹3,000 for most hotels', 'international' => '₹3,000-₹8,000+ (premium demand)', 'winner' => 'Neutral (different tiers)'],
            ['aspect' => 'Language Support', 'indian' => 'Hindi + English widely spoken', 'international' => 'English common, limited other languages', 'winner' => 'Neutral'],
            ['aspect' => 'Tour Guides', 'indian' => 'Many Hindi-speaking guides', 'international' => 'English-speaking guides more expensive', 'winner' => 'Indian (cost)'],
            ['aspect' => 'Vegetarian Options', 'indian' => 'Abundant everywhere', 'international' => 'Limited, need to request', 'winner' => 'Indian'],
            ['aspect' => 'Travel Style', 'indian' => 'Family tours, groups, guided packages', 'international' => 'Solo/couples, self-guided, independent', 'winner' => 'Neutral'],
            ['aspect' => 'Travel Tips', 'indian' => 'Navigate easily, familiar culture', 'international' => 'May need extra planning, guides helpful', 'winner' => 'Indian'],
        ],
        'faqs' => [
            ['q' => 'Are international tourists overcharged?', 'a' => 'Not on CSNExplore. We offer transparent pricing for all tourists, Indian and international, with no discrimination.'],
            ['q' => 'Is English widely understood?', 'a' => 'Yes, especially in hotels, restaurants, and tourist areas. Our staff and guides are English-proficient.'],
            ['q' => 'What facilities do international tourists need?', 'a' => 'Credit card acceptance, English menus, English-speaking guides (available on CSNExplore), and Western amenities.'],
        ]
    ]
];

// Validate comparison type
if (!isset($comparisons[$type])) {
    $type = 'hotels-vs-homestays';
}

$data = $comparisons[$type];
$page_title = $data['title'];
$page_meta = [
    'description' => $data['description'],
    'canonical' => 'https://csnexplore.com/compare?type=' . urlencode($type),
    'type' => 'website',
    'image' => 'https://csnexplore.com/images/compare-hero.webp',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Comparisons', 'url' => '/compare'],
        ['name' => str_replace('-', ' vs ', ucwords($type, '-')), 'url' => '/compare?type=' . urlencode($type)],
    ],
];

// Generate Comparison schema
$comparison_items = [];
foreach ($data['comparison'] as $item) {
    $comparison_items[] = [
        '@type' => 'ListItem',
        'position' => count($comparison_items) + 1,
        'name' => $item['aspect'],
        'description' => $item[array_key_exists('hotels', $item) ? 'hotels' : (array_key_exists('car', $item) ? 'car' : (array_key_exists('ajanta', $item) ? 'ajanta' : 'package'))]
    ];
}

$comparison_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'itemListElement' => $comparison_items
];

// FAQ schema for comparisons
$faq_items = [];
foreach ($data['faqs'] as $faq) {
    $faq_items[] = [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a']
        ]
    ];
}

$faq_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faq_items
];

$extra_head = '<script type="application/ld+json">' . json_encode($comparison_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'
            . '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

require 'header.php';
?>

<main style="background: #f8f6f6;">

<!-- Hero Section -->
<section class="relative h-[340px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" width="800" height="600" class="w-full h-full object-cover"
             src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&q=80&auto=format"
             alt="Comparison Guide"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black"></div>
    </div>
    
    <div class="relative z-10 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-3"><?php echo $data['h1']; ?></h1>
        <p class="text-lg text-white/90 max-w-2xl mx-auto px-5"><?php echo $data['intro']; ?></p>
    </div>
</section>

<!-- Content Section -->
<section class="max-w-5xl mx-auto px-5 py-16">
    
    <!-- Comparison Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-12">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-primary to-orange-500 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Aspect</th>
                        <th class="px-6 py-4 text-left font-semibold"><?php echo strtoupper(array_keys((array)$data['comparison'][0])[1]); ?></th>
                        <th class="px-6 py-4 text-left font-semibold"><?php echo strtoupper(array_keys((array)$data['comparison'][0])[2]); ?></th>
                        <th class="px-6 py-4 text-center font-semibold">Winner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['comparison'] as $row): ?>
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-800"><?php echo $row['aspect']; ?></td>
                        <td class="px-6 py-4 text-gray-700"><?php echo $row[array_keys($row)[1]]; ?></td>
                        <td class="px-6 py-4 text-gray-700"><?php echo $row[array_keys($row)[2]]; ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block bg-primary/20 text-primary px-3 py-1 rounded-full text-sm font-semibold">
                                <?php echo $row['winner']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-gradient-to-r from-primary/10 to-orange-50 rounded-lg p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Common Questions</h2>
        <div class="space-y-4">
            <?php foreach ($data['faqs'] as $faq): ?>
            <details class="group bg-white rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                <summary class="font-semibold text-gray-800 cursor-pointer flex items-center justify-between hover:text-primary transition-colors">
                    <span><?php echo $faq['q']; ?></span>
                    <span class="material-symbols-outlined text-2xl group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-gray-600 mt-4 text-sm leading-relaxed"><?php echo $faq['a']; ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-primary to-orange-500 rounded-lg p-8 text-white text-center">
        <h3 class="text-2xl font-bold mb-3">Ready to Book?</h3>
        <p class="mb-6">Browse our offerings and book the perfect experience for your trip.</p>
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="<?php echo BASE_PATH; ?>/listing/stays" class="btn-white inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">bed</span>
                Browse Hotels
            </a>
            <a href="<?php echo BASE_PATH; ?>/listing/cars" class="btn-white inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">directions_car</span>
                Browse Cars
            </a>
            <a href="<?php echo BASE_PATH; ?>/listing/attractions" class="btn-white inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">confirmation_number</span>
                Browse Attractions
            </a>
        </div>
    </div>

</section>

</main>

<?php require 'footer.php'; ?>
