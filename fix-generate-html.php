<?php
$file = 'php/api/generate_html.php';
$content = file_get_contents($file);

// Fix the querySelector with single quotes inside PHP string
$content = str_replace(
    "var bgLayer = wrap ? wrap.querySelector(\"div[style*='filter:blur']\") : null;\n    if (bgLayer) bgLayer.style.backgroundImage = \"url('\" + _slideImages[_slideIndex] + \"')\";",
    "var bgLayer = wrap ? wrap.querySelector(\"[data-blur]\") : null;\n    if (bgLayer) bgLayer.style.backgroundImage = \"url(\" + _slideImages[_slideIndex] + \")\";",
    $content
);

file_put_contents($file, $content);
echo "Done. filter:blur remaining: " . (strpos($content, "filter:blur']") !== false ? "YES (problem)" : "no") . PHP_EOL;
echo "data-blur selector present: " . (strpos($content, "[data-blur]") !== false ? "yes" : "NO (problem)") . PHP_EOL;
