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
            'Aurangabad travel'
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
                'Aurangabad caves'
            ],
            'stays' => [
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
                'Treebo hotels Aurangabad'
            ],
            'cars' => [
                'car rental Aurangabad',
                'self drive car Chhatrapati Sambhajinagar',
                'car hire Aurangabad',
                'Ajanta Caves car rental',
                'Ellora Caves car hire',
                'cab Aurangabad',
                'taxi Aurangabad',
                'Innova hire Aurangabad',
                'Swift rental Aurangabad',
                'Ertiga booking Aurangabad',
                'chauffeur driven car Aurangabad'
            ],
            'bikes' => [
                'bike rental Aurangabad',
                'scooter hire Chhatrapati Sambhajinagar',
                'motorcycle rental Aurangabad',
                'Activa rent Aurangabad',
                'Royal Enfield hire Aurangabad',
                'two wheeler rental Aurangabad',
                'bike on rent Aurangabad',
                'scooty rental Aurangabad'
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
                'heritage sites Aurangabad'
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
                'street food Aurangabad'
            ],
            'buses' => [
                'bus from Aurangabad',
                'MSRTC Shivneri',
                'bus booking Chhatrapati Sambhajinagar',
                'intercity bus Aurangabad',
                'Aurangabad to Mumbai bus',
                'Aurangabad to Pune bus',
                'bus tickets Aurangabad',
                'online bus booking Aurangabad'
            ],
            'blogs' => [
                'Aurangabad travel blog',
                'Ajanta Caves guide 2026',
                'Ellora Caves tips',
                'Chhatrapati Sambhajinagar travel tips',
                'Aurangabad itinerary',
                'best time to visit Aurangabad',
                'Aurangabad travel guide',
                'things to know about Aurangabad'
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
            'bikes' => "Bike Rentals Aurangabad | Scooters & Motorcycles in Sambhajinagar – CSNExplore",
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
            'bikes' => 'Rent bikes & scooters in Aurangabad from ₹300/day. Hero Splendor, Honda Activa, Royal Enfield. Explore Ellora Caves on two wheels.',
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
     */
    public static function generateOrganizationSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => 'CSNExplore',
            'alternateName' => 'CSN Explore',
            'url' => SITE_URL,
            'logo' => SITE_URL . '/images/travelhub.png',
            'image' => SITE_URL . '/images/travelhub.png',
            'description' => 'Leading tourism portal for Chhatrapati Sambhajinagar (Aurangabad). Book hotels, car rentals, bike rentals, and explore Ajanta & Ellora Caves.',
            'telephone' => '+91-8600968888',
            'email' => 'supportcsnexplore@gmail.com',
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
                'https://twitter.com/csnexplore'
            ],
            'areaServed' => [
                [
                    '@type' => 'City',
                    'name' => 'Chhatrapati Sambhajinagar',
                    'alternateName' => 'Aurangabad'
                ]
            ],
            'knowsAbout' => [
                'Ajanta Caves',
                'Ellora Caves',
                'Bibi Ka Maqbara',
                'Daulatabad Fort',
                'Hotel Booking',
                'Car Rental',
                'Bike Rental',
                'Tourism Services'
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
            'image' => SITE_URL . '/images/travelhub.png',
            'telephone' => '+91-8600968888',
            'email' => 'supportcsnexplore@gmail.com',
            'url' => SITE_URL,
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
     * Generate WebSite schema with SearchAction
     */
    public static function generateWebSiteSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'CSNExplore',
            'alternateName' => 'CSN Explore - Chhatrapati Sambhajinagar Tourism',
            'url' => SITE_URL,
            'potentialAction' => [
                [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => SITE_URL . '/listing/stays?search={search_term_string}'
                    ],
                    'query-input' => 'required name=search_term_string'
                ]
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'CSNExplore',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => SITE_URL . '/images/travelhub.png',
                    'width' => 800,
                    'height' => 600
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
