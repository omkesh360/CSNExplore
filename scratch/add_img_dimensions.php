<?php
$dir = new RecursiveDirectoryIterator('c:/xampp/htdocs/CSNExplore');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.(php|html)$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    if (strpos($path, '/vendor/') !== false || strpos($path, '\\vendor\\') !== false) continue;
    if (strpos($path, '/scratch/') !== false || strpos($path, '\\scratch\\') !== false) continue;

    $content = file_get_contents($path);
    $original = $content;
    
    // Add width and height to img tags that don't have them
    // Skip if width= or height= is already present
    $content = preg_replace_callback('/<img\s+([^>]+)>/i', function($m) {
        $attrs = $m[1];
        if (stripos($attrs, 'width=') !== false || stripos($attrs, 'height=') !== false) {
            return $m[0];
        }
        
        // If it's a logo (travelhub.png), give it smaller dimensions
        if (stripos($attrs, 'travelhub.png') !== false || stripos($attrs, 'logo') !== false) {
            return '<img width="180" height="40" ' . $attrs . '>';
        }
        
        // Otherwise give it standard dimensions that maintain aspect ratio
        // Often 800x600 is safe
        return '<img width="800" height="600" ' . $attrs . '>';
    }, $content);
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        $count++;
        echo "Modified: $path\n";
    }
}
echo "Total files modified: $count\n";
