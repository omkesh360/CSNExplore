<?php
/**
 * regenerate-sitemap.php
 * Generates ONE single sitemap.xml at the website root containing EVERY URL:
 *   - Static pages
 *   - All listing detail HTML files on disk (listing-detail/*.html)
 *   - All blog HTML files on disk (blogs/*.html)
 *   - DB records for listings/blogs not yet generated as HTML
 *
 * POST /php/api/regenerate-sitemap.php  → requires admin JWT
 * Returns JSON: { success, total, breakdown, ping, file, generated_at }
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try { requireAdmin(); } catch (Exception $e) { sendError('Unauthorized', 401); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Method not allowed', 405);

try {
    $db    = getDB();
    $root  = dirname(__DIR__, 2);
    $today = date('Y-m-d');

    // Detect base URL
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'csnexplore.com';
    if (!preg_match('/^[a-zA-Z0-9.\-:]+$/', $host)) $host = 'csnexplore.com';
    $base   = $scheme . '://' . $host;

    $urls      = [];
    $breakdown = [];

    // ── 1. Static pages ───────────────────────────────────────────────────────
    $staticPages = [
        [''                    , '1.0', 'daily'],
        ['about'               , '0.7', 'monthly'],
        ['contact'             , '0.6', 'monthly'],
        ['blogs'               , '0.8', 'daily'],
        ['faq'                 , '0.5', 'monthly'],
        ['listing/stays'       , '0.9', 'weekly'],
        ['listing/cars'        , '0.9', 'weekly'],
        ['listing/bikes'       , '0.9', 'weekly'],
        ['listing/restaurants' , '0.8', 'weekly'],
        ['listing/attractions' , '0.9', 'weekly'],
        ['listing/buses'       , '0.8', 'weekly'],
    ];
    foreach ($staticPages as [$loc, $pri, $freq]) {
        $urls[] = ['loc' => $base . '/' . ltrim($loc, '/'), 'lastmod' => $today, 'changefreq' => $freq, 'priority' => $pri];
    }
    $breakdown['static'] = count($staticPages);

    // ── 2. Listing detail pages — scan disk ───────────────────────────────────
    // Build a lookup of DB updated_at and image by slug for enrichment
    $listingMeta = [];
    $listingTypes = [
        'stays'       => ['nameCol' => 'name',     'priority' => '0.8'],
        'cars'        => ['nameCol' => 'name',      'priority' => '0.8'],
        'bikes'       => ['nameCol' => 'name',      'priority' => '0.7'],
        'attractions' => ['nameCol' => 'name',      'priority' => '0.9'],
        'restaurants' => ['nameCol' => 'name',      'priority' => '0.7'],
        'buses'       => ['nameCol' => 'operator',  'priority' => '0.6'],
    ];
    foreach ($listingTypes as $type => $cfg) {
        $rows = $db->fetchAll("SELECT id, {$cfg['nameCol']} AS name, image, updated_at FROM {$type}");
        foreach ($rows as $r) {
            $slug = generateSlug($type, $r['id'], $r['name']);
            $listingMeta[$slug] = [
                'priority'   => $cfg['priority'],
                'updated_at' => $r['updated_at'],
                'image'      => $r['image'] ?? '',
                'name'       => $r['name'],
            ];
        }
    }

    // Scan all HTML files in listing-detail/
    $listingDir   = $root . '/listing-detail/';
    $listingFiles = is_dir($listingDir) ? glob($listingDir . '*.html') : [];
    $listingCount = 0;
    
    // Initialize category counters
    $breakdown['stays'] = 0;
    $breakdown['cars'] = 0;
    $breakdown['bikes'] = 0;
    $breakdown['restaurants'] = 0;
    $breakdown['attractions'] = 0;
    $breakdown['buses'] = 0;
    
    foreach ($listingFiles as $file) {
        $slug    = basename($file, '.html');
        $meta    = $listingMeta[$slug] ?? null;
        $lastmod = $meta ? substr($meta['updated_at'] ?? $today, 0, 10) : $today;
        $pri     = $meta['priority'] ?? '0.7';
        $entry   = [
            'loc'        => $base . '/listing-detail/' . $slug,
            'lastmod'    => $lastmod,
            'changefreq' => 'weekly',
            'priority'   => $pri,
        ];
        if (!empty($meta['image'])) {
            $imgUrl = strpos($meta['image'], 'http') === 0
                ? $meta['image']
                : $base . '/' . ltrim($meta['image'], '/');
            $entry['image']       = $imgUrl;
            $entry['image_title'] = $meta['name'] ?? $slug;
        }
        $urls[] = $entry;
        $listingCount++;
        
        // Count by category based on slug prefix
        if (strpos($slug, 'stays-') === 0) $breakdown['stays']++;
        elseif (strpos($slug, 'cars-') === 0) $breakdown['cars']++;
        elseif (strpos($slug, 'bikes-') === 0) $breakdown['bikes']++;
        elseif (strpos($slug, 'restaurants-') === 0) $breakdown['restaurants']++;
        elseif (strpos($slug, 'attractions-') === 0) $breakdown['attractions']++;
        elseif (strpos($slug, 'buses-') === 0) $breakdown['buses']++;
    }
    $breakdown['listings_total'] = $listingCount;

    // ── 3. Blog pages — scan disk ─────────────────────────────────────────────
    // Build DB lookup for enrichment
    $blogMeta = [];
    $dbBlogs  = $db->fetchAll("SELECT id, title, image, updated_at FROM blogs");
    foreach ($dbBlogs as $b) {
        $slug = generateSlug('blogs', $b['id'], $b['title']);
        $blogMeta[$slug] = [
            'updated_at' => $b['updated_at'],
            'image'      => $b['image'] ?? '',
            'title'      => $b['title'],
        ];
    }

    // Scan all HTML files in blogs/
    $blogDir   = $root . '/blogs/';
    $blogFiles = is_dir($blogDir) ? glob($blogDir . '*.html') : [];
    $blogCount = 0;
    foreach ($blogFiles as $file) {
        $slug    = basename($file, '.html');
        $meta    = $blogMeta[$slug] ?? null;
        $lastmod = $meta ? substr($meta['updated_at'] ?? $today, 0, 10) : $today;
        $entry   = [
            'loc'        => $base . '/blogs/' . $slug,
            'lastmod'    => $lastmod,
            'changefreq' => 'monthly',
            'priority'   => '0.7',
        ];
        if (!empty($meta['image'])) {
            $imgUrl = strpos($meta['image'], 'http') === 0
                ? $meta['image']
                : $base . '/' . ltrim($meta['image'], '/');
            $entry['image']       = $imgUrl;
            $entry['image_title'] = $meta['title'] ?? $slug;
        }
        $urls[] = $entry;
        $blogCount++;
    }
    $breakdown['blogs'] = $blogCount;

    // ── 4. Build single sitemap.xml ───────────────────────────────────────────
    $total = count($urls);
    $xml   = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml  .= '<!-- CSNExplore Sitemap | Generated: ' . date('Y-m-d H:i:s') . ' | Total URLs: ' . $total . ' -->' . "\n";
    $xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $xml  .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

    foreach ($urls as $u) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>"        . htmlspecialchars($u['loc'],        ENT_XML1) . "</loc>\n";
        $xml .= "    <lastmod>"    . htmlspecialchars($u['lastmod'],    ENT_XML1) . "</lastmod>\n";
        $xml .= "    <changefreq>" . htmlspecialchars($u['changefreq'], ENT_XML1) . "</changefreq>\n";
        $xml .= "    <priority>"   . htmlspecialchars($u['priority'],   ENT_XML1) . "</priority>\n";
        if (!empty($u['image'])) {
            $xml .= "    <image:image>\n";
            $xml .= "      <image:loc>" . htmlspecialchars($u['image'], ENT_XML1) . "</image:loc>\n";
            if (!empty($u['image_title'])) {
                $xml .= "      <image:title>" . htmlspecialchars($u['image_title'], ENT_XML1) . "</image:title>\n";
            }
            $xml .= "    </image:image>\n";
        }
        $xml .= "  </url>\n";
    }
    $xml .= '</urlset>';

    // Write to root
    $sitemapPath = $root . '/sitemap.xml';
    if (file_put_contents($sitemapPath, $xml) === false) {
        throw new Exception('Failed to write sitemap.xml — check write permissions on ' . $root);
    }

    // ── 5. Ping search engines ────────────────────────────────────────────────
    $sitemapUrl  = $base . '/sitemap.xml';
    $pingTargets = [
        'Google' => 'https://www.google.com/ping?sitemap=' . urlencode($sitemapUrl),
        'Bing'   => 'https://www.bing.com/ping?sitemap='   . urlencode($sitemapUrl),
    ];
    $pingResults = [];
    foreach ($pingTargets as $engine => $pingUrl) {
        $ctx = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true]]);
        $res = @file_get_contents($pingUrl, false, $ctx);
        $pingResults[$engine] = ($res !== false) ? 'notified' : 'failed';
    }

    // ── 6. Log activity ───────────────────────────────────────────────────────
    try {
        $db->insert('activity_logs', [
            'actor_id'    => null,
            'actor_name'  => 'System',
            'actor_role'  => 'system',
            'action_type' => 'system_init',
            'description' => "Regenerated sitemap.xml — {$total} URLs ({$breakdown['static']} static, {$breakdown['listings_total']} listings, {$breakdown['blogs']} blogs)",
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);
    } catch (Exception $e) { /* ignore */ }

    sendJson([
        'success'      => true,
        'total'        => $total,
        'breakdown'    => $breakdown,
        'ping'         => $pingResults,
        'file'         => $sitemapUrl,
        'generated_at' => date('Y-m-d H:i:s'),
    ]);

} catch (Exception $e) {
    error_log('Sitemap regen error: ' . $e->getMessage());
    sendError('Sitemap generation failed: ' . $e->getMessage(), 500);
}
