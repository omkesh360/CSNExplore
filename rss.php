<?php
// rss.php — RSS 2.0 Feed Generator for SEO
// Pinging this file or submitting it to Google Publisher Center helps index content instantly.
require_once __DIR__ . '/php/config.php';

header('Content-Type: application/rss+xml; charset=utf-8');

$db = getDB();
$siteUrl = rtrim(SITE_URL, '/');
$feedUrl = $siteUrl . '/rss.php';

$items = [];

// Fetch latest blogs
try {
    $blogs = $db->fetchAll("SELECT id, title, content, image, updated_at, author FROM blogs WHERE status='published' ORDER BY updated_at DESC LIMIT 20");
    foreach ($blogs as $blog) {
        $slug = generateSlug('blogs', $blog['id'], $blog['title']) . '.html';
        $desc = strip_tags(substr($blog['content'] ?? '', 0, 300)) . '...';
        $items[] = [
            'title' => $blog['title'],
            'link' => $siteUrl . '/blogs/' . $slug,
            'description' => $desc,
            'pubDate' => date(DATE_RSS, strtotime($blog['updated_at'])),
            'author' => $blog['author'] ?? 'Admin',
            'guid' => $siteUrl . '/blogs/' . $blog['id']
        ];
    }
} catch (Exception $e) {}

// Fetch latest items across tables
$types = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];
foreach ($types as $type) {
    try {
        $nameCol = ($type === 'buses') ? 'operator' : 'name';
        $descCol = ($type === 'buses') ? 'bus_type' : 'description';
        
        $listings = $db->fetchAll("SELECT id, $nameCol AS name, image, updated_at, $descCol AS description FROM $type WHERE is_active=1 ORDER BY updated_at DESC LIMIT 5");
        foreach ($listings as $listing) {
            $slug = generateSlug($type, $listing['id'], $listing['name']) . '.html';
            $desc = strip_tags(substr($listing['description'] ?? '', 0, 300)) . '...';
            $items[] = [
                'title' => 'New in ' . ucfirst($type) . ': ' . $listing['name'],
                'link' => $siteUrl . '/listing-detail/' . $slug,
                'description' => $desc,
                'pubDate' => date(DATE_RSS, strtotime($listing['updated_at'])),
                'author' => 'CSNExplore',
                'guid' => $siteUrl . '/listing-detail/' . $type . '/' . $listing['id']
            ];
        }
    } catch (Exception $e) {}
}

// Sort items by pubDate descending
usort($items, function($a, $b) {
    return strtotime($b['pubDate']) - strtotime($a['pubDate']);
});
$items = array_slice($items, 0, 50); // Keep top 50

$lastBuildDate = !empty($items) ? $items[0]['pubDate'] : date(DATE_RSS);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>CSNExplore - Latest Travel Updates</title>
    <link><?php echo htmlspecialchars($siteUrl); ?></link>
    <description>Latest travel guides, stories, and listings for Chhatrapati Sambhajinagar.</description>
    <language>en-us</language>
    <lastBuildDate><?php echo htmlspecialchars($lastBuildDate); ?></lastBuildDate>
    <atom:link href="<?php echo htmlspecialchars($feedUrl); ?>" rel="self" type="application/rss+xml" />
    <?php foreach ($items as $item): ?>
    <item>
      <title><?php echo htmlspecialchars($item['title'], ENT_XML1); ?></title>
      <link><?php echo htmlspecialchars($item['link'], ENT_XML1); ?></link>
      <description><![CDATA[<?php echo $item['description']; ?>]]></description>
      <pubDate><?php echo htmlspecialchars($item['pubDate']); ?></pubDate>
      <guid isPermaLink="false"><?php echo htmlspecialchars($item['guid'], ENT_XML1); ?></guid>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>
