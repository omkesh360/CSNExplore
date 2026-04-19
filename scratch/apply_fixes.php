<?php
$file = 'c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php';
$content = file_get_contents($file);

// Replace Duplicate Free Cancellation text
$cancel_target = "</form>\r\n          <p class=\"text-center text-xs text-slate-600 font-medium mt-3\">Free cancellation · No hidden charges</p>\r\n          </div>";
$cancel_replace = "</form>\r\n          </div>";

if (strpos($content, $cancel_target) !== false) {
    $content = str_replace($cancel_target, $cancel_replace, $content);
} else {
    // Try with \n instead of \r\n
    $cancel_target2 = "</form>\n          <p class=\"text-center text-xs text-slate-600 font-medium mt-3\">Free cancellation · No hidden charges</p>\n          </div>";
    $content = str_replace($cancel_target2, $cancel_replace, $content);
}

// Add gallery-grid class
$css_target = "/* ── Detail Page Specifics ── */\r\n.glass-overlay";
$css_replace = "/* ── Detail Page Specifics ── */\r\n.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));gap:1rem;}\r\n.glass-overlay";
if (strpos($content, $css_target) !== false) {
    $content = str_replace($css_target, $css_replace, $content);
} else {
    $css_target2 = "/* ── Detail Page Specifics ── */\n.glass-overlay";
    $css_replace2 = "/* ── Detail Page Specifics ── */\n.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));gap:1rem;}\n.glass-overlay";
    $content = str_replace($css_target2, $css_replace2, $content);
}

file_put_contents($file, $content);
echo "OK";
