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
    || (function_exists('env') ? env('APP_ENV') : getenv('APP_ENV')) === 'local'
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

// ── Static pages ────────────────----------------------------------------------
$static = [
    [''                    , '1.0', 'daily'],
    ['listing/stays'       , '0.9', 'daily'],
    ['listing/cars'        , '0.9', 'daily'],
    ['listing/bikes'       , '0.9', 'daily'],
    ['listing/restaurants' , '0.9', 'daily'],
    ['listing/attractions' , '0.9', 'daily'],
    ['travel-guide'        , '0.9', 'monthly'],
    ['bus'                 , '0.8', 'weekly'],
    ['blogs'               , '0.8', 'daily'],
    ['itineraries'         , '0.8', 'weekly'],
    ['near-attractions'    , '0.8', 'weekly'],
    ['suggestor'           , '0.8', 'weekly'],
    ['compare'             , '0.7', 'weekly'],
    ['about'               , '0.7', 'monthly'],
    ['contact'             , '0.7', 'monthly'],
    ['faq'                 , '0.7', 'monthly'],
    ['privacy'             , '0.5', 'monthly'],
    ['terms'               , '0.5', 'monthly'],
    ['login'               , '0.3', 'monthly'],
    ['register'            , '0.3', 'monthly'],
    ['forgot-password'     , '0.3', 'monthly'],
];
foreach ($static as [$loc, $pri, $freq]) {
    $urls[] = ['loc' => $base . '/' . ltrim($loc, '/'), 'lastmod' => $today, 'changefreq' => $freq, 'priority' => $pri];
}
echo 'Static pages: ' . count($static) . "\n";

// ── Build DB meta lookups ─────────────────────────────────────────────────────
$listingMeta = [];
$blogMeta    = [];

if ($db) {
    $types = [
        'stays'       => ['name',     '0.80'],
        'cars'        => ['name',     '0.80'],
        'bikes'       => ['name',     '0.70'],
        'attractions' => ['name',     '0.90'],
        'restaurants' => ['name',     '0.70'],
    ];
    foreach ($types as $type => [$col, $pri]) {
        try {
            $rows = $db->fetchAll("SELECT id, $col AS name, image, updated_at, location, description FROM $type");
            foreach ($rows as $r) {
                $slug = generateSlug($type, $r['id'], $r['name']);
                $listingMeta[$slug] = [
                    'priority'    => $pri,
                    'updated_at'  => $r['updated_at'],
                    'image'       => $r['image'] ?? '',
                    'name'        => $r['name'],
                    'location'    => $r['location'] ?? '',
                    'description' => $r['description'] ?? '',
                ];
            }
            unset($rows);
        } catch (Exception $e) {}
    }
    // Special handling for buses (uses operator/from_location/to_location/bus_type)
    try {
        $rows = $db->fetchAll("SELECT id, operator AS name, image, updated_at, bus_type AS description, from_location, to_location FROM buses");
        foreach ($rows as $r) {
            $slug = generateSlug('buses', $r['id'], $r['name']);
            $listingMeta[$slug] = [
                'priority'    => '0.6',
                'updated_at'  => $r['updated_at'],
                'image'       => $r['image'] ?? '',
                'name'        => $r['name'],
                'location'    => ($r['from_location'] ?? '') . ' to ' . ($r['to_location'] ?? ''),
                'description' => $r['description'] ?? '',
            ];
        }
        unset($rows);
    } catch (Exception $e) {}

    try {
        $dbBlogs = $db->fetchAll("SELECT id, title, image, updated_at, created_at, meta_description AS description FROM blogs");
        foreach ($dbBlogs as $b) {
            $slug = generateSlug('blogs', $b['id'], $b['title']);
            $blogMeta[$slug] = [
                'updated_at'  => $b['updated_at'],
                'created_at'  => $b['created_at'],
                'image'       => $b['image'] ?? '',
                'title'       => $b['title'],
                'description' => $b['description'] ?? '',
            ];
        }
        unset($dbBlogs);
    } catch (Exception $e) {}
}

// ── Listing detail pages — scan disk ─────────────────────────────────────────
$listingDir   = $root . '/listing-detail/';
$listingFiles = is_dir($listingDir) ? glob($listingDir . '*.html') : [];
foreach ($listingFiles as $file) {
    $slug    = basename($file, '.html');
    $meta    = $listingMeta[$slug] ?? null;
    $lastmod = $meta ? substr($meta['updated_at'] ?? $today, 0, 10) : date('Y-m-d', filemtime($file));
    $entry   = [
        'loc'        => $base . '/listing-detail/' . $slug,
        'lastmod'    => $lastmod,
        'changefreq' => 'weekly',
        'priority'   => $meta['priority'] ?? '0.7',
    ];
    if (!empty($meta['image'])) {
        $entry['image']       = strpos($meta['image'], 'http') === 0 ? $meta['image'] : $base . '/' . ltrim($meta['image'], '/');
        $entry['image_title'] = $meta['name'] ?? $slug;
        if (!empty($meta['location'])) {
            $entry['image_geo'] = $meta['location'];
        }
        if (!empty($meta['description'])) {
            $entry['image_caption'] = $meta['description'];
        }
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
    
    $createdDate = $meta && isset($meta['created_at']) ? strtotime($meta['created_at']) : filemtime($file);
    $ageInDays = (time() - $createdDate) / 86400;
    $priority = '0.80';
    if ($ageInDays < 30) $priority = '0.85';
    if ($ageInDays < 7) $priority = '0.90';
    
    $entry   = [
        'loc'        => $base . '/blogs/' . $slug,
        'lastmod'    => $lastmod,
        'changefreq' => 'weekly',
        'priority'   => $priority
    ];
    if (!empty($meta['image'])) {
        $entry['image']       = strpos($meta['image'], 'http') === 0 ? $meta['image'] : $base . '/' . ltrim($meta['image'], '/');
        $entry['image_title'] = $meta['title'] ?? $slug;
        if (!empty($meta['description'])) {
            $entry['image_caption'] = $meta['description'];
        }
    }
    $urls[] = $entry;
}
echo 'Blog pages: ' . count($blogFiles) . "\n";

// Helper to decode HTML entities recursively to prevent double-escaping in the sitemap XML
function cleanSitemapText($text) {
    if (empty($text)) return '';
    $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    while ($decoded !== $text) {
        $text = $decoded;
        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return $decoded;
}

// ── Build XML ─────────────────────────────────────────────────────────────────
$total = count($urls);
$xml   = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml  .= '<!-- CSNExplore Sitemap | Generated: ' . date('Y-m-d H:i:s') . ' | Total URLs: ' . $total . ' -->' . "\n";
$xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($urls as $u) {
    $xml .= "  <url>\n    <loc>" . htmlspecialchars(cleanSitemapText($u['loc']), ENT_XML1) . "</loc>\n";
    $xml .= "    <lastmod>" . htmlspecialchars(cleanSitemapText($u['lastmod']), ENT_XML1) . "</lastmod>\n";
    $xml .= "    <changefreq>" . htmlspecialchars(cleanSitemapText($u['changefreq']), ENT_XML1) . "</changefreq>\n";
    $xml .= "    <priority>" . htmlspecialchars(cleanSitemapText($u['priority']), ENT_XML1) . "</priority>\n";
    if (!empty($u['image'])) {
        $xml .= "    <image:image>\n      <image:loc>" . htmlspecialchars(cleanSitemapText($u['image']), ENT_XML1) . "</image:loc>\n";
        if (!empty($u['image_title'])) $xml .= "      <image:title>" . htmlspecialchars(cleanSitemapText($u['image_title']), ENT_XML1) . "</image:title>\n";
        if (!empty($u['image_caption'])) {
            $cap = strip_tags(cleanSitemapText($u['image_caption']));
            if (strlen($cap) > 150) $cap = substr($cap, 0, 147) . '...';
            $xml .= "      <image:caption>" . htmlspecialchars($cap, ENT_XML1) . "</image:caption>\n";
        }
        if (!empty($u['image_geo'])) $xml .= "      <image:geo_location>" . htmlspecialchars(cleanSitemapText($u['image_geo']), ENT_XML1) . "</image:geo_location>\n";
        $xml .= "    </image:image>\n";
    }
    $xml .= "  </url>\n";
}
$xml .= '</urlset>';

$outPath = $root . '/sitemap.xml';
file_put_contents($outPath, $xml);
echo "Done — {$total} total URLs written to sitemap.xml\n";
