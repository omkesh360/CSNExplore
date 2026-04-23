<?php
/**
 * generate_sitemap.php — Dynamic XML sitemap (index + split sitemaps)
 *
 * Routes (via .htaccess):
 *   /sitemap.xml          → sitemap index
 *   /sitemap-static.xml   → static pages
 *   /sitemap-stays.xml    → stays listings
 *   /sitemap-cars.xml     → cars listings
 *   /sitemap-bikes.xml    → bikes listings
 *   /sitemap-attractions.xml
 *   /sitemap-restaurants.xml
 *   /sitemap-buses.xml
 *   /sitemap-blogs.xml    → blog posts
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'csnexplore.com';
$base   = $scheme . '://' . $host;
$today  = date('Y-m-d');

// Determine which sitemap to serve
$uri    = $_SERVER['REQUEST_URI'] ?? '/sitemap.xml';
$which  = basename(parse_url($uri, PHP_URL_PATH), '.xml'); // e.g. 'sitemap', 'sitemap-blogs'

$db = getDB();

// ── Sitemap Index ─────────────────────────────────────────────────────────────
if ($which === 'sitemap') {
    $sitemaps = ['sitemap-static','sitemap-stays','sitemap-cars','sitemap-bikes',
                 'sitemap-attractions','sitemap-restaurants','sitemap-buses','sitemap-blogs'];
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($sitemaps as $sm) {
        echo "  <sitemap>\n";
        echo "    <loc>" . htmlspecialchars($base . '/' . $sm . '.xml', ENT_XML1) . "</loc>\n";
        echo "    <lastmod>$today</lastmod>\n";
        echo "  </sitemap>\n";
    }
    echo '</sitemapindex>';
    exit;
}

// ── Static pages ──────────────────────────────────────────────────────────────
if ($which === 'sitemap-static') {
    $urls = [
        ['loc' => '',                      'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => 'about',                 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => 'contact',               'priority' => '0.6', 'changefreq' => 'monthly'],
        ['loc' => 'blogs',                 'priority' => '0.8', 'changefreq' => 'daily'],
        ['loc' => 'listing/stays',         'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => 'listing/cars',          'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => 'listing/bikes',         'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => 'listing/restaurants',   'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => 'listing/attractions',   'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => 'listing/buses',         'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => 'privacy',               'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => 'terms',                 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => 'suggestor',             'priority' => '0.7', 'changefreq' => 'monthly'],
    ];
    _output_urlset($base, $urls, $today);
    exit;
}

// ── Blog sitemap ──────────────────────────────────────────────────────────────
if ($which === 'sitemap-blogs') {
    $urls = [];
    try {
        $blogs = $db->fetchAll("SELECT id, title, updated_at FROM blogs WHERE status = 'published' ORDER BY id DESC LIMIT 600");
        foreach ($blogs as $blog) {
            $slug    = $blog['id'] . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $blog['title']), '-'));
            $lastmod = substr($blog['updated_at'] ?? $today, 0, 10);
            $urls[]  = ['loc' => 'blogs/' . $slug, 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $lastmod];
        }
    } catch (Exception $e) { error_log('Sitemap blog error: ' . $e->getMessage()); }
    _output_urlset($base, $urls, $today);
    exit;
}

// ── Listing sitemaps ──────────────────────────────────────────────────────────
$listingMap = [
    'sitemap-stays'       => ['table' => 'stays',       'nameCol' => 'name',     'priority' => '0.8'],
    'sitemap-cars'        => ['table' => 'cars',        'nameCol' => 'name',     'priority' => '0.8'],
    'sitemap-bikes'       => ['table' => 'bikes',       'nameCol' => 'name',     'priority' => '0.7'],
    'sitemap-attractions' => ['table' => 'attractions', 'nameCol' => 'name',     'priority' => '0.9'],
    'sitemap-restaurants' => ['table' => 'restaurants', 'nameCol' => 'name',     'priority' => '0.7'],
    'sitemap-buses'       => ['table' => 'buses',       'nameCol' => 'operator', 'priority' => '0.6'],
];

if (isset($listingMap[$which])) {
    $lt   = $listingMap[$which];
    $type = str_replace('sitemap-', '', $which);
    $urls = [];
    try {
        $rows = $db->fetchAll("SELECT id, {$lt['nameCol']} as name, updated_at FROM {$lt['table']} WHERE is_active = 1");
        foreach ($rows as $row) {
            $slug    = $type . '-' . $row['id'] . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $row['name']), '-'));
            $lastmod = substr($row['updated_at'] ?? $today, 0, 10);
            $urls[]  = ['loc' => 'listing-detail/' . $slug, 'priority' => $lt['priority'], 'changefreq' => 'weekly', 'lastmod' => $lastmod];
        }
    } catch (Exception $e) { error_log("Sitemap $which error: " . $e->getMessage()); }
    _output_urlset($base, $urls, $today);
    exit;
}

// Fallback — serve index
header('Location: /sitemap.xml', true, 301);
exit;

// ── Helper ────────────────────────────────────────────────────────────────────
function _output_urlset(string $base, array $urls, string $today): void {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $u) {
        $loc     = $base . '/' . ltrim($u['loc'], '/');
        $lastmod = $u['lastmod'] ?? $today;
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
        echo "    <lastmod>$lastmod</lastmod>\n";
        echo "    <changefreq>" . htmlspecialchars($u['changefreq'], ENT_XML1) . "</changefreq>\n";
        echo "    <priority>" . htmlspecialchars($u['priority'], ENT_XML1) . "</priority>\n";
        echo "  </url>\n";
    }
    echo '</urlset>';
}
