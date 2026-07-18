<?php
/**
 * CSNExplore SEO Optimizer - Comprehensive SEO Enhancement System
 * Implements 20+ years of SEO best practices for Chhatrapati Sambhajinagar & Aurangabad
 * 
 * Features:
 * - Dynamic meta tag generation with dual city names
 * - Structured data (JSON-LD) for all content types
 * - Image optimization and lazy loading
 * - Performance optimization
 * - Schema.org markup
 * - Open Graph and Twitter Cards
 * - Breadcrumb navigation
 * - FAQ schema
 * - Local business optimization
 */

if (!defined('SITE_URL')) define('SITE_URL', 'https://csnexplore.com');
if (!defined('SITE_NAME')) define('SITE_NAME', 'CSNExplore');

class SEOOptimizer {
    
    /**
     * Generate comprehensive SEO keywords combining both city names
     */
    public static function generateKeywords($type, $itemName = '', $additionalKeywords = []) {
        $baseKeywords = [
            'Chhatrapati Sambhajinagar',
            'Aurangabad',
            'Aurangabad Maharashtra',
            'Sambhajinagar tourism',
            'Aurangabad travel',
            'csnexplore',
            'csn explore',
            'csnxplore',
            'cnsexplore',
            'csnexplore.com',
            'csnexplore.in',
            'scsnexplore',
            'csnexplore tourism portal',
            'csnexplore aurangabad',
            'csnexplore sambhajinagar',
            'auranagabd',
            'auaranagabd',
            'aurangabd',
            'aurangbad',
            'aurnagabad',
            'Chtarapati sambhajinagar',
            'chatrapati sambhajinagar',
            'shambhaji nagar',
            'chhatrapati sambhajinagar tourism',
            'explore aurangabad',
            'aurangabad local guide',
            'aurangabad tourist information',
            'maharashtra tourism aurangabad'
        ];
        
        $typeKeywords = match($type) {
            'home' => [
                'Chhatrapati Sambhajinagar tourism',
                'Aurangabad travel guide',
                'Ajanta Caves tour',
                'Ellora Caves visit',
                'hotels Sambhajinagar',
                'hotels Aurangabad',
                'car rental Aurangabad',
                'bike rental Aurangabad',
                'Aurangabad attractions',
                'Sambhajinagar hotels',
                'things to do in Aurangabad',
                'Aurangabad tour packages',
                'Bibi Ka Maqbara',
                'Daulatabad Fort',
                'Aurangabad caves',
                'bike rental aurangabad csnexplore',
                'bike rental sambhajinagar csnexplore',
                'csnexplore bike rental',
                'scsnexplore bike rental aurangabad',
                'bike rental under 5000 csnexplore',
                'scooter rental aurangabad csnexplore',
                'two wheeler rental csnexplore',
                'car rentals near me aurangabad csnexplore',
                'cab booking aurangabad csnexplore',
                'car rentals under 3000 csnexplore',
                'sedan aurangabad csnexplore',
                'suv csnexplore',
                'car rental sambhajinagar csnexplore',
                'taxi service aurangabad csnexplore',
                'hotels csnexplore',
                'hostel aurangabad csnexplore',
                'hostels near satara csnexplore',
                'jain hotels aurangabad csnexplore',
                'hotels under xyz csnexplore',
                'hotels near aurangabad csnexplore',
                'budget stays csnexplore',
                'dine near aurangabad csnexplore',
                'veg restaurant aurangabad csnexplore',
                'biryani shop aurangabad csnexplore',
                'budget under 2 days csnexplore',
                'under 3 days csnexplore',
                'solo travelling csnexplore',
                'women travel safety csnexplore',
                'ajanta caves tour csnexplore',
                'ellora caves guide csnexplore',
                'bibi ka maqbara tickets csnexplore',
                'daulatabad fort trip csnexplore',
                'mhaismal hill station csnexplore',
                'jayakwadi dam sightseeing csnexplore',
                'aurangabad local tour csnexplore.in',
                'activa rental aurangabad csnexplore',
                'bullet rental sambhajinagar csnexplore',
                'ev scooter rental csnexplore',
                'electric bike rental aurangabad csnexplore',
                'innova rental aurangabad csnexplore',
                'self drive cars csnexplore.com',
                'cheap bike rentals scsnexplore',
                'homestays aurangabad csnexplore',
                'ajanta view homestay csnexplore',
                'couple friendly hotels csnexplore',
                'family resorts near aurangabad csnexplore',
                'luxury hotels sambhajinagar csnexplore',
                'cheap lodges aurangabad scsnexplore',
                'safe stays for women csnexplore',
                'aurangabad weekend trip csnexplore',
                'maharashtra tourism csnexplore.com',
                'family tour packages csnexplore',
                'solo backpacker sambhajinagar csnexplore',
                'aurangabad travel guide csnexplore.in',
                'tourist cab packages csnexplore',
                'heritage walk aurangabad csnexplore',
                'best misal pav aurangabad csnexplore',
                'maharashtrian thali csnexplore',
                'street food gul mandi csnexplore',
                'family restaurants sambhajinagar csnexplore',
                'cafes in aurangabad csnexplore',
                'dhaba near aurangabad csnexplore',
                'late night food aurangabad csnexplore',
                'ajanta ellora budget tour csnexplore',
                'cheap travel options csnexplore',
                'budget friendly stays csnexplore.in',
                'low cost trip planner csnexplore',
                'affordable sightseeing csnexplore',
                'budget hotels csnexplore.com',
                'backpacker stays scsnexplore',
                'homestays near sambhajinagar csnexplore',
                'bikes names csnexplore',
                'bikes rentals under 5000 csnexplore',
                'scooty rent per day csnexplore',
                'electric scooter rental csnexplore',
                'bullet rental aurangabad csnexplore',
                'scsnexplore bike rent',
                'car rental sambhajinagar csnexplore.com',
                'outstation cab booking csnexplore',
                'self drive car csnexplore.in',
                'taxi service near me csnexplore',
                'jain hotels dining csnexplore',
                'continental food csnexplore',
                'best restaurants csnexplore.com',
                'street food guide csnexplore',
                'hostels for solo travelers csnexplore',
                'lady travelling women safely single traveller csnexplore',
                'safe stays for women travelers csnexplore',
                'female solo travel guide csnexplore',
                'safe cab booking csnexplore',
                'top 10 things to do in aurangabad',
                'aurangabad 1 day itinerary',
                'aurangabad 2 day itinerary',
                'aurangabad weekend getaway',
                'best travel agency in aurangabad',
                'tourist spots in chhatrapati sambhajinagar',
                'aurangabad local sightseeing packages',
                'ajanta ellora tour packages from aurangabad',
                'best places to visit in aurangabad maharashtra',
                'aurangabad tourism official website alternative',
                'aurangabad tourism guide pdf',
                'chhatrapati sambhajinagar points of interest',
                'things to do in aurangabad at night',
                'aurangabad local market shopping',
                'aurangabad historical places list',
                'ajanta ellora caves package from mumbai',
                'aurangabad sightseeing tour bus',
                'book cab for ajanta ellora caves',
                'places to visit near aurangabad in monsoon',
                'aurangabad famous things to buy'
            ],
            'stays' => [
                'luxury hotels in aurangabad',
                'hotel naivedya aurangabad',
                'naivedya thali aurangabad',
                'its home aurangabad',
                'best time to visit ajanta ellora',
                'best time to visit ajanta and ellora caves',
                'dolphin travels aurangabad',
                'hemalkasa tourist places',
                'auranagabad hotel booking',
                'aurangabad hotel booking',
                'aurangabad hotel booking price',
                'aurangabad hotel booking online',
                'aurangabad hotel booking budget',
                'mtdc aurangabad hotel booking',
                'aurangabad bihar hotel booking',
                'the salt hotel aurangabad booking',
                'hotel booking in aurangabad maharashtra',
                'lemon tree hotel aurangabad booking',
                'aurangabad bihar hotel room booking price',
                'hotel booking at aurangabad',
                'aurangabad hotel booking trivago',
                'aurangabad hotel booking bihar',
                'aurangabad hotel book',
                'aurangabad hotel aurangabad',
                'aurangabad hotel best',
                'aurangabad hotel cost',
                'hotel aurangabad contact number',
                'hotel aurangabad price',
                'aurangabad hotel charges',
                'dubai aurangabad hotel',
                'dubai hotel aurangabad maharashtra',
                'hotel aurangabad family',
                'aurangabad book hotel',
                'ginger hotel aurangabad booking',
                'hotel aurangabad gymkhana',
                'hotel aurangabad maharashtra',
                'hotel booking in aurangabad',
                'hotel booking in aurangabad near railway station',
                'hotel booking in aurangabad bihar',
                'online hotel booking in aurangabad maharashtra',
                'hotel room booking in aurangabad',
                'hotel aurangabad kranti chowk',
                'aurangabad hotel lodge',
                'aurangabad hotel low price',
                'aurangabad hotel list',
                'aurangabad maharashtra hotel booking',
                'aurangabad mtdc hotel booking',
                'maharashtra aurangabad hotel',
                'mgm aurangabad hotel',
                'aurangabad hotel number',
                'taj hotel aurangabad online booking',
                'oyo hotel booking aurangabad',
                'aurangabad hotel prices',
                'aurangabad hotel room booking',
                'aurangabad bihar hotel room booking',
                'hotel aurangabad road',
                'aurangabad hotel rate',
                'aurangabad hotel room price',
                'aurangabad hotel raj',
                'taj hotel aurangabad booking',
                'aurangabad hotel to stay',
                'aurangabad hotels tripadvisor',
                'vits hotel aurangabad booking',
                'aurangabad vip hotel',
                'aurangabad hotels with bar',
                'aurangabad hotels agoda',
                'hotel aurangabad photos',
                'zostel aurangabad booking',
                'aurangabad hotel contact number',
                '2 star hotels in aurangabad maharashtra',
                'aurangabad 2 star hotel',
                'aurangabad hotel 3 star',
                '3 star hotels in aurangabad with tariff',
                'aurangabad hotels 4 star',
                'aurangabad 4 star hotels list',
                '4 * hotels in aurangabad',
                '5 star hotels in aurangabad with price',
                'aurangabad hotel 5 star',
                '5 star hotels in aurangabad maharashtra',
                '7 apple hotel aurangabad booking',
                'aurangabad 7 star hotel',
                '7 hotel aurangabad',
                'aurangabad hotel 7 apple',
                'hotel 7 12 aurangabad photos',
                'aurangabad hotel rates',
                'hotels Chhatrapati Sambhajinagar',
                'hotels Aurangabad',
                'homestay Aurangabad',
                'budget hotels Sambhajinagar',
                'luxury hotels Aurangabad',
                'book hotel Chhatrapati Sambhajinagar',
                'best hotels near Ajanta Caves',
                'hotels near Ellora Caves',
                'Aurangabad accommodation',
                'resorts in Aurangabad',
                'guest house Aurangabad',
                'OYO hotels Aurangabad',
                'Treebo hotels Aurangabad',
                'hotels csnexplore',
                'hostel aurangabad csnexplore',
                'hostels near satara csnexplore',
                'jain hotels aurangabad csnexplore',
                'hotels under xyz csnexplore',
                'hotels near aurangabad csnexplore',
                'budget stays csnexplore',
                'homestays aurangabad csnexplore',
                'ajanta view homestay csnexplore',
                'couple friendly hotels csnexplore',
                'family resorts near aurangabad csnexplore',
                'luxury hotels sambhajinagar csnexplore',
                'cheap lodges aurangabad scsnexplore',
                'safe stays for women csnexplore',
                'budget friendly stays csnexplore.in',
                'budget hotels csnexplore.com',
                'backpacker stays scsnexplore',
                'homestays near sambhajinagar csnexplore',
                'hostels for solo travelers csnexplore',
                'safe stays for women travelers csnexplore',
                'best hotels near aurangabad railway station',
                'cheap hotels near aurangabad bus stand',
                'luxury resorts in chhatrapati sambhajinagar',
                'aurangabad hotel booking with free breakfast',
                'couple friendly hotels in aurangabad no questions asked',
                'pet friendly hotels aurangabad',
                '5 star hotels in aurangabad',
                '3 star hotels in aurangabad',
                'budget accommodation aurangabad',
                'aurangabad dharamshala booking',
                'hourly hotels in aurangabad',
                'unmarried couple friendly hotels in aurangabad maharashtra',
                'hotels near prozone mall aurangabad',
                'hotels in cidco aurangabad',
                'MTDC resort aurangabad booking',
                'budget hotels near aurangabad airport',
                'luxury stays near ellora caves',
                'service apartments in aurangabad',
                'hotels in aurangabad with swimming pool',
                'best resorts near aurangabad for weekend'
            ],
            'cars' => [
                'self drive car in aurangabad',
                'self drive cars in aurangabad',
                'self drive car aurangabad',
                'car and bike rental aurangabad',
                'bike and car rental aurangabad',
                'car and bike rentals near me',
                'auranagabad car rentals',
                'aurangabad car rentals',
                'aurangabad car rental',
                'aurangabad car rental service',
                'aurangabad car rental with driver',
                'aurangabad car rental without driver',
                'aurangabad car rental reviews',
                'aurangabad car rental self drive',
                'aurangabad car rental photos',
                'aurangabad car rental price',
                'mumbai to aurangabad car rental',
                'pune to aurangabad car rental',
                'car rental at aurangabad',
                'aurangabad airport car rental',
                'aurangabad car rental rates',
                'aurangabad rent a car',
                'aurangabad bihar car rental',
                'aurangabad to nanded by car rental',
                'speedo bike & car rental aurangabad',
                'aurangabad rental cars',
                'clear car rental aurangabad',
                'cheapest car rental aurangabad',
                'cheapest car rental aurangabad maharashtra',
                'rental cars in aurangabad',
                'do car rental companies bring the car to you',
                'car rental aurangabad price',
                'car rental aurangabad airport',
                'aurangabad to dhule car rental',
                'car rental from aurangabad to nashik',
                'hyderabad to aurangabad car rental',
                'aurangabad car hire',
                'car rental in aurangabad',
                'car rental in aurangabad with driver',
                'car rental in aurangabad maharashtra',
                'car rental in aurangabad bihar',
                'car rental in aurangabad without driver',
                'best car rental in aurangabad',
                'luxury car rental in aurangabad',
                'cheapest car rental in aurangabad',
                'best car rental in aurangabad with driver',
                'cheapest car rental in aurangabad with driver',
                'aurangabad luxury car rental',
                'aurangabad to manmad car rental',
                'ms car rental aurangabad',
                'best car rental in aurangabad monthly',
                'ms car rental chhatrapati sambhajinagar aurangabad reviews',
                'aurangabad to nashik car rental',
                'aurangabad to nagpur car rental',
                'nasik to aurangabad car rental',
                'aurangabad to nashik car rental price',
                'aurangabad car on rent',
                'aurangabad to shirdi car rental price',
                'pune to aurangabad car rental price',
                'aurangabad car rental tripadvisor',
                'aurangabad to shirdi car rental',
                'self car rental aurangabad',
                'priti taxi car rental service aurangabad bihar reviews',
                'subodh car rental aurangabad',
                'priti taxi car rental service aurangabad bihar',
                'priti taxi car rental service aurangabad bihar photos',
                'best car rental service in aurangabad',
                'car rental aurangabad to pune',
                'car rental aurangabad to mumbai',
                'taxi aurangabad car rental',
                'aurangabad thane car rental',
                'wedding car rental aurangabad',
                'best car rental in aurangabad without driver',
                '10 seater car rental aurangabad',
                'self drive car Chhatrapati Sambhajinagar',
                'car hire Aurangabad',
                'Ajanta Caves car rental',
                'Ellora Caves car hire',
                'cab Aurangabad',
                'taxi Aurangabad',
                'Innova hire Aurangabad',
                'Swift rental Aurangabad',
                'Ertiga booking Aurangabad',
                'chauffeur driven car Aurangabad',
                'car rentals near me aurangabad csnexplore',
                'cab booking aurangabad csnexplore',
                'car rentals under 3000 csnexplore',
                'sedan aurangabad csnexplore',
                'suv csnexplore',
                'car rental sambhajinagar csnexplore',
                'taxi service aurangabad csnexplore',
                'innova rental aurangabad csnexplore',
                'self drive cars csnexplore.com',
                'tourist cab packages csnexplore',
                'car rental sambhajinagar csnexplore.com',
                'outstation cab booking csnexplore',
                'self drive car csnexplore.in',
                'taxi service near me csnexplore',
                'safe cab booking csnexplore',
                'innova crysta rental aurangabad',
                'swift dzire taxi aurangabad',
                'aurangabad airport taxi service',
                'cab from aurangabad airport to ajanta caves',
                'aurangabad to ellora caves taxi fare',
                'self drive suv aurangabad',
                'cheapest self drive cars aurangabad',
                'aurangabad outstation cabs',
                'aurangabad local sightseeing taxi fare',
                'tempo traveller on rent in aurangabad',
                'mumbai to aurangabad cab booking',
                'pune to aurangabad taxi fare',
                'nashik to aurangabad drop taxi',
                'aurangabad to shirdi outstation cab',
                'rent a car for outstation in aurangabad',
                'aurangabad car rental without deposit',
                '7 seater car on rent in aurangabad',
                'luxury car rental aurangabad for wedding',
                'mahindra thar on rent aurangabad',
                'self drive cars at aurangabad railway station'
            ],
            'bikes' => [
                'bike rental aurangabad',
                'rent bike in aurangabad',
                'bike on rent in aurangabad',
                'aurangabad bike rent',
                'aurangabad scooter rental',
                'auranagabad bike rentals',
                'aurangabad bike rentals',
                'aurangabad bike rental',
                'aurangabad bike rental price',
                'aurangabad bike hire',
                'bike rentals at aurangabad',
                'bike rentals in aurangabad',
                'bike rent in aurangabad',
                'bike rental in aurangabad',
                'aurangabad bike on rent',
                'ua bike rentals',
                'aurora bike rentals',
                'urbana bike rental',
                'aurangabad rent bike',
                'aurangabad bihar bike rent',
                'aurangabad bike ride',
                'ziarat e bike',
                '4 wheeler bike rental',
                '4 bike rental',
                '6 bike rack rental',
                '6 person bike rental near me',
                '6 seater bike rental',
                'scooter hire Chhatrapati Sambhajinagar',
                'motorcycle rental Aurangabad',
                'Activa rent Aurangabad',
                'Royal Enfield hire Aurangabad',
                'two wheeler rental Aurangabad',
                'bike on rent Aurangabad',
                'scooty rental Aurangabad',
                'bike rental aurangabad csnexplore',
                'bike rental sambhajinagar csnexplore',
                'csnexplore bike rental',
                'scsnexplore bike rental aurangabad',
                'bike rental under 5000 csnexplore',
                'scooter rental aurangabad csnexplore',
                'two wheeler rental csnexplore',
                'activa rental aurangabad csnexplore',
                'bullet rental sambhajinagar csnexplore',
                'ev scooter rental csnexplore',
                'electric bike rental aurangabad csnexplore',
                'cheap bike rentals scsnexplore',
                'bikes names csnexplore',
                'bikes rentals under 5000 csnexplore',
                'scooty rent per day csnexplore',
                'electric scooter rental csnexplore',
                'bullet rental aurangabad csnexplore',
                'scsnexplore bike rent',
                'honda activa on rent in aurangabad',
                'royal enfield bullet on rent aurangabad',
                'bike rental near aurangabad railway station',
                'scooty on rent near aurangabad bus stand',
                'monthly bike rental aurangabad',
                'cheapest bike rental chhatrapati sambhajinagar',
                'two wheeler on rent for ajanta caves',
                'rent a bike in aurangabad without deposit',
                'ev scooter on rent aurangabad',
                'bike hire aurangabad maharashtra',
                'honda shine on rent aurangabad',
                'ktm duke on rent aurangabad',
                'yamaha fz rent in aurangabad',
                'scooty rental in aurangabad near railway station',
                'bike rent in aurangabad contact number',
                'two wheeler rental agency in aurangabad',
                'cheapest scooty on rent in chhatrapati sambhajinagar',
                'aurangabad to ajanta on bike',
                'bike trip to ellora caves',
                'sports bike on rent in aurangabad',
                // competitor/alternative keywords – users searching these may be looking for CSNExplore
                '7 scooters rental services aurangabad',
                'joyride rental aurangabad',
                'joyride bikes aurangabad',
                'ride your bike aurangabad',
                'ridobiko bike rental',
                'ridobiko aurangabad',
                'eezee rentals',
                'ezee bike rentals',
                'raghubir bike rental',
                'snaprides',
                'banjara ride',
                'club ride',
                'just my ride',
                'prime ride',
                'rudra bike rental',
                'ridez bike rental',
                'freedom bike rental',
                'rent 2 wheeler near me aurangabad',
                'rent bike in kolhapur',
                'bike rental kolhapur',
                'bike on rent in kolhapur',
                'kolhapur bike rent',
                'bike rental in kolhapur',
                'bike rentals in shirdi',
                'shirdi bike rentals',
                'bike rental in dimapur',
                'bike rental thanjavur',
                'thanjavur bike rental',
                'bike rental in kudal',
                'scooty rent in alibaug',
                'self drive scooty aurangabad',
                'bike and car rental aurangabad',
                'car and bike rental aurangabad',
                'car and bike rentals near me',
                'bike on rent in vapi',
                'shine bike price in aurangabad',
                'aurangabad bike rent alternative'
            ],
            'attractions' => [
                'Ajanta Caves tour',
                'Ellora Caves visit',
                'Bibi Ka Maqbara Aurangabad',
                'Daulatabad Fort',
                'tourist places Chhatrapati Sambhajinagar',
                'Aurangabad caves',
                'Panchakki Aurangabad',
                'Grishneshwar Temple',
                'Aurangabad sightseeing',
                'places to visit in Aurangabad',
                'Aurangabad tourism',
                'heritage sites Aurangabad',
                'ajanta caves tour csnexplore',
                'ellora caves guide csnexplore',
                'bibi ka maqbara tickets csnexplore',
                'daulatabad fort trip csnexplore',
                'mhaismal hill station csnexplore',
                'jayakwadi dam sightseeing csnexplore',
                'aurangabad local tour csnexplore.in',
                'heritage walk aurangabad csnexplore',
                'ajanta ellora budget tour csnexplore',
                'affordable sightseeing csnexplore',
                'ajanta caves ticket booking online',
                'ellora caves entry fee 2026',
                'bibi ka maqbara timings',
                'daulatabad fort trekking',
                'grishneshwar jyotirlinga temple timings',
                'bhadra maruti temple khuldabad',
                'siddharth garden zoo aurangabad',
                'aurangabad caves trek',
                'places to visit near aurangabad within 100 kms',
                'chhatrapati shivaji maharaj museum aurangabad',
                'tomb of aurangzeb khuldabad',
                'soneri mahal aurangabad',
                'aurangabad caves location',
                'bani begum garden khuldabad',
                'jayakwadi dam paithan',
                'paithani saree shopping in aurangabad',
                'ajanta caves closed on which day',
                'ellora caves closed on tuesday',
                'aurangabad points of interest map',
                'chhatrapati sambhajinagar tourist map'
            ],
            'restaurants' => [
                'restaurants Chhatrapati Sambhajinagar',
                'best food Aurangabad',
                'biryani Sambhajinagar',
                'dine out Aurangabad',
                'Aurangabad cuisine',
                'Naan Qalia Aurangabad',
                'best restaurants Aurangabad',
                'food delivery Aurangabad',
                'cafes in Aurangabad',
                'street food Aurangabad',
                'dine near aurangabad csnexplore',
                'veg restaurant aurangabad csnexplore',
                'biryani shop aurangabad csnexplore',
                'best misal pav aurangabad csnexplore',
                'maharashtrian thali csnexplore',
                'street food gul mandi csnexplore',
                'family restaurants sambhajinagar csnexplore',
                'cafes in aurangabad csnexplore',
                'dhaba near aurangabad csnexplore',
                'late night food aurangabad csnexplore',
                'jain hotels dining csnexplore',
                'continental food csnexplore',
                'best restaurants csnexplore.com',
                'street food guide csnexplore',
                'best non veg restaurants in aurangabad',
                'top 10 veg restaurants in aurangabad',
                'famous naan qalia in aurangabad',
                'best biryani in chhatrapati sambhajinagar',
                'romantic dinner places in aurangabad',
                'rooftop restaurants in aurangabad',
                'best street food in aurangabad',
                'authentic maharashtrian thali aurangabad',
                'family pure veg restaurants aurangabad',
                'late night cafes in aurangabad',
                'best street food in gulmandi aurangabad',
                'famous non veg hotels in aurangabad',
                'best biryani near cidco aurangabad',
                'aurangabad food tour',
                'famous misal pav near me aurangabad',
                'pure veg thali in chhatrapati sambhajinagar',
                'best family dhaba on aurangabad pune highway',
                'candle light dinner near aurangabad',
                'best cafes for couples in aurangabad',
                'midnight food delivery in aurangabad'
            ],
            'buses' => [
                'shivneri bus near me',
                'shivneri bus aurangabad',
                'bus from Aurangabad',
                'MSRTC Shivneri',
                'bus booking Chhatrapati Sambhajinagar',
                'intercity bus Aurangabad',
                'Aurangabad to Mumbai bus',
                'Aurangabad to Pune bus',
                'bus tickets Aurangabad',
                'online bus booking Aurangabad',
                'aurangabad to pune shivneri bus booking',
                'aurangabad to mumbai sleeper bus',
                'aurangabad to nashik msrtc bus time table',
                'aurangabad central bus stand enquiry number',
                'cidco bus stand aurangabad timetable',
                'ac volvo bus aurangabad',
                'cheapest bus tickets from aurangabad',
                'aurangabad to nagpur bus booking',
                'aurangabad to shirdi bus schedule',
                'online st bus ticket booking aurangabad',
                'pune to aurangabad bus shivshahi',
                'mumbai to aurangabad bus volvo',
                'aurangabad to indore bus sleeper',
                'aurangabad to hyderabad bus online ticket',
                'msrtc aurangabad bus stand enquiry',
                'aurangabad central bus stand to railway station distance',
                'private bus booking aurangabad',
                'ac sleeper bus aurangabad to pune',
                'aurangabad to nanded msrtc bus',
                'cheapest bus from aurangabad to pune'
            ],
            'blogs' => [
                'Aurangabad travel blog',
                'Ajanta Caves guide 2026',
                'Ellora Caves tips',
                'Chhatrapati Sambhajinagar travel tips',
                'Aurangabad itinerary',
                'best time to visit Aurangabad',
                'Aurangabad travel guide',
                'things to know about Aurangabad',
                'budget under 2 days csnexplore',
                'under 3 days csnexplore',
                'solo travelling csnexplore',
                'women travel safety csnexplore',
                'aurangabad weekend trip csnexplore',
                'maharashtra tourism csnexplore.com',
                'family tour packages csnexplore',
                'solo backpacker sambhajinagar csnexplore',
                'aurangabad travel guide csnexplore.in',
                'cheap travel options csnexplore',
                'low cost trip planner csnexplore',
                'lady travelling women safely single traveller csnexplore',
                'female solo travel guide csnexplore',
                'ajanta ellora caves travel tips',
                'how to reach aurangabad',
                'best time to visit chhatrapati sambhajinagar',
                'aurangabad travel itinerary 3 days',
                'hidden gems in aurangabad',
                'solo trip to aurangabad',
                'budget travel guide aurangabad',
                'historical facts about aurangabad',
                'what to buy in aurangabad',
                'himroo shawl shopping aurangabad',
                'aurangabad travel blog 2026',
                'is aurangabad safe for female travellers',
                'best time to visit ajanta and ellora caves',
                'how many days are enough for aurangabad',
                'aurangabad food guide for tourists',
                'shopping guide chhatrapati sambhajinagar',
                'historical stories of daulatabad fort',
                'aurangabad to ajanta distance and travel time',
                'weekend trip to aurangabad from mumbai',
                'photography tips for ellora caves'
            ],
            default => $baseKeywords
        };
        
        $allKeywords = array_merge($baseKeywords, $typeKeywords, $additionalKeywords);
        
        if ($itemName) {
            array_unshift($allKeywords, $itemName);
            $allKeywords[] = "$itemName Aurangabad";
            $allKeywords[] = "$itemName Chhatrapati Sambhajinagar";
        }
        
        return implode(', ', array_unique($allKeywords));
    }
    
    /**
     * Generate optimized title with both city names
     */
    public static function generateTitle($type, $itemName = '', $customTitle = '') {
        if ($customTitle) return $customTitle;
        
        $year = date('Y');
        
        return match($type) {
            'home' => "CSNExplore – Hotels, Car & Bike Rentals, Ajanta Caves Tours | Chhatrapati Sambhajinagar (Aurangabad) $year",
            'stays' => "Hotels & Homestays in Chhatrapati Sambhajinagar (Aurangabad) | Book Now – CSNExplore",
            'cars' => "Car Rentals Chhatrapati Sambhajinagar (Aurangabad) | Self Drive & Chauffeur – CSNExplore",
            'bikes' => "Aurangabad Scooter Rental & Bike Rentals | Book Now – CSNExplore",
            'attractions' => "Ajanta & Ellora Caves Tours | Top Attractions Sambhajinagar (Aurangabad) – CSNExplore",
            'restaurants' => "Best Restaurants Chhatrapati Sambhajinagar (Aurangabad) | Dine Out – CSNExplore",
            'buses' => "Bus Routes from Aurangabad | Book Bus Tickets Sambhajinagar – CSNExplore",
            'blogs' => "Aurangabad Travel Blog $year | Ajanta Caves Guide – CSNExplore",
            'contact' => "Contact CSNExplore | Aurangabad Tourism Helpline +91-8600968888",
            'about' => "About CSNExplore | Chhatrapati Sambhajinagar (Aurangabad) Tourism Portal",
            'listing' => $itemName ? "$itemName | Chhatrapati Sambhajinagar (Aurangabad) – CSNExplore" : "Listings – CSNExplore",
            default => "CSNExplore – Chhatrapati Sambhajinagar (Aurangabad) Tourism"
        };
    }
    
    /**
     * Generate optimized meta description with both city names
     */
    public static function generateDescription($type, $itemName = '', $customDesc = '') {
        if ($customDesc) return substr($customDesc, 0, 160);
        
        return match($type) {
            'home' => 'Discover Chhatrapati Sambhajinagar (Aurangabad) with CSNExplore. Book hotels, rent cars & bikes, explore Ajanta & Ellora Caves, find restaurants. 500+ verified listings.',
            'stays' => 'Browse 500+ hotels, homestays & resorts in Chhatrapati Sambhajinagar (Aurangabad). Best prices, free cancellation. Book near Ajanta & Ellora Caves today.',
            'cars' => 'Rent a car in Chhatrapati Sambhajinagar (Aurangabad) from ₹800/day. Self-drive or with driver. Perfect for Ajanta Caves day trips. Book now!',
            'bikes' => 'Looking for Aurangabad scooter rental? Rent bikes & scooters in Aurangabad from ₹300/day. Best bike rentals in Aurangabad. Book your ride today!',
            'attractions' => 'Explore Ajanta Caves, Ellora Caves, Bibi Ka Maqbara & 15+ attractions in Chhatrapati Sambhajinagar (Aurangabad). Timings, entry fees & guided tours.',
            'restaurants' => 'Discover best restaurants, cafes & street food in Chhatrapati Sambhajinagar (Aurangabad). Biryani, Naan Qalia, thali & more. Read reviews.',
            'buses' => 'Book bus tickets from Aurangabad. MSRTC Shivneri, Volvo AC, sleeper buses to Mumbai, Pune & Nashik. Schedules, fares & online booking.',
            'blogs' => 'Read expert travel guides for Chhatrapati Sambhajinagar (Aurangabad). Ajanta Caves 2026 guide, Ellora tips, hotel reviews & local food guides.',
            'contact' => 'Contact CSNExplore for hotel bookings, car rentals & tour packages in Chhatrapati Sambhajinagar (Aurangabad). Call +91-8600968888 or WhatsApp.',
            'about' => 'CSNExplore is Chhatrapati Sambhajinagar (Aurangabad) leading tourism portal. 500+ hotels, car rentals, bike rentals & guided tours. Trusted since 2024.',
            'listing' => $itemName ? "Book $itemName in Chhatrapati Sambhajinagar (Aurangabad). Best prices, verified reviews, instant confirmation. CSNExplore." : "Browse listings in Chhatrapati Sambhajinagar (Aurangabad) – CSNExplore",
            default => 'Explore Chhatrapati Sambhajinagar (Aurangabad) with CSNExplore – Your trusted tourism partner.'
        };
    }
    
    /**
     * Generate comprehensive Organization schema
     * Enhanced for Google Knowledge Panel entity recognition
     */
    public static function generateOrganizationSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => ['TravelAgency', 'Organization'],
            '@id' => SITE_URL . '/#organization',
            'name' => 'CSNExplore',
            'legalName' => 'CSNExplore Tourism Portal',
            'alternateName' => [
                'CSN Explore',
                'csnexplore',
                'csnxplore',
                'CSN Explore Aurangabad',
                'CSNExplore.com',
                'CSNExplore.in'
            ],
            'brand' => [
                '@type' => 'Brand',
                'name' => 'CSNExplore',
                'description' => 'Premium travel portal for Chhatrapati Sambhajinagar (Aurangabad), India'
            ],
            'url' => SITE_URL,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => SITE_URL . '/images/Logo-light-optimized.webp',
                'width' => 240,
                'height' => 72
            ],
            'image' => SITE_URL . '/images/Logo-light-optimized.webp',
            'description' => 'CSNExplore is the leading tourism portal for Chhatrapati Sambhajinagar (Aurangabad), Maharashtra. Book verified hotels, self-drive car rentals, bike rentals, and explore Ajanta & Ellora Caves — all in one place.',
            'slogan' => 'Explore Chhatrapati Sambhajinagar Your Way',
            'telephone' => '+91-8600968888',
            'email' => 'supportcsnexplore@gmail.com',
            'founder' => [
                '@type' => 'Person',
                'name' => 'Omkesh'
            ],
            'foundingDate' => '2024-01-01',
            'foundingLocation' => [
                '@type' => 'Place',
                'name' => 'Chhatrapati Sambhajinagar, Maharashtra, India'
            ],
            'numberOfEmployees' => [
                '@type' => 'QuantitativeValue',
                'value' => 15
            ],
            'knowsLanguage' => [
                ['@type' => 'Language', 'name' => 'English'],
                ['@type' => 'Language', 'name' => 'Hindi'],
                ['@type' => 'Language', 'name' => 'Marathi']
            ],
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => '+91-8600968888',
                    'contactType' => 'customer service',
                    'contactOption' => 'TollFree',
                    'areaServed' => 'IN',
                    'availableLanguage' => ['English', 'Hindi', 'Marathi'],
                    'hoursAvailable' => [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                        'opens' => '09:00',
                        'closes' => '21:00'
                    ]
                ],
                [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer support',
                    'email' => 'supportcsnexplore@gmail.com',
                    'areaServed' => 'IN'
                ]
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Jay Tower, Samadhan Colony, Padampura',
                'addressLocality' => 'Chhatrapati Sambhajinagar',
                'addressRegion' => 'Maharashtra',
                'postalCode' => '431005',
                'addressCountry' => 'IN'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 19.8762,
                'longitude' => 75.3433
            ],
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'opens' => '09:00',
                'closes' => '21:00'
            ],
            'priceRange' => '₹₹',
            'sameAs' => [
                'https://www.instagram.com/csnexplore_/',
                'https://www.facebook.com/csnexplore',
                'https://twitter.com/csnexplore',
                'https://x.com/csnexplore',
                'https://about.me/csnexplore',
                'https://csnexplore.com',
                'https://csnexplore.in'
            ],
            'areaServed' => [
                [
                    '@type' => 'City',
                    'name' => 'Chhatrapati Sambhajinagar',
                    'alternateName' => 'Aurangabad'
                ],
                [
                    '@type' => 'AdministrativeArea',
                    'name' => 'Maharashtra',
                    'containedInPlace' => [
                        '@type' => 'Country',
                        'name' => 'India'
                    ]
                ]
            ],
            'knowsAbout' => [
                'Ajanta Caves UNESCO World Heritage Site',
                'Ellora Caves UNESCO World Heritage Site',
                'Bibi Ka Maqbara Aurangabad',
                'Daulatabad Fort',
                'Hotel Booking Chhatrapati Sambhajinagar',
                'Car Rental Aurangabad',
                'Bike Rental Aurangabad',
                'Tourism Services Maharashtra',
                'Travel Portal India',
                'Aurangabad Tourism'
            ],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'CSNExplore Travel Services',
                'itemListElement' => [
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Hotel & Homestay Booking in Chhatrapati Sambhajinagar']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Car Rental Aurangabad']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Bike Rental Aurangabad']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Ajanta & Ellora Caves Tour Packages']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Restaurant Discovery Chhatrapati Sambhajinagar']],
                    ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Bus Ticket Booking from Aurangabad']]
                ]
            ]
        ];
    }
    
    /**
     * Generate Local Business schema
     */
    public static function generateLocalBusinessSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'CSNExplore',
            'image' => SITE_URL . '/images/Logo-light-optimized.webp',
            'telephone' => '+91-8600968888',
            'email' => 'supportcsnexplore@gmail.com',
            'url' => SITE_URL,
            'hasMap' => 'https://maps.google.com/?q=Jay+Tower+Padampura+Chhatrapati+Sambhajinagar',
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'CSNExplore Services',
                'itemListElement' => [
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Hotel Booking'
                        ]
                    ],
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Car Rental'
                        ]
                    ],
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Bike Rental'
                        ]
                    ]
                ]
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Jay Tower, Padampura',
                'addressLocality' => 'Chhatrapati Sambhajinagar',
                'addressRegion' => 'Maharashtra',
                'postalCode' => '431005',
                'addressCountry' => 'IN'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 19.8762,
                'longitude' => 75.3433
            ],
            'openingHours' => 'Mo-Su 09:00-21:00',
            'priceRange' => '₹₹',
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => '1247',
                'bestRating' => '5',
                'worstRating' => '1'
            ]
        ];
    }
    
    /**
     * Generate ItemList Schema for Top Attractions (Homepage)
     */
    public static function generateItemListSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Top Historical Wonders to Visit in Chhatrapati Sambhajinagar',
            'description' => 'Verified ticket details and tour guide listings for local heritage sights.',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'item' => [
                        '@type' => 'TouristAttraction',
                        'name' => 'Ajanta Caves',
                        'description' => 'Ancient rock-cut Buddhist caves featuring magnificent fresco paintings.',
                        'url' => SITE_URL . '/attractions/ajanta-caves'
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'item' => [
                        '@type' => 'TouristAttraction',
                        'name' => 'Ellora Caves',
                        'description' => 'Monolithic rock-cut temples featuring the world-famous Kailash Temple.',
                        'url' => SITE_URL . '/attractions/ellora-caves'
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'item' => [
                        '@type' => 'TouristAttraction',
                        'name' => 'Bibi Ka Maqbara',
                        'description' => 'The scenic 17th-century burial monument mimicking the Taj Mahal architecture.',
                        'url' => SITE_URL . '/attractions/bibi-ka-maqbara'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Generate WebSite schema with SearchAction
     * Enhanced with @id linking for entity disambiguation
     */
    public static function generateWebSiteSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => SITE_URL . '/#website',
            'name' => 'CSNExplore',
            'alternateName' => [
                'CSN Explore',
                'csnexplore',
                'CSNExplore Tourism Portal',
                'CSNExplore Aurangabad',
                'CSNExplore Chhatrapati Sambhajinagar'
            ],
            'description' => 'Official website of CSNExplore — Chhatrapati Sambhajinagar (Aurangabad) premier travel and tourism portal.',
            'url' => SITE_URL . '/',
            'inLanguage' => ['en-IN', 'hi-IN', 'mr-IN'],
            'copyrightHolder' => [
                '@type' => 'Organization',
                '@id' => SITE_URL . '/#organization',
                'name' => 'CSNExplore'
            ],
            'potentialAction' => [
                [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => SITE_URL . '/listing?search={search_term_string}'
                    ],
                    'query-input' => 'required name=search_term_string'
                ],
                [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => SITE_URL . '/blogs?search={search_term_string}'
                    ],
                    'query-input' => 'required name=search_term_string'
                ]
            ],
            'publisher' => [
                '@type' => 'Organization',
                '@id' => SITE_URL . '/#organization',
                'name' => 'CSNExplore',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => SITE_URL . '/images/Logo-light-optimized.webp',
                    'width' => 240,
                    'height' => 72
                ]
            ]
        ];
    }
    
    /**
     * Generate optimized image alt text
     */
    public static function generateAltText($type, $name, $location = 'Chhatrapati Sambhajinagar') {
        $suffix = match($type) {
            'cars' => 'car rental',
            'bikes' => 'bike rental',
            'stays' => 'hotel',
            'attractions' => 'tourist attraction',
            'restaurants' => 'restaurant',
            'buses' => 'bus route',
            default => ''
        };
        
        $altText = trim("$name $suffix $location (Aurangabad)");
        return htmlspecialchars($altText) . ' – CSNExplore';
    }
    
    /**
     * Generate FAQ schema
     */
    public static function generateFAQSchema($faqs) {
        if (empty($faqs)) return null;
        
        $faqItems = [];
        foreach ($faqs as $faq) {
            $faqItems[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqItems
        ];
    }
    
    /**
     * Generate Breadcrumb schema
     */
    public static function generateBreadcrumbSchema($breadcrumbs) {
        if (empty($breadcrumbs)) return null;
        
        $items = [];
        foreach ($breadcrumbs as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => strpos($crumb['url'], 'http') === 0 ? $crumb['url'] : SITE_URL . $crumb['url']
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }
    
    /**
     * Generate HowTo Schema for the homepage
     */
    public static function generateHowToSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => 'How to Book a Stay or Rent a Vehicle in Chhatrapati Sambhajinagar',
            'description' => 'A step-by-step guide to booking verified stays, hotels, self-drive cars, or bike rentals in Chhatrapati Sambhajinagar (Aurangabad) via CSNExplore.',
            'estimatedCost' => [
                '@type' => 'HowToSupply',
                'estimatedCost' => '0',
                'currency' => 'INR'
            ],
            'totalTime' => 'PT5M',
            'step' => [
                [
                    '@type' => 'HowToStep',
                    'name' => 'Select Service Type',
                    'text' => 'Visit CSNExplore and choose between Stays, Cars, or Bikes depending on your travel needs in Chhatrapati Sambhajinagar.',
                    'url' => SITE_URL . '/'
                ],
                [
                    '@type' => 'HowToStep',
                    'name' => 'Browse Verified Listings',
                    'text' => 'Browse through our extensive list of hotels, homestays, self-drive cars, and scooters available in Aurangabad. Filter by price, ratings, and specific locations.',
                    'url' => SITE_URL . '/listing'
                ],
                [
                    '@type' => 'HowToStep',
                    'name' => 'Check Details & Reviews',
                    'text' => 'Click on any hotel or vehicle listing to view high-resolution photos, detailed specifications, pricing structure, and authentic reviews from previous travelers.',
                    'url' => SITE_URL . '/listing'
                ],
                [
                    '@type' => 'HowToStep',
                    'name' => 'Submit Booking Request',
                    'text' => 'Click on the book button, enter your dates and contact information, and submit your booking request securely.',
                    'url' => SITE_URL . '/listing'
                ],
                [
                    '@type' => 'HowToStep',
                    'name' => 'Verify and Confirm',
                    'text' => 'Our team will quickly verify availability. Check your booking status in the My Bookings portal or via email/SMS to finalize your booking.',
                    'url' => SITE_URL . '/my-booking'
                ]
            ]
        ];
    }
    
    /**
     * Render all SEO meta tags
     */
    public static function renderMetaTags($config) {
        $title = $config['title'] ?? self::generateTitle('home');
        $description = $config['description'] ?? self::generateDescription('home');
        $keywords = $config['keywords'] ?? self::generateKeywords('home');
        $canonical = $config['canonical'] ?? SITE_URL;
        $image = $config['image'] ?? SITE_URL . '/images/travelhub.png';
        $type = $config['type'] ?? 'website';
        
        $html = '';
        
        // Basic meta tags
        $html .= '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
        $html .= '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . "\n";
        $html .= '<link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";
        
        // Open Graph tags
        $html .= '<meta property="og:type" content="' . htmlspecialchars($type) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . htmlspecialchars($canonical) . '">' . "\n";
        $html .= '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
        $html .= '<meta property="og:image:width" content="1200">' . "\n";
        $html .= '<meta property="og:image:height" content="630">' . "\n";
        $html .= '<meta property="og:site_name" content="CSNExplore">' . "\n";
        $html .= '<meta property="og:locale" content="en_IN">' . "\n";
        $html .= '<meta property="og:locale:alternate" content="hi_IN">' . "\n";
        
        // Twitter Card tags
        $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '<meta name="twitter:site" content="@csnexplore">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
        
        // Additional SEO tags
        $html .= '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";
        $html .= '<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
        $html .= '<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
        
        // Geographic tags
        $html .= '<meta name="geo.region" content="IN-MH">' . "\n";
        $html .= '<meta name="geo.placename" content="Chhatrapati Sambhajinagar">' . "\n";
        $html .= '<meta name="geo.position" content="19.8762;75.3433">' . "\n";
        $html .= '<meta name="ICBM" content="19.8762, 75.3433">' . "\n";
        
        return $html;
    }
    
    /**
     * Render JSON-LD schema
     */
    public static function renderSchema($schema) {
        if (empty($schema)) return '';
        
        return '<script type="application/ld+json">' . "\n" .
               json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) .
               "\n" . '</script>' . "\n";
    }
}
