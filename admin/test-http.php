<?php
$opts = [
    "http" => [
        "method" => "POST"
    ]
];
$context = stream_context_create($opts);
$result = file_get_contents('http://localhost/CSNexplore/php/api/run-regenerate.php', false, $context);
echo "RESPONSE:\n" . $result;
