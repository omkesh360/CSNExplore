<?php
$url = "http://localhost/CSNexplore/CSNExplore/php/api/get_related.php?type=stays&exclude=1&limit=6";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
echo $res;
curl_close($ch);
