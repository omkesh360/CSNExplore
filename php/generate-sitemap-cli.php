<?php
/**
 * CLI sitemap generator — disk-first, DB-optional
 * Scans listing-detail/ and blogs/ HTML files directly.
 * DB is used only for image/lastmod enrichment if available.
 * Usage: php php/generate-sitemap-cli.php
 */
if (php_sapi_name() !== 'cli') die('CLI only');

chdir(dirname(__DIR__)); // ensure we're at project root
$root  = __DIR__ . '/..';

// Detect environment and set base URL
$isLocal = (
    (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false))
    || getenv('APP_ENV') === 'local'
    || php_sapi_name() === 'cli' // CLI defaults to production URL
);
$base  = $isLocal && php_sapi_name() !== 'cli' ? 'http://localhost/CSNExplore' : 'https://csnexplore.com';

$today = date('Y-m-d');
$urls  = [];

// Try to load DB for enrichment (optional)
$db = null;
try {
    require_once __DIR__ . '/config.php';
    $db = getDB();
} catch (Exception $e) {
    echo "Note: DB unavailable ({$e->getMessage()}) — using disk-only mode\n";
}

// ── Static pages ──────────────────────────────────────────────────────────────
// SEO-optimized priorities and frequencies based on Google best practices
$static = [
    // Homepage - highest priority, updated daily
    [''                    , '1.0', 'daily'],
    
    // Main listing pages - very high priority (conversion pages)
    ['listing/stays'       , '0.95', 'daily'],
    ['listing/cars'        , '0.95', 'daily'],
    ['listing/bikes'       , '0.95', 'daily'],
    ['listing/attractions' , '0.95', 'daily'],
    ['listing/restaurants' , '0.90', 'daily'],
    ['listing/buses'       , '0.90', 'weekly'],
    
    // Blog listing - high priority for content marketing
    ['blogs'               , '0.85', 'daily'],
    
    // About & Contact - medium-high priority
    ['about'               , '0.75', 'monthly'],
    ['contact'             , '0.70', 'monthly'],
    
    // FAQ - medium priority
    ['faq'                 , '0.60', 'monthly'],
];
foreach ($static as [$loc, $pri, $freq]) {
    $urls[] = ['loc' => $base . '/' . ltrim($loc, '/'), 'lastmod' => $today, 'changefreq' => $freq, 'priority' => $pri];
}
echo 'Static pages: ' . count($static) . "\n";

// ── Build DB meta lookups ─────────────────────────────────────────────────────
$listingMeta = [];
$blogMeta    = [];

if ($db) {
    // SEO-optimized priorities for listing detail pages
    // Higher priority for high-value conversion pages
    $types = [
        'stays'       => ['name',     '0.90'],  // Hotels - high conversion value
        'cars'        => ['name',     '0.90'],  // Car rentals - high conversion value
        'bikes'       => ['name',     '0.85'],  // Bike rentals - good conversion value
        'attractions' => ['name',     '0.95'],  // Attractions - highest value (tourism focus)
        'restaurants' => ['name',     '0.80'],  // Restaurants - medium-high value
        'buses'       => ['operator', '0.75'],  // Buses - medium value
    ];
    foreach ($types as $type => [$col, $pri]) {
        try {
            $rows = $db->fetchAll("SELECT id, $col AS name, image, updated_at FROM $type");
            foreach ($rows as $r) {
                $slug = generateSlug($type, $r['id'], $r['name']);
                $listingMeta[$slug] = ['priority' => $pri, 'updated_at' => $r['updated_at'], 'image' => $r['image'] ?? '', 'name' => $r['name']];
            }
        } catch (Exception $e) {}
    }
    try {
        $dbBlogs = $db->fetchAll("SELECT id, title, image, updated_at, created_at FROM blogs");
        foreach ($dbBlogs as $b) {
            $slug = generateSlug('blogs', $b['id'], $b['title']);
            $blogMeta[$slug] = [
                'updated_at' => $b['updated_at'],
                'created_at' => $b['created_at'],
                'image'      => $b['image'] ?? '',
                'title'      => $b['title']
            ];
        }
    } catch (Exception $e) {}
}

// ── Listing detail pages — scan disk ─────────────────────────────────────────
$listingDir   = $root . '/listing-detail/';
$listingFiles = is_dir($listingDir) ? glob($listingDir . '*.html') : [];
foreach ($listingFiles as $file) {
    $slug    = basename($file, '.html');
    $meta    = $listingMeta[$slug] ?? null;
    $lastmod = $meta ? substr($meta['updated_at'] ?? $today, 0, 10) : date('Y-m-d', filemtime($file));
    $entry   = ['loc' => $base . '/listing-detail/' . $slug, 'lastmod' => $lastmod, 'changefreq' => 'weekly', 'priority' => $meta['priority'] ?? '0.7'];
    if (!empty($meta['image'])) {
        $entry['image']       = strpos($meta['image'], 'http') === 0 ? $meta['image'] : $base . '/' . ltrim($meta['image'], '/');
        $entry['image_title'] = $meta['name'] ?? $slug;
    }
    $urls[] = $entry;
}
echo 'Listing pages: ' . count($listingFiles) . "\n";

// ── Blog pages — scan disk ────────────────────────────────────────────────────
$blogDir   = $root . '/blogs/';
$blogFiles = is_dir($blogDir) ? glob($blogDir . '*.html') : [];
foreach ($blogFiles as $file) {
    $slug    = basename($file, '.html');
    $meta    = $blogMeta[$slug] ?? null;
    $lastmod = $meta ? substr($meta['updated_at'] ?? $today, 0, 10) : date('Y-m-d', filemtime($file));
    
    // SEO: Dynamic priority based on content freshness (Google loves fresh content)
    $createdDate = $meta && isset($meta['created_at']) ? strtotime($meta['created_at']) : filemtime($file);
    $ageInDays = (time() - $createdDate) / 86400;
    $priority = '0.80'; // Base priority for blogs
    if ($ageInDays < 30) $priority = '0.85'; // Recent blogs (< 1 month) - higher priority
    if ($ageInDays < 7) $priority = '0.90';  // Very recent blogs (< 1 week) - highest priority
    
    $entry   = [
        'loc'        => $base . '/blogs/' . $slug,
        'lastmod'    => $lastmod,
        'changefreq' => 'weekly',  // Weekly crawl for better indexing
        'priority'   => $priority
    ];
    if (!empty($meta['image'])) {
        $entry['image']       = strpos($meta['image'], 'http') === 0 ? $meta['image'] : $base . '/' . ltrim($meta['image'], '/');
        $entry['image_title'] = $meta['title'] ?? $slug;
    }
    $urls[] = $entry;
}
echo 'Blog pages: ' . count($blogFiles) . "\n";

// ── Build XML ─────────────────────────────────────────────────────────────────
$total = count($urls);
$xml   = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml  .= '<!-- CSNExplore Sitemap | Generated: ' . date('Y-m-d H:i:s') . ' | Total URLs: ' . $total . ' -->' . "\n";
$xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($urls as $u) {
    $xml .= "  <url>\n    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
    $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n    <changefreq>{$u['changefreq']}</changefreq>\n    <priority>{$u['priority']}</priority>\n";
    if (!empty($u['image'])) {
        $xml .= "    <image:image>\n      <image:loc>" . htmlspecialchars($u['image'], ENT_XML1) . "</image:loc>\n";
        if (!empty($u['image_title'])) $xml .= "      <image:title>" . htmlspecialchars($u['image_title'], ENT_XML1) . "</image:title>\n";
        $xml .= "    </image:image>\n";
    }
    $xml .= "  </url>\n";
}
$xml .= '</urlset>';

$outPath = $root . '/sitemap.xml';
file_put_contents($outPath, $xml);
echo "Done — {$total} total URLs written to sitemap.xml\n";
