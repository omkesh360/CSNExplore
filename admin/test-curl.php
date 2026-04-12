<?php
$ch = curl_init("http://localhost/CSNexplore/php/api/run-regenerate.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$response = curl_exec($ch);
curl_close($ch);
echo "RESPONSE:\n" . $response;
