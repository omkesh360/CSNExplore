<?php
// php/api/seed_blogs.php – Seeds all 300 blog posts
// Visit: /php/api/seed_blogs.php?secret=csnexplore_seed
require_once __DIR__ . '/../config.php';

$secret = $_GET['secret'] ?? $argv[1] ?? '';
if (php_sapi_name() !== 'cli' && $secret !== 'csnexplore_seed') {
    http_response_code(403); die('Forbidden');
}

$db = getDB()->getConnection();

$existing = $db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
if ($existing > 0) {
    echo "SKIP blogs (already has $existing rows)\n";
    if (php_sapi_name() !== 'cli') echo "<br>Blogs already seeded. Delete existing blogs first to re-seed.";
    exit;
}

// ── Blog images pool (Unsplash) ───────────────────────────────────────────────
$images = [
    'heritage'   => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80',
    'caves'      => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=80',
    'fort'       => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=800&q=80',
    'food'       => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&q=80',
    'travel'     => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80',
    'hotel'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
    'bike'       => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
    'car'        => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&q=80',
    'bus'        => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800&q=80',
    'restaurant' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80',
    'nature'     => 'https://images.unsplash.com/photo-1534430480872-3498386e7856?w=800&q=80',
    'temple'     => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80',
    'market'     => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800&q=80',
    'seo'        => 'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=800&q=80',
    'guide'      => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&q=80',
];

// ── Category + image mapping ──────────────────────────────────────────────────
function getBlogMeta($title, $index) {
    global $images;
    $t = strtolower($title);
    if (str_contains($t,'ajanta') || str_contains($t,'ellora') || str_contains($t,'cave')) return ['Heritage', $images['caves'], 'Heritage'];
    if (str_contains($t,'fort') || str_contains($t,'daulatabad')) return ['Heritage', $images['fort'], 'Heritage'];
    if (str_contains($t,'hotel') || str_contains($t,'stay') || str_contains($t,'hostel') || str_contains($t,'resort') || str_contains($t,'accommodation') || str_contains($t,'lodge') || str_contains($t,'homestay') || str_contains($t,'airbnb')) return ['Hotels & Stays', $images['hotel'], 'Hotels'];
    if (str_contains($t,'food') || str_contains($t,'restaurant') || str_contains($t,'eat') || str_contains($t,'dine') || str_contains($t,'biryani') || str_contains($t,'thali') || str_contains($t,'street food') || str_contains($t,'cuisine') || str_contains($t,'cafe') || str_contains($t,'dessert') || str_contains($t,'breakfast') || str_contains($t,'lunch') || str_contains($t,'dinner') || str_contains($t,'buffet')) return ['Food & Dining', $images['food'], 'Food'];
    if (str_contains($t,'bike') || str_contains($t,'motorcycle') || str_contains($t,'scooter')) return ['Transport', $images['bike'], 'Transport'];
    if (str_contains($t,'car') || str_contains($t,'drive') || str_contains($t,'vehicle')) return ['Transport', $images['car'], 'Transport'];
    if (str_contains($t,'bus') || str_contains($t,'transport') || str_contains($t,'train') || str_contains($t,'airport') || str_contains($t,'railway') || str_contains($t,'auto') || str_contains($t,'cab') || str_contains($t,'taxi') || str_contains($t,'travel by')) return ['Transport', $images['bus'], 'Transport'];
    if (str_contains($t,'temple') || str_contains($t,'mosque') || str_contains($t,'religious') || str_contains($t,'pilgrimage') || str_contains($t,'grishneshwar')) return ['Heritage', $images['temple'], 'Heritage'];
    if (str_contains($t,'market') || str_contains($t,'shopping') || str_contains($t,'mall')) return ['Shopping', $images['market'], 'Shopping'];
    if (str_contains($t,'nature') || str_contains($t,'lake') || str_contains($t,'garden') || str_contains($t,'park') || str_contains($t,'wildlife') || str_contains($t,'waterfall') || str_contains($t,'bird')) return ['Nature', $images['nature'], 'Nature'];
    if (str_contains($t,'seo') || str_contains($t,'rank') || str_contains($t,'google') || str_contains($t,'website') || str_contains($t,'blog') || str_contains($t,'content') || str_contains($t,'affiliate') || str_contains($t,'monetiz') || str_contains($t,'traffic') || str_contains($t,'keyword') || str_contains($t,'booking')) return ['Travel Business', $images['seo'], 'Travel Business'];
    return ['Travel Guide', $images['guide'], 'Travel Guide'];
}

function generateContent($title) {
    $city = 'Chhatrapati Sambhajinagar';
    return "<h2>Introduction</h2>
<p>Welcome to our comprehensive guide on <strong>{$title}</strong>. Whether you're a first-time visitor or a seasoned traveler to {$city}, this guide will help you make the most of your experience.</p>

<h2>Why This Matters</h2>
<p>{$city}, formerly known as Aurangabad, is a city rich in history, culture, and natural beauty. Located in the Marathwada region of Maharashtra, it serves as the gateway to the UNESCO World Heritage Sites of Ajanta and Ellora Caves.</p>

<h2>Key Highlights</h2>
<ul>
<li>Discover the best experiences {$city} has to offer</li>
<li>Practical tips and insider knowledge for travelers</li>
<li>Budget-friendly options for every type of traveler</li>
<li>Local insights you won't find in mainstream guides</li>
</ul>

<h2>Getting There</h2>
<p>{$city} is well-connected by air, rail, and road. The Chhatrapati Sambhajinagar Airport (IXU) has regular flights from Mumbai, Delhi, and Hyderabad. The city is also accessible via the Central Railway line.</p>

<h2>Best Time to Visit</h2>
<p>The ideal time to visit {$city} is between October and March when the weather is pleasant. Monsoon season (July–September) brings lush greenery but can affect outdoor activities.</p>

<h2>Local Tips</h2>
<p>Always carry water and sunscreen during summer months. Hire a local guide for heritage sites to get the most out of your visit. Try the local Maharashtrian cuisine — especially the Naan Qalia and Shahi Tukda.</p>

<h2>Conclusion</h2>
<p>We hope this guide helps you plan an unforgettable trip. For bookings, transport, and more travel tips, explore CSNExplore — your complete travel companion for {$city}.</p>";
}

// ── All 300 blog titles ───────────────────────────────────────────────────────
$titles = [
    // A. General Travel Guides (1–40)
    'Complete Travel Guide to Chhatrapati Sambhajinagar in 2026',
    'Top Places to Visit in Chhatrapati Sambhajinagar',
    'Best Time to Visit Chhatrapati Sambhajinagar',
    '1 Day Itinerary for Chhatrapati Sambhajinagar',
    '2 Day Travel Plan for Chhatrapati Sambhajinagar',
    'Budget Travel Guide to Chhatrapati Sambhajinagar',
    'Luxury Travel Experience in Chhatrapati Sambhajinagar',
    'Travel Cost Breakdown for Chhatrapati Sambhajinagar',
    'Family Trip Guide to Chhatrapati Sambhajinagar',
    'Solo Travel Guide to Chhatrapati Sambhajinagar',
    'Weekend Trip Plan for Chhatrapati Sambhajinagar',
    'Hidden Gems in Chhatrapati Sambhajinagar',
    'Travel Checklist for Chhatrapati Sambhajinagar',
    'What to Pack for Chhatrapati Sambhajinagar Trip',
    'Safety Tips for Tourists in Chhatrapati Sambhajinagar',
    'First-Time Visitor Guide to Chhatrapati Sambhajinagar',
    'Travel Tips for Exploring Chhatrapati Sambhajinagar',
    'Top Tourist Attractions Near Chhatrapati Sambhajinagar',
    'Why Visit Chhatrapati Sambhajinagar in 2026',
    'Travel Guide for Couples in Chhatrapati Sambhajinagar',
    'Travel Guide for Students Visiting Chhatrapati Sambhajinagar',
    'Travel Guide for Senior Citizens in Chhatrapati Sambhajinagar',
    'Travel Guide for Foreign Tourists in Chhatrapati Sambhajinagar',
    'Local Culture Guide to Chhatrapati Sambhajinagar',
    'History of Chhatrapati Sambhajinagar for Tourists',
    'Travel Mistakes to Avoid in Chhatrapati Sambhajinagar',
    'Nightlife Guide to Chhatrapati Sambhajinagar',
    'Shopping Guide to Chhatrapati Sambhajinagar',
    'Local Markets Guide in Chhatrapati Sambhajinagar',
    'Festivals Guide to Chhatrapati Sambhajinagar',
    'Monsoon Travel Guide to Chhatrapati Sambhajinagar',
    'Summer Travel Guide to Chhatrapati Sambhajinagar',
    'Winter Travel Guide to Chhatrapati Sambhajinagar',
    'Best Photography Spots in Chhatrapati Sambhajinagar',
    'Sunrise and Sunset Spots in Chhatrapati Sambhajinagar',
    'Offbeat Travel Guide to Chhatrapati Sambhajinagar',
    'Travel Budget Tips for Chhatrapati Sambhajinagar',
    'Local Language Tips for Tourists in Chhatrapati Sambhajinagar',
    'Travel Guide for Digital Nomads in Chhatrapati Sambhajinagar',
    'Best Travel Apps for Visiting Chhatrapati Sambhajinagar',
    // B. Ajanta & Ellora Focus (41–80)
    'Complete Guide to Ajanta Caves',
    'Complete Guide to Ellora Caves',
    'Ajanta vs Ellora: Which One to Visit First',
    'Ajanta Caves Travel Tips for 2026',
    'Ellora Caves Travel Tips for 2026',
    'Best Time to Visit Ajanta Caves',
    'Best Time to Visit Ellora Caves',
    'Ajanta Caves Entry Fees and Timings',
    'Ellora Caves Entry Fees and Timings',
    'One Day Ajanta Trip Guide',
    'One Day Ellora Trip Guide',
    'Ajanta and Ellora in 2 Days Itinerary',
    'Photography Tips for Ajanta Caves',
    'History of Ajanta Caves Explained',
    'History of Ellora Caves Explained',
    'How to Reach Ajanta Caves from Sambhajinagar',
    'How to Reach Ellora Caves from Sambhajinagar',
    'Ajanta Caves Architecture Guide',
    'Ellora Kailasa Temple Explained',
    'Best Hotels Near Ajanta Caves',
    'Best Hotels Near Ellora Caves',
    'Ajanta Caves Travel Cost Breakdown',
    'Ellora Caves Travel Cost Breakdown',
    'Ajanta Travel by Bus Guide',
    'Ellora Travel by Bike Guide',
    'Ajanta Caves for Foreign Tourists',
    'Ellora Caves UNESCO World Heritage Guide',
    'Ajanta and Ellora Travel Mistakes to Avoid',
    'Local Guide vs Self Tour at Ajanta Ellora',
    'Ajanta Caves Nearby Attractions',
    'Ellora Caves Nearby Attractions',
    'Ajanta Tour Packages Guide',
    'Ellora Tour Packages Guide',
    'Ajanta Caves Hidden Spots',
    'Ellora Caves Secret Spots',
    'Ajanta Travel FAQs Answered',
    'Ellora Travel FAQs Answered',
    'Ajanta Travel with Family Guide',
    'Ellora Travel with Kids Guide',
    'Ajanta Ellora Combined Budget Plan',
    // C. Hotels & Stays (81–130)
    'Best Hotels in Chhatrapati Sambhajinagar',
    'Budget Hotels Under Rs 1000 in Sambhajinagar',
    'Hotels Under Rs 2000 in Sambhajinagar',
    'Luxury Hotels in Chhatrapati Sambhajinagar',
    '3-Star Hotels Guide in Sambhajinagar',
    '5-Star Hotels Guide in Sambhajinagar',
    'Family-Friendly Hotels in Sambhajinagar',
    'Couple-Friendly Hotels in Sambhajinagar',
    'Safe Hotels for Solo Travelers in Sambhajinagar',
    'Hotels Near Airport in Sambhajinagar',
    'Hotels Near Railway Station in Sambhajinagar',
    'Hotels Near Bus Stand in Sambhajinagar',
    'Hotels Near Ellora Caves',
    'Hotels Near Ajanta Caves',
    'Cheap Lodges in Chhatrapati Sambhajinagar',
    'Best Guest Houses in Sambhajinagar',
    'Boutique Stays in Sambhajinagar',
    'Homestays Guide in Sambhajinagar',
    'OYO Hotels Review in Sambhajinagar',
    'Zostel and Hostel Guide in Sambhajinagar',
    'Top Rated Hotels by Google Reviews in Sambhajinagar',
    'Best Value for Money Hotels in Sambhajinagar',
    'Hotels with Swimming Pool in Sambhajinagar',
    'Hotels with Parking in Sambhajinagar',
    'Pet-Friendly Hotels in Sambhajinagar',
    'Business Hotels in Sambhajinagar',
    'Weekend Stay Deals in Sambhajinagar',
    'Last Minute Hotel Booking Tips for Sambhajinagar',
    'Hotel Booking Hacks for Sambhajinagar',
    'Compare Hotel Prices Guide for Sambhajinagar',
    'Airbnb Options in Sambhajinagar',
    'Safe Areas to Stay in Sambhajinagar',
    'Hotel vs Homestay in Sambhajinagar',
    'Best Area to Stay in Sambhajinagar',
    'Cheap Accommodation for Students in Sambhajinagar',
    'Hotels for Long Stay in Sambhajinagar',
    'Monthly Rental Options in Sambhajinagar',
    'Budget Backpacker Stays in Sambhajinagar',
    'Premium Resort Guide in Sambhajinagar',
    'Farm Stay Experience Near Sambhajinagar',
    'Staycation Ideas in Sambhajinagar',
    'Eco-Friendly Hotels in Sambhajinagar',
    'Hotels with Free Breakfast in Sambhajinagar',
    'Top Luxury Resorts Near Sambhajinagar',
    'Hotel Reviews Detailed Guide Sambhajinagar',
    'Newly Opened Hotels in Sambhajinagar 2026',
    'Hotels Near Tourist Spots in Sambhajinagar',
    'Hotels Near Shopping Areas in Sambhajinagar',
    'Hotels for Corporate Travelers in Sambhajinagar',
    'Hotels with Best Views in Sambhajinagar',
    // D. Food & Restaurants (131–180)
    'Best Restaurants in Chhatrapati Sambhajinagar',
    'Street Food Guide to Sambhajinagar',
    'Famous Food in Chhatrapati Sambhajinagar',
    'Top Biryani Places in Sambhajinagar',
    'Best Veg Restaurants in Sambhajinagar',
    'Best Non-Veg Restaurants in Sambhajinagar',
    'Cafes Guide in Sambhajinagar',
    'Coffee Shops Guide in Sambhajinagar',
    'Budget Food Places in Sambhajinagar',
    'Luxury Dining Restaurants in Sambhajinagar',
    'Family Restaurants in Sambhajinagar',
    'Couple Dining Places in Sambhajinagar',
    'Late Night Food Options in Sambhajinagar',
    'Best Breakfast Spots in Sambhajinagar',
    'Best Lunch Places in Sambhajinagar',
    'Best Dinner Restaurants in Sambhajinagar',
    'Top Food Under Rs 200 in Sambhajinagar',
    'Top Buffets in Sambhajinagar',
    'Must-Try Local Dishes in Sambhajinagar',
    'Hyderabadi Food Spots in Sambhajinagar',
    'Maharashtrian Food Guide in Sambhajinagar',
    'Mughlai Food Spots in Sambhajinagar',
    'Top Pizza Places in Sambhajinagar',
    'Top Burger Places in Sambhajinagar',
    'Best Desserts in Sambhajinagar',
    'Ice Cream Parlours in Sambhajinagar',
    'Food Delivery Guide in Sambhajinagar',
    'Zomato vs Swiggy in Sambhajinagar',
    'Best Rooftop Restaurants in Sambhajinagar',
    'Romantic Dining Places in Sambhajinagar',
    'Instagrammable Cafes in Sambhajinagar',
    'Hidden Food Spots in Sambhajinagar',
    'Clean and Hygienic Restaurants in Sambhajinagar',
    'Street Food Safety Tips in Sambhajinagar',
    'Best Food Near Ellora Caves',
    'Best Food Near Ajanta Caves',
    'Cheap Eats Guide in Sambhajinagar',
    'Top Thali Restaurants in Sambhajinagar',
    'Chinese Food Guide in Sambhajinagar',
    'South Indian Food Guide in Sambhajinagar',
    'Fast Food Guide in Sambhajinagar',
    'Best Bakeries in Sambhajinagar',
    'Top Sweet Shops in Sambhajinagar',
    'Midnight Food Guide in Sambhajinagar',
    'Best Juice Centers in Sambhajinagar',
    'Healthy Food Options in Sambhajinagar',
    'Diet-Friendly Restaurants in Sambhajinagar',
    'Food for Travelers Guide in Sambhajinagar',
    'Top Rated Restaurants Reviews in Sambhajinagar',
    'Complete Food Blog Guide to Sambhajinagar',
    // E. Transport & Rentals (181–220)
    'How to Reach Chhatrapati Sambhajinagar',
    'Complete Transport Guide to Sambhajinagar',
    'Local Transport Options in Sambhajinagar',
    'Auto Rickshaw Fare Guide in Sambhajinagar',
    'Cab Services Guide in Sambhajinagar',
    'Ola vs Uber Comparison in Sambhajinagar',
    'Bike Rental Guide in Sambhajinagar',
    'Car Rental Guide in Sambhajinagar',
    'Self Drive Car Options in Sambhajinagar',
    'Airport Transfer Guide in Sambhajinagar',
    'Railway Station Transport Guide in Sambhajinagar',
    'Bus Travel Guide in Sambhajinagar',
    'Travel by Bike Tips in Sambhajinagar',
    'Travel by Car Tips in Sambhajinagar',
    'Best Routes from Pune to Sambhajinagar',
    'Best Routes from Mumbai to Sambhajinagar',
    'Best Routes from Nashik to Sambhajinagar',
    'Distance Chart Guide from Sambhajinagar',
    'Petrol Pump Locations Guide in Sambhajinagar',
    'Parking Guide in Sambhajinagar',
    'Traffic Rules Guide for Sambhajinagar',
    'Local Travel Cost Guide in Sambhajinagar',
    'Shared Cab Guide in Sambhajinagar',
    'Travel Pass Options in Sambhajinagar',
    'Public Transport Tips in Sambhajinagar',
    'Electric Vehicle Rentals in Sambhajinagar',
    'Travel Safety Tips in Sambhajinagar',
    'Night Travel Guide in Sambhajinagar',
    'Highway Travel Guide from Sambhajinagar',
    'Travel Map Guide for Sambhajinagar',
    'Google Maps Tips for Travelers in Sambhajinagar',
    'Travel Apps Guide for Sambhajinagar',
    'Bus Booking Guide for Sambhajinagar',
    'Train Booking Guide for Sambhajinagar',
    'Airport Guide for Sambhajinagar',
    'Travel Budget Calculator for Sambhajinagar',
    'Car Rental Price Comparison in Sambhajinagar',
    'Bike Rental Price Comparison in Sambhajinagar',
    'Taxi Booking Tips in Sambhajinagar',
    'Transport FAQs for Sambhajinagar',
    // F. Attractions & Experiences (221–260)
    'Top Things to Do in Chhatrapati Sambhajinagar',
    'Adventure Activities Guide in Sambhajinagar',
    'Historical Places Guide in Sambhajinagar',
    'Forts Near Chhatrapati Sambhajinagar',
    'Religious Places Guide in Sambhajinagar',
    'Temples Guide in Sambhajinagar',
    'Mosques Guide in Sambhajinagar',
    'Museums Guide in Sambhajinagar',
    'Parks and Gardens Guide in Sambhajinagar',
    'Water Parks Guide Near Sambhajinagar',
    'Shopping Malls Guide in Sambhajinagar',
    'Local Markets Guide in Sambhajinagar',
    'Night Attractions Guide in Sambhajinagar',
    'Cultural Experiences Guide in Sambhajinagar',
    'Heritage Walk Guide in Sambhajinagar',
    'Photography Tour Guide in Sambhajinagar',
    'Food Walk Guide in Sambhajinagar',
    'Cycling Tour Guide in Sambhajinagar',
    'Trekking Near Chhatrapati Sambhajinagar',
    'Weekend Getaways from Sambhajinagar',
    'One Day Trips from Sambhajinagar',
    'Picnic Spots Near Sambhajinagar',
    'Romantic Places in Sambhajinagar',
    'Family Outings in Sambhajinagar',
    'Kids Attractions in Sambhajinagar',
    'Educational Trips in Sambhajinagar',
    'Best Viewpoints in Sambhajinagar',
    'Hidden Waterfalls Near Sambhajinagar',
    'Sunrise Points Near Sambhajinagar',
    'Sunset Points Near Sambhajinagar',
    'Rainy Season Attractions in Sambhajinagar',
    'Summer Attractions in Sambhajinagar',
    'Winter Attractions in Sambhajinagar',
    'Instagram Spots in Sambhajinagar',
    'Best Reels Locations in Sambhajinagar',
    'Travel Vlog Guide for Sambhajinagar',
    'Drone Photography Spots in Sambhajinagar',
    'Local Experiences Guide in Sambhajinagar',
    'Art and Culture Guide in Sambhajinagar',
    'Festival Experiences in Sambhajinagar',
    // G. SEO + Booking Intent (261–300)
    'Book Hotels Online in Sambhajinagar',
    'Best Hotel Booking Website for Sambhajinagar',
    'Cheap Hotel Booking Tips for Sambhajinagar',
    'Last Minute Booking Deals in Sambhajinagar',
    'Compare Hotels Online in Sambhajinagar',
    'Book Bike Rentals Online in Sambhajinagar',
    'Book Car Rentals Online in Sambhajinagar',
    'Book Tour Packages for Sambhajinagar',
    'Best Travel Packages for Sambhajinagar',
    'Ajanta Ellora Tour Booking Guide',
    'Book Local Guide Online in Sambhajinagar',
    'Online Ticket Booking Guide for Sambhajinagar',
    'Travel Deals Guide for Sambhajinagar',
    'Discount Travel Tips for Sambhajinagar',
    'Cheapest Travel Options in Sambhajinagar',
    'Budget Travel Hacks for Sambhajinagar',
    'Travel Affiliate Guide for Sambhajinagar',
    'Travel Website SEO Guide',
    'How to Rank Travel Website on Google',
    'Local SEO for Travel Business in Sambhajinagar',
    'Google My Business Guide for Travel',
    'Travel Keywords Guide for Sambhajinagar',
    'Blog SEO Guide for Travel Websites',
    'Travel Content Strategy Guide',
    'How to Get Traffic to Travel Website',
    'How to Rank on Google Maps for Travel',
    'Local Listings Guide for Travel Business',
    'Review Management Guide for Travel',
    'Backlink Strategy for Travel Site',
    'Travel Website Monetization Guide',
    'Affiliate Marketing for Travel Websites',
    'Ads Strategy for Travel Website',
    'Travel Funnel Strategy Guide',
    'Booking Conversion Tips for Travel',
    'UI UX for Travel Website Design',
    'Travel Website Design Guide',
    'Travel Blog Writing Guide',
    'Travel Content Ideas for 2026',
    'How to Build Travel Brand Online',
    'Travel Website Growth Strategy 2026',
];

// ── Generate dates: Jan 1 to Apr 15, 2026 (105 days for 300 blogs) ────────────
$start = strtotime('2026-01-01');
$end   = strtotime('2026-04-15');
$total_days = ($end - $start) / 86400; // 104 days
$total_blogs = count($titles);

$stmt = $db->prepare("INSERT INTO blogs (title, content, author, image, status, category, read_time, tags, meta_description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$count = 0;
foreach ($titles as $i => $title) {
    // Spread dates evenly across the range
    $day_offset = (int)round(($i / ($total_blogs - 1)) * $total_days);
    $date = date('Y-m-d H:i:s', $start + ($day_offset * 86400) + rand(28800, 72000)); // random time 8am-8pm

    [$category, $image, $tag] = getBlogMeta($title, $i);
    $content  = generateContent($title);
    $read_time = rand(4, 12) . ' min read';
    $meta     = 'Discover ' . $title . ' — your complete guide to Chhatrapati Sambhajinagar travel, tourism, and local experiences.';
    $tags     = json_encode([$tag, 'Sambhajinagar', 'Travel', 'Maharashtra']);

    $stmt->execute([$title, $content, 'CSNExplore Team', $image, 'published', $category, $read_time, $tags, $meta, $date, $date]);
    $count++;
}

echo "SEEDED blogs: $count rows\n";
if (php_sapi_name() !== 'cli') {
    echo "<br><strong>Done!</strong> $count blog posts seeded successfully.";
    echo "<br><a href='../../blogs.php'>View Blogs</a>";
}
