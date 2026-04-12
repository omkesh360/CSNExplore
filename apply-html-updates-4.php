<?php
$files = glob("listing-detail/*.html");
$updated = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;

    // 1. Update Grid to use Flex on Mobile
    $content = str_replace(
        '<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">', 
        '<div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 lg:items-start">', 
        $content
    );

    // 2. Set Info Section to order-1
    $content = str_replace(
        '<div class="lg:col-span-2 space-y-5">', 
        '<div class="lg:col-span-2 space-y-5 order-1 lg:order-1 w-full">', 
        $content
    );

    // 3. Set Booking Card to order-2
    $content = str_replace(
        '<div class="lg:col-span-1">', 
        '<div class="lg:col-span-1 order-2 lg:order-2 w-full">', 
        $content
    );

    // 4. Split Back Link and Similar Listings into an order-3 wrapper
    $pattern = '/(\s*<a href="\.\.\/listing\/[^"]+" class="inline-flex[^>]+>.*?<\/a>\s*)<\/div>\s*(<div id="related-listings-section".*?<\/script>)/is';
    
    if (preg_match($pattern, $content, $m)) {
        $backLink = $m[1];
        $relatedListings = $m[2];
        
        $replacement = "\n</div>\n" . 
                       "<div class=\"lg:col-span-2 order-3 lg:order-3 w-full\">\n" . 
                       $backLink . "\n" . 
                       $relatedListings . "\n" . 
                       "</div>\n";
                       
        $content = preg_replace($pattern, $replacement, $content, 1);
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        $updated++;
    }
}

echo "Total HTML pages updated for mobile layout ordering: $updated\n";
?>
