<?php
/**
 * Site Health Check for CSNExplore
 * Comprehensive health monitoring for SEO, performance, security, and AI search readiness
 * Run from command line: php php/site-health-check.php
 */

require_once __DIR__ . '/config.php';

class SiteHealthCheck {
    private $issues = [];
    private $warnings = [];
    private $passed = [];
    private $score = 0;
    
    public function __construct() {
        echo "🏥 Starting Comprehensive Site Health Check...\n\n";
    }
    
    public function runFullCheck() {
        $this->checkSEO();
        $this->checkPerformance();
        $this->checkSecurity();
        $this->checkAIReadiness();
        $this->checkAccessibility();
        $this->checkMobile();
        $this->checkContent();
        $this->checkTechnical();
        
        $this->calculateScore();
        $this->printReport();
    }
    
    private function checkSEO() {
        echo "🔍 Checking SEO Health...\n";
        
        // Check robots.txt
        $robotsPath = dirname(__DIR__) . '/robots.txt';
        if (file_exists($robotsPath)) {
            $content = file_get_contents($robotsPath);
            
            // Check for AI bots
            $aiBots = ['GPTBot', 'Claude-Web', 'PerplexityBot', 'Google-Extended'];
            $foundBots = 0;
            foreach ($aiBots as $bot) {
                if (stripos($content, $bot) !== false) {
                    $foundBots++;
                }
            }
            
            if ($foundBots >= 3) {
                $this->passed[] = "✅ AI search bots allowed in robots.txt ($foundBots/4)";
            } else {
                $this->warnings[] = "⚠️  Only $foundBots/4 AI bots found in robots.txt";
            }
            
            // Check for sitemaps
            if (stripos($content, 'Sitemap:') !== false) {
                $sitemapCount = substr_count(strtolower($content), 'sitemap:');
                $this->passed[] = "✅ $sitemapCount sitemaps referenced in robots.txt";
            } else {
                $this->issues[] = "❌ No sitemaps in robots.txt";
            }
        } else {
            $this->issues[] = "❌ robots.txt missing";
        }
        
        // Check AI metadata
        $aiMetaPath = dirname(__DIR__) . '/ai-metadata.json';
        if (file_exists($aiMetaPath)) {
            $this->passed[] = "✅ AI metadata file exists";
            
            $content = file_get_contents($aiMetaPath);
            $json = json_decode($content, true);
            if ($json && isset($json['@context'])) {
                $this->passed[] = "✅ AI metadata is valid JSON-LD";
            } else {
                $this->warnings[] = "⚠️  AI metadata JSON may be invalid";
            }
        } else {
            $this->warnings[] = "⚠️  AI metadata file missing";
        }
        
        // Check sitemap generator
        $sitemapPath = dirname(__DIR__) . '/php/api/generate_sitemap.php';
        if (file_exists($sitemapPath)) {
            $this->passed[] = "✅ Sitemap generator exists";
        } else {
            $this->issues[] = "❌ Sitemap generator missing";
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
                $this->warnings[] = "⚠️  Compression not detected";
            }
            
            if (stripos($content, 'mod_expires') !== false) {
                $this->passed[] = "✅ Browser caching enabled";
            } else {
                $this->warnings[] = "⚠️  Browser caching not detected";
            }
            
            if (stripos($content, 'mod_headers') !== false) {
                $this->passed[] = "✅ Custom headers configured";
            } else {
                $this->warnings[] = "⚠️  Custom headers not detected";
            }
        }
        
        // Check for image optimization
        $imageDir = dirname(__DIR__) . '/images';
        if (is_dir($imageDir)) {
            $webpImages = glob($imageDir . '/*.webp');
            $jpgImages = glob($imageDir . '/*.{jpg,jpeg}', GLOB_BRACE);
            
            if (count($webpImages) > 0) {
                $this->passed[] = "✅ WebP images found (" . count($webpImages) . ")";
            } else {
                $this->warnings[] = "⚠️  No WebP images found";
            }
            
            if (count($jpgImages) > count($webpImages)) {
                $this->warnings[] = "⚠️  More JPG than WebP images - consider converting";
            }
        }
    }
    
    private function checkSecurity() {
        echo "🔒 Checking Security...\n";
        
        // Check .htaccess for security headers
        $htaccessPath = dirname(__DIR__) . '/.htaccess';
        if (file_exists($htaccessPath)) {
            $content = file_get_contents($htaccessPath);
            
            $securityHeaders = [
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
                'X-XSS-Protection' => '1',
                'Strict-Transport-Security' => 'HSTS'
            ];
            
            foreach ($securityHeaders as $header => $desc) {
                if (stripos($content, $header) !== false) {
                    $this->passed[] = "✅ $desc header configured";
                } else {
                    $this->warnings[] = "⚠️  $desc header missing";
                }
            }
        }
        
        // Check for sensitive files
        $sensitiveFiles = ['.env', '.git', 'composer.json', 'package.json'];
        foreach ($sensitiveFiles as $file) {
            $path = dirname(__DIR__) . '/' . $file;
            if (file_exists($path)) {
                // Check if blocked in .htaccess
                if (file_exists($htaccessPath)) {
                    $htContent = file_get_contents($htaccessPath);
                    if (stripos($htContent, $file) !== false) {
                        $this->passed[] = "✅ $file blocked in .htaccess";
                    } else {
                        $this->warnings[] = "⚠️  $file exists but not blocked";
                    }
                }
            }
        }
    }
    
    private function checkAIReadiness() {
        echo "🤖 Checking AI Search Readiness...\n";
        
        // Check robots.txt for AI bots
        $robotsPath = dirname(__DIR__) . '/robots.txt';
        if (file_exists($robotsPath)) {
            $content = file_get_contents($robotsPath);
            
            $aiBots = [
                'GPTBot' => 'ChatGPT',
                'Claude-Web' => 'Claude',
                'PerplexityBot' => 'Perplexity',
                'Google-Extended' => 'Gemini',
                'CCBot' => 'Common Crawl'
            ];
            
            foreach ($aiBots as $bot => $name) {
                if (stripos($content, $bot) !== false) {
                    $this->passed[] = "✅ $name bot allowed";
                } else {
                    $this->warnings[] = "⚠️  $name bot not explicitly allowed";
                }
            }
        }
        
        // Check for structured data
        $indexPath = dirname(__DIR__) . '/index.php';
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            
            $schemas = [
                'Organization' => '@type":"Organization',
                'LocalBusiness' => '@type":"LocalBusiness',
                'WebSite' => '@type":"WebSite'
            ];
            
            foreach ($schemas as $name => $pattern) {
                if (stripos($content, $pattern) !== false) {
                    $this->passed[] = "✅ $name schema present";
                } else {
                    $this->warnings[] = "⚠️  $name schema not found";
                }
            }
        }
    }
    
    private function checkAccessibility() {
        echo "♿ Checking Accessibility...\n";
        
        // Check for semantic HTML
        $indexPath = dirname(__DIR__) . '/index.php';
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            
            $semanticTags = ['<header', '<nav', '<main', '<article', '<section', '<footer'];
            $foundTags = 0;
            
            foreach ($semanticTags as $tag) {
                if (stripos($content, $tag) !== false) {
                    $foundTags++;
                }
            }
            
            if ($foundTags >= 4) {
                $this->passed[] = "✅ Semantic HTML5 tags used ($foundTags/6)";
            } else {
                $this->warnings[] = "⚠️  Limited semantic HTML ($foundTags/6)";
            }
            
            // Check for alt text
            if (preg_match_all('/<img[^>]+>/i', $content, $matches)) {
                $totalImages = count($matches[0]);
                $imagesWithAlt = 0;
                
                foreach ($matches[0] as $img) {
                    if (stripos($img, 'alt=') !== false) {
                        $imagesWithAlt++;
                    }
                }
                
                if ($imagesWithAlt === $totalImages) {
                    $this->passed[] = "✅ All images have alt text ($imagesWithAlt/$totalImages)";
                } else {
                    $this->warnings[] = "⚠️  Some images missing alt text ($imagesWithAlt/$totalImages)";
                }
            }
        }
    }
    
    private function checkMobile() {
        echo "📱 Checking Mobile Optimization...\n";
        
        $indexPath = dirname(__DIR__) . '/index.php';
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            
            // Check viewport
            if (stripos($content, 'viewport') !== false) {
                $this->passed[] = "✅ Viewport meta tag present";
            } else {
                $this->issues[] = "❌ Viewport meta tag missing";
            }
            
            // Check for responsive framework
            if (stripos($content, 'tailwind') !== false || stripos($content, 'bootstrap') !== false) {
                $this->passed[] = "✅ Responsive CSS framework detected";
            } else {
                $this->warnings[] = "⚠️  No responsive framework detected";
            }
        }
    }
    
    private function checkContent() {
        echo "📝 Checking Content Quality...\n";
        
        try {
            $db = getDB();
            
            // Check blog posts
            $blogCount = $db->fetchOne("SELECT COUNT(*) as count FROM blogs WHERE status='published'");
            if ($blogCount && $blogCount['count'] > 0) {
                $this->passed[] = "✅ {$blogCount['count']} published blog posts";
            } else {
                $this->warnings[] = "⚠️  No published blog posts";
            }
            
            // Check listings
            $listingTypes = ['stays', 'cars', 'bikes', 'attractions', 'restaurants', 'buses'];
            foreach ($listingTypes as $type) {
                $count = $db->fetchOne("SELECT COUNT(*) as count FROM $type WHERE is_active=1");
                if ($count && $count['count'] > 0) {
                    $this->passed[] = "✅ {$count['count']} active $type listings";
                } else {
                    $this->warnings[] = "⚠️  No active $type listings";
                }
            }
        } catch (Exception $e) {
            $this->warnings[] = "⚠️  Could not check database content";
        }
    }
    
    private function checkTechnical() {
        echo "🔧 Checking Technical Issues...\n";
        
        // Check for common files
        $requiredFiles = [
            'index.php' => 'Homepage',
            'header.php' => 'Header template',
            'footer.php' => 'Footer template',
            '.htaccess' => 'Apache config',
            'robots.txt' => 'Robots file'
        ];
        
        foreach ($requiredFiles as $file => $desc) {
            $path = dirname(__DIR__) . '/' . $file;
            if (file_exists($path)) {
                $this->passed[] = "✅ $desc exists";
            } else {
                $this->issues[] = "❌ $desc missing";
            }
        }
        
        // Check PHP version
        $phpVersion = phpversion();
        if (version_compare($phpVersion, '7.4', '>=')) {
            $this->passed[] = "✅ PHP version $phpVersion (good)";
        } else {
            $this->warnings[] = "⚠️  PHP version $phpVersion (consider upgrading)";
        }
    }
    
    private function calculateScore() {
        $total = count($this->passed) + count($this->warnings) + count($this->issues);
        if ($total > 0) {
            $this->score = round((count($this->passed) / $total) * 100);
        }
    }
    
    private function printReport() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "🏥 SITE HEALTH REPORT\n";
        echo str_repeat("=", 70) . "\n\n";
        
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
        
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "🎯 OVERALL HEALTH SCORE: {$this->score}%\n";
        
        if ($this->score >= 90) {
            echo "🌟 Excellent! Your site is in great health.\n";
        } elseif ($this->score >= 75) {
            echo "👍 Good! Address warnings to improve further.\n";
        } elseif ($this->score >= 60) {
            echo "⚠️  Fair. Several issues need attention.\n";
        } else {
            echo "❌ Poor. Immediate action required.\n";
        }
        
        echo str_repeat("=", 70) . "\n\n";
        
        // Recommendations
        echo "📋 RECOMMENDATIONS:\n\n";
        
        if (count($this->issues) > 0) {
            echo "1. Fix critical issues first (marked with ❌)\n";
        }
        if (count($this->warnings) > 0) {
            echo "2. Address warnings to improve score (marked with ⚠️)\n";
        }
        echo "3. Run this check weekly to maintain site health\n";
        echo "4. Monitor Google Search Console for crawl errors\n";
        echo "5. Test in AI search engines (ChatGPT, Claude, Perplexity)\n\n";
    }
}

// Run health check if called from command line
if (php_sapi_name() === 'cli') {
    $healthCheck = new SiteHealthCheck();
    $healthCheck->runFullCheck();
}
