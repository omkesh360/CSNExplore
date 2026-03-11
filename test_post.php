<?php
$token = base64_encode(json_encode(['id' => 1, 'email' => 'admin@csnexplore.com', 'role' => 'admin', 'name' => 'Admin']));
$ch = curl_init('http://localhost:8000/api/stays');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'name' => 'Test',
    'type' => 'Resort',
    'location' => 'Here',
    'description' => 'A nice place',
    'image' => '/abc.jpg'
]);

$response = curl_exec($ch);
echo "HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Response: $response\n";
