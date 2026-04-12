<?php
// apply-html-updates.php
$files = glob("listing-detail/*.html");
$updated = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $modified = false;
    
    // 1. Remove old huge Location Map at the bottom
    if (strpos($content, '<!-- Location Map Section -->') !== false) {
        $content = preg_replace('/<!-- Location Map Section -->\s*<div class="border-t border-slate-200 bg-slate-50 py-14">.*?<\/iframe>\s*<\/div>\s*<\/div>\s*<\/div>/s', '', $content);
        $modified = true;
    }
    
    // 2. Add small Location Map inside the right column (if not already there)
    if (strpos($content, '<!-- Location Map (Inside Booking Card) -->') === false) {
        $search = '<p class="text-center text-xs text-slate-600 font-medium mt-3">Free cancellation · No hidden charges</p>
          </div>
        </div>';
        
        $mapHtml = '
          <p class="text-center text-xs text-slate-600 font-medium mt-3">Free cancellation · No hidden charges</p>
          </div>
          <!-- Location Map (Inside Booking Card) -->
          <div class="border-t border-slate-100 p-5 pt-4">
             <h3 class="text-base font-bold mb-3 text-slate-800">Location Map</h3>
             <div class="rounded-xl overflow-hidden border border-slate-200" style="height: 250px;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
             </div>
          </div>
        </div>';
        
        // Let's make the search string more robust in case of whitespace differences
        $pattern = '/<p class="text-center text-xs text-slate-600 font-medium mt-3">Free cancellation · No hidden charges<\/p>\s*<\/div>\s*<\/div>/s';
        
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $mapHtml, $content, 1);
            $modified = true;
        }
    }
    
    if ($modified) {
        file_put_contents($file, $content);
        $updated++;
        echo "Updated: $file\n";
    }
}
echo "Total HTML pages updated: $updated\n";
?>
