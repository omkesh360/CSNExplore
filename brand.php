<?php
/**
 * brand.php — CSNExplore Brand Entity Page
 * 
 * This page serves as the authoritative brand identity document for CSNExplore.
 * It provides Google's crawlers with structured, machine-readable entity data
 * to help establish CSNExplore as a distinct named entity (not a misspelling).
 *
 * Key SEO functions:
 *  1. Comprehensive Organization + Brand JSON-LD with all sameAs social profiles
 *  2. Visible, crawlable brand story mentioning all brand name variants
 *  3. Explicit brand disambiguation text (csnexplore ≠ "CSN explore" misspelling)
 *  4. Internal links to all key pages for link equity distribution
 */
require_once 'php/config.php';

$page_title  = 'CSNExplore – Official Brand Page | Chhatrapati Sambhajinagar Tourism';
$current_page = 'brand';

$page_meta = [
    'description' => 'Official brand page for CSNExplore – the premier travel and tourism portal for Chhatrapati Sambhajinagar (Aurangabad), Maharashtra. Hotels, car rentals, bike rentals, Ajanta & Ellora Caves tours.',
    'canonical'   => 'https://csnexplore.com/brand',
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/Logo-light-optimized.webp',
    'breadcrumbs' => [
        ['name' => 'Home',       'url' => '/'],
        ['name' => 'Brand Info', 'url' => '/brand'],
    ],
];

$extra_head = <<<'SCHEMA'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": ["Organization", "TravelAgency"],
      "@id": "https://csnexplore.com/#organization",
      "name": "CSNExplore",
      "legalName": "CSNExplore Tourism Portal",
      "alternateName": [
        "CSN Explore",
        "csnexplore",
        "csnxplore",
        "CSNExplore.com",
        "CSNExplore.in",
        "CSN Explore Aurangabad",
        "CSNExplore Sambhajinagar"
      ],
      "brand": {
        "@type": "Brand",
        "name": "CSNExplore",
        "slogan": "Explore Chhatrapati Sambhajinagar Your Way",
        "description": "CSNExplore is the leading tourism platform for Chhatrapati Sambhajinagar (Aurangabad), Maharashtra. The brand name CSNExplore is derived from Chhatrapati Sambhajinagar (CSN) + Explore. It is a distinct brand — not a misspelling of any other entity.",
        "logo": {
          "@type": "ImageObject",
          "url": "https://csnexplore.com/images/Logo-light-optimized.webp",
          "width": 240,
          "height": 72
        }
      },
      "url": "https://csnexplore.com",
      "logo": {
        "@type": "ImageObject",
        "url": "https://csnexplore.com/images/Logo-light-optimized.webp",
        "width": 240,
        "height": 72
      },
      "image": "https://csnexplore.com/images/Logo-light-optimized.webp",
      "description": "CSNExplore (abbreviated from Chhatrapati Sambhajinagar Explore) is the leading tourism portal for Chhatrapati Sambhajinagar (also known as Aurangabad), Maharashtra, India. The platform offers hotel bookings, self-drive car rentals, bike rentals, guided tours to Ajanta & Ellora Caves, restaurant discovery, and bus ticket booking.",
      "slogan": "Explore Chhatrapati Sambhajinagar Your Way",
      "telephone": "+91-8600968888",
      "email": "supportcsnexplore@gmail.com",
      "foundingDate": "2024-01-01",
      "foundingLocation": {
        "@type": "Place",
        "name": "Chhatrapati Sambhajinagar, Maharashtra, India"
      },
      "founder": {
        "@type": "Person",
        "name": "Omkesh"
      },
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Jay Tower, Samadhan Colony, Padampura",
        "addressLocality": "Chhatrapati Sambhajinagar",
        "addressRegion": "Maharashtra",
        "postalCode": "431005",
        "addressCountry": "IN"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 19.8762,
        "longitude": 75.3433
      },
      "contactPoint": [
        {
          "@type": "ContactPoint",
          "telephone": "+91-8600968888",
          "contactType": "customer service",
          "areaServed": "IN",
          "availableLanguage": ["English", "Hindi", "Marathi"]
        },
        {
          "@type": "ContactPoint",
          "email": "supportcsnexplore@gmail.com",
          "contactType": "customer support",
          "areaServed": "IN"
        }
      ],
      "sameAs": [
        "https://www.instagram.com/csnexplore_/",
        "https://www.facebook.com/csnexplore",
        "https://twitter.com/csnexplore",
        "https://x.com/csnexplore",
        "https://about.me/csnexplore",
        "https://csnexplore.com",
        "https://csnexplore.in"
      ],
      "areaServed": [
        {
          "@type": "City",
          "name": "Chhatrapati Sambhajinagar",
          "alternateName": "Aurangabad"
        },
        {
          "@type": "AdministrativeArea",
          "name": "Maharashtra"
        }
      ],
      "knowsAbout": [
        "Ajanta Caves UNESCO World Heritage Site",
        "Ellora Caves UNESCO World Heritage Site",
        "Bibi Ka Maqbara Aurangabad",
        "Daulatabad Fort",
        "Hotel Booking Chhatrapati Sambhajinagar",
        "Car Rental Aurangabad",
        "Bike Rental Aurangabad",
        "Tourism Services Maharashtra"
      ],
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "CSNExplore Travel Services",
        "itemListElement": [
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Hotel & Homestay Booking in Chhatrapati Sambhajinagar" } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Car Rental Aurangabad (Self-Drive & Chauffeur)" } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Bike & Scooter Rental Aurangabad" } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Ajanta & Ellora Caves Guided Tours" } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Restaurant Discovery & Dining in Chhatrapati Sambhajinagar" } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "Bus Ticket Booking from Aurangabad" } },
          { "@type": "Offer", "itemOffered": { "@type": "Service", "name": "AI Trip Planner for Chhatrapati Sambhajinagar" } }
        ]
      }
    },
    {
      "@type": "WebPage",
      "@id": "https://csnexplore.com/brand",
      "url": "https://csnexplore.com/brand",
      "name": "CSNExplore – Official Brand Page",
      "isPartOf": { "@id": "https://csnexplore.com/#website" },
      "about": { "@id": "https://csnexplore.com/#organization" },
      "description": "Official brand information page for CSNExplore, the premier tourism portal of Chhatrapati Sambhajinagar (Aurangabad), Maharashtra.",
      "inLanguage": "en-IN"
    }
  ]
}
</script>
SCHEMA;

require 'header.php';
?>

<main>
<!-- Hero -->
<section class="relative py-28 bg-gradient-to-br from-slate-900 via-slate-800 to-black overflow-hidden">
    <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at 20% 50%, rgba(236,91,19,0.4) 0%, transparent 60%), radial-gradient(circle at 80% 50%, rgba(236,91,19,0.2) 0%, transparent 60%)"></div>
    <div class="relative z-10 max-w-[1140px] mx-auto px-5 text-center">
        <div data-reveal>
            <span class="inline-block px-4 py-1.5 rounded-full bg-primary/20 border border-primary/30 text-primary font-bold text-xs uppercase tracking-widest mb-6">Brand Information</span>
            <h1 class="text-4xl md:text-6xl font-serif font-black text-white mb-6 leading-tight">
                Welcome to <span class="text-primary">CSNExplore</span>
            </h1>
            <p class="text-lg text-white/70 max-w-3xl mx-auto leading-relaxed mb-8">
                <strong class="text-white">CSNExplore</strong> (short for <em>Chhatrapati Sambhajinagar Explore</em>) is the premier travel and tourism platform for Chhatrapati Sambhajinagar, Maharashtra, India — also historically known as Aurangabad.
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="<?php echo BASE_PATH; ?>/" class="bg-primary text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-orange-600 transition-colors shadow-lg">Explore Now</a>
                <a href="<?php echo BASE_PATH; ?>/about" class="bg-white/10 border border-white/20 text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-white/20 transition-colors">About Us</a>
                <a href="<?php echo BASE_PATH; ?>/contact" class="bg-white/10 border border-white/20 text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-white/20 transition-colors">Contact</a>
            </div>
        </div>
    </div>
</section>

<!-- Brand Identity Section -->
<section class="py-16 bg-white">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="grid lg:grid-cols-2 gap-12 items-start">

            <!-- Left: Brand Story -->
            <div data-reveal="left">
                <span class="text-primary font-bold text-xs uppercase tracking-widest">Brand Identity</span>
                <h2 class="text-3xl font-serif font-bold text-slate-900 mt-2 mb-6">What is CSNExplore?</h2>
                <div class="space-y-4 text-slate-600 leading-relaxed">
                    <p>
                        <strong>CSNExplore</strong> is a registered tourism brand whose name is derived from <strong>C</strong>hhatrapati <strong>S</strong>ambhaji<strong>n</strong>agar + <strong>Explore</strong>. The brand was founded to create a single, comprehensive digital platform for travelers visiting the heritage city of Chhatrapati Sambhajinagar (widely known as Aurangabad) in Maharashtra, India.
                    </p>
                    <p>
                        If you have searched for <em>csnexplore</em>, <em>csn explore</em>, <em>csnxplore</em>, <em>cnsexplore</em>, or <em>scsnexplore</em> — you have found the official platform. These are all recognized name variants for our brand.
                    </p>
                    <p>
                        Our platform serves travelers who are looking for verified hotels, self-drive car and bike rentals, restaurant recommendations, guided tours to Ajanta & Ellora Caves (UNESCO World Heritage Sites), and bus ticket booking — all in one place.
                    </p>
                    <p>
                        The city we serve — <strong>Chhatrapati Sambhajinagar</strong> — was formerly and is still popularly known as <em>Aurangabad</em>. Travelers often search with variant spellings including <em>auranagabd, auaranagabd, aurangabd, aurangbad, aurnagabad, Chtarapati sambhajinagar, chatrapati sambhajinagar, sambhajinagar</em>, and <em>shambhaji nagar</em>. Regardless of how you spell it, CSNExplore is your local tourism authority for this region.
                    </p>
                </div>

                <!-- Brand name callout -->
                <div class="mt-8 p-5 rounded-2xl bg-orange-50 border border-orange-100">
                    <p class="text-sm font-bold text-orange-700 mb-2">🔤 Searching for CSNExplore?</p>
                    <p class="text-sm text-slate-600">
                        The official brand name is <strong>CSNExplore</strong>. You may also know us as:
                        <span class="inline-flex flex-wrap gap-1.5 mt-2">
                            <?php
                            $aliases = ['csnexplore', 'csn explore', 'csnxplore', 'cnsexplore', 'scsnexplore', 'csnexplore.com', 'csnexplore.in'];
                            foreach ($aliases as $alias): ?>
                                <code class="bg-white border border-orange-200 text-orange-600 px-2 py-0.5 rounded text-xs font-mono"><?php echo htmlspecialchars($alias); ?></code>
                            <?php endforeach; ?>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Right: Brand Details Card -->
            <div data-reveal="right" class="space-y-6">
                <!-- Quick Facts -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <h3 class="font-bold text-slate-900 text-lg mb-4">Brand Quick Facts</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Brand Name</dt>
                            <dd class="text-slate-800 font-semibold">CSNExplore</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Full Form</dt>
                            <dd class="text-slate-800">Chhatrapati Sambhajinagar + Explore</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Category</dt>
                            <dd class="text-slate-800">Travel &amp; Tourism Portal</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Location</dt>
                            <dd class="text-slate-800">Chhatrapati Sambhajinagar (Aurangabad), Maharashtra, India</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Founded</dt>
                            <dd class="text-slate-800">2024</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Website</dt>
                            <dd><a href="https://csnexplore.com" class="text-primary font-semibold hover:underline">csnexplore.com</a></dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Phone</dt>
                            <dd><a href="tel:+918600968888" class="text-primary font-semibold hover:underline">+91-8600968888</a></dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-32 shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider leading-5">Email</dt>
                            <dd><a href="mailto:supportcsnexplore@gmail.com" class="text-primary font-semibold hover:underline">supportcsnexplore@gmail.com</a></dd>
                        </div>
                    </dl>
                </div>

                <!-- Social Profiles -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <h3 class="font-bold text-slate-900 text-lg mb-4">Official Social Profiles</h3>
                    <p class="text-xs text-slate-500 mb-4">All profiles below are the <strong>official</strong> CSNExplore accounts. If you find any other accounts claiming to be CSNExplore, please <a href="<?php echo BASE_PATH; ?>/contact" class="text-primary hover:underline">contact us</a>.</p>
                    <div class="space-y-2">
                        <?php
                        $socials = [
                            ['name' => 'Instagram',  'handle' => '@csnexplore_',  'url' => 'https://www.instagram.com/csnexplore_/',       'color' => '#E4405F'],
                            ['name' => 'Facebook',   'handle' => 'csnexplore',    'url' => 'https://www.facebook.com/csnexplore',          'color' => '#1877F2'],
                            ['name' => 'X (Twitter)','handle' => '@csnexplore',   'url' => 'https://twitter.com/csnexplore',               'color' => '#000000'],
                            ['name' => 'About.me',   'handle' => 'csnexplore',    'url' => 'https://about.me/csnexplore',                  'color' => '#00ADEF'],
                            ['name' => 'Website',    'handle' => 'csnexplore.com','url' => 'https://csnexplore.com',                       'color' => '#ec5b13'],
                            ['name' => 'Website .in','handle' => 'csnexplore.in', 'url' => 'https://csnexplore.in',                        'color' => '#ec5b13'],
                        ];
                        foreach ($socials as $s): ?>
                        <a href="<?php echo htmlspecialchars($s['url']); ?>" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-white transition-colors border border-transparent hover:border-slate-200 group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background:<?php echo $s['color']; ?>1a; border:1.5px solid <?php echo $s['color']; ?>33">
                                <span class="text-xs font-black" style="color:<?php echo $s['color']; ?>"><?php echo strtoupper(substr($s['name'], 0, 1)); ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($s['name']); ?></p>
                                <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($s['handle']); ?></p>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors text-lg">open_in_new</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview (Internal Linking Hub) -->
<section class="py-16 bg-slate-50">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="text-center mb-10" data-reveal>
            <span class="text-primary font-bold text-xs uppercase tracking-widest">Our Services</span>
            <h2 class="text-3xl font-serif font-bold text-slate-900 mt-2">Everything You Need in Chhatrapati Sambhajinagar</h2>
            <p class="text-slate-500 mt-3 max-w-2xl mx-auto">CSNExplore is your one-stop tourism platform for Chhatrapati Sambhajinagar (Aurangabad), Maharashtra.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" data-reveal>
            <?php
            $services = [
                ['icon' => 'bed',                  'title' => 'Hotels & Stays',       'desc' => 'Verified stays in Chhatrapati Sambhajinagar',    'url' => '/listing?type=stays'],
                ['icon' => 'directions_car',        'title' => 'Car Rentals',           'desc' => 'Self-drive & chauffeur cars in Aurangabad',      'url' => '/listing?type=cars'],
                ['icon' => 'motorcycle',            'title' => 'Bike Rentals',          'desc' => 'Scooters & bikes in Sambhajinagar',              'url' => '/listing?type=bikes'],
                ['icon' => 'confirmation_number',   'title' => 'Attractions',           'desc' => 'Ajanta, Ellora & Sambhajinagar attractions',     'url' => '/listing?type=attractions'],
                ['icon' => 'restaurant',            'title' => 'Dine & Eat',            'desc' => 'Top restaurants in Chhatrapati Sambhajinagar',   'url' => '/listing?type=restaurants'],
                ['icon' => 'directions_bus',        'title' => 'Bus Tickets',           'desc' => 'Book buses from/to Aurangabad',                  'url' => '/bus'],
                ['icon' => 'auto_fix_high',         'title' => 'AI Trip Planner',       'desc' => 'Smart itineraries for Sambhajinagar',            'url' => '/suggestor'],
                ['icon' => 'article',               'title' => 'Travel Guides',         'desc' => 'Blogs & guides for Aurangabad travel',           'url' => '/blogs'],
            ];
            foreach ($services as $svc): ?>
            <a href="<?php echo BASE_PATH . $svc['url']; ?>" class="bg-white rounded-2xl p-5 border border-slate-100 hover:border-primary/30 hover:shadow-md hover:-translate-y-1 transition-all group">
                <span class="material-symbols-outlined text-primary text-3xl mb-3 block" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24"><?php echo $svc['icon']; ?></span>
                <h3 class="font-bold text-slate-900 text-sm mb-1 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($svc['title']); ?></h3>
                <p class="text-xs text-slate-500 leading-relaxed"><?php echo htmlspecialchars($svc['desc']); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Disambiguation Block (Hidden SEO, visible text) -->
<section class="py-12 bg-white border-t border-slate-100">
    <div class="max-w-[1140px] mx-auto px-5">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl font-serif font-bold text-slate-900 mb-4">About the CSNExplore Brand Name</h2>
            <p class="text-slate-600 leading-relaxed text-sm mb-4">
                <strong>CSNExplore</strong> is an original brand name — it is not a variant spelling or abbreviation of any other company.
                The name is a portmanteau: <strong>CSN</strong> = <strong>C</strong>hhatrapati <strong>S</strong>ambhaji<strong>n</strong>agar, and <strong>Explore</strong> = discovering its wonders.
                If you have searched Google for <em>csnexplore</em> and been redirected or shown corrections to other terms, please use <strong>Search instead for csnexplore</strong> to find our official website at <a href="https://csnexplore.com" class="text-primary font-semibold hover:underline">csnexplore.com</a>.
            </p>
            <p class="text-slate-500 text-xs leading-loose">
                Recognized name variants: csnexplore &bull; csn explore &bull; csnxplore &bull; cnsexplore &bull; scsnexplore &bull; csnexplore.com &bull; csnexplore.in &bull; CSNExplore Tourism Portal &bull; CSNExplore Aurangabad &bull; CSNExplore Sambhajinagar
            </p>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-primary">
    <div class="max-w-[1140px] mx-auto px-5 text-center">
        <h2 class="text-3xl font-serif font-black text-white mb-4">Ready to explore Chhatrapati Sambhajinagar?</h2>
        <p class="text-white/80 mb-8 max-w-xl mx-auto">Book hotels, rent cars or bikes, discover attractions and plan your perfect trip — all on CSNExplore.</p>
        <a href="<?php echo BASE_PATH; ?>/" class="inline-block bg-white text-primary px-8 py-4 rounded-full font-bold text-lg hover:bg-slate-100 transition-colors shadow-xl">
            Start Exploring
        </a>
    </div>
</section>
</main>

<?php require 'footer.php'; ?>
