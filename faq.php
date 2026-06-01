<?php
// faq.php – Comprehensive FAQ Page with Schema Markup
$page_title = "FAQ – Frequently Asked Questions | CSNExplore – Chhatrapati Sambhajinagar (Aurangabad)";
$current_page = "faq";
require_once 'php/config.php';

$page_meta = [
    'description' => 'Frequently asked questions about travelling in Aurangabad (Chhatrapati Sambhajinagar / Sambhajianagar). Find answers about hotels, car rentals, bike rentals, Ajanta Caves, Ellora Caves, and everything else for your perfect Aurangabad trip.',
    'keywords' => 'travel Aurangabad FAQ, Sambhajianagar travel guide, travel auranagabd, how to travel Aurangabad, auranagabd tourism questions, csnexplore FAQ, hotel booking Aurangabad, car rental Chhatrapati Sambhajinagar, bike rental Sambhajianagar, Ajanta Caves tour, aurangabd, aurangbad, aurnagabad, Chtarapati sambhajinagar, chatrapati sambhajinagar, shambhaji nagar, csn explore, csnxplore, cnsexplore',
    'canonical' => 'https://csnexplore.com/faq',
    'type' => 'website',
    'image' => 'https://csnexplore.com/images/Logo-light-optimized.webp',
];

// FAQ Categories
$faqs = [
    'General' => [
        [
            'question' => 'What is CSNExplore?',
            'answer' => 'CSNExplore is a comprehensive tourism portal for Chhatrapati Sambhajinagar (formerly Aurangabad), Maharashtra. We provide hotel bookings, car rentals, bike rentals, attraction tours, restaurant reservations, and bus ticket booking—all in one platform.'
        ],
        [
            'question' => 'How do I travel in Aurangabad (Chhatrapati Sambhajinagar)?',
            'answer' => 'The best way to travel in Aurangabad (Chhatrapati Sambhajinagar) is to rent a car or bike through CSNExplore. We offer verified vehicles from ₹300/day for bikes and ₹800/day for cars. For group travel, book a Maruti Ertiga or Toyota Innova for outstation trips to Ajanta and Ellora Caves. Our bus section also covers MSRTC and private intercity routes.'
        ],
        [
            'question' => 'What is the best time to travel in Chhatrapati Sambhajinagar (Aurangabad)?',
            'answer' => 'The best time to travel in Chhatrapati Sambhajinagar (Aurangabad) is from October to March. The weather is cool and pleasant, ideal for visiting Ajanta Caves, Ellora Caves, Bibi Ka Maqbara, and Daulatabad Fort. Avoid May and June due to extreme heat.'
        ],
        [
            'question' => 'How do I reach Aurangabad (Chhatrapati Sambhajinagar / Sambhajinagar)?',
            'answer' => 'Aurangabad (Chhatrapati Sambhajinagar) is accessible by air (Chhatrapati Sambhajinagar Airport with flights from Mumbai, Delhi, Hyderabad), by train (Devgiri Express, Tapovan Express) and by road (buses from Mumbai, Pune, Nashik via CSNExplore bus booking). The city is about 375 km from Mumbai.'
        ],
        [
            'question' => 'Is CSNExplore safe and secure?',
            'answer' => 'Yes. CSNExplore uses SSL encryption, secure payment gateways, and follows strict data protection protocols. All transactions are verified and secure. We are HTTPS enabled with industry-standard security.'
        ],
        [
            'question' => 'What cities does CSNExplore serve?',
            'answer' => 'Our primary focus is Chhatrapati Sambhajinagar (Aurangabad). We also provide services and information for nearby attractions like Ajanta Caves, Ellora Caves, and surrounding areas in Maharashtra.'
        ],
        [
            'question' => 'How do I contact CSNExplore?',
            'answer' => 'You can reach us via: Phone: +91-8600968888 | Email: support@csnexplore.com | WhatsApp: Click WhatsApp icon in footer | Address: Jay Tower, Padampura, Chhatrapati Sambhajinagar, Maharashtra 431005'
        ],
    ],
    'Hotel Bookings' => [
        [
            'question' => 'What is your cancellation policy?',
            'answer' => 'Most hotels on CSNExplore offer free cancellation up to 48 hours before check-in. Some offer flexible cancellation up to 72 hours. Check individual hotel policies for specific terms before booking.'
        ],
        [
            'question' => 'Do you offer group discounts?',
            'answer' => 'Yes! For group bookings of 10+ rooms, contact our team at +91-8600968888 or support@csnexplore.com for special rates and customized packages.'
        ],
        [
            'question' => 'Can I modify my booking?',
            'answer' => 'Yes, you can modify your booking through your account or by contacting us. Modifications are subject to availability and hotel policies. Request changes at least 24 hours before check-in.'
        ],
        [
            'question' => 'What payment methods are accepted?',
            'answer' => 'We accept credit cards (Visa, Mastercard, American Express), debit cards, net banking, UPI, wallets (PayTM, Google Pay), and cash on arrival for some properties.'
        ],
        [
            'question' => 'Are taxes included in the displayed price?',
            'answer' => 'Prices shown are base rates. GST (18%) and other applicable taxes will be added at checkout. The final amount including all taxes is clearly shown before payment.'
        ],
    ],
    'Car Rentals' => [
        [
            'question' => 'What are the age requirements for renting a car?',
            'answer' => 'Minimum age is 21 years with a valid Indian driving license. International driving license is accepted. Your license must be valid for the entire rental period.'
        ],
        [
            'question' => 'Is insurance included in car rental?',
            'answer' => 'Yes, comprehensive insurance is included in all rentals. Damage waiver covers accidental damage. Additional premium coverage available for zero-deductible options.'
        ],
        [
            'question' => 'What documents are required?',
            'answer' => 'Required documents: Valid driving license, ID proof (Aadhar/Passport), PAN card, and address proof. Rental agreement is provided at pickup. A security deposit may be required.'
        ],
        [
            'question' => 'Can I rent a car for Ajanta & Ellora Caves?',
            'answer' => 'Absolutely! Many travelers use CSNExplore for Ajanta & Ellora cave tours. We provide cars suitable for these trips. Ajanta is ~100km and Ellora is ~30km from Chhatrapati Sambhajinagar.'
        ],
        [
            'question' => 'What if the car breaks down?',
            'answer' => 'We provide 24/7 roadside assistance. In case of breakdown, call our support team. A replacement vehicle will be sent or we\'ll assist in repairs.'
        ],
    ],
    'Bike Rentals' => [
        [
            'question' => 'Do I need a license to rent a bike?',
            'answer' => 'Yes, a valid Indian driving license (two-wheeler category) is mandatory. International driving permit is also accepted. License must be valid for the rental period.'
        ],
        [
            'question' => 'Are helmets and safety gear provided?',
            'answer' => 'Yes, helmets are mandatory and provided free with all bike rentals. Safety jackets and gloves are available upon request. All gear is regularly sanitized.'
        ],
        [
            'question' => 'What bikes are available?',
            'answer' => 'We offer Hero Splendor, Honda Activa, Hero Pleasure, Royal Enfield, TVS Star City, and more. Each bike is well-maintained, fuel-efficient, and suitable for local exploration.'
        ],
        [
            'question' => 'Is fuel included?',
            'answer' => 'Rentals are charged at a daily rate. Fuel is not included—you pay for fuel separately. Returns should have the same fuel level as pickup.'
        ],
        [
            'question' => 'What is the security deposit?',
            'answer' => 'Security deposit varies by bike model (₹500-₹2000). It\'s refunded in full if the bike is returned undamaged and in good condition.'
        ],
    ],
    'Attractions & Tours' => [
        [
            'question' => 'How do I book attraction tickets?',
            'answer' => 'Browse attractions on CSNExplore, select your preferred date and time, and book online. E-tickets are sent to your email and phone. Present these at the attraction entry point.'
        ],
        [
            'question' => 'Are guided tours available?',
            'answer' => 'Yes! Many attractions offer guided tours with certified guides. English, Hindi, and regional language guides are available. Book directly on CSNExplore.'
        ],
        [
            'question' => 'What\'s the best time to visit Ajanta Caves?',
            'answer' => 'Best season: October to February (cool weather). Avoid May-June (extreme heat). Monsoon (July-September) offers lush landscapes but some caves may be slippery. Sunrise visits are magical!'
        ],
        [
            'question' => 'Are there discounts for students or seniors?',
            'answer' => 'Many attractions offer student and senior discounts. Check individual listing pages for eligibility and applicable discounts. Valid ID required.'
        ],
        [
            'question' => 'Can I take photographs at attractions?',
            'answer' => 'Photography policies vary by attraction. Generally, still cameras are allowed but professional cameras need special permission. Video cameras may require paid permits. Check before visiting.'
        ],
    ],
    'Restaurants & Dining' => [
        [
            'question' => 'Can I make table reservations?',
            'answer' => 'Yes, you can reserve tables through CSNExplore for most restaurants. Bookings are subject to availability. Reserve at least 1-2 hours in advance for peak times.'
        ],
        [
            'question' => 'Do you have vegetarian options?',
            'answer' => 'Yes, CSNExplore has numerous vegetarian restaurants and many multi-cuisine restaurants with extensive vegetarian menus. Filter by cuisine type to find options.'
        ],
        [
            'question' => 'What\'s the average meal cost in Aurangabad?',
            'answer' => 'Budget meals: ₹100-₹300 per person | Mid-range: ₹400-₹800 | Premium: ₹800-₹2000+. Prices vary by restaurant type and cuisine.'
        ],
        [
            'question' => 'Are there special discounts for group bookings?',
            'answer' => 'Many restaurants offer group discounts (10+ people). Contact them directly through CSNExplore or call our support team for special rates.'
        ],
        [
            'question' => 'Which cuisines are popular in Aurangabad?',
            'answer' => 'Hyderabadi biryani is the star! Also popular: Nizam cuisine, local Marathi food, North Indian, Chinese, Italian, and Continental. Vegetarian thali is traditional and delicious.'
        ],
    ],
    'Bus Bookings' => [
        [
            'question' => 'Which routes are available?',
            'answer' => 'We provide routes to Mumbai, Pune, Nashik, Nagpur, Hyderabad, Bangalore, and more. MSRTC Shivneri buses, private operators, and luxury coaches available.'
        ],
        [
            'question' => 'How do I get my ticket?',
            'answer' => 'E-tickets are sent via email and SMS after booking. Print them or show digitally at boarding points. No physical counter required.'
        ],
        [
            'question' => 'Can I cancel my bus ticket?',
            'answer' => 'Yes, cancellations allowed 24 hours before departure with a 10-15% cancellation fee. Check specific operator policies for exact terms.'
        ],
        [
            'question' => 'What facilities are available on buses?',
            'answer' => 'Most buses offer: Comfortable reclining seats | Air conditioning | Charging points | Toilet | Complimentary snacks. Luxury coaches have additional amenities.'
        ],
        [
            'question' => 'When should I arrive before departure?',
            'answer' => 'Arrive 30-45 minutes before local departures and 1-2 hours before intercity/night bus departures. Check your ticket for exact boarding time.'
        ],
    ],
    'Travel Blogs & Planning' => [
        [
            'question' => 'How do I use the Trip Planner?',
            'answer' => 'Visit CSNExplore\'s Trip Planner section. Select your travel dates, interests, and budget. Our AI recommends hotels, attractions, restaurants, and activities for your trip.'
        ],
        [
            'question' => 'Can you create a custom itinerary?',
            'answer' => 'Yes! Contact our travel experts at support@csnexplore.com or call +91-8600968888. We create personalized itineraries based on your preferences, budget, and duration.'
        ],
        [
            'question' => 'How many days should I spend in Chhatrapati Sambhajinagar?',
            'answer' => '2-3 days is ideal: Day 1 - Ajanta Caves | Day 2 - Ellora Caves | Day 3 - City attractions (Bibi Ka Maqbara, Daulatabad Fort). Extend for deeper exploration.'
        ],
        [
            'question' => 'Are the blog guides updated?',
            'answer' => 'Yes, our travel blogs are updated regularly (2026 edition). They include latest information, prices, timings, and helpful tips from recent travelers.'
        ],
        [
            'question' => 'Can I contribute travel stories?',
            'answer' => 'We love travel stories! Contact us at content@csnexplore.com with your story, photos, and details. Selected contributions may be featured on our platform.'
        ],
    ],
    'Account & Technical' => [
        [
            'question' => 'How do I create an account?',
            'answer' => 'Click "Register" on the website. Provide email, phone number, and password. Verify your email and phone. Your account is ready to book!'
        ],
        [
            'question' => 'What if I forgot my password?',
            'answer' => 'Click "Forgot Password" on login page. Enter your email. Reset link sent to your email. Click and create a new password. You\'ll be logged in immediately.'
        ],
        [
            'question' => 'Can I link multiple bookings to one account?',
            'answer' => 'Yes! All bookings under the same email address are linked to one account. Track all hotel stays, car rentals, bike rentals, and attraction tickets in one place.'
        ],
        [
            'question' => 'Is my personal data safe?',
            'answer' => 'Absolutely. We use 256-bit SSL encryption, follow GDPR standards, and never share personal data with third parties without consent. Your privacy is our priority.'
        ],
        [
            'question' => 'Why can\'t I see the app on my phone?',
            'answer' => 'CSNExplore is fully mobile-responsive on the web. No separate app needed! Visit csnexplore.com on your browser. It works perfectly on all devices and browsers.'
        ],
    ]
];

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'FAQ', 'url' => '/faq'],
];

// Generate FAQ schema
$faq_main_entity = [];
foreach ($faqs as $category => $questions) {
    foreach ($questions as $item) {
        $faq_main_entity[] = [
            '@type' => 'Question',
            'name' => $item['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['answer']
            ]
        ];
    }
}

$faq_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faq_main_entity
];

$extra_head = '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

require 'header.php';
?>

<main style="background: #f8f6f6;">

<!-- Hero Section -->
<section class="relative h-[380px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" width="800" height="600" class="w-full h-full object-cover"
             src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=800&q=80&auto=format"
             alt="Frequently Asked Questions"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black"></div>
    </div>
    
    <div class="relative z-10 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">Frequently Asked Questions</h1>
        <p class="text-lg text-white/90 max-w-2xl mx-auto px-5">Quick answers to common questions about CSNExplore bookings and travel planning</p>
    </div>
</section>

<!-- Content Section -->
<section class="max-w-4xl mx-auto px-5 py-16">
    <div class="mb-12">
        <p class="text-center text-gray-600 mb-8">Find answers to questions about hotels, car rentals, bike rentals, attractions, restaurants, and more.</p>
        
        <!-- FAQ Categories -->
        <div class="space-y-8">
            <?php foreach ($faqs as $category => $questions): ?>
            <div id="category-<?php echo strtolower(str_replace(' ', '-', $category)); ?>" class="bg-white rounded-lg shadow-lg p-8 transition-all hover:shadow-xl">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="text-primary">→</span><?php echo $category; ?>
                </h2>
                
                <div class="space-y-5">
                    <?php foreach ($questions as $idx => $faq): ?>
                    <details class="group border-l-4 border-transparent hover:border-primary pl-4 py-3 transition-all">
                        <summary class="font-semibold text-gray-800 cursor-pointer flex items-center justify-between hover:text-primary transition-colors">
                            <span><?php echo $faq['question']; ?></span>
                            <span class="material-symbols-outlined text-2xl group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <p class="text-gray-600 mt-3 text-sm leading-relaxed"><?php echo $faq['answer']; ?></p>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Still Have Questions Section -->
    <div class="bg-gradient-to-r from-primary/10 to-orange-50 rounded-lg p-8 text-center border-t-4 border-primary">
        <h3 class="text-2xl font-bold text-gray-800 mb-3">Didn't Find Your Answer?</h3>
        <p class="text-gray-600 mb-6">Our support team is here to help! Contact us anytime for quick assistance.</p>
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="btn-primary inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">call</span>
                Call +91-8600968888
            </a>
            <a href="mailto:support@csnexplore.com" class="btn-outline inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">mail</span>
                Email Support
            </a>
            <a href="<?php echo BASE_PATH; ?>/contact" class="btn-outline inline-flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">mail</span>
                Contact Form
            </a>
        </div>
    </div>
</section>

</main>

<?php require 'footer.php'; ?>
