<?php
$file = realpath(__DIR__ . '/php/config.php');
if (function_exists('opcache_invalidate')) {
    $res = opcache_invalidate($file, true);
    echo "Invalidated: " . ($res ? "yes" : "no");
} else {
    echo "opcache_invalidate not found";
}
