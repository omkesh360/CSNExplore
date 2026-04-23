<?php
/**
 * php/seo-meta.php — Centralised SEO meta + schema generator
 * Usage: require_once 'php/seo-meta.php'; then call seo_meta($context)
 * Returns an array: ['title','description','keywords','canonical','og_image',
 *                    'schema_json','breadcrumb_json','faq_json']
 */

if (!defined('SITE_URL')) define('SITE_URL', 'https://csnexplore.com');
if (!defined('SITE_NAME')) define('SITE_NAME', 'CSNExplore');
if (!defined('SITE_PHONE')) define('SITE_PHONE', '+91-8600968888');
if (!defined('SITE_ADDRESS')) define('SITE_ADDRESS', 'Jay Tower, Padampura, Chhatrapati Sambhajinagar, Maharashtra 431005');

// ── Primary keyword sets per page type ───────────────────────────────────────
$_SEO_KW = [
    'home'        => 'Chhatrapati Sambhajinagar tourism, Aurangabad travel guide, Ajanta Caves tour, Ellora Caves visit, hotels Sambhajinagar, car rental Aurangabad, bike rental Aurangabad, CSNExplore',
    'stays'       => 'hotels Chhatrapati Sambhajinagar, homestay Aurangabad, budget hotels Sambhajinagar, luxury hotels Aurangabad, book hotel Chhatrapati Sambhajinagar',
    'cars'        => 'car rental Aurangabad, self drive car Chhatrapati Sambhajinagar, Maruti Ertiga hire, Ajanta Caves car rental, cab Aurangabad',
    'bikes'       => 'bike rental Aurangabad, scooter hire Chhatrapati Sambhajinagar, Hero Splendor rent, motorcycle rental Aurangabad',
    'attractions' => 'Ajanta Caves tour, Ellora Caves visit, Bibi Ka Maqbara Aurangabad, Daulatabad Fort, tourist places Chhatrapati Sambhajinagar',
    'restaurants' => 'restaurants Chhatrapati Sambhajinagar, best food Aurangabad, biryani Sambhajinagar, dine out Aurangabad',
    'buses'       => 'bus from Aurangabad, MSRTC Shivneri, bus booking Chhatrapati Sambhajinagar, intercity bus Aurangabad',
    'blogs'       => 'Aurangabad travel blog, Ajanta Caves guide 2026, Ellora Caves tips, Chhatrapati Sambhajinagar travel tips',
    'contact'     => 'contact CSNExplore, Aurangabad tourism helpline, book tour Chhatrapati Sambhajinagar',
    'about'       => 'about CSNExplore, Aurangabad tourism portal, Chhatrapati Sambhajinagar travel company',
];

/**
 * Generate complete SEO metadata for a page.
 *
 * @param array $ctx {
 *   type        string  'home'|'stays'|'cars'|'bikes'|'attractions'|'restaurants'|'buses'|'blogs'|'blog'|'listing'|'contact'|'about'
 *   item        array   DB row for listing/blog (optional)
 *   breadcrumbs array   [['name'=>'Home','url'=>'/'],['name'=>'Cars','url'=>'/listing/cars']]
 *   faqs        array   [['q'=>'...','a'=>'...']] (optional)
 *   price       string  formatted price string e.g. '₹1,200'
 *   price_unit  string  '/ day' | '/ night' etc.
 * }
 */
function seo_meta(array $ctx): array {
    global $_SEO_KW;

    $type  = $ctx['type']  ?? 'home';
    $item  = $ctx['item']  ?? [];
    $price = $ctx['price'] ?? '';
    $unit  = $ctx['price_unit'] ?? '';
    $bcs   = $ctx['breadcrumbs'] ?? [];
    $faqs  = $ctx['faqs'] ?? [];

    $name     = $item['name'] ?? $item['operator'] ?? '';
    $location = $item['location'] ?? 'Chhatrapati Sambhajinagar';
    $desc_raw = strip_tags($item['description'] ?? $item['content'] ?? '');
    $img_raw  = $item['image'] ?? '';
    $img_abs  = $img_raw
        ? (strpos($img_raw, 'http') === 0 ? $img_raw : SITE_URL . '/' . ltrim($img_raw, '/'))
        : SITE_URL . '/images/og-image.jpg';

    // ── Title ─────────────────────────────────────────────────────────────────
    $title = match($type) {
        'home'        => 'CSNExplore – Hotels, Car & Bike Rentals, Ajanta Caves Tours | Chhatrapati Sambhajinagar',
        'stays'       => 'Hotels & Homestays in Chhatrapati Sambhajinagar | Book Now – CSNExplore',
        'cars'        => 'Car Rentals Chhatrapati Sambhajinagar | Self Drive & Chauffeur – CSNExplore',
        'bikes'       => 'Bike Rentals Aurangabad | Scooters & Motorcycles – CSNExplore',
        'attractions' => 'Ajanta & Ellora Caves Tours | Top Attractions Sambhajinagar – CSNExplore',
        'restaurants' => 'Best Restaurants Chhatrapati Sambhajinagar | Dine Out – CSNExplore',
        'buses'       => 'Bus Routes from Aurangabad | Book Bus Tickets – CSNExplore',
        'blogs'       => 'Aurangabad Travel Blog 2026 | Ajanta Caves Guide – CSNExplore',
        'contact'     => 'Contact CSNExplore | Aurangabad Tourism Helpline',
        'about'       => 'About CSNExplore | Chhatrapati Sambhajinagar Tourism Portal',
        'listing'     => _seo_listing_title($item, $type, $price, $unit, $location),
        'blog'        => _seo_blog_title($item),
        default       => 'CSNExplore – Chhatrapati Sambhajinagar Tourism',
    };

    // ── Description ───────────────────────────────────────────────────────────
    $description = match($type) {
        'home'        => 'Discover Chhatrapati Sambhajinagar with CSNExplore. Book hotels, rent cars & bikes, explore Ajanta & Ellora Caves, find restaurants and bus routes. 500+ listings, verified reviews.',
        'stays'       => 'Browse 500+ hotels, homestays & resorts in Chhatrapati Sambhajinagar. Best prices, free cancellation. Book your stay near Ajanta & Ellora Caves today.',
        'cars'        => 'Rent a car in Chhatrapati Sambhajinagar from ₹800/day. Self-drive or with driver. Maruti Swift, Ertiga, Innova & more. Perfect for Ajanta Caves day trips.',
        'bikes'       => 'Rent bikes & scooters in Aurangabad from ₹300/day. Hero Splendor, Honda Activa, Royal Enfield & more. Explore Ellora Caves on two wheels.',
        'attractions' => 'Explore Ajanta Caves, Ellora Caves, Bibi Ka Maqbara & 15+ top attractions in Chhatrapati Sambhajinagar. Timings, entry fees & guided tours.',
        'restaurants' => 'Discover the best restaurants, cafes & street food in Chhatrapati Sambhajinagar. Biryani, thali, multi-cuisine & more. Read reviews & book a table.',
        'buses'       => 'Book bus tickets from Aurangabad. MSRTC Shivneri, Volvo AC, sleeper buses to Mumbai, Pune & Nashik. Schedules, fares & online booking.',
        'blogs'       => 'Read expert travel guides for Chhatrapati Sambhajinagar. Ajanta Caves 2026 guide, Ellora Caves tips, hotel reviews, car rental advice & local food guides.',
        'contact'     => 'Contact CSNExplore for hotel bookings, car rentals & tour packages in Chhatrapati Sambhajinagar. Call +91-8600968888 or WhatsApp us.',
        'about'       => 'CSNExplore is Chhatrapati Sambhajinagar\'s leading tourism portal. We connect travellers with 500+ hotels, car rentals, bike rentals & guided tours.',
        'listing'     => _seo_listing_desc($item, $type, $price, $unit, $location, $desc_raw),
        'blog'        => _seo_blog_desc($item, $desc_raw),
        default       => 'Explore Chhatrapati Sambhajinagar with CSNExplore.',
    };

    // ── Keywords ──────────────────────────────────────────────────────────────
    $kw_base = $_SEO_KW[$type] ?? $_SEO_KW['home'];
    if ($name) $kw_base = htmlspecialchars($name) . ', ' . $kw_base;
    $keywords = $kw_base;

    // ── Canonical ─────────────────────────────────────────────────────────────
    $canonical = match($type) {
        'home'        => SITE_URL . '/',
        'stays'       => SITE_URL . '/listing/stays',
        'cars'        => SITE_URL . '/listing/cars',
        'bikes'       => SITE_URL . '/listing/bikes',
        'attractions' => SITE_URL . '/listing/attractions',
        'restaurants' => SITE_URL . '/listing/restaurants',
        'buses'       => SITE_URL . '/listing/buses',
        'blogs'       => SITE_URL . '/blogs',
        'contact'     => SITE_URL . '/contact',
        'about'       => SITE_URL . '/about',
        'listing'     => $ctx['canonical'] ?? SITE_URL,
        'blog'        => $ctx['canonical'] ?? SITE_URL . '/blogs/' . ($item['id'] ?? ''),
        default       => SITE_URL . '/',
    };

    // ── Schema JSON-LD ────────────────────────────────────────────────────────
    $schema = _seo_schema($type, $item, $canonical, $img_abs, $description, $price, $unit, $location);

    // ── Breadcrumb schema ─────────────────────────────────────────────────────
    $breadcrumb_json = '';
    if (!empty($bcs)) {
        $bcItems = [];
        foreach ($bcs as $i => $bc) {
            $bcItems[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $bc['name'],
                'item'     => strpos($bc['url'], 'http') === 0 ? $bc['url'] : SITE_URL . $bc['url'],
            ];
        }
        $breadcrumb_json = json_encode([
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $bcItems,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    // ── FAQ schema ────────────────────────────────────────────────────────────
    $faq_json = '';
    if (!empty($faqs)) {
        $faqItems = [];
        foreach ($faqs as $faq) {
            $faqItems[] = [
                '@type'          => 'Question',
                'name'           => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ];
        }
        $faq_json = json_encode([
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $faqItems,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    return compact('title', 'description', 'keywords', 'canonical', 'img_abs', 'schema', 'breadcrumb_json', 'faq_json');
}

// ── Internal helpers ──────────────────────────────────────────────────────────

function _seo_listing_title(array $item, string $type, string $price, string $unit, string $location): string {
    $name = htmlspecialchars($item['name'] ?? $item['operator'] ?? 'Listing');
    $loc  = htmlspecialchars($location);
    return match($type) {
        'cars'        => "$name Self Drive $price$unit | Caves Tour – CSNExplore",
        'bikes'       => "$name Bike Rental $price$unit | $loc – CSNExplore",
        'stays'       => "$name $loc $price$unit | Book Now – CSNExplore",
        'attractions' => "$name | Entry Fee & Timings $loc – CSNExplore",
        'restaurants' => "$name Restaurant $loc | Menu & Reviews – CSNExplore",
        'buses'       => "$name Bus Route | Book Ticket – CSNExplore",
        default       => "$name | $loc – CSNExplore",
    };
}

function _seo_listing_desc(array $item, string $type, string $price, string $unit, string $location, string $desc_raw): string {
    $name = $item['name'] ?? $item['operator'] ?? '';
    $loc  = $location;
    if ($desc_raw) return substr($desc_raw, 0, 155) . (strlen($desc_raw) > 155 ? '...' : '');
    return match($type) {
        'cars'        => "$name available for self-drive or with driver in $loc. $price$unit. Well-maintained, AC, insured. Book now on CSNExplore.",
        'bikes'       => "$name available for rent in $loc. $price$unit. Perfect for exploring Ajanta & Ellora Caves. Book on CSNExplore.",
        'stays'       => "$name in $loc. $price$unit. Free cancellation, verified reviews. Book your stay on CSNExplore.",
        'attractions' => "Visit $name in $loc. Check timings, entry fees & guided tour options on CSNExplore.",
        'restaurants' => "Dine at $name in $loc. View menu, prices & reviews on CSNExplore.",
        'buses'       => "Book $name bus from $loc. Check schedule, fares & availability on CSNExplore.",
        default       => "$name in $loc. Book on CSNExplore.",
    };
}

function _seo_blog_title(array $item): string {
    $title = $item['title'] ?? 'Travel Guide';
    // Append year if not present
    if (!preg_match('/20\d\d/', $title)) $title .= ' ' . date('Y');
    return htmlspecialchars($title) . ' | CSNExplore';
}

function _seo_blog_desc(array $item, string $desc_raw): string {
    if (!empty($item['meta_description'])) return htmlspecialchars(substr($item['meta_description'], 0, 160));
    if ($desc_raw) return substr(strip_tags($desc_raw), 0, 155) . '...';
    return 'Read the complete guide on ' . htmlspecialchars($item['title'] ?? 'travel') . ' at CSNExplore.';
}

function _seo_schema(string $type, array $item, string $canonical, string $img, string $desc, string $price, string $unit, string $location): string {
    $name = $item['name'] ?? $item['operator'] ?? SITE_NAME;
    $rating = (float)($item['rating'] ?? 0);
    $reviews = (int)($item['reviews'] ?? 0);

    $base = [
        '@context' => 'https://schema.org',
        'url'      => $canonical,
        'image'    => $img,
        'description' => $desc,
    ];

    switch ($type) {
        case 'home':
            $schema = array_merge($base, [
                '@type'       => 'TravelAgency',
                'name'        => SITE_NAME,
                'telephone'   => SITE_PHONE,
                'address'     => [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => 'Jay Tower, Padampura',
                    'addressLocality' => 'Chhatrapati Sambhajinagar',
                    'addressRegion'   => 'Maharashtra',
                    'postalCode'      => '431005',
                    'addressCountry'  => 'IN',
                ],
                'geo' => ['@type' => 'GeoCoordinates', 'latitude' => 19.8762, 'longitude' => 75.3433],
                'openingHours' => 'Mo-Su 09:00-21:00',
                'priceRange'   => '₹₹',
                'sameAs'       => ['https://www.instagram.com/csnexplore_/'],
            ]);
            break;

        case 'stays':
        case 'listing':
            if (isset($item['price_per_night'])) {
                $schema = array_merge($base, [
                    '@type'       => 'LodgingBusiness',
                    'name'        => $name,
                    'address'     => ['@type' => 'PostalAddress', 'addressLocality' => $location, 'addressRegion' => 'Maharashtra', 'addressCountry' => 'IN'],
                    'telephone'   => SITE_PHONE,
                    'priceRange'  => $price ? $price . $unit : '₹₹',
                ]);
                if ($rating > 0 && $reviews > 0) {
                    $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => $rating, 'reviewCount' => $reviews, 'bestRating' => 5];
                }
            } else {
                $schema = _seo_product_schema($base, $name, $price, $unit, $rating, $reviews, $location, $img, $desc);
            }
            break;

        case 'cars':
        case 'bikes':
            $schema = _seo_product_schema($base, $name, $price, $unit, $rating, $reviews, $location, $img, $desc);
            break;

        case 'attractions':
            $schema = array_merge($base, [
                '@type'   => 'TouristAttraction',
                'name'    => $name,
                'address' => ['@type' => 'PostalAddress', 'addressLocality' => $location, 'addressRegion' => 'Maharashtra', 'addressCountry' => 'IN'],
            ]);
            if (!empty($item['entry_fee'])) $schema['publicAccess'] = true;
            if ($rating > 0 && $reviews > 0) {
                $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => $rating, 'reviewCount' => $reviews, 'bestRating' => 5];
            }
            break;

        case 'restaurants':
            $schema = array_merge($base, [
                '@type'       => 'FoodEstablishment',
                'name'        => $name,
                'servesCuisine' => $item['cuisine'] ?? 'Indian',
                'address'     => ['@type' => 'PostalAddress', 'addressLocality' => $location, 'addressRegion' => 'Maharashtra', 'addressCountry' => 'IN'],
                'telephone'   => SITE_PHONE,
                'priceRange'  => $price ? $price . $unit : '₹₹',
            ]);
            if ($rating > 0 && $reviews > 0) {
                $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => $rating, 'reviewCount' => $reviews, 'bestRating' => 5];
            }
            break;

        case 'blog':
            $schema = array_merge($base, [
                '@type'         => 'Article',
                'headline'      => $item['title'] ?? '',
                'author'        => ['@type' => 'Person', 'name' => $item['author'] ?? 'CSNExplore Team'],
                'publisher'     => ['@type' => 'Organization', 'name' => SITE_NAME, 'logo' => ['@type' => 'ImageObject', 'url' => SITE_URL . '/images/travelhub.png']],
                'datePublished' => substr($item['created_at'] ?? date('Y-m-d'), 0, 10),
                'dateModified'  => substr($item['updated_at'] ?? date('Y-m-d'), 0, 10),
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
            ]);
            break;

        default:
            $schema = array_merge($base, [
                '@type' => 'WebPage',
                'name'  => SITE_NAME,
            ]);
    }

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function _seo_product_schema(array $base, string $name, string $price, string $unit, float $rating, int $reviews, string $location, string $img, string $desc): array {
    $schema = array_merge($base, [
        '@type'  => 'Product',
        'name'   => $name,
        'brand'  => ['@type' => 'Brand', 'name' => SITE_NAME],
        'offers' => [
            '@type'         => 'Offer',
            'priceCurrency' => 'INR',
            'price'         => preg_replace('/[^0-9.]/', '', $price) ?: '0',
            'priceSpecification' => ['@type' => 'UnitPriceSpecification', 'price' => preg_replace('/[^0-9.]/', '', $price) ?: '0', 'priceCurrency' => 'INR', 'unitText' => ltrim($unit, '/ ')],
            'availability'  => 'https://schema.org/InStock',
            'seller'        => ['@type' => 'Organization', 'name' => SITE_NAME],
        ],
    ]);
    if ($rating > 0 && $reviews > 0) {
        $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => $rating, 'reviewCount' => $reviews, 'bestRating' => 5];
    }
    return $schema;
}

/**
 * Render all meta tags + schema scripts into HTML string.
 * Call echo seo_render($meta) inside <head>.
 */
function seo_render(array $meta, string $og_type = 'website'): string {
    $out  = '<meta name="description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $out .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords']) . '">' . "\n";
    $out .= '<link rel="canonical" href="' . htmlspecialchars($meta['canonical']) . '">' . "\n";
    // OG
    $out .= '<meta property="og:type" content="' . htmlspecialchars($og_type) . '">' . "\n";
    $out .= '<meta property="og:url" content="' . htmlspecialchars($meta['canonical']) . '">' . "\n";
    $out .= '<meta property="og:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
    $out .= '<meta property="og:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $out .= '<meta property="og:image" content="' . htmlspecialchars($meta['img_abs']) . '">' . "\n";
    $out .= '<meta property="og:site_name" content="CSNExplore">' . "\n";
    $out .= '<meta property="og:locale" content="en_IN">' . "\n";
    // Twitter
    $out .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $out .= '<meta name="twitter:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
    $out .= '<meta name="twitter:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $out .= '<meta name="twitter:image" content="' . htmlspecialchars($meta['img_abs']) . '">' . "\n";
    // Schema
    if (!empty($meta['schema'])) {
        $out .= '<script type="application/ld+json">' . $meta['schema'] . '</script>' . "\n";
    }
    if (!empty($meta['breadcrumb_json'])) {
        $out .= '<script type="application/ld+json">' . $meta['breadcrumb_json'] . '</script>' . "\n";
    }
    if (!empty($meta['faq_json'])) {
        $out .= '<script type="application/ld+json">' . $meta['faq_json'] . '</script>' . "\n";
    }
    return $out;
}

/**
 * Generate alt text for images.
 * e.g. seo_alt_text('cars', 'Maruti Swift', 'Chhatrapati Sambhajinagar')
 *   → "Maruti Swift car rental Chhatrapati Sambhajinagar – CSNExplore"
 */
function seo_alt_text(string $type, string $name, string $location = 'Chhatrapati Sambhajinagar'): string {
    $suffix = match($type) {
        'cars'        => 'car rental',
        'bikes'       => 'bike rental',
        'stays'       => 'hotel',
        'attractions' => 'tourist attraction',
        'restaurants' => 'restaurant',
        'buses'       => 'bus route',
        default       => '',
    };
    return trim("$name $suffix $location") . ' – CSNExplore';
}
