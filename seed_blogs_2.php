<?php
require 'c:/xampp/htdocs/CSNExplore/php/config.php';

$db = getDB();

$blogs = [
    [
        'title' => 'A Foodie\'s Guide to Authentic Maharashtrian Thali in Aurangabad',
        'content' => '<h2>The Ultimate Thali Experience</h2><p>No trip to Chhatrapati Sambhajinagar is complete without indulging in an authentic Maharashtrian Thali. The thali is more than just a meal; it is a cultural showcase of the region\'s culinary diversity.</p><h3>What to Expect</h3><p>A typical thali here includes Puran Poli, Zunka Bhakar, Bharli Vangi (stuffed eggplant), Amti, and an array of chutneys, pickles, and papads. The food is a perfect balance of sweet, spicy, and tangy flavors.</p><h3>Top Recommendations</h3><p><strong>Bhoj Restaurant:</strong> Famous for its massive Rajasthani and Maharashtrian crossover thalis.<br><strong>Naivedya:</strong> Offers a premium, unlimited pure veg thali experience that is highly hygienic and family-friendly.<br><strong>Hotel Sai Prasad:</strong> For a more rustic, spicy local touch, this is the place to go.</p><h3>A Culinary Must-Do</h3><p>Don\'t forget to finish your meal with a glass of cool Sol Kadhi or sweet Shrikhand. These thalis offer the best value for money and a true taste of Maharashtra.</p>',
        'excerpt' => 'Discover the best places to enjoy an authentic, mouth-watering Maharashtrian Thali in the city.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Food & Drink',
        'read_time' => '4 min read',
        'tags' => json_encode(['Food', 'Thali', 'Restaurants']),
        'meta_title' => 'Best Authentic Maharashtrian Thali in Aurangabad',
        'meta_description' => 'Craving authentic local food? Read our foodie\'s guide to the best Maharashtrian Thalis in Chhatrapati Sambhajinagar (Aurangabad).',
        'meta_keywords' => 'Maharashtrian Thali Aurangabad, pure veg thali Sambhajinagar, Bhoj restaurant, local food',
        'slug' => 'foodies-guide-authentic-maharashtrian-thali-aurangabad',
        'focus_keyword' => 'Maharashtrian Thali in Aurangabad',
        'seo_score' => 92
    ],
    [
        'title' => 'Bibi Ka Maqbara: Exploring the Taj of the Deccan',
        'content' => '<h2>A Monument of Love in the South</h2><p>Often referred to as the \'Taj of the Deccan\', Bibi Ka Maqbara is a stunning mausoleum built in 1660 by Mughal Emperor Aurangzeb in memory of his first wife, Dilras Banu Begum.</p><h3>Architecture and History</h3><p>While it bears a striking resemblance to the Taj Mahal in Agra, it has its own unique charm. The monument is framed by the scenic Sikhara hills and is surrounded by typical Mughal Charbagh (four-part gardens). The intricate lattice work and the interplay of marble and plaster are exquisite.</p><h3>Best Time to Visit</h3><p>The monument is best visited during the early morning hours to beat the crowds and capture stunning photographs in the soft dawn light. Alternatively, an evening visit offers a beautiful view of the monument illuminated against the night sky.</p><h3>Visitor Tips</h3><p>The entry fee is nominal. Combine your visit with the nearby Panchakki to make the most of your afternoon.</p>',
        'excerpt' => 'A complete guide to visiting Bibi Ka Maqbara, the beautiful Taj of the Deccan.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Heritage',
        'read_time' => '3 min read',
        'tags' => json_encode(['Heritage', 'Monuments', 'Mughal Architecture']),
        'meta_title' => 'Bibi Ka Maqbara: The Taj of the Deccan | Travel Guide',
        'meta_description' => 'Explore Bibi Ka Maqbara in Chhatrapati Sambhajinagar. Discover its history, architecture, entry fees, and the best time to visit this Mughal masterpiece.',
        'meta_keywords' => 'Bibi Ka Maqbara, Taj of the Deccan, Aurangabad monuments, Maharashtra tourism',
        'slug' => 'bibi-ka-maqbara-exploring-taj-of-deccan',
        'focus_keyword' => 'Bibi Ka Maqbara',
        'seo_score' => 95
    ],
    [
        'title' => 'Daulatabad Fort Trek: Tips, Timings, and What to Expect',
        'content' => '<h2>Conquering the Invincible Fort</h2><p>Daulatabad Fort, also known as Devagiri, stands as one of the most powerful and unconquered forts in medieval Indian history. A trek to its summit is a must-do for history buffs and adventure seekers.</p><h3>The Architecture of Defense</h3><p>The fort is renowned for its complex defense mechanisms, including an intricate moat, massive spiked gates, and the infamous Andheri (Dark Passage) - a deceptive, pitch-black tunnel designed to trap invading armies.</p><h3>The Trek to the Top</h3><p>The trek involves climbing around 750 steep stairs. It is physically demanding but incredibly rewarding. At the summit, you are treated to a panoramic view of the surrounding plains.</p><h3>Tips for Visitors</h3><p>1. Start early. The climb gets exhausting under the midday sun.<br>2. Carry at least two bottles of water per person.<br>3. Wear comfortable trekking shoes.<br>4. Don\'t miss the stunning Chand Minar located near the entrance.</p>',
        'excerpt' => 'Everything you need to know about trekking to the top of the historic Daulatabad Fort.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Adventure',
        'read_time' => '4 min read',
        'tags' => json_encode(['Trekking', 'Forts', 'History']),
        'meta_title' => 'Daulatabad Fort Trek: Tips, Timings & Guide',
        'meta_description' => 'Planning to trek Daulatabad Fort? Read our complete guide on timings, stairs, the dark passage, and essential tips for reaching the summit.',
        'meta_keywords' => 'Daulatabad Fort trek, Devagiri fort, Aurangabad forts, trekking in Sambhajinagar',
        'slug' => 'daulatabad-fort-trek-tips-timings-what-to-expect',
        'focus_keyword' => 'Daulatabad Fort trek',
        'seo_score' => 93
    ],
    [
        'title' => 'The Best Luxury Resorts and Spa Hotels in Aurangabad',
        'content' => '<h2>Unwind in Royal Comfort</h2><p>After days of exploring rugged caves and ancient forts, treating yourself to a luxury stay is the perfect way to conclude your trip to Chhatrapati Sambhajinagar.</p><h3>Welcomhotel by ITC Hotels, Rama International</h3><p>Sprawling over 13 acres of landscaped gardens, this resort offers premium luxury. Their spa services are top-notch, and the dining options bring both global and local Peshwai cuisines to your table.</p><h3>Vivanta Aurangabad (Taj)</h3><p>Designed like a palace, Vivanta offers Mughal-inspired architecture and world-class hospitality. It\'s a quiet oasis featuring a massive pool and exquisite spa therapies to rejuvenate tired travelers.</p><h3>Meadows Resort and Spa</h3><p>Located slightly away from the city chaos, Meadows offers a boutique resort experience. It is perfect for families and couples looking for a serene getaway with lush green lawns and private cottages.</p><h3>Why Choose Luxury?</h3><p>The heat and physical exertion of visiting Ajanta and Daulatabad can be draining. A luxury spa hotel ensures you wake up refreshed and ready for your next adventure.</p>',
        'excerpt' => 'A curated list of the top luxury resorts and spa hotels for a relaxing stay in the city.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Accommodation',
        'read_time' => '4 min read',
        'tags' => json_encode(['Luxury', 'Hotels', 'Resorts', 'Spa']),
        'meta_title' => 'Best Luxury Resorts & Spa Hotels in Aurangabad',
        'meta_description' => 'Looking for luxury? Discover the best 5-star resorts and spa hotels in Chhatrapati Sambhajinagar for a relaxing and premium vacation experience.',
        'meta_keywords' => 'luxury resorts Aurangabad, 5 star hotels Sambhajinagar, Vivanta Taj Aurangabad, Rama International',
        'slug' => 'best-luxury-resorts-spa-hotels-aurangabad',
        'focus_keyword' => 'luxury resorts in Aurangabad',
        'seo_score' => 91
    ],
    [
        'title' => 'Cab vs. Self-Drive Car in Aurangabad: Which is Better for Tourists?',
        'content' => '<h2>Choosing Your Mode of Transport</h2><p>When planning a trip to the heritage sites of Chhatrapati Sambhajinagar, one of the biggest questions is whether to hire a cab with a driver or rent a self-drive car.</p><h3>The Case for a Chauffeured Cab</h3><p>Hiring a cab is stress-free. The local drivers know the routes to Ajanta, Ellora, and Daulatabad intimately. They can navigate the traffic, act as informal local guides, and you don\'t have to worry about parking or fatigue. It is ideal for families and older travelers.</p><h3>The Case for Self-Drive</h3><p>Renting a self-drive car through CSNExplore offers ultimate freedom. You can leave at your own pace, stop anywhere for photographs, and play your own music. The highways are generally excellent. It is highly recommended for young couples and adventure-seeking groups.</p><h3>The Verdict</h3><p>If you value convenience and local expertise, book a cab. If you value privacy and flexibility, a self-drive car is your best bet. Both options are readily available and competitively priced in the city.</p>',
        'excerpt' => 'Weighing the pros and cons of hiring a cab versus renting a self-drive car for tourists.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Transport',
        'read_time' => '3 min read',
        'tags' => json_encode(['Car Rental', 'Cabs', 'Transport']),
        'meta_title' => 'Cab vs Self-Drive Car in Aurangabad: Tourist Guide',
        'meta_description' => 'Should you hire a cab or rent a self-drive car in Chhatrapati Sambhajinagar? Read our comparison to find the best transport option for your trip.',
        'meta_keywords' => 'self drive car Aurangabad, hire cab Sambhajinagar, taxi vs self drive, tourist transport',
        'slug' => 'cab-vs-self-drive-car-aurangabad-tourist-guide',
        'focus_keyword' => 'self-drive car in Aurangabad',
        'seo_score' => 89
    ],
    [
        'title' => 'Exploring Aurangabad on an Electric Scooter: A Complete Guide',
        'content' => '<h2>Eco-Friendly Travel in a Historic City</h2><p>Electric scooters are rapidly becoming the preferred mode of transport for tourists navigating the bustling streets of Chhatrapati Sambhajinagar. They are silent, eco-friendly, and incredibly fun to ride.</p><h3>Why Choose an EV?</h3><p>The city\'s primary attractions like Bibi Ka Maqbara, Panchakki, and the local markets are clustered within a 10-15 km radius. An electric scooter easily covers these distances on a single charge while saving you money on fuel.</p><h3>Charging Infrastructure</h3><p>Many top hotels and hostels now offer free EV charging points for their guests. Additionally, rental agencies via CSNExplore ensure the scooters are fully charged and provide portable chargers for overnight use.</p><h3>What You Should Know</h3><p>EV scooters are perfect for city exploration, but we do not recommend them for the long 100km highway journey to Ajanta Caves due to range limitations. Use them for local sightseeing and evening food runs!</p>',
        'excerpt' => 'A guide to renting and navigating the city using eco-friendly electric scooters.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Transport',
        'read_time' => '3 min read',
        'tags' => json_encode(['Electric Scooter', 'EV', 'Green Travel']),
        'meta_title' => 'Exploring Aurangabad on an Electric Scooter',
        'meta_description' => 'Rent an electric scooter in Chhatrapati Sambhajinagar for an eco-friendly and affordable way to explore local city attractions like Bibi Ka Maqbara.',
        'meta_keywords' => 'electric scooter rental Aurangabad, EV rent Sambhajinagar, eco friendly travel',
        'slug' => 'exploring-aurangabad-on-electric-scooter-guide',
        'focus_keyword' => 'electric scooter in Aurangabad',
        'seo_score' => 90
    ],
    [
        'title' => 'The Complete Guide to Pure Veg and Jain Restaurants in Aurangabad',
        'content' => '<h2>Delicious Dining Without Compromise</h2><p>For tourists with strict dietary preferences, finding pure vegetarian and Jain food in a new city can be daunting. Fortunately, Chhatrapati Sambhajinagar boasts an excellent array of pure veg restaurants.</p><h3>Premium Dining</h3><p><strong>Kailash Veg Restaurant:</strong> Located near the central bus stand, it is a legendary spot offering excellent North Indian and Maharashtrian vegetarian fare.</p><h3>Thali and Traditional</h3><p><strong>Naivedya:</strong> As mentioned in our Thali guide, this is a 100% pure veg restaurant with excellent Jain food options prepared without onion and garlic.</p><h3>Street Food Safety</h3><p>If you are looking for pure veg street food, the stalls in Nirala Bazaar are highly recommended. Always specify your dietary requirements, and the vendors are usually very accommodating.</p><h3>Quick Bites</h3><p><strong>Smile Veg:</strong> A great family restaurant offering a multi-cuisine pure veg menu. Perfect for a quick lunch before heading out to Ellora.</p>',
        'excerpt' => 'A comprehensive guide to the best pure vegetarian and Jain-friendly restaurants in the city.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Food & Drink',
        'read_time' => '3 min read',
        'tags' => json_encode(['Pure Veg', 'Jain Food', 'Restaurants']),
        'meta_title' => 'Best Pure Veg and Jain Restaurants in Aurangabad',
        'meta_description' => 'Discover the top pure veg and Jain-friendly restaurants in Chhatrapati Sambhajinagar. Enjoy delicious local and North Indian food without compromise.',
        'meta_keywords' => 'pure veg restaurants Aurangabad, Jain food Sambhajinagar, Kailash veg, Naivedya thali',
        'slug' => 'complete-guide-pure-veg-jain-restaurants-aurangabad',
        'focus_keyword' => 'pure veg restaurants in Aurangabad',
        'seo_score' => 92
    ],
    [
        'title' => '5 Offbeat Monsoon Destinations Near Chhatrapati Sambhajinagar',
        'content' => '<h2>Monsoon Magic in Marathwada</h2><p>While summer can be harsh, the monsoon transforms the landscape around Chhatrapati Sambhajinagar into a lush, green paradise. Here are 5 offbeat spots to visit during the rains.</p><h3>1. Mhaismal</h3><p>A beautiful hill station just 40 km from the city. During the monsoon, the plateau is covered in mist, offering breathtaking views of the surrounding valleys.</p><h3>2. Gautala Autramghat</h3><p>The sanctuary comes alive during the rains. The waterfalls are in full flow, and the trekking trails are incredibly vibrant.</p><h3>3. Sulibhanjan</h3><p>Located near Daulatabad, this hill offers a serene trekking experience culminating in a small temple with a panoramic view of the wet, green plains.</p><h3>4. Jayakwadi Dam</h3><p>Watching the sheer volume of water released from the massive dam gates during heavy monsoons is an awe-inspiring experience.</p><h3>5. Ajanta Viewpoint</h3><p>While the caves are famous, the specific viewpoint from the opposite hill during the monsoon shows a spectacular U-shaped gorge with a cascading waterfall right next to the ancient caves.</p>',
        'excerpt' => 'Discover the lush, green, offbeat destinations near the city that come alive during the monsoon.',
        'author' => 'CSNExplore Team',
        'status' => 'published',
        'category' => 'Hidden Gems',
        'read_time' => '4 min read',
        'tags' => json_encode(['Monsoon', 'Nature', 'Offbeat']),
        'meta_title' => '5 Offbeat Monsoon Destinations Near Aurangabad',
        'meta_description' => 'Experience the magic of the rains. Discover 5 offbeat monsoon destinations near Chhatrapati Sambhajinagar including Mhaismal and Gautala.',
        'meta_keywords' => 'monsoon destinations Aurangabad, Mhaismal hill station, places to visit in monsoon Sambhajinagar',
        'slug' => '5-offbeat-monsoon-destinations-near-chhatrapati-sambhajinagar',
        'focus_keyword' => 'monsoon destinations near Aurangabad',
        'seo_score' => 94
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
echo "Batch 2 done!\n";
