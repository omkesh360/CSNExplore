<?php
// sitemap.php — Centralized XML sitemap generator with caching and image support
require_once __DIR__ . '/php/config.php';

$cacheFile = __DIR__ . '/sitemap.xml';
$cacheTime = 3600; // 1 hour

// Serve cached sitemap if valid to improve performance and prevent DB load
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime) && !isset($_GET['nocache'])) {
    header('Content-Type: application/xml; charset=utf-8');
    readfile($cacheFile);
    exit;
}

$db = getDB();
$siteUrl = rtrim(SITE_URL, '/');

// Build URL set
$urls = [];
$today = date('Y-m-d');

// 1. Static Routes
$staticRoutes = [
    ['route' => '/',              'priority' => '1.0', 'freq' => 'daily'],
    ['route' => '/suggestor',     'priority' => '0.8', 'freq' => 'weekly'],
    ['route' => '/about',         'priority' => '0.7', 'freq' => 'monthly'],
    ['route' => '/brand',         'priority' => '0.8', 'freq' => 'monthly'],
    ['route' => '/contact',       'priority' => '0.7', 'freq' => 'monthly'],
    ['route' => '/faq',           'priority' => '0.7', 'freq' => 'monthly'],
    ['route' => '/itineraries',   'priority' => '0.7', 'freq' => 'weekly'],
    ['route' => '/travel-guide',  'priority' => '0.7', 'freq' => 'weekly'],
    ['route' => '/explore',       'priority' => '0.7', 'freq' => 'weekly'],
    ['route' => '/blogs',         'priority' => '0.8', 'freq' => 'daily'],
    // ── Clean URL listing pages (individual, fully indexable) ───────────────
    ['route' => '/hotels',        'priority' => '0.9', 'freq' => 'daily'],
    ['route' => '/car-rentals',   'priority' => '0.9', 'freq' => 'daily'],
    ['route' => '/bike-rentals',  'priority' => '0.9', 'freq' => 'daily'],
    ['route' => '/attractions',   'priority' => '0.9', 'freq' => 'daily'],
    ['route' => '/restaurants',   'priority' => '0.9', 'freq' => 'daily'],
    ['route' => '/bus-rentals',   'priority' => '0.8', 'freq' => 'weekly'],
];


foreach ($staticRoutes as $r) {
    $urls[] = [
        'loc' => $siteUrl . $r['route'],
        'lastmod' => $today,
        'changefreq' => $r['freq'],
        'priority' => $r['priority']
    ];
}

// 2. Dynamic Blogs
try {
    $blogs = $db->fetchAll("SELECT id, title, image, updated_at, meta_description FROM blogs WHERE status='published'");
    foreach ($blogs as $blog) {
        $slug = generateSlug('blogs', $blog['id'], $blog['title']) . '.html';
        $entry = [
            'loc' => $siteUrl . '/blogs/' . $slug,
            'lastmod' => date('Y-m-d', strtotime($blog['updated_at'])),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];
        if (!empty($blog['image'])) {
            $imgUrl = get_working_image_url($blog['image']);
            $entry['image'] = [
                'loc' => strpos($imgUrl, 'http') === 0 ? $imgUrl : $siteUrl . '/' . ltrim($imgUrl, '/'),
                'title' => $blog['title'],
                'caption' => $blog['meta_description'] ?? ''
            ];
        }
        $urls[] = $entry;
    }
} catch (Exception $e) {
    // Silently continue
}

// 3. Dynamic Listings
$types = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];
foreach ($types as $type) {
    try {
        $nameCol = ($type === 'buses') ? 'operator' : 'name';
        $descCol = ($type === 'buses') ? 'bus_type' : 'description';
        
        $items = $db->fetchAll("SELECT id, $nameCol AS name, image, updated_at, $descCol AS description FROM $type WHERE is_active=1");
        foreach ($items as $item) {
            $slug = generateSlug($type, $item['id'], $item['name']) . '.html';
            $entry = [
                'loc' => $siteUrl . '/listing-detail/' . $slug,
                'lastmod' => date('Y-m-d', strtotime($item['updated_at'])),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
            if (!empty($item['image'])) {
                $imgUrl = get_working_image_url($item['image']);
                $entry['image'] = [
                    'loc' => strpos($imgUrl, 'http') === 0 ? $imgUrl : $siteUrl . '/' . ltrim($imgUrl, '/'),
                    'title' => $item['name'],
                    'caption' => $item['description'] ?? ''
                ];
            }
            $urls[] = $entry;
        }
    } catch (Exception $e) {
        // Silently continue
    }
}

// Helper to sanitize XML text
function cleanXmlText($text) {
    if (empty($text)) return '';
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return htmlspecialchars(strip_tags($text), ENT_XML1, 'UTF-8');
}

// Build XML Output
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

foreach ($urls as $u) {
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . cleanXmlText($u['loc']) . "</loc>\n";
    $xml .= "    <lastmod>" . cleanXmlText($u['lastmod']) . "</lastmod>\n";
    $xml .= "    <changefreq>" . cleanXmlText($u['changefreq']) . "</changefreq>\n";
    $xml .= "    <priority>" . cleanXmlText($u['priority']) . "</priority>\n";
    if (isset($u['image'])) {
        $xml .= "    <image:image>\n";
        $xml .= "      <image:loc>" . cleanXmlText($u['image']['loc']) . "</image:loc>\n";
        $xml .= "      <image:title>" . cleanXmlText($u['image']['title']) . "</image:title>\n";
        if (!empty($u['image']['caption'])) {
            $cap = cleanXmlText($u['image']['caption']);
            if (strlen($cap) > 150) $cap = substr($cap, 0, 147) . '...';
            $xml .= "      <image:caption>" . $cap . "</image:caption>\n";
        }
        $xml .= "    </image:image>\n";
    }
    $xml .= "  </url>\n";
}
$xml .= "</urlset>\n";

// Write to static file for caching
@file_put_contents($cacheFile, $xml);

// Serve XML
header('Content-Type: application/xml; charset=utf-8');
echo $xml;
exit;
