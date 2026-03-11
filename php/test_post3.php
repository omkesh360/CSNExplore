<?php
require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/config.php';

$token = createJWT(['id' => 1, 'email' => 'admin@csnexplore.com', 'role' => 'admin', 'name' => 'Admin'], JWT_SECRET);

$ch = curl_init('http://localhost:8000/api/stays');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'name' => 'Test Stay',
    'type' => 'Hotel',
    'location' => 'Mumbai',
    'price' => '5000',
    'rating' => '4.5',
    'image' => '/abc.jpg'
]);

$response = curl_exec($ch);
echo "HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Response: $response\n";
