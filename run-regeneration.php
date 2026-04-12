<!DOCTYPE html>
<html>
<head>
    <title>Regenerate Listing Pages</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #000; color: #0f0; }
        .output { white-space: pre-wrap; }
        .success { color: #0f0; }
        .error { color: #f00; }
    </style>
</head>
<body>
    <h1>Regenerating All Listing Detail Pages...</h1>
    <div class="output">
<?php
// Disable output buffering
if (ob_get_level()) ob_end_flush();
flush();

// Run the regeneration script
echo "Starting regeneration...\n\n";
flush();

// Include and run
require_once __DIR__ . '/php/regenerate-with-animations.php';

echo "\n\n✅ COMPLETE! All listing detail pages have been regenerated with animations.\n";
?>
    </div>
</body>
</html>
