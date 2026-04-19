<?php
$header_php = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/header.php');
$gen_html = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php');

// Extract the exact CSS block for `#site-header` from `header.php`
// In header.php, it starts with /* ══ Site Header – ALWAYS STICKY (position:fixed is permanent) ══ */
if (preg_match('/(\/\* ══ Site Header – ALWAYS STICKY[^*]+(?:\*(?!\/)[^*]*)*\*\/.*?#site-header\.pill-mode \.hdr-wa-btn[^{]*{[^}]*}[\r\n\s]*\.hdr-call-btn, \.hdr-wa-btn[^{]*{[^}]*}[\r\n\s]*\.hdr-call-btn::before, \.hdr-wa-btn::before[^{]*{[^}]*}[\r\n\s]*#site-header-placeholder[^{]*{[^}]*})/s', $header_php, $matches)) {
    $header_css = $matches[1];
    
    // Now locate the similar block in generate_html.php
    // In generate_html.php, it's under /* ── Site Header – Pill Mode Logic ── */
    if (preg_match('/(\/\* ── Site Header – Pill Mode Logic ── \*\/.*?#site-header\.pill-mode \.hdr-call-btn, #site-header\.pill-mode \.hdr-wa-btn[^{]*{[^}]*})/s', $gen_html, $gen_matches)) {
        
        $gen_html = str_replace($gen_matches[1], $header_css, $gen_html);
        file_put_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php', $gen_html);
        echo "Successfully updated CSS from header.php\n";
    } else {
        echo "Could not find target CSS block in generate_html.php\n";
    }
    
} else {
    echo "Could not extract CSS from header.php\n";
}
