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
    
    // Add auto=format to unsplash links if missing
    $content = preg_replace_callback('/(https:\/\/images\.unsplash\.com\/[^"\'\s>]+)/i', function($m) {
        $url = $m[1];
        
        // Ensure auto=format is present
        if (strpos($url, 'auto=format') === false) {
            if (strpos($url, '?') !== false) {
                $url .= '&auto=format';
            } else {
                $url .= '?auto=format';
            }
        }
        
        // Reduce w=1600 or w=1200 to w=800
        $url = preg_replace('/w=1[26]00/', 'w=800', $url);
        
        return $url;
    }, $content);
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        $count++;
        echo "Modified: $path\n";
    }
}
echo "Total files modified: $count\n";
