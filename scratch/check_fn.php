<?php
define('SKIP_REGENERATE', true);
require 'c:\xampp\htdocs\CSNexplore\CSNExplore\php\api\generate_html.php';

if (function_exists('generateCompleteHTML')) {
    echo "Function exists\n";
} else {
    echo "Function DOES NOT exist\n";
}
