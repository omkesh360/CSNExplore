<?php
/**
 * Sitemap Generator
 * Automatically generates XML sitemap for SEO
 */

class SitemapGenerator {
    private $baseUrl = 'https://csnexplore.com';
    private $outputFile;
    
    public function __construct($outputFile = null) {
        $this->outputFile = $outputFile ?? __DIR__ . '/../public/sitemap.xml';
    }
    
    /**
     * Generate complete sitemap
     */
    public function generate() {
        $urls = [];
        
        // Add static pages
        $urls = array_merge($urls, $this->getStaticPages());
        
        // Add listing pages
        $urls = array_merge($urls, $this->getListingPages());
        
        // Add blog pages
        $urls = array_merge($urls, $this->getBlogPages());
        
        // Generate XML
        $xml = $this->generateXML($urls);
        
        // Save to file
        file_put_contents($this->outputFile, $xml);
        
        return count($urls);
    }
    
    /**
     * Get static pages
     */
    private function getStaticPages() {
        $pages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/about.html', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/contact.html', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/stays.html', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/car-rentals.html', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/bike-rentals.html', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/restaurant.html', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/attraction.html', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/bus.html', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/blogs.html', 'priority' => '0.8', 'changefreq' => 'daily']
        ];
        
        return $pages;
    }
    
    /**
     * Get listing pages
     */
    private function getListingPages() {
        $pages = [];
        $publicDir = __DIR__ . '/../public';
        
        // Detail pages
        $patterns = [
            'stay-detail*.html',
            'car-rental-detail*.html',
            'bike-rental-detail*.html',
            'restaurant-detail*.html',
            'attraction-detail*.html'
        ];
        
        foreach ($patterns as $pattern) {
            $files = glob($publicDir . '/' . $pattern);
            
            foreach ($files as $file) {
                $filename = basename($file);
                $pages[] = [
                    'loc' => '/' . $filename,
                    'priority' => '0.7',
                    'changefreq' => 'weekly',
                    'lastmod' => date('Y-m-d', filemtime($file))
                ];
            }
        }
        
        return $pages;
    }
    
    /**
     * Get blog pages
     */
    private function getBlogPages() {
        $pages = [];
        $publicDir = __DIR__ . '/../public';
        
        $files = glob($publicDir . '/blog-*.html');
        
        foreach ($files as $file) {
            $filename = basename($file);
            $pages[] = [
                'loc' => '/' . $filename,
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => date('Y-m-d', filemtime($file))
            ];
        }
        
        return $pages;
    }
    
    /**
     * Generate XML from URLs
     */
    private function generateXML($urls) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $this->baseUrl . $url['loc'] . '</loc>' . "\n";
            
            if (isset($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            }
            
            if (isset($url['changefreq'])) {
                $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            }
            
            if (isset($url['priority'])) {
                $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            }
            
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}

// CLI usage
if (php_sapi_name() === 'cli') {
    echo "Generating sitemap...\n";
    $generator = new SitemapGenerator();
    $count = $generator->generate();
    echo "Sitemap generated with {$count} URLs\n";
}

// Web usage
if (isset($_GET['generate']) && $_GET['generate'] === 'sitemap') {
    $generator = new SitemapGenerator();
    $count = $generator->generate();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'urls' => $count,
        'message' => 'Sitemap generated successfully'
    ]);
}
