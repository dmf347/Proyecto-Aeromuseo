<?php
$ch = curl_init('http://localhost/Proyecto_Angular/backend/api/auth/verify.php?token=d6d103adf069c7a6f1016c051940265d36ff3ab93b57925b30550ac90affa57c');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo "Output:\n$response\n";
