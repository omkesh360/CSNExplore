<?php
$header_php = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/header.php');
$gen_html = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php');

// Extract the mobile menu script
if (preg_match('/(var _mob = document\.getElementById.*?document\.getElementById\(\'mob-close\'\)\.addEventListener[^\n]*\n)/s', $header_php, $mob_matches)) {
    $mob_js = $mob_matches[1];
    
    // Check if it's already there
    if (strpos($gen_html, 'var _mob = document.getElementById') === false) {
        // Find where to inject it. We can put it right before "// ══ Scroll → Pill Header"
        $scroll_marker = '// ══ Scroll → Pill Header';
        if (strpos($gen_html, $scroll_marker) !== false) {
            $gen_html = str_replace($scroll_marker, $mob_js . "\n        " . $scroll_marker, $gen_html);
            file_put_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php', $gen_html);
            echo "Successfully injected mobile menu JS\n";
        } else {
            echo "Scroll marker not found\n";
        }
    } else {
        echo "Mobile menu JS already exists\n";
    }
} else {
    echo "Could not extract mobile menu JS from header.php\n";
}
