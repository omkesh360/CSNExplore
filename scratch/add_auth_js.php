<?php
$header_php = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/header.php');
$gen_html = file_get_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php');

// Extract the Auth block
if (preg_match('/(\/\/ Auth.*?\}\)\(\);)/s', $header_php, $auth_matches)) {
    $auth_js = $auth_matches[1];
    
    // Find where to inject it.
    $scroll_end_marker = "update();\n            });\n        })();";
    if (strpos($gen_html, $scroll_end_marker) !== false) {
        $gen_html = str_replace($scroll_end_marker, $scroll_end_marker . "\n\n        " . $auth_js, $gen_html);
        file_put_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php', $gen_html);
        echo "Successfully injected Auth JS\n";
    } else {
        // Let's try matching just the end `})();` of the scroll block
        // The scroll block ends with: `update(); }); })();`
        // Wait, looking at update_header_js.php, I replaced the block with the one from header.php which ends with:
        // window.addEventListener('load', function(){ MH = measureMarquee(); update(); }); })();
        
        if (preg_match('/update\(\);\s*\}\);\s*\}\)\(\);/s', $gen_html, $end_matches)) {
            $gen_html = str_replace($end_matches[0], $end_matches[0] . "\n\n        " . $auth_js, $gen_html);
            file_put_contents('c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php', $gen_html);
            echo "Successfully injected Auth JS\n";
        } else {
            echo "Scroll end marker not found\n";
        }
    }
} else {
    echo "Could not extract Auth JS from header.php\n";
}
