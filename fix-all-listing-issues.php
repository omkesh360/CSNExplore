<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600);

require_once __DIR__ . '/php/config.php';

$db = getDB();

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   Fixing All Listing Detail Pages Issues                    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$total = 0;

// Get all listing detail HTML files
$files = glob(__DIR__ . '/listing-detail/*.html');

foreach ($files as $idx => $file) {
    $num = $idx + 1;
    $filename = basename($file);
    
    // Read file
    $html = file_get_contents($file);
    
    // 1. Fix logo path - change from broken path to correct relative path
    $html = preg_replace(
        '/<img src="\.\/[^"]*travelhub\.png" alt="CSNExplore" class="h-8 sm:h-9 object-contain">/',
        '<img src="../images/travelhub.png" alt="CSNExplore" class="h-8 sm:h-9 object-contain">',
        $html
    );
    
    // 2. Fix all external URLs to internal relative URLs (clean URLs without .php)
    $html = str_replace('https://darkorange-boar-588892.hostingersite.com/', '../', $html);
    
    // 3. Remove .php extensions for clean URLs
    $html = preg_replace('/href="\.\.\/([^"]+)\.php"/', 'href="../$1"', $html);
    
    // 4. Add 15-line description if missing
    // Extract category and ID from filename
    if (preg_match('/^(stays|cars|bikes)-(\d+)-/', $filename, $matches)) {
        $category = $matches[1];
        $id = $matches[2];
        
        // Get listing data from database
        $listing = $db->fetchAll("SELECT * FROM {$category} WHERE id = :id", [':id' => $id]);
        
        if (!empty($listing)) {
            $listing = $listing[0];
            $name = $listing['name'] ?? $listing['operator'] ?? 'Listing';
            $location = $listing['location'] ?? 'Chhatrapati Sambhajinagar';
            
            // Generate 15-line description based on category
            $description = generateDescription($category, $name, $location);
            
            // Find and replace the description section
            // Look for the "About This" section and add description
            $html = preg_replace(
                '/(<div class="bg-white rounded-2xl p-5 sm:px-6 shadow-sm border border-slate-100">.*?<h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">.*?<span class="material-symbols-outlined text-primary text-\[22px\]">info<\/span>.*?About This .*?<\/h2>.*?<p class="text-sm text-slate-600 leading-relaxed">)(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($description) . '$3',
                $html
            );
        }
    }
    
    // Save file
    file_put_contents($file, $html);
    
    echo "[$num/" . count($files) . "] ✓ $filename\n";
    $total++;
}

echo "\n✅ Successfully fixed $total listing detail pages!\n";
echo "   - Logo paths updated to ../images/travelhub.png\n";
echo "   - All URLs changed to internal relative paths\n";
echo "   - Clean URLs (removed .php extensions)\n";
echo "   - Added 15-line descriptions\n\n";

function generateDescription($category, $name, $location) {
    $descriptions = [
        'stays' => "Experience comfort and convenience at {$name}, located in the heart of {$location}. Our accommodation offers modern amenities and exceptional service to make your stay memorable. Whether you're visiting for business or leisure, we provide the perfect base for exploring the city.\n\nOur rooms are thoughtfully designed with your comfort in mind, featuring contemporary furnishings and all essential amenities. Each space is meticulously maintained to ensure a clean and welcoming environment. We take pride in offering personalized service that caters to your individual needs.\n\nThe property is strategically located near major attractions, shopping centers, and transportation hubs, making it easy to explore everything {$location} has to offer. Our friendly staff is always available to assist with recommendations and arrangements for local tours and activities.\n\nWe understand that every traveler has unique requirements, which is why we offer flexible check-in and check-out times. Our commitment to guest satisfaction means we go above and beyond to ensure your stay exceeds expectations. Book with us for an unforgettable experience in {$location}.",
        
        'cars' => "Rent the {$name} for your next adventure in {$location} and experience the freedom of self-drive exploration. This well-maintained vehicle offers reliability, comfort, and style for all your travel needs. Whether you're planning a family trip, business travel, or a weekend getaway, this car is perfect for you.\n\nOur rental service includes comprehensive insurance coverage, 24/7 roadside assistance, and flexible rental periods to suit your schedule. The vehicle is thoroughly inspected and sanitized before each rental to ensure your safety and peace of mind. We maintain our fleet to the highest standards for optimal performance.\n\nWith spacious interiors and modern features, the {$name} provides a comfortable driving experience for both short city trips and long highway journeys. The fuel-efficient engine helps you save on costs while enjoying smooth performance. Air conditioning, power steering, and entertainment systems come standard.\n\nOur transparent pricing includes no hidden charges, and we offer competitive rates for daily, weekly, and monthly rentals. Pick-up and drop-off services are available at convenient locations across {$location}. Experience hassle-free car rental with our professional service and well-maintained vehicles.",
        
        'bikes' => "Discover {$location} on two wheels with our {$name} rental service. This reliable and fuel-efficient bike is perfect for navigating city streets and exploring nearby attractions at your own pace. Whether you're a solo traveler or looking for an economical transportation option, our bike rental service has you covered.\n\nThe {$name} is known for its excellent mileage, comfortable seating, and easy handling, making it ideal for both short trips and day-long adventures. We ensure each bike is in top condition with regular maintenance and thorough safety checks before every rental. Your safety is our priority.\n\nOur rental package includes a helmet, basic toolkit, and 24/7 support in case of any issues. We provide flexible rental options from hourly to monthly plans, catering to tourists, students, and professionals alike. The booking process is simple and quick, getting you on the road in no time.\n\nExplore the historic sites, local markets, and hidden gems of {$location} with the freedom and flexibility that only a bike can offer. Our competitive pricing and excellent customer service make us the preferred choice for bike rentals. Ride with confidence and create unforgettable memories.",
    ];
    
    return $descriptions[$category] ?? "Discover the best of {$location} with {$name}. We offer exceptional service and quality that exceeds expectations.";
}
?>
