<?php
/**
 * generate_sitemap.php — Dynamic XML sitemap generator
 * Accessible at /sitemap.xml via .htaccess rewrite
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

// Detect base URL
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'csnexplore.com';
$base     = $scheme . '://' . $host;

$today    = date('Y-m-d');
$db       = getDB();

$urls = [];

// ── Static pages ──────────────────────────────────────────────────────────────
$staticPages = [
    ['loc' => '',              'priority' => '1.0',  'changefreq' => 'daily'],
    ['loc' => 'about',         'priority' => '0.7',  'changefreq' => 'monthly'],
    ['loc' => 'contact',       'priority' => '0.6',  'changefreq' => 'monthly'],
    ['loc' => 'blogs',         'priority' => '0.8',  'changefreq' => 'daily'],
    ['loc' => '/listing/stays',       'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/listing/cars',        'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/listing/bikes',       'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/listing/restaurants', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => '/listing/attractions', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/listing/buses',       'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => 'privacy',       'priority' => '0.3',  'changefreq' => 'yearly'],
    ['loc' => 'terms',         'priority' => '0.3',  'changefreq' => 'yearly'],
];

foreach ($staticPages as $p) {
    $urls[] = [
        'loc'        => $base . '/' . $p['loc'],
        'lastmod'    => $today,
        'changefreq' => $p['changefreq'],
        'priority'   => $p['priority'],
    ];
}

// ── Blog posts ────────────────────────────────────────────────────────────────
try {
    $blogs = $db->fetchAll("SELECT id, title, updated_at FROM blogs WHERE status = 'published' ORDER BY id DESC LIMIT 500");
    foreach ($blogs as $blog) {
        // Generate slug from title (same logic as generate_html.php)
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $blog['title']), '-'));
        $slug = $blog['id'] . '-' . $slug;
        $lastmod = substr($blog['updated_at'] ?? $today, 0, 10);
        $urls[] = [
            'loc'        => $base . '/blogs/' . $slug,
            'lastmod'    => $lastmod,
            'changefreq' => 'monthly',
            'priority'   => '0.6',
        ];
    }
} catch (Exception $e) {
    error_log('Sitemap blog error: ' . $e->getMessage());
}

// ── Listing detail pages ──────────────────────────────────────────────────────
$listingTypes = [
    ['table' => 'stays',       'prefix' => 'stays',       'nameCol' => 'name'],
    ['table' => 'cars',        'prefix' => 'cars',        'nameCol' => 'name'],
    ['table' => 'bikes',       'prefix' => 'bikes',       'nameCol' => 'name'],
    ['table' => 'restaurants', 'prefix' => 'restaurants', 'nameCol' => 'name'],
    ['table' => 'attractions', 'prefix' => 'attractions', 'nameCol' => 'name'],
    ['table' => 'buses',       'prefix' => 'buses',       'nameCol' => 'operator'],
];

foreach ($listingTypes as $lt) {
    try {
        $rows = $db->fetchAll("SELECT id, {$lt['nameCol']} as name, updated_at FROM {$lt['table']} WHERE is_active = 1");
        foreach ($rows as $row) {
            $slug    = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $row['name']), '-'));
            $slug    = $lt['prefix'] . '-' . $row['id'] . '-' . $slug;
            $lastmod = substr($row['updated_at'] ?? $today, 0, 10);
            $urls[]  = [
                'loc'        => $base . '/listing-detail/' . $slug,
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ];
        }
    } catch (Exception $e) {
        error_log('Sitemap listing error (' . $lt['table'] . '): ' . $e->getMessage());
    }
}

// ── Output XML ────────────────────────────────────────────────────────────────
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
    echo "    <lastmod>" . htmlspecialchars($url['lastmod'], ENT_XML1) . "</lastmod>\n";
    echo "    <changefreq>" . htmlspecialchars($url['changefreq'], ENT_XML1) . "</changefreq>\n";
    echo "    <priority>" . htmlspecialchars($url['priority'], ENT_XML1) . "</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>';
