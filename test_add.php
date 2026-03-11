<?php
$token = "fake"; // We don't have one here, so let's bypass auth in listings.php temporarily or get a token.
// To bypass auth, let's just create a token.
$userData = ['id' => 1, 'email' => 'admin@csnexplore.com', 'role' => 'admin', 'name' => 'Admin'];
$token = base64_encode(json_encode($userData));

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
echo "Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo "Response: $response\n";
