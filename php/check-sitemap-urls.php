<?php
/**
 * check-sitemap-urls.php
 * Checks if all URLs in sitemap.xml actually exist as files
 * Run: php php/check-sitemap-urls.php
 */

$root = dirname(__DIR__);
$sitemapFile = $root . '/sitemap.xml';

if (!file_exists($sitemapFile)) {
    die("❌ sitemap.xml not found at: $sitemapFile\n");
}

echo "🔍 Checking URLs in sitemap.xml...\n\n";

$xml = simplexml_load_file($sitemapFile);
$missing = [];
$found = 0;
$total = 0;

// Check if it's a sitemap index
if ($xml->getName() === 'sitemapindex') {
    echo "📋 Found sitemap index. Checking sub-sitemaps...\n\n";
    
    foreach ($xml->sitemap as $sitemap) {
        $subSitemapUrl = (string)$sitemap->loc;
        $subSitemapFile = str_replace('https://csnexplore.com/', $root . '/', $subSitemapUrl);
        $subSitemapFile = str_replace('http://csnexplore.com/', $root . '/', $subSitemapFile);
        
        echo "  Checking: " . basename($subSitemapFile) . "\n";
        
        if (!file_exists($subSitemapFile)) {
            echo "    ⚠️  Sitemap file not found: $subSitemapFile\n";
            continue;
        }
        
        $subXml = simplexml_load_file($subSitemapFile);
        foreach ($subXml->url as $url) {
            $total++;
            $loc = (string)$url->loc;
            
            // Convert URL to file path
            $path = str_replace('https://csnexplore.com/', '', $loc);
            $path = str_replace('http://csnexplore.com/', '', $path);
            
            // Check different file types
            $checks = [
                $root . '/' . $path . '.html',
                $root . '/' . $path . '.php',
                $root . '/' . $path,
            ];
            
            $exists = false;
            foreach ($checks as $check) {
                if (file_exists($check)) {
                    $exists = true;
                    $found++;
                    break;
                }
            }
            
            if (!$exists) {
                $missing[] = $loc;
            }
        }
    }
} else {
    // Regular sitemap
    foreach ($xml->url as $url) {
        $total++;
        $loc = (string)$url->loc;
        
        // Convert URL to file path
        $path = str_replace('https://csnexplore.com/', '', $loc);
        $path = str_replace('http://csnexplore.com/', '', $path);
        
        // Check different file types
        $checks = [
            $root . '/' . $path . '.html',
            $root . '/' . $path . '.php',
            $root . '/' . $path,
        ];
        
        $exists = false;
        foreach ($checks as $check) {
            if (file_exists($check)) {
                $exists = true;
                $found++;
                break;
            }
        }
        
        if (!$exists) {
            $missing[] = $loc;
        }
    }
}

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 SITEMAP AUDIT SUMMARY\n";
echo str_repeat("=", 70) . "\n";
echo "Total URLs in sitemap: $total\n";
echo "✅ Found: $found\n";
echo "❌ Missing: " . count($missing) . "\n\n";

if (count($missing) > 0) {
    echo "🚨 MISSING FILES:\n\n";
    foreach ($missing as $url) {
        echo "  ❌ $url\n";
    }
    
    echo "\n💡 RECOMMENDED ACTIONS:\n\n";
    echo "1. Regenerate HTML files:\n";
    echo "   c:\\xampp\\php\\php.exe c:\\xampp\\htdocs\\CSNExplore\\php\\api\\generate_html.php\n\n";
    
    echo "2. Regenerate sitemap:\n";
    echo "   c:\\xampp\\php\\php.exe c:\\xampp\\htdocs\\CSNExplore\\php\\generate-sitemap-cli.php\n\n";
    
    echo "3. Submit updated sitemap to Google Search Console\n";
} else {
    echo "✅ All URLs in sitemap have corresponding files!\n";
}

echo "\n";
