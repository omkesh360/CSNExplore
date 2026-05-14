<?php
/**
 * audit-404-pages.php
 * Checks all HTML files referenced in database and removes dead links
 * Run: php php/audit-404-pages.php
 */

require_once __DIR__ . '/config.php';

$db = getDB();
$root = dirname(__DIR__);
$issues = [];
$fixed = 0;

echo "🔍 Auditing HTML files for 404 issues...\n\n";

// Check blogs
echo "📝 Checking blogs...\n";
$blogs = $db->fetchAll("SELECT id, title, status FROM blogs WHERE status = 'published'");
foreach ($blogs as $blog) {
    $slug = generateSlug('blogs', $blog['id'], $blog['title']);
    $file = $root . '/blogs/' . $slug . '.html';
    
    if (!file_exists($file)) {
        $issues[] = [
            'type' => 'blog',
            'id' => $blog['id'],
            'title' => $blog['title'],
            'expected_file' => $file,
            'url' => 'https://csnexplore.com/blogs/' . $slug
        ];
        echo "  ❌ Missing: {$blog['title']} (ID: {$blog['id']})\n";
        echo "     Expected: $file\n";
    }
}

// Check listings
$listingTypes = [
    'stays' => 'name',
    'cars' => 'name',
    'bikes' => 'name',
    'attractions' => 'name',
    'restaurants' => 'name',
    'buses' => 'operator'
];

foreach ($listingTypes as $table => $nameCol) {
    echo "\n🏷️  Checking $table...\n";
    $rows = $db->fetchAll("SELECT id, $nameCol as name, is_active FROM $table WHERE is_active = 1");
    
    foreach ($rows as $row) {
        $slug = generateSlug($table, $row['id'], $row['name']);
        $file = $root . '/listing-detail/' . $slug . '.html';
        
        if (!file_exists($file)) {
            $issues[] = [
                'type' => $table,
                'id' => $row['id'],
                'title' => $row['name'],
                'expected_file' => $file,
                'url' => 'https://csnexplore.com/listing-detail/' . $slug
            ];
            echo "  ❌ Missing: {$row['name']} (ID: {$row['id']})\n";
            echo "     Expected: $file\n";
        }
    }
}

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 AUDIT SUMMARY\n";
echo str_repeat("=", 70) . "\n";
echo "Total issues found: " . count($issues) . "\n\n";

if (count($issues) > 0) {
    echo "💡 RECOMMENDED ACTIONS:\n\n";
    echo "1. Regenerate missing HTML files:\n";
    echo "   php php/api/generate_html.php\n\n";
    
    echo "2. Or deactivate/unpublish missing items:\n";
    foreach ($issues as $issue) {
        if ($issue['type'] === 'blog') {
            echo "   UPDATE blogs SET status='draft' WHERE id={$issue['id']}; -- {$issue['title']}\n";
        } else {
            echo "   UPDATE {$issue['type']} SET is_active=0 WHERE id={$issue['id']}; -- {$issue['title']}\n";
        }
    }
    
    echo "\n3. After fixing, regenerate sitemap:\n";
    echo "   php php/generate-sitemap-cli.php\n\n";
    
    echo "4. Submit updated sitemap to Google Search Console\n";
} else {
    echo "✅ No issues found! All published items have corresponding HTML files.\n";
}

echo "\n";
