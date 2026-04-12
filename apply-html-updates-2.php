<?php
// apply-html-updates-2.php
$files = glob("listing-detail/*.html");
$updated = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $modified = false;
    
    // 1. Move Similar Listings
    $pattern = '/<div id="related-listings-section".*?<\/script>/s';
    if(preg_match($pattern, $content, $matches)) {
        $relatedListings = $matches[0];
        
        // Remove it from the bottom
        $content = str_replace($relatedListings, '', $content);
        
        // Adjust classes for the left column layout
        // Instead of full width max-w-6xl block, we make it native to the column.
        $relatedListings = str_replace('class="border-t border-slate-200 bg-white py-14"', 'class="mt-8 pt-8 border-t border-gray-200"', $relatedListings);
        $relatedListings = str_replace('<div class="max-w-6xl mx-auto px-4">', '<div class="w-full">', $relatedListings);
        $relatedListings = str_replace('lg:grid-cols-4 gap-6', 'sm:grid-cols-2 lg:grid-cols-2 gap-4', $relatedListings);
        $relatedListings = str_replace('mb-8 flex items-center gap-3', 'mb-6 flex items-center gap-2', $relatedListings);
        $relatedListings = str_replace('text-2xl font-serif font-black', 'text-xl font-bold', $relatedListings);
        
        // Find injection point: The Back arrow link in the left column.
        $injectPattern = '/(<a href="\.\.\/listing\/[^"]+" class="inline-flex items-center gap-2[^>]+>.*?<\/a>\s*<\/div>)/is';
        if (preg_match($injectPattern, $content, $injectMatches)) {
            $content = preg_replace($injectPattern, "$1\n\n" . $relatedListings . "\n\n", $content, 1);
            $modified = true;
        }
    }
    
    // 2. Wrap Booking flow in Check Availability gate
    if (strpos($content, 'id="check-availability-gate"') === false) {
        $gateWrapperStart = '
            <div id="check-availability-gate">
               <button type="button" id="btn-check-availability" class="w-full bg-[#ec5b13] text-white font-black py-4 rounded-xl shadow-md hover:bg-orange-600 transition-all text-base">Check Availability</button>
               <p class="text-center text-xs text-slate-600 font-medium mt-3">Free cancellation · No hidden charges</p>
            </div>
            <div id="booking-actions-container" class="hidden">
        ';
        
        // Insert BEFORE <!-- Login required gate -->
        $content = str_replace('<!-- Login required gate -->', $gateWrapperStart . "\n<!-- Login required gate -->", $content);
        
        // Find </form> AND remove the old "Free cancellation" text below it, then close the wrapper
        $formEndPattern = '/<\/form>\s*<p class="text-center text-xs text-slate-600 font-medium mt-3">Free cancellation[^<]+<\/p>/s';
        if (preg_match($formEndPattern, $content, $formEndMatches)) {
            $content = preg_replace($formEndPattern, "</form>\n            </div>", $content, 1);
            $modified = true;
        }
    }
    
    // 3. Add JS for Check Availability button
    if (strpos($content, 'btn-check-availability') !== false && strpos($content, 'check-availability-gate.style.display') === false) {
        $jsToAdd = "
<script>
document.getElementById('btn-check-availability')?.addEventListener('click', function() {
    var checkGate = document.getElementById('check-availability-gate');
    var bookingContainer = document.getElementById('booking-actions-container');
    if (checkGate) checkGate.style.display = 'none';
    if (bookingContainer) bookingContainer.classList.remove('hidden');
});
</script>
";
        $content = str_replace('</body>', $jsToAdd . '</body>', $content);
        $modified = true;
    }
    
    if ($modified) {
        file_put_contents($file, $content);
        $updated++;
    }
}
echo "Total HTML pages updated: $updated\n";
?>
