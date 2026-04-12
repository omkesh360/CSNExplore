<?php
$files = glob("listing-detail/*.html");
$updated = 0;

$oldBtn = '<button type="submit" class="bg-primary text-white font-bold py-2.5 rounded-xl text-sm hover:bg-orange-600 hover:scale-[1.02] transition-all duration-200">Subscribe</button>';
$newBtn = '<button type="submit" style="background-color: #ec5b13;" class="text-white font-bold py-2.5 rounded-xl text-sm hover:scale-[1.02] transition-all duration-200" onmouseover="this.style.backgroundColor=\'#d44e0e\'" onmouseout="this.style.backgroundColor=\'#ec5b13\'">Subscribe</button>';

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, $oldBtn) !== false) {
        $content = str_replace($oldBtn, $newBtn, $content);
        file_put_contents($file, $content);
        $updated++;
    } else if (strpos($content, 'Subscribe') !== false) {
        // Fallback regex if spacing differs
        $pattern = '/<button type="submit" class="bg-primary text-white[^>]+>Subscribe<\/button>/s';
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $newBtn, $content);
            file_put_contents($file, $content);
            $updated++;
        }
    }
}
echo "Total HTML pages updated for subscribe button: $updated\n";
?>
