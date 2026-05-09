<?php
/**
 * SEO Audit Script for CSNExplore
 * Checks all pages for SEO compliance
 * Run from command line: php php/seo-audit.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/seo-optimizer.php';

class SEOAudit {
    private $db;
    private $issues = [];
    private $warnings = [];
    private $passed = [];
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function runFullAudit() {
        echo "🔍 Starting SEO Audit for CSNExplore...\n\n";
        
        $this->checkMetaTags();
        $this->checkImages();
        $this->checkStructuredData();
        $this->checkPerformance();
        $this->checkMobileOptimization();
        $this->checkSitemap();
        $this->checkRobotsTxt();
        $this->checkSSL();
        $this->checkCanonicals();
        $this->checkInternalLinks();
        
        $this->printReport();
    }
    
    private function checkMetaTags() {
        echo "📋 Checking Meta Tags...\n";
        
        $pages = [
            'index.php' => 'Home',
            'about.php' => 'About',
            'contact.php' => 'Contact',
            'blogs.php' => 'Blogs'
        ];
        
        foreach ($pages as $file => $name) {
            $path = dirname(__DIR__) . '/' . $file;
            if (!file_exists($path)) {
                $this->issues[] = "❌ Missing file: $file";
                continue;
            }
            
            $content = file_get_contents($path);
            
            // Check for title tag
            if (!preg_match('/<title>(.+?)<\/title>/i', $content, $matches)) {
                $this->issues[] = "❌ $name: Missing <title> tag";
            } else {
                $title = $matches[1];
                if (strlen($title) < 30 || strlen($title) > 60) {
                    $this->warnings[] = "⚠️  $name: Title length should be 30-60 chars (current: " . strlen($title) . ")";
                }
                if (!stripos($title, 'Chhatrapati Sambhajinagar') && !stripos($title, 'Aurangabad')) {
                    $this->warnings[] = "⚠️  $name: Title missing city name keywords";
                }
                $this->passed[] = "✅ $name: Has valid <title> tag";
            }
            
            // Check for meta description
            if (!preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.+?)["\']/i', $content, $matches)) {
                $this->issues[] = "❌ $name: Missing meta description";
            } else {
                $desc = $matches[1];
                if (strlen($desc) < 120 || strlen($desc) > 160) {
                    $this->warnings[] = "⚠️  $name: Description should be 120-160 chars (current: " . strlen($desc) . ")";
                }
                $this->passed[] = "✅ $name: Has meta description";
            }
            
            // Check for canonical
            if (!preg_match('/<link\s+rel=["\']canonical["\']/i', $content)) {
                $this->issues[] = "❌ $name: Missing canonical URL";
            } else {
                $this->passed[] = "✅ $name: Has canonical URL";
            }
            
            // Check for Open Graph tags
            if (!preg_match('/<meta\s+property=["\']og:title["\']/i', $content)) {
                $this->warnings[] = "⚠️  $name: Missing Open Graph tags";
            } else {
                $this->passed[] = "✅ $name: Has Open Graph tags";
            }
        }
    }
    
    private function checkImages() {
        echo "🖼️  Checking Images...\n";
        
        try {
            // Check stays images
            $stays = $this->db->fetchAll("SELECT id, name, image FROM stays WHERE is_active = 1 LIMIT 10");
            foreach ($stays as $stay) {
                if (empty($stay['image'])) {
                    $this->warnings[] = "⚠️  Stay '{$stay['name']}' missing image";
                } else {
                    $this->passed[] = "✅ Stay '{$stay['name']}' has image";
                }
            }
            
            // Check for WebP format
            $imageDir = dirname(__DIR__) . '/images';
            if (is_dir($imageDir)) {
                $images = glob($imageDir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
                if (count($images) > 0) {
                    $this->warnings[] = "⚠️  Found " . count($images) . " non-WebP images. Consider converting to WebP.";
                }
            }
            
        } catch (Exception $e) {
            $this->issues[] = "❌ Error checking images: " . $e->getMessage();
        }
    }
    
    private function checkStructuredData() {
        echo "📊 Checking Structured Data...\n";
        
        $indexPath = dirname(__DIR__) . '/index.php';
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            
            // Check for Organization schema
            if (stripos($content, '"@type":"Organization"') !== false || 
                stripos($content, '"@type": "Organization"') !== false) {
                $this->passed[] = "✅ Has Organization schema";
            } else {
                $this->issues[] = "❌ Missing Organization schema";
            }
            
            // Check for LocalBusiness schema
            if (stripos($content, '"@type":"LocalBusiness"') !== false || 
                stripos($content, '"@type": "LocalBusiness"') !== false) {
                $this->passed[] = "✅ Has LocalBusiness schema";
            } else {
                $this->warnings[] = "⚠️  Missing LocalBusiness schema";
            }
            
            // Check for WebSite schema
            if (stripos($content, '"@type":"WebSite"') !== false || 
                stripos($content, '"@type": "WebSite"') !== false) {
                $this->passed[] = "✅ Has WebSite schema";
            } else {
                $this->warnings[] = "⚠️  Missing WebSite schema";
            }
        }
    }
    
    private function checkPerformance() {
        echo "⚡ Checking Performance...\n";
        
        // Check .htaccess for compression
        $htaccessPath = dirname(__DIR__) . '/.htaccess';
        if (file_exists($htaccessPath)) {
            $content = file_get_contents($htaccessPath);
            
            if (stripos($content, 'mod_deflate') !== false || stripos($content, 'mod_brotli') !== false) {
                $this->passed[] = "✅ Compression enabled (Gzip/Brotli)";
            } else {
                $this->issues[] = "❌ Compression not enabled";
            }
            
            if (stripos($content, 'mod_expires') !== false) {
                $this->passed[] = "✅ Browser caching enabled";
            } else {
                $this->issues[] = "❌ Browser caching not enabled";
            }
        }
    }
    
    private function checkMobileOptimization() {
        echo "📱 Checking Mobile Optimization...\n";
        
        $indexPath = dirname(__DIR__) . '/index.php';
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            
            if (stripos($content, 'viewport') !== false) {
                $this->passed[] = "✅ Has viewport meta tag";
            } else {
                $this->issues[] = "❌ Missing viewport meta tag";
            }
            
            if (stripos($content, 'mobile-responsive') !== false || 
                stripos($content, 'responsive') !== false ||
                stripos($content, 'tailwind') !== false) {
                $this->passed[] = "✅ Mobile-responsive design detected";
            } else {
                $this->warnings[] = "⚠️  Mobile responsiveness unclear";
            }
        }
    }
    
    private function checkSitemap() {
        echo "🗺️  Checking Sitemap...\n";
        
        $sitemapPath = dirname(__DIR__) . '/php/api/generate_sitemap.php';
        if (file_exists($sitemapPath)) {
            $this->passed[] = "✅ Sitemap generator exists";
            
            // Check if sitemap is accessible
            $content = file_get_contents($sitemapPath);
            if (stripos($content, 'sitemap-stays') !== false &&
                stripos($content, 'sitemap-cars') !== false &&
                stripos($content, 'sitemap-blogs') !== false) {
                $this->passed[] = "✅ Multiple sitemaps configured";
            }
        } else {
            $this->issues[] = "❌ Sitemap generator missing";
        }
    }
    
    private function checkRobotsTxt() {
        echo "🤖 Checking robots.txt...\n";
        
        $robotsPath = dirname(__DIR__) . '/robots.txt';
        if (file_exists($robotsPath)) {
            $content = file_get_contents($robotsPath);
            
            if (stripos($content, 'Sitemap:') !== false) {
                $this->passed[] = "✅ robots.txt has sitemap reference";
            } else {
                $this->warnings[] = "⚠️  robots.txt missing sitemap reference";
            }
            
            if (stripos($content, 'Disallow: /admin') !== false) {
                $this->passed[] = "✅ Admin area blocked in robots.txt";
            } else {
                $this->warnings[] = "⚠️  Admin area not blocked";
            }
        } else {
            $this->issues[] = "❌ robots.txt missing";
        }
    }
    
    private function checkSSL() {
        echo "🔒 Checking SSL/HTTPS...\n";
        
        $htaccessPath = dirname(__DIR__) . '/.htaccess';
        if (file_exists($htaccessPath)) {
            $content = file_get_contents($htaccessPath);
            
            if (stripos($content, 'HTTPS') !== false || stripos($content, 'https://') !== false) {
                $this->passed[] = "✅ HTTPS redirect configured";
            } else {
                $this->warnings[] = "⚠️  HTTPS redirect not found in .htaccess";
            }
            
            if (stripos($content, 'Strict-Transport-Security') !== false) {
                $this->passed[] = "✅ HSTS header configured";
            } else {
                $this->warnings[] = "⚠️  HSTS header not configured";
            }
        }
    }
    
    private function checkCanonicals() {
        echo "🔗 Checking Canonical URLs...\n";
        
        try {
            // Check a few listing pages
            $stays = $this->db->fetchAll("SELECT id, name FROM stays WHERE is_active = 1 LIMIT 5");
            foreach ($stays as $stay) {
                $slug = generateSlug('stays', $stay['id'], $stay['name']);
                $file = dirname(__DIR__) . '/listing-detail/' . $slug . '.html';
                
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    if (stripos($content, 'rel="canonical"') !== false) {
                        $this->passed[] = "✅ Stay '{$stay['name']}' has canonical URL";
                    } else {
                        $this->warnings[] = "⚠️  Stay '{$stay['name']}' missing canonical URL";
                    }
                }
            }
        } catch (Exception $e) {
            $this->warnings[] = "⚠️  Could not check canonicals: " . $e->getMessage();
        }
    }
    
    private function checkInternalLinks() {
        echo "🔗 Checking Internal Links...\n";
        
        $headerPath = dirname(__DIR__) . '/header.php';
        if (file_exists($headerPath)) {
            $content = file_get_contents($headerPath);
            
            // Count internal links
            preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $content, $matches);
            $internalLinks = 0;
            foreach ($matches[1] as $link) {
                if (strpos($link, 'http') === false || strpos($link, 'csnexplore.com') !== false) {
                    $internalLinks++;
                }
            }
            
            if ($internalLinks > 5) {
                $this->passed[] = "✅ Good internal linking structure ($internalLinks links in header)";
            } else {
                $this->warnings[] = "⚠️  Limited internal links in header";
            }
        }
    }
    
    private function printReport() {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 SEO AUDIT REPORT\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "✅ PASSED (" . count($this->passed) . "):\n";
        foreach ($this->passed as $item) {
            echo "  $item\n";
        }
        
        echo "\n⚠️  WARNINGS (" . count($this->warnings) . "):\n";
        foreach ($this->warnings as $item) {
            echo "  $item\n";
        }
        
        echo "\n❌ ISSUES (" . count($this->issues) . "):\n";
        foreach ($this->issues as $item) {
            echo "  $item\n";
        }
        
        $total = count($this->passed) + count($this->warnings) + count($this->issues);
        $score = $total > 0 ? round((count($this->passed) / $total) * 100) : 0;
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🎯 SEO SCORE: $score%\n";
        
        if ($score >= 90) {
            echo "🌟 Excellent! Your SEO is in great shape.\n";
        } elseif ($score >= 70) {
            echo "👍 Good! Address warnings to improve further.\n";
        } elseif ($score >= 50) {
            echo "⚠️  Fair. Several issues need attention.\n";
        } else {
            echo "❌ Poor. Immediate action required.\n";
        }
        
        echo str_repeat("=", 60) . "\n\n";
    }
}

// Run audit if called from command line
if (php_sapi_name() === 'cli') {
    $audit = new SEOAudit();
    $audit->runFullAudit();
}
