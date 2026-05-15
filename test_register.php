<?php
$data = ['nombre' => 'Test', 'email' => 'davidmf185@gmail.com', 'password' => 'password123'];

$ch = curl_init('http://localhost/Proyecto_Angular/backend/api/auth/register.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch) . "\n";
}
curl_close($ch);

echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
