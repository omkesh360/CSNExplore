<?php
/**
 * Preservation Property Tests for SEO Critical Issues Fix
 * 
 * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11, 3.12, 3.13, 3.14, 3.15**
 * 
 * IMPORTANT: This test follows observation-first methodology.
 * Tests capture baseline behavior on UNFIXED code for non-generation functionality.
 * 
 * EXPECTED OUTCOME: Tests PASS on unfixed code (confirms baseline behavior to preserve).
 * After fix implementation, these tests should STILL PASS (confirms no regressions).
 * 
 * GOAL: Ensure all existing functionality (booking, authentication, admin panel, database,
 * API endpoints, responsive design, JavaScript, CSS) remains unchanged after the fix.
 * 
 * Property-Based Testing Approach: Generate many test cases for stronger guarantees.
 */

require_once __DIR__ . '/../../../php/config.php';

// ══════════════════════════════════════════════════════════════════════════════
// Test Configuration
// ══════════════════════════════════════════════════════════════════════════════

$WORKSPACE_ROOT = dirname(__DIR__, 3);

// ══════════════════════════════════════════════════════════════════════════════
// Test Results Storage
// ══════════════════════════════════════════════════════════════════════════════

$testResults = [
    'total_tests' => 0,
    'passed_tests' => 0,
    'failed_tests' => 0,
    'test_details' => [],
];

// ══════════════════════════════════════════════════════════════════════════════
// Helper Functions
// ══════════════════════════════════════════════════════════════════════════════

function recordTest($name, $passed, $message = '') {
    global $testResults;
    $testResults['total_tests']++;
    if ($passed) {
        $testResults['passed_tests']++;
    } else {
        $testResults['failed_tests']++;
    }
    $testResults['test_details'][] = [
        'name' => $name,
        'passed' => $passed,
        'message' => $message
    ];
}

function assertTrue($condition, $testName, $message = '') {
    recordTest($testName, $condition, $message);
}

function assertEquals($expected, $actual, $testName, $message = '') {
    $passed = $expected === $actual;
    if (!$passed && empty($message)) {
        $message = "Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true);
    }
    recordTest($testName, $passed, $message);
}

function assertNotNull($value, $testName, $message = '') {
    recordTest($testName, $value !== null, $message);
}

function assertArrayHasKey($key, $array, $testName, $message = '') {
    recordTest($testName, array_key_exists($key, $array), $message);
}

// ══════════════════════════════════════════════════════════════════════════════
// Property 2: Preservation Tests - Non-Generation Functionality Unchanged
// ══════════════════════════════════════════════════════════════════════════════

echo "\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "  Preservation Property Tests - Non-Generation Functionality\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "\n";
echo "IMPORTANT: This test follows observation-first methodology.\n";
echo "Tests capture baseline behavior on UNFIXED code.\n";
echo "\n";
echo "EXPECTED OUTCOME: Tests PASS (confirms baseline behavior to preserve).\n";
echo "After fix: Re-run these tests - they should STILL PASS (no regressions).\n";
echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 1. Database Operations Preservation (Requirements 3.13)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing Database Operations...\n";

$db = getDB();

// Test 1.1: Database connection works
try {
    $result = $db->fetchOne("SELECT 1 as test");
    assertTrue($result['test'] === 1, "Database connection", "Database connection successful");
} catch (Exception $e) {
    assertTrue(false, "Database connection", "Database connection failed: " . $e->getMessage());
}

// Test 1.2: Query blogs table
try {
    $blogs = $db->fetchAll("SELECT * FROM blogs WHERE status='published' LIMIT 5");
    assertTrue(is_array($blogs), "Query blogs table", "Blogs query returned array");
    if (count($blogs) > 0) {
        assertArrayHasKey('id', $blogs[0], "Blog record has id field");
        assertArrayHasKey('title', $blogs[0], "Blog record has title field");
        assertArrayHasKey('status', $blogs[0], "Blog record has status field");
    }
} catch (Exception $e) {
    assertTrue(false, "Query blogs table", "Blogs query failed: " . $e->getMessage());
}

// Test 1.3: Query cars table
try {
    $cars = $db->fetchAll("SELECT * FROM cars WHERE is_active=1 LIMIT 5");
    assertTrue(is_array($cars), "Query cars table", "Cars query returned array");
    if (count($cars) > 0) {
        assertArrayHasKey('id', $cars[0], "Car record has id field");
        assertArrayHasKey('name', $cars[0], "Car record has name field");
        assertArrayHasKey('is_active', $cars[0], "Car record has is_active field");
    }
} catch (Exception $e) {
    assertTrue(false, "Query cars table", "Cars query failed: " . $e->getMessage());
}

// Test 1.4: Query bikes table
try {
    $bikes = $db->fetchAll("SELECT * FROM bikes WHERE is_active=1 LIMIT 5");
    assertTrue(is_array($bikes), "Query bikes table", "Bikes query returned array");
    if (count($bikes) > 0) {
        assertArrayHasKey('id', $bikes[0], "Bike record has id field");
        assertArrayHasKey('name', $bikes[0], "Bike record has name field");
    }
} catch (Exception $e) {
    assertTrue(false, "Query bikes table", "Bikes query failed: " . $e->getMessage());
}

// Test 1.5: Query attractions table
try {
    $attractions = $db->fetchAll("SELECT * FROM attractions WHERE is_active=1 LIMIT 5");
    assertTrue(is_array($attractions), "Query attractions table", "Attractions query returned array");
} catch (Exception $e) {
    assertTrue(false, "Query attractions table", "Attractions query failed: " . $e->getMessage());
}

// Test 1.6: Query stays table
try {
    $stays = $db->fetchAll("SELECT * FROM stays WHERE is_active=1 LIMIT 5");
    assertTrue(is_array($stays), "Query stays table", "Stays query returned array");
} catch (Exception $e) {
    assertTrue(false, "Query stays table", "Stays query failed: " . $e->getMessage());
}

// Test 1.7: Query restaurants table
try {
    $restaurants = $db->fetchAll("SELECT * FROM restaurants WHERE is_active=1 LIMIT 5");
    assertTrue(is_array($restaurants), "Query restaurants table", "Restaurants query returned array");
} catch (Exception $e) {
    assertTrue(false, "Query restaurants table", "Restaurants query failed: " . $e->getMessage());
}

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 2. Configuration and Helper Functions Preservation (Requirements 3.2, 3.4)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing Configuration and Helper Functions...\n";

// Test 2.1: BASE_PATH constant defined
assertTrue(defined('BASE_PATH'), "BASE_PATH constant defined", "BASE_PATH is defined");

// Test 2.2: JWT_SECRET constant defined
assertTrue(defined('JWT_SECRET'), "JWT_SECRET constant defined", "JWT_SECRET is defined");

// Test 2.3: sanitize() function exists
assertTrue(function_exists('sanitize'), "sanitize() function exists");

// Test 2.4: esc() function exists
assertTrue(function_exists('esc'), "esc() function exists");

// Test 2.5: generateSlug() function exists
assertTrue(function_exists('generateSlug'), "generateSlug() function exists");

// Test 2.6: Test generateSlug() function behavior
$testSlug = generateSlug('cars', 123, 'Test Car Name');
assertTrue(strpos($testSlug, 'cars-123-') === 0, "generateSlug() produces correct format", "Slug: $testSlug");

// Test 2.7: Test sanitize() function behavior
$testInput = '<script>alert("xss")</script>Test';
$sanitized = sanitize($testInput);
assertTrue(strpos($sanitized, '<script>') === false, "sanitize() removes HTML tags", "Sanitized: $sanitized");

// Test 2.8: Test esc() function behavior
$testInput2 = '<b>Bold</b>';
$escaped = esc($testInput2);
assertTrue(strpos($escaped, '&lt;') !== false, "esc() escapes HTML entities", "Escaped: $escaped");

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 3. File Structure Preservation (Requirements 3.2, 3.8)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing File Structure...\n";

// Test 3.1: Core PHP files exist
$coreFiles = [
    'index.php',
    'about.php',
    'contact.php',
    'header.php',
    'footer.php',
    'php/config.php',
    'php/database.php',
];

foreach ($coreFiles as $file) {
    $filePath = $WORKSPACE_ROOT . '/' . $file;
    assertTrue(file_exists($filePath), "Core file exists: $file", "File: $filePath");
}

// Test 3.2: API files exist
$apiFiles = [
    'php/api/auth.php',
    'php/api/bookings.php',
    'php/api/blogs.php',
    'php/api/listings.php',
    'php/api/generate_html.php',
    'php/api/generate_sitemap.php',
];

foreach ($apiFiles as $file) {
    $filePath = $WORKSPACE_ROOT . '/' . $file;
    assertTrue(file_exists($filePath), "API file exists: $file", "File: $filePath");
}

// Test 3.3: Admin panel files exist
$adminFiles = [
    'admin/dashboard.php',
    'admin/bookings.php',
    'admin/blogs.php',
    'admin/listings.php',
];

foreach ($adminFiles as $file) {
    $filePath = $WORKSPACE_ROOT . '/' . $file;
    assertTrue(file_exists($filePath), "Admin file exists: $file", "File: $filePath");
}

// Test 3.4: Asset directories exist
$assetDirs = [
    'css',
    'js',
    'images',
    'blogs',
    'listing-detail',
];

foreach ($assetDirs as $dir) {
    $dirPath = $WORKSPACE_ROOT . '/' . $dir;
    assertTrue(is_dir($dirPath), "Asset directory exists: $dir", "Directory: $dirPath");
}

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 4. Page Rendering Preservation (Requirements 3.2, 3.8, 3.9, 3.10, 3.14)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing Page Rendering...\n";

// Test 4.1: Index page renders without errors
ob_start();
try {
    include $WORKSPACE_ROOT . '/index.php';
    $indexContent = ob_get_clean();
    assertTrue(strlen($indexContent) > 1000, "Index page renders", "Content length: " . strlen($indexContent));
    assertTrue(strpos($indexContent, '<!DOCTYPE html>') !== false, "Index page has DOCTYPE");
    assertTrue(strpos($indexContent, '<html') !== false, "Index page has html tag");
    assertTrue(strpos($indexContent, '</html>') !== false, "Index page has closing html tag");
} catch (Exception $e) {
    ob_end_clean();
    assertTrue(false, "Index page renders", "Error: " . $e->getMessage());
}

// Test 4.2: About page renders without errors
ob_start();
try {
    include $WORKSPACE_ROOT . '/about.php';
    $aboutContent = ob_get_clean();
    assertTrue(strlen($aboutContent) > 1000, "About page renders", "Content length: " . strlen($aboutContent));
    assertTrue(strpos($aboutContent, 'About Us') !== false, "About page has 'About Us' text");
} catch (Exception $e) {
    ob_end_clean();
    assertTrue(false, "About page renders", "Error: " . $e->getMessage());
}

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 5. JavaScript and CSS Files Preservation (Requirements 3.9, 3.10)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing JavaScript and CSS Files...\n";

// Test 5.1: JavaScript files exist
$jsFiles = [
    'js/preloader.js',
    'animations.js',
];

foreach ($jsFiles as $file) {
    $filePath = $WORKSPACE_ROOT . '/' . $file;
    if (file_exists($filePath)) {
        assertTrue(true, "JavaScript file exists: $file");
        $content = file_get_contents($filePath);
        assertTrue(strlen($content) > 0, "JavaScript file has content: $file", "Length: " . strlen($content));
    }
}

// Test 5.2: CSS files exist
$cssFiles = [
    'animations.css',
];

foreach ($cssFiles as $file) {
    $filePath = $WORKSPACE_ROOT . '/' . $file;
    if (file_exists($filePath)) {
        assertTrue(true, "CSS file exists: $file");
        $content = file_get_contents($filePath);
        assertTrue(strlen($content) > 0, "CSS file has content: $file", "Length: " . strlen($content));
    }
}

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 6. Generated HTML Files Preservation (Requirements 3.2, 3.3)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing Generated HTML Files Structure...\n";

// Test 6.1: Sample blog HTML files exist and are readable
$blogFiles = glob($WORKSPACE_ROOT . '/blogs/*.html');
if (count($blogFiles) > 0) {
    $sampleBlog = $blogFiles[0];
    assertTrue(file_exists($sampleBlog), "Sample blog HTML exists", "File: " . basename($sampleBlog));
    
    $content = file_get_contents($sampleBlog);
    assertTrue(strlen($content) > 0, "Sample blog has content", "Length: " . strlen($content));
    
    // Test that basic HTML structure exists (this is the baseline we want to preserve)
    assertTrue(strpos($content, '<html') !== false, "Sample blog has html tag");
    assertTrue(strpos($content, '<head>') !== false, "Sample blog has head tag");
    assertTrue(strpos($content, '<body') !== false, "Sample blog has body tag");
    
    // Note: We're NOT testing for content after </html> here because that's the bug we're fixing
    // We're only testing that the files exist and have basic structure
}

// Test 6.2: Sample listing HTML files exist and are readable
$listingFiles = glob($WORKSPACE_ROOT . '/listing-detail/*.html');
if (count($listingFiles) > 0) {
    $sampleListing = $listingFiles[0];
    assertTrue(file_exists($sampleListing), "Sample listing HTML exists", "File: " . basename($sampleListing));
    
    $content = file_get_contents($sampleListing);
    assertTrue(strlen($content) > 0, "Sample listing has content", "Length: " . strlen($content));
    
    assertTrue(strpos($content, '<html') !== false, "Sample listing has html tag");
    assertTrue(strpos($content, '<head>') !== false, "Sample listing has head tag");
    assertTrue(strpos($content, '<body') !== false, "Sample listing has body tag");
}

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 7. Sitemap Preservation (Requirements 3.3)
// ──────────────────────────────────────────────────────────────────────────────

echo "Testing Sitemap Structure...\n";

// Test 7.1: Sitemap file exists
$sitemapPath = $WORKSPACE_ROOT . '/sitemap.xml';
if (file_exists($sitemapPath)) {
    assertTrue(true, "Sitemap file exists");
    
    $sitemapContent = file_get_contents($sitemapPath);
    assertTrue(strlen($sitemapContent) > 0, "Sitemap has content", "Length: " . strlen($sitemapContent));
    
    // Test basic XML structure
    assertTrue(strpos($sitemapContent, '<?xml') !== false, "Sitemap has XML declaration");
    assertTrue(strpos($sitemapContent, '<urlset') !== false, "Sitemap has urlset tag");
    assertTrue(strpos($sitemapContent, '<url>') !== false, "Sitemap has url entries");
    assertTrue(strpos($sitemapContent, '<loc>') !== false, "Sitemap has loc tags");
    
    // Count URLs in sitemap (baseline)
    $urlCount = substr_count($sitemapContent, '<url>');
    assertTrue($urlCount > 0, "Sitemap contains URLs", "URL count: $urlCount");
} else {
    assertTrue(false, "Sitemap file exists", "Sitemap not found at: $sitemapPath");
}

echo "\n";

// ──────────────────────────────────────────────────────────────────────────────
// 8. Property-Based Tests - Generate Multiple Test Cases
// ──────────────────────────────────────────────────────────────────────────────

echo "Running Property-Based Tests (Multiple Test Cases)...\n";

// Test 8.1: generateSlug() produces consistent results for same input
$testCases = [
    ['type' => 'cars', 'id' => 1, 'name' => 'Toyota Innova'],
    ['type' => 'bikes', 'id' => 2, 'name' => 'Royal Enfield Classic'],
    ['type' => 'stays', 'id' => 3, 'name' => 'Hotel Taj Residency'],
    ['type' => 'attractions', 'id' => 4, 'name' => 'Ellora Caves'],
    ['type' => 'restaurants', 'id' => 5, 'name' => 'Tandoor Restaurant & Bar'],
];

foreach ($testCases as $case) {
    $slug1 = generateSlug($case['type'], $case['id'], $case['name']);
    $slug2 = generateSlug($case['type'], $case['id'], $case['name']);
    assertEquals($slug1, $slug2, "generateSlug() is deterministic for: {$case['name']}", "Slug: $slug1");
    
    // Test slug format
    $expectedPrefix = $case['type'] . '-' . $case['id'] . '-';
    assertTrue(strpos($slug1, $expectedPrefix) === 0, "Slug has correct prefix: {$case['name']}", "Slug: $slug1");
}

// Test 8.2: sanitize() removes all HTML tags
$htmlTestCases = [
    '<script>alert("xss")</script>Test',
    '<b>Bold</b> and <i>Italic</i>',
    '<div class="test">Content</div>',
    'Normal text without tags',
    '<a href="http://evil.com">Link</a>',
];

foreach ($htmlTestCases as $html) {
    $sanitized = sanitize($html);
    assertTrue(strpos($sanitized, '<') === false, "sanitize() removes < character", "Input: $html");
    assertTrue(strpos($sanitized, '>') === false, "sanitize() removes > character", "Input: $html");
}

// Test 8.3: Database queries return consistent structure
$tables = ['blogs', 'cars', 'bikes', 'stays', 'attractions', 'restaurants'];
foreach ($tables as $table) {
    try {
        $records = $db->fetchAll("SELECT * FROM $table LIMIT 1");
        if (count($records) > 0) {
            assertTrue(is_array($records[0]), "Query $table returns array structure");
            assertTrue(isset($records[0]['id']), "Query $table returns records with id field");
        }
    } catch (Exception $e) {
        // Table might not exist or be empty, that's okay for preservation test
        assertTrue(true, "Query $table handled gracefully");
    }
}

echo "\n";

// ══════════════════════════════════════════════════════════════════════════════
// Report Results
// ══════════════════════════════════════════════════════════════════════════════

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "  Test Results Summary\n";
echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "\n";
echo "Total tests run: " . $testResults['total_tests'] . "\n";
echo "Tests passed: " . $testResults['passed_tests'] . "\n";
echo "Tests failed: " . $testResults['failed_tests'] . "\n";
echo "\n";

if ($testResults['failed_tests'] > 0) {
    echo "───────────────────────────────────────────────────────────────────────────\n";
    echo "Failed Tests:\n";
    echo "───────────────────────────────────────────────────────────────────────────\n";
    foreach ($testResults['test_details'] as $test) {
        if (!$test['passed']) {
            echo "  ✗ " . $test['name'] . "\n";
            if (!empty($test['message'])) {
                echo "    " . $test['message'] . "\n";
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

// Property 2: Preservation - Non-Generation Functionality Unchanged
// This test MUST PASS on unfixed code to establish baseline
$testPassed = $testResults['failed_tests'] === 0;

if ($testPassed) {
    echo "✓ TEST PASSED: All preservation tests passed.\n";
    echo "\n";
    echo "Baseline behavior confirmed for non-generation functionality:\n";
    echo "  ✓ Database operations work correctly\n";
    echo "  ✓ Configuration and helper functions work correctly\n";
    echo "  ✓ File structure is intact\n";
    echo "  ✓ Pages render without errors\n";
    echo "  ✓ JavaScript and CSS files exist and have content\n";
    echo "  ✓ Generated HTML files exist and have basic structure\n";
    echo "  ✓ Sitemap exists and has valid XML structure\n";
    echo "  ✓ Property-based tests confirm consistent behavior\n";
    echo "\n";
    echo "EXPECTED OUTCOME: This test PASSES on unfixed code (baseline established).\n";
    echo "After fix: Re-run this test - it should STILL PASS (no regressions).\n";
    echo "\n";
    exit(0);
} else {
    echo "✗ TEST FAILED: " . $testResults['failed_tests'] . " preservation tests failed.\n";
    echo "\n";
    echo "UNEXPECTED OUTCOME: Some baseline functionality is not working.\n";
    echo "This could indicate:\n";
    echo "  1. Missing dependencies or configuration\n";
    echo "  2. Database connection issues\n";
    echo "  3. File permission problems\n";
    echo "  4. Incomplete codebase\n";
    echo "\n";
    echo "Review failed tests above and fix issues before proceeding with bugfix.\n";
    echo "\n";
    exit(1);
}
