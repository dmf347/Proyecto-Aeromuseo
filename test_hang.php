<?php
$data = ['nombre' => 'Test', 'email' => 'davidmf185@gmail.com', 'password' => 'password123'];

$ch = curl_init('http://localhost:8000/backend/api/auth/register.php');
// wait, we don't know the port. Let's try 8000 just in case.
