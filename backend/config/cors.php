<?php
/**
 * Middleware CORS + JSON headers
 * Se incluye en todos los endpoints de la API
 */

// Permitir peticiones desde Angular (localhost:4200 en desarrollo)
$allowedOrigins = [
    'http://localhost:4200',
    'http://localhost:4000',
    'http://127.0.0.1:4200',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: http://localhost:4200");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// Responder inmediatamente a preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
