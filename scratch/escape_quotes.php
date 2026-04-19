<?php
$gen_html = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php');

if (preg_match('/(\/\* ── Motion System ── \*\/.*?<\/script>\s*<\/head>)/s', $gen_html, $matches)) {
    $block = $matches[1];
    
    // Unescape first, just in case there are some \' already, to avoid double escaping
    $block_unescaped = stripslashes($block);
    
    // Now safely escape all single quotes
    $block_escaped = str_replace("'", "\\'", $block_unescaped);
    
    // Replace the block back
    $gen_html = str_replace($matches[1], $block_escaped, $gen_html);
    file_put_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php', $gen_html);
    echo "Successfully escaped single quotes\n";
} else {
    echo "Could not find target block to escape\n";
}
