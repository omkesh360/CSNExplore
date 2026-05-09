<?php
$dir = new RecursiveDirectoryIterator('c:/xampp/htdocs/CSNExplore');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.(php|html)$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
$img_count = 0;

foreach($files as $file) {
    $path = $file[0];
    if (strpos($path, '/vendor/') !== false || strpos($path, '\\vendor\\') !== false) continue;
    if (strpos($path, '/scratch/') !== false || strpos($path, '\\scratch\\') !== false) continue;

    $content = file_get_contents($path);
    $original = $content;
    
    // Add loading="lazy" to img tags
    $content = preg_replace_callback('/<img([^>]+)>/i', function($m) use (&$img_count) {
        $attrs = $m[1];
        
        // Skip if it already has loading= attribute
        if (stripos($attrs, 'loading=') !== false) {
            return $m[0];
        }
        
        // Skip if it has fetchpriority="high"
        if (stripos($attrs, 'fetchpriority="high"') !== false || stripos($attrs, "fetchpriority='high'") !== false) {
            return $m[0];
        }
        
        // Skip header/footer logos (travelhub.png) to prevent LCP delay
        if (stripos($attrs, 'travelhub.png') !== false) {
            return $m[0];
        }

        // It is safe to add lazy loading
        $img_count++;
        return '<img loading="lazy"' . $attrs . '>';
    }, $content);
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        $count++;
        echo "Modified: $path\n";
    }
}
echo "Total files modified: $count\n";
echo "Total img tags updated: $img_count\n";
