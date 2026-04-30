<?php
/**
 * Bug Condition Exploration Test for SEO Critical Issues
 * 
 * **Validates: Requirements 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 1.10, 1.11, 1.12, 1.13, 1.14, 1.15, 1.16, 1.17, 1.18, 1.19, 1.20, 1.21, 1.22, 1.23, 1.24, 1.25, 1.26, 1.27, 1.28, 1.29**
 * 
 * CRITICAL: This test is EXPECTED TO FAIL on unfixed code.
 * Failure confirms the bugs exist. This is the SUCCESS case for exploration tests.
 * 
 * This test encodes the expected behavior - it will validate the fix when it passes after implementation.
 * 
 * GOAL: Surface counterexamples that demonstrate the bugs exist in generated HTML files.
 * 
 * Test Strategy: Scoped PBT Approach - Test concrete failing cases from the 610 identified SEO issues
 */

require_once __DIR__ . '/../../../php/config.php';

// ══════════════════════════════════════════════════════════════════════════════
// Test Configuration
// ══════════════════════════════════════════════════════════════════════════════

$WORKSPACE_ROOT = dirname(__DIR__, 3);
$BLOGS_DIR = $WORKSPACE_ROOT . '/blogs';
$LISTING_DETAIL_DIR = $WORKSPACE_ROOT . '/listing-detail';

// ══════════════════════════════════════════════════════════════════════════════
// Test Results Storage
// ══════════════════════════════════════════════════════════════════════════════

$testResults = [
    'total_files_tested' => 0,
    'total_issues_found' => 0,
    'html_structure_issues' => [],
    'content_quality_issues' => [],
    'page_speed_issues' => [],
    'technical_seo_issues' => [],
    'sitemap_issues' => [],
];

// ══════════════════════════════════════════════════════════════════════════════
// Helper Functions
// ══════════════════════════════════════════════════════════════════════════════

function getHtmlFiles($directory) {
    if (!is_dir($directory)) {
        return [];
    }
    $files = glob($directory . '/*.html');
    return $files ?: [];
}

function analyzeHtmlStructure($filePath, $content) {
    $issues = [];
    
    // Check for content after </html> tag
    if (preg_match('/<\/html>\s*(.+)/s', $content, $matches)) {
        $afterHtml = trim($matches[1]);
        if (!empty($afterHtml) && strlen($afterHtml) > 10) {
            $issues[] = [
                'type' => 'content_after_html',
                'file' => basename($filePath),
                'detail' => 'Content found after </html> tag: ' . substr($afterHtml, 0, 100) . '...'
            ];
        }
    }
    
    // Check for duplicate closing tags
    $bodyClosingCount = substr_count($content, '</body>');
    $htmlClosingCount = substr_count($content, '</html>');
    
    if ($bodyClosingCount > 1) {
        $issues[] = [
            'type' => 'duplicate_body_closing',
            'file' => basename($filePath),
            'detail' => "Found $bodyClosingCount </body> closing tags (expected 1)"
        ];
    }
    
    if ($htmlClosingCount > 1) {
        $issues[] = [
            'type' => 'duplicate_html_closing',
            'file' => basename($filePath),
            'detail' => "Found $htmlClosingCount </html> closing tags (expected 1)"
        ];
    }
    
    // Check canonical URL matches
    if (preg_match('/<link rel="canonical" href="([^"]+)"/', $content, $canonicalMatch)) {
        $canonical = $canonicalMatch[1];
        $expectedPath = str_replace('.html', '', basename($filePath));
        if (strpos($canonical, $expectedPath) === false) {
            $issues[] = [
                'type' => 'canonical_mismatch',
                'file' => basename($filePath),
                'detail' => "Canonical URL '$canonical' does not match file path"
            ];
        }
    }
    
    return $issues;
}

function analyzeContentQuality($filePath, $content) {
    $issues = [];
    
    // Check title length
    if (preg_match('/<title>([^<]+)<\/title>/', $content, $titleMatch)) {
        $title = $titleMatch[1];
        $titleLength = strlen($title);
        
        if ($titleLength < 50) {
            $issues[] = [
                'type' => 'title_too_short',
                'file' => basename($filePath),
                'detail' => "Title length: $titleLength characters (minimum 50 recommended). Title: '$title'"
            ];
        } elseif ($titleLength > 60) {
            $issues[] = [
                'type' => 'title_too_long',
                'file' => basename($filePath),
                'detail' => "Title length: $titleLength characters (maximum 60 recommended). Title: '$title'"
            ];
        }
    }
    
    // Check H1 tags for lowercase start
    if (preg_match_all('/<h1[^>]*>([^<]+)<\/h1>/', $content, $h1Matches)) {
        foreach ($h1Matches[1] as $h1Text) {
            $trimmed = trim(strip_tags($h1Text));
            if (!empty($trimmed) && ctype_lower($trimmed[0])) {
                $issues[] = [
                    'type' => 'h1_lowercase_start',
                    'file' => basename($filePath),
                    'detail' => "H1 starts with lowercase: '$trimmed'"
                ];
            }
        }
    }
    
    // Check H2 tags for lowercase start
    if (preg_match_all('/<h2[^>]*>([^<]+)<\/h2>/', $content, $h2Matches)) {
        foreach ($h2Matches[1] as $h2Text) {
            $trimmed = trim(strip_tags($h2Text));
            if (!empty($trimmed) && ctype_lower($trimmed[0])) {
                $issues[] = [
                    'type' => 'h2_lowercase_start',
                    'file' => basename($filePath),
                    'detail' => "H2 starts with lowercase: '$trimmed'"
                ];
            }
        }
    }
    
    // Check for HTML tags nested in H1
    if (preg_match_all('/<h1[^>]*>.*?<[^\/].*?<\/h1>/s', $content, $h1NestedMatches)) {
        foreach ($h1NestedMatches[0] as $h1) {
            $issues[] = [
                'type' => 'h1_nested_html',
                'file' => basename($filePath),
                'detail' => "H1 contains nested HTML tags: " . substr($h1, 0, 100)
            ];
        }
    }
    
    // Check for HTML tags nested in H2
    if (preg_match_all('/<h2[^>]*>.*?<[^\/].*?<\/h2>/s', $content, $h2NestedMatches)) {
        foreach ($h2NestedMatches[0] as $h2) {
            $issues[] = [
                'type' => 'h2_nested_html',
                'file' => basename($filePath),
                'detail' => "H2 contains nested HTML tags: " . substr($h2, 0, 100)
            ];
        }
    }
    
    // Check alt tags for one-word descriptions
    if (preg_match_all('/<img[^>]+alt="([^"]*)"/', $content, $altMatches)) {
        foreach ($altMatches[1] as $alt) {
            $wordCount = str_word_count($alt);
            if ($wordCount === 1) {
                $issues[] = [
                    'type' => 'one_word_alt_tag',
                    'file' => basename($filePath),
                    'detail' => "One-word alt tag: '$alt'"
                ];
            }
        }
    }
    
    // Check anchor text for one-word or empty
    if (preg_match_all('/<a[^>]*>([^<]*)<\/a>/', $content, $anchorMatches)) {
        foreach ($anchorMatches[1] as $anchorText) {
            $trimmed = trim(strip_tags($anchorText));
            if (empty($trimmed)) {
                $issues[] = [
                    'type' => 'empty_anchor_text',
                    'file' => basename($filePath),
                    'detail' => "Empty anchor text found"
                ];
            } elseif (str_word_count($trimmed) === 1) {
                $issues[] = [
                    'type' => 'one_word_anchor_text',
                    'file' => basename($filePath),
                    'detail' => "One-word anchor text: '$trimmed'"
                ];
            }
        }
    }
    
    // Check text-to-code ratio
    $textContent = strip_tags($content);
    $textLength = strlen(trim($textContent));
    $totalLength = strlen($content);
    $ratio = $totalLength > 0 ? ($textLength / $totalLength) * 100 : 0;
    
    if ($ratio < 10) {
        $issues[] = [
            'type' => 'low_text_to_code_ratio',
            'file' => basename($filePath),
            'detail' => sprintf("Text-to-code ratio: %.2f%% (minimum 10%% recommended)", $ratio)
        ];
    }
    
    return $issues;
}

function analyzePageSpeed($filePath, $content) {
    $issues = [];
    
    // Check for images without width/height attributes
    if (preg_match_all('/<img[^>]+>/', $content, $imgMatches)) {
        foreach ($imgMatches[0] as $imgTag) {
            $hasWidth = preg_match('/width=/', $imgTag);
            $hasHeight = preg_match('/height=/', $imgTag);
            
            if (!$hasWidth || !$hasHeight) {
                $issues[] = [
                    'type' => 'image_missing_dimensions',
                    'file' => basename($filePath),
                    'detail' => "Image missing width/height: " . substr($imgTag, 0, 100)
                ];
            }
        }
    }
    
    // Check for images without lazy loading
    if (preg_match_all('/<img[^>]+>/', $content, $imgMatches)) {
        foreach ($imgMatches[0] as $imgTag) {
            $hasLazy = preg_match('/loading=["\']lazy["\']/', $imgTag);
            
            if (!$hasLazy) {
                $issues[] = [
                    'type' => 'image_missing_lazy_loading',
                    'file' => basename($filePath),
                    'detail' => "Image missing lazy loading: " . substr($imgTag, 0, 100)
                ];
            }
        }
    }
    
    // Check for WebP format support
    $hasWebP = preg_match('/<picture>|\.webp/', $content);
    if (!$hasWebP) {
        $issues[] = [
            'type' => 'no_webp_format',
            'file' => basename($filePath),
            'detail' => "No WebP image format detected (legacy JPEG/PNG only)"
        ];
    }
    
    // Check for excessive HTML comments
    if (preg_match_all('/<!--.*?-->/s', $content, $commentMatches)) {
        $totalCommentLength = 0;
        foreach ($commentMatches[0] as $comment) {
            $totalCommentLength += strlen($comment);
        }
        
        if ($totalCommentLength > 1000) {
            $issues[] = [
                'type' => 'excessive_html_comments',
                'file' => basename($filePath),
                'detail' => "Excessive HTML comments: $totalCommentLength characters (>1000)"
            ];
        }
    }
    
    return $issues;
}

function analyzeTechnicalSEO($filePath, $content) {
    $issues = [];
    
    // Check for GTM code
    $hasGTM = preg_match('/googletagmanager\.com\/gtm\.js|GTM-[A-Z0-9]+/', $content);
    if (!$hasGTM) {
        $issues[] = [
            'type' => 'missing_gtm',
            'file' => basename($filePath),
            'detail' => "Google Tag Manager code not found"
        ];
    }
    
    // Check for internal links (basic check - would need full validation in real scenario)
    if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/', $content, $linkMatches)) {
        foreach ($linkMatches[1] as $href) {
            // Check for whitespace in URLs
            if (preg_match('/\s/', $href)) {
                $issues[] = [
                    'type' => 'url_with_whitespace',
                    'file' => basename($filePath),
                    'detail' => "URL contains whitespace: '$href'"
                ];
            }
        }
    }
    
    return $issues;
}

function analyzeSitemap() {
    global $WORKSPACE_ROOT;
    $issues = [];
    
    // Check if sitemap exists
    $sitemapPath = $WORKSPACE_ROOT . '/sitemap.xml';
    if (!file_exists($sitemapPath)) {
        $issues[] = [
            'type' => 'sitemap_missing',
            'file' => 'sitemap.xml',
            'detail' => "Sitemap file not found"
        ];
        return $issues;
    }
    
    // Parse sitemap and check URLs
    $sitemapContent = file_get_contents($sitemapPath);
    if (preg_match_all('/<loc>([^<]+)<\/loc>/', $sitemapContent, $urlMatches)) {
        foreach ($urlMatches[1] as $url) {
            // Extract path from URL
            $path = parse_url($url, PHP_URL_PATH);
            if ($path) {
                $filePath = $WORKSPACE_ROOT . $path;
                
                // Check if file exists
                if (!file_exists($filePath)) {
                    $issues[] = [
                        'type' => 'sitemap_404_url',
                        'file' => 'sitemap.xml',
                        'detail' => "Sitemap includes non-existent URL: $url"
                    ];
                }
            }
        }
    }
    
    return $issues;
}

// ══════════════════════════════════════════════════════════════════════════════
// Main Test Execution
// ══════════════════════════════════════════════════════════════════════════════

echo "\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "  SEO Critical Issues - Bug Condition Exploration Test\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "\n";
echo "CRITICAL: This test is EXPECTED TO FAIL on unfixed code.\n";
echo "Failure confirms the bugs exist. This is the SUCCESS case for exploration.\n";
echo "\n";
echo "Testing generated HTML files for SEO issues...\n";
echo "\n";

// Collect all HTML files
$blogFiles = getHtmlFiles($BLOGS_DIR);
$listingFiles = getHtmlFiles($LISTING_DETAIL_DIR);
$allFiles = array_merge($blogFiles, $listingFiles);

echo "Found " . count($blogFiles) . " blog files\n";
echo "Found " . count($listingFiles) . " listing detail files\n";
echo "Total files to test: " . count($allFiles) . "\n";
echo "\n";

// Test each file
foreach ($allFiles as $filePath) {
    $testResults['total_files_tested']++;
    $content = file_get_contents($filePath);
    
    // Run all analysis functions
    $htmlStructureIssues = analyzeHtmlStructure($filePath, $content);
    $contentQualityIssues = analyzeContentQuality($filePath, $content);
    $pageSpeedIssues = analyzePageSpeed($filePath, $content);
    $technicalSEOIssues = analyzeTechnicalSEO($filePath, $content);
    
    // Collect issues
    $testResults['html_structure_issues'] = array_merge($testResults['html_structure_issues'], $htmlStructureIssues);
    $testResults['content_quality_issues'] = array_merge($testResults['content_quality_issues'], $contentQualityIssues);
    $testResults['page_speed_issues'] = array_merge($testResults['page_speed_issues'], $pageSpeedIssues);
    $testResults['technical_seo_issues'] = array_merge($testResults['technical_seo_issues'], $technicalSEOIssues);
    
    $testResults['total_issues_found'] += count($htmlStructureIssues) + count($contentQualityIssues) + count($pageSpeedIssues) + count($technicalSEOIssues);
}

// Analyze sitemap
$sitemapIssues = analyzeSitemap();
$testResults['sitemap_issues'] = $sitemapIssues;
$testResults['total_issues_found'] += count($sitemapIssues);

// ══════════════════════════════════════════════════════════════════════════════
// Report Results
// ══════════════════════════════════════════════════════════════════════════════

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "  Test Results Summary\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "\n";
echo "Total files tested: " . $testResults['total_files_tested'] . "\n";
echo "Total issues found: " . $testResults['total_issues_found'] . "\n";
echo "\n";

// Report by category
$categories = [
    'html_structure_issues' => 'HTML Structure Issues',
    'content_quality_issues' => 'Content Quality Issues',
    'page_speed_issues' => 'Page Speed Issues',
    'technical_seo_issues' => 'Technical SEO Issues',
    'sitemap_issues' => 'Sitemap Issues',
];

foreach ($categories as $key => $label) {
    $issues = $testResults[$key];
    $count = count($issues);
    
    echo "───────────────────────────────────────────────────────────────────────────\n";
    echo "$label: $count\n";
    echo "───────────────────────────────────────────────────────────────────────────\n";
    
    if ($count > 0) {
        // Group by type
        $byType = [];
        foreach ($issues as $issue) {
            $type = $issue['type'];
            if (!isset($byType[$type])) {
                $byType[$type] = [];
            }
            $byType[$type][] = $issue;
        }
        
        foreach ($byType as $type => $typeIssues) {
            echo "\n  " . str_replace('_', ' ', ucwords($type, '_')) . ": " . count($typeIssues) . " occurrences\n";
            
            // Show first 5 examples
            $examples = array_slice($typeIssues, 0, 5);
            foreach ($examples as $example) {
                echo "    - " . $example['file'] . ": " . $example['detail'] . "\n";
            }
            
            if (count($typeIssues) > 5) {
                echo "    ... and " . (count($typeIssues) - 5) . " more\n";
            }
        }
    }
    
    echo "\n";
}

// ══════════════════════════════════════════════════════════════════════════════
// Test Assertion
// ══════════════════════════════════════════════════════════════════════════════

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "  Test Assertion\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "\n";

// Property 1: Bug Condition - SEO Critical Issues Exist in Generated HTML
// This test MUST FAIL on unfixed code to confirm bugs exist
$testPassed = $testResults['total_issues_found'] === 0;

if ($testPassed) {
    echo "✓ TEST PASSED: No SEO issues found in generated HTML files.\n";
    echo "\n";
    echo "All generated HTML files meet SEO requirements:\n";
    echo "  - Valid HTML structure (no content after </html>, exactly one closing tag)\n";
    echo "  - SEO-optimized content (titles 50-60 chars, proper capitalization, descriptive alt/anchor text)\n";
    echo "  - Performance optimizations (image dimensions, lazy loading, WebP format)\n";
    echo "  - Technical SEO compliance (GTM code, valid URLs, no 4xx errors)\n";
    echo "\n";
    exit(0);
} else {
    echo "✗ TEST FAILED: Found " . $testResults['total_issues_found'] . " SEO issues in generated HTML files.\n";
    echo "\n";
    echo "EXPECTED OUTCOME: This test is EXPECTED TO FAIL on unfixed code.\n";
    echo "This failure confirms the bugs exist and provides counterexamples.\n";
    echo "\n";
    echo "Counterexamples documented above demonstrate:\n";
    echo "  - HTML structure defects (content after </html>, duplicate tags, canonical mismatches)\n";
    echo "  - Content quality issues (short titles, lowercase headings, poor alt/anchor text)\n";
    echo "  - Page speed issues (missing dimensions, no lazy loading, legacy formats)\n";
    echo "  - Technical SEO issues (missing GTM, invalid URLs)\n";
    echo "  - Sitemap issues (4xx errors in sitemap URLs)\n";
    echo "\n";
    echo "Next step: Implement the fix in generate_html.php and generate_sitemap.php\n";
    echo "After fix: Re-run this test - it should PASS (no issues found)\n";
    echo "\n";
    exit(1);
}
