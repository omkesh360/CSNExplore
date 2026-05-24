<?php
/**
 * One-time migration script to set up SEO columns and populate listing tables with default optimized metadata.
 */
require_once 'c:/xampp/htdocs/CSNExplore/php/config.php';
require_once 'c:/xampp/htdocs/CSNExplore/php/seo-optimizer.php';

$db = getDB();

$categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

echo "=== STEP 1: Altering Tables ===\n";

function addColumnIfNotExists($db, $table, $column, $definition) {
    try {
        $db->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        echo "  - Added column `$column` to `$table`.\n";
    } catch (Exception $e) {
        // Suppress error if column already exists (typically SQLSTATE 42S21 / Error 1060)
        if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), '1060') !== false) {
            echo "  - Column `$column` already exists in `$table`.\n";
        } else {
            echo "  - Error adding `$column` to `$table`: " . $e->getMessage() . "\n";
        }
    }
}

foreach ($categories as $category) {
    echo "Altering table `$category`...\n";
    addColumnIfNotExists($db, $category, 'meta_title', 'VARCHAR(255) DEFAULT NULL');
    addColumnIfNotExists($db, $category, 'meta_description', 'TEXT DEFAULT NULL');
    addColumnIfNotExists($db, $category, 'meta_keywords', 'TEXT DEFAULT NULL');
    addColumnIfNotExists($db, $category, 'slug', 'VARCHAR(255) DEFAULT NULL');
    addColumnIfNotExists($db, $category, 'focus_keyword', 'VARCHAR(255) DEFAULT NULL');
    addColumnIfNotExists($db, $category, 'seo_score', 'INT(11) DEFAULT 0');
}

echo "\n=== STEP 2: Calculating SEO Score Function ===\n";

function calculatePHPSEOScore($title, $desc, $focus, $slug, $keywordsStr) {
    $score = 0;
    
    // Title checks (30 points)
    $titleLen = mb_strlen($title);
    if ($titleLen >= 50 && $titleLen <= 60) {
        $score += 15;
    } elseif ($titleLen > 0) {
        $score += 5;
    }
    
    if ($focus && stripos($title, $focus) !== false) {
        $score += 15;
    }
    
    // Description checks (30 points)
    $descLen = mb_strlen($desc);
    if ($descLen >= 120 && $descLen <= 160) {
        $score += 15;
    } elseif ($descLen > 0) {
        $score += 5;
    }
    
    if ($focus && stripos($desc, $focus) !== false) {
        $score += 15;
    }
    
    // Focus keyword (20 points)
    if ($focus && mb_strlen($focus) >= 3) {
        $score += 20;
    }
    
    // Slug (10 points)
    if ($slug && mb_strlen($slug) > 0) {
        $score += 10;
    }
    
    // Meta keywords (10 points)
    $keywords = array_filter(array_map('trim', explode(',', $keywordsStr)));
    if (count($keywords) >= 3) {
        $score += 10;
    }
    
    return $score;
}

echo "\n=== STEP 3: Seeding Listings with Default Optimized SEO Data ===\n";

foreach ($categories as $category) {
    $nameCol = ($category === 'buses') ? 'operator' : 'name';
    $locCol = ($category === 'buses') ? 'from_location' : 'location';
    echo "Processing `$category` listings...\n";
    try {
        $items = $db->fetchAll("SELECT id, `$nameCol` as name, `$locCol` as location FROM `$category`");
        $count = 0;
        foreach ($items as $item) {
            $id = $item['id'];
            $name = $item['name'];
            
            // Skip empty names
            if (empty($name)) continue;
            
            // Generate slug, default title, default desc, default keywords
            $slug = generateSlug($category, $id, $name);
            $defaultTitle = SEOOptimizer::generateTitle('listing', $name);
            $defaultDesc = SEOOptimizer::generateDescription('listing', $name);
            $defaultKeywords = SEOOptimizer::generateKeywords($category, $name);
            
            // Focus Keyword
            $catLabel = match($category) {
                'stays' => 'Hotel',
                'cars' => 'Car Rental',
                'bikes' => 'Bike Rental',
                'restaurants' => 'Restaurant',
                'attractions' => 'Attraction',
                'buses' => 'Bus Booking',
                default => ucfirst($category)
            };
            $focusKeyword = $name . ' ' . $catLabel;
            
            // Calculate SEO score
            $score = calculatePHPSEOScore($defaultTitle, $defaultDesc, $focusKeyword, $slug, $defaultKeywords);
            
            // Update item
            $db->update($category, [
                'meta_title' => $defaultTitle,
                'meta_description' => $defaultDesc,
                'meta_keywords' => $defaultKeywords,
                'slug' => $slug,
                'focus_keyword' => $focusKeyword,
                'seo_score' => $score
            ], 'id = :id', [':id' => $id]);
            
            $count++;
        }
        echo "  - Initialized $count items in `$category`.\n";
    } catch (Exception $e) {
        echo "  - Error seeding `$category`: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Migration Completed! ===\n";
