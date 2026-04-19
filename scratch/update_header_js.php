<?php
$header_php = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/header.php');
$gen_html = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php');

// Extract the JS block from header.php
if (preg_match('/(\/\/ ══ Scroll → Pill Header.*?\}\)\(\);)/s', $header_php, $matches)) {
    $header_js = $matches[1];
    
    // the old JS in generate_html.php is marked with "// ── Sticky Header & Scroll Logic ──"
    if (preg_match('/(\/\/ ── Sticky Header & Scroll Logic ──.*?\}\)\(\);)/s', $gen_html, $gen_matches)) {
        $gen_html = str_replace($gen_matches[1], $header_js, $gen_html);
        file_put_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php', $gen_html);
        echo "Successfully updated JS from header.php\n";
    } else {
        echo "Could not find target JS block in generate_html.php\n";
    }
} else {
    echo "Could not extract JS from header.php\n";
}
