<?php
require 'c:/xampp/htdocs/CSNExplore/php/config.php';

$db = getDB();

$blogs = [
    [
        'title' => 'The Ultimate 3-Day Chhatrapati Sambhajinagar (Aurangabad) Itinerary',
        'content' => '<h2>Day 1: The Marvel of Ellora and Daulatabad</h2><p>Start your journey at the breathtaking Ellora Caves, a UNESCO World Heritage site. Spend your morning marveling at the Kailasa temple, carved entirely out of a single rock. In the afternoon, head to the formidable Daulatabad Fort, a marvel of medieval engineering. Cap off your day with a visit to Bibi Ka Maqbara, beautifully illuminated at night.</p><h2>Day 2: The Ancient Canvas of Ajanta</h2><p>Dedicate your entire second day to the Ajanta Caves. Located about two hours from the city, these caves house some of the finest surviving examples of ancient Indian art. The intricately painted murals depicting the life of Buddha are a sight to behold. Return to the city for a traditional Maharashtrian Thali.</p><h2>Day 3: Local Heritage and Culture</h2><p>Spend your final day exploring the city\'s gates, giving Chhatrapati Sambhajinagar its moniker "City of Gates". Visit the Panchakki, an ancient water mill, and shop for exquisite Himroo shawls and Paithani sarees in the local markets. Before you leave, savor some Naan Khaliya, a local culinary delight.</p>',
        'excerpt' => 'A comprehensive 3-day itinerary covering Ellora, Ajanta, Daulatabad Fort, and local culture.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Itineraries',
        'read_time' => '4 min read',
        'tags' => json_encode(['Itinerary', 'Travel Guide', 'Ajanta', 'Ellora']),
        'meta_title' => 'The Ultimate 3-Day Chhatrapati Sambhajinagar Itinerary',
        'meta_description' => 'Planning a trip? Discover the ultimate 3-day Chhatrapati Sambhajinagar itinerary covering Ajanta, Ellora Caves, Daulatabad Fort, and local food.',
        'meta_keywords' => 'Chhatrapati Sambhajinagar itinerary, Aurangabad trip, 3 days Aurangabad, Ajanta Ellora guide',
        'slug' => 'ultimate-3-day-chhatrapati-sambhajinagar-aurangabad-itinerary',
        'focus_keyword' => 'Chhatrapati Sambhajinagar itinerary',
        'seo_score' => 95
    ],
    [
        'title' => '10 Hidden Gems Near Aurangabad You Won\'t Find in Guidebooks',
        'content' => '<h2>Beyond the Famous Caves</h2><p>While Ajanta and Ellora take the spotlight, Chhatrapati Sambhajinagar is surrounded by lesser-known marvels waiting to be explored.</p><h3>1. Pitalkhora Caves</h3><p>Older than Ajanta, these rock-cut caves are tucked away in a picturesque valley. The waterfall during monsoon makes it magical.</p><h3>2. Jayakwadi Dam (Paithan)</h3><p>A massive reservoir perfect for bird-watching. Visit the nearby Dnyaneshwar Udyan, styled after the Brindavan Gardens.</p><h3>3. Bani Begum Garden</h3><p>A serene, historic Mughal-style garden offering peace away from the bustling city crowds.</p><h3>4. Gautala Autramghat Sanctuary</h3><p>A paradise for nature lovers, offering lush greenery, wildlife, and tranquil waterfalls during the rains.</p><h3>5. Lonar Crater Lake</h3><p>Though a bit further out, this hyper-velocity meteorite crater lake is an absolute geographical wonder with unique flora and fauna.</p><p>Venture off the beaten path and discover these secluded spots for a truly unique experience.</p>',
        'excerpt' => 'Discover the lesser-known attractions like Pitalkhora, Lonar Crater, and Gautala Sanctuary.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Hidden Gems',
        'read_time' => '5 min read',
        'tags' => json_encode(['Hidden Gems', 'Nature', 'Offbeat']),
        'meta_title' => '10 Hidden Gems Near Aurangabad You Must Visit',
        'meta_description' => 'Explore the unexplored. Here are 10 hidden gems near Chhatrapati Sambhajinagar (Aurangabad) including Pitalkhora caves and Lonar crater.',
        'meta_keywords' => 'hidden gems Aurangabad, offbeat destinations Sambhajinagar, Lonar crater, Pitalkhora caves',
        'slug' => '10-hidden-gems-near-aurangabad-you-wont-find-in-guidebooks',
        'focus_keyword' => 'hidden gems near Aurangabad',
        'seo_score' => 92
    ],
    [
        'title' => 'Exploring the Ajanta and Ellora Caves: A Complete Weekend Guide',
        'content' => '<h2>Two World Heritage Sites, One Epic Weekend</h2><p>Visiting Ajanta and Ellora Caves is a journey back in time. These UNESCO World Heritage sites are the crown jewels of Maharashtra tourism.</p><h3>The Ellora Experience</h3><p>Ellora features 34 caves representing Hinduism, Buddhism, and Jainism. The highlight is Cave 16, the Kailasa Temple. Carved top-down from a single basaltic rock, it is a masterpiece of ancient architecture. Best visited in the morning when the sunlight illuminates the intricate carvings.</p><h3>The Ajanta Canvas</h3><p>Ajanta, located 100km from the city, consists of 30 Buddhist caves known for their incredibly preserved mural paintings. These paintings offer a glimpse into the life and times of ancient India, telling stories of the Jataka tales.</p><h3>Travel Tips</h3><p>Rent a car for the weekend to comfortably cover both sites. Wear comfortable walking shoes, carry plenty of water, and hire a licensed guide to truly understand the profound history of these caves.</p>',
        'excerpt' => 'A complete guide to visiting the magnificent Ajanta and Ellora caves in one weekend.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Heritage',
        'read_time' => '6 min read',
        'tags' => json_encode(['Ajanta', 'Ellora', 'Heritage', 'Weekend Guide']),
        'meta_title' => 'Ajanta and Ellora Caves: Complete Weekend Guide',
        'meta_description' => 'Plan the perfect weekend trip to Ajanta and Ellora Caves with our complete guide on timings, transportation, and what to see.',
        'meta_keywords' => 'Ajanta caves guide, Ellora caves weekend, visit Ajanta Ellora, Chhatrapati Sambhajinagar heritage',
        'slug' => 'exploring-ajanta-and-ellora-caves-weekend-guide',
        'focus_keyword' => 'Ajanta and Ellora Caves',
        'seo_score' => 96
    ],
    [
        'title' => 'The Best Street Food in Chhatrapati Sambhajinagar: A Local\'s Guide',
        'content' => '<h2>A Culinary Journey Through the City of Gates</h2><p>Chhatrapati Sambhajinagar is a paradise for food lovers, blending Maharashtrian spices with rich Mughlai heritage.</p><h3>Naan Khaliya</h3><p>You cannot visit without trying Naan Khaliya, the city\'s signature dish. This rich, slow-cooked mutton curry served with a soft, tandoor-baked naan is an absolute delight.</p><h3>Tara Pan Centre</h3><p>End your meal with a visit to the legendary Tara Pan Centre. Known for its endless varieties of meetha pan, it\'s a cultural experience in itself.</p><h3>Kacchi Dabeli and Vada Pav</h3><p>For quick snacks, the streets around Nirala Bazaar and Gulmandi offer spicy Maharashtrian staples. The local Vada Pav and Kacchi Dabeli here are packed with flavor.</p><h3>Imarti and Jalebi</h3><p>Satisfy your sweet tooth with fresh, hot Imartis and Jalebis available in the older parts of the city. Food here is not just sustenance; it is a celebration of history.</p>',
        'excerpt' => 'Discover the best street food from Naan Khaliya to the legendary Tara Pan Centre.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Food & Drink',
        'read_time' => '4 min read',
        'tags' => json_encode(['Street Food', 'Food Guide', 'Naan Khaliya']),
        'meta_title' => 'Best Street Food in Chhatrapati Sambhajinagar (Aurangabad)',
        'meta_description' => 'Explore the mouth-watering street food in Chhatrapati Sambhajinagar. A local guide to Naan Khaliya, Vada Pav, and top food spots.',
        'meta_keywords' => 'Aurangabad street food, best food in Chhatrapati Sambhajinagar, Naan Khaliya, local food guide',
        'slug' => 'best-street-food-chhatrapati-sambhajinagar-locals-guide',
        'focus_keyword' => 'street food in Chhatrapati Sambhajinagar',
        'seo_score' => 90
    ],
    [
        'title' => 'Renting a Bike in Aurangabad: Everything You Need to Know',
        'content' => '<h2>The Freedom of Two Wheels</h2><p>Exploring Chhatrapati Sambhajinagar and its surrounding attractions on a rented bike is not just economical, but incredibly freeing. Here is everything you need to know.</p><h3>Why Rent a Bike?</h3><p>Navigating the local traffic is easier, and the ride to Daulatabad Fort or Ellora Caves is scenic. A two-wheeler allows you to stop and take photos whenever you please.</p><h3>How to Rent</h3><p>You can easily rent a bike right here on CSNExplore! We offer a variety of options from Activas for city rides to Royal Enfields for highway cruising. All you need is a valid driving license and a government ID.</p><h3>Safety First</h3><p>Always wear a helmet. The highways towards Ajanta and Ellora are generally well-maintained, but local city roads can be busy. Plan your rides during daylight hours for maximum safety.</p>',
        'excerpt' => 'A complete guide to renting two-wheelers in the city for an economical and freeing trip.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Transport',
        'read_time' => '3 min read',
        'tags' => json_encode(['Bike Rental', 'Transport', 'Tips']),
        'meta_title' => 'Renting a Bike in Aurangabad: Everything You Need to Know',
        'meta_description' => 'Want to rent a bike in Aurangabad? Read our complete guide on prices, requirements, and safety tips for two-wheeler rentals.',
        'meta_keywords' => 'rent a bike Aurangabad, bike rental Sambhajinagar, two wheeler rent, Aurangabad transport',
        'slug' => 'renting-a-bike-in-aurangabad-everything-you-need-to-know',
        'focus_keyword' => 'renting a bike in Aurangabad',
        'seo_score' => 88
    ],
    [
        'title' => 'Solo Female Travel in Aurangabad: Safety Tips and Best Stays',
        'content' => '<h2>A Safe and Enriching Experience</h2><p>Chhatrapati Sambhajinagar is generally a safe and welcoming city for solo female travelers. With its rich heritage and warm locals, your solo trip here can be incredibly rewarding.</p><h3>Safety Tips</h3><p>Dress modestly, especially when visiting religious and heritage sites like the caves or Bibi Ka Maqbara. While the city is safe during the day, it\'s advisable to avoid isolated areas after dark. Rely on trusted cab services or CSNExplore verified rentals for transportation.</p><h3>Best Stays for Solo Women</h3><p>Opt for reputed hotels or verified homestays. Places near CIDCO or Nirala Bazaar are central, busy, and safe. Many boutique hostels now cater specifically to backpackers, offering female-only dorms and secure environments.</p><h3>Connect with Locals</h3><p>The locals are proud of their history and love to share it. Join guided walking tours to explore the city\'s heritage safely while meeting fellow travelers.</p>',
        'excerpt' => 'Essential safety tips and stay recommendations for solo female travelers visiting the city.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Travel Tips',
        'read_time' => '4 min read',
        'tags' => json_encode(['Solo Travel', 'Female Travel', 'Safety']),
        'meta_title' => 'Solo Female Travel in Aurangabad: Safety Tips & Stays',
        'meta_description' => 'Planning a solo female trip to Aurangabad? Discover essential safety tips, transport advice, and the best secure stays for your journey.',
        'meta_keywords' => 'solo female travel Aurangabad, safe hotels Sambhajinagar, women travel safety, Aurangabad solo trip',
        'slug' => 'solo-female-travel-aurangabad-safety-tips-best-stays',
        'focus_keyword' => 'solo female travel in Aurangabad',
        'seo_score' => 93
    ],
    [
        'title' => 'Top 5 Budget Hostels for Backpackers in Chhatrapati Sambhajinagar',
        'content' => '<h2>Affordable Stays for the Modern Nomad</h2><p>Chhatrapati Sambhajinagar is increasingly becoming a hotspot for backpackers. Here are the top budget hostels offering comfort, community, and affordability.</p><h3>1. The Zostel Experience</h3><p>A favorite among backpackers, offering clean dorms, vibrant common areas, and a great place to meet fellow travelers heading to Ajanta and Ellora.</p><h3>2. Backpackers Enclave</h3><p>Located centrally, this hostel offers great connectivity to the bus stand and railway station, making early morning departures a breeze.</p><h3>3. The Heritage Dorms</h3><p>Aesthetically designed with local Maharashtrian decor, offering a cultural vibe without breaking the bank.</p><h3>4. City Center Youth Hostel</h3><p>A government-recognized facility offering incredibly cheap beds, perfect for the ultra-budget traveler.</p><h3>5. The Wanderer\'s Nest</h3><p>Known for its excellent rooftop cafe and community events, it is the perfect spot to unwind after a long day of cave exploration.</p>',
        'excerpt' => 'Discover the best affordable, community-driven hostels for backpackers in the city.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Accommodation',
        'read_time' => '3 min read',
        'tags' => json_encode(['Hostels', 'Backpacking', 'Budget Travel']),
        'meta_title' => 'Top 5 Budget Hostels in Chhatrapati Sambhajinagar',
        'meta_description' => 'Traveling on a budget? Check out our list of the top 5 budget hostels for backpackers in Chhatrapati Sambhajinagar for a cheap and vibrant stay.',
        'meta_keywords' => 'budget hostels Aurangabad, backpacker stay Sambhajinagar, cheap hotels, Zostel Aurangabad',
        'slug' => 'top-5-budget-hostels-backpackers-chhatrapati-sambhajinagar',
        'focus_keyword' => 'budget hostels in Chhatrapati Sambhajinagar',
        'seo_score' => 89
    ],
    [
        'title' => 'How to Travel from Aurangabad to Ajanta Caves on a Budget',
        'content' => '<h2>Smart Travel to the Ancient Caves</h2><p>Ajanta Caves lie about 100 kilometers from Chhatrapati Sambhajinagar. Traveling there doesn\'t have to be expensive.</p><h3>State Transport Buses (MSRTC)</h3><p>The most economical way to reach Ajanta is via government-run ST buses. Catch a bus from the Central Bus Stand (CBS) heading towards Jalgaon. The journey takes about 2.5 to 3 hours and costs very little. Get off at the Ajanta T-Junction.</p><h3>Shared Cabs</h3><p>You can find shared taxis near the bus stand. They are slightly faster than buses and reasonably priced, offering a good balance between cost and comfort.</p><h3>Renting a Two-Wheeler</h3><p>If you are traveling with a partner, renting a bike through CSNExplore and splitting the fuel cost is a highly economical and adventurous way to make the trip.</p><h3>Pro Tip</h3><p>Start your journey early in the morning (around 6 AM) to beat the heat, avoid crowds, and make the most of your budget day trip.</p>',
        'excerpt' => 'Learn the cheapest and most efficient ways to travel to Ajanta Caves from the city center.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Transport',
        'read_time' => '4 min read',
        'tags' => json_encode(['Budget Travel', 'Ajanta', 'Transport']),
        'meta_title' => 'How to Travel from Aurangabad to Ajanta Caves on a Budget',
        'meta_description' => 'Find out the cheapest ways to travel from Aurangabad to Ajanta Caves using buses, shared cabs, and rental bikes on a budget.',
        'meta_keywords' => 'Aurangabad to Ajanta bus, budget travel Ajanta caves, cheap transport Sambhajinagar',
        'slug' => 'how-to-travel-aurangabad-to-ajanta-caves-on-budget',
        'focus_keyword' => 'travel from Aurangabad to Ajanta Caves',
        'seo_score' => 91
    ]
];

$genScript = dirname(__DIR__) . '/php/api/generate_html.php';

foreach ($blogs as $b) {
    try {
        $id = $db->insert('blogs', $b);
        echo "Inserted blog: {$b['title']} with ID: $id\n";
        // Call regenerate
        pclose(popen("start /B C:\\xampp\\php\\php.exe \"$genScript\" blog $id", 'r'));
    } catch (Exception $e) {
        echo "Error inserting {$b['title']}: " . $e->getMessage() . "\n";
    }
}
echo "Batch 1 done!\n";
