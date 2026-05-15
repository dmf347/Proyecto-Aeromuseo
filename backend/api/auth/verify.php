<?php
/**
 * GET /api/auth/verify.php?token=XXXX
 *
 * Valida el token de verificación, activa la cuenta del usuario
 * y limpia el token para que no pueda reutilizarse.
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token) || strlen($token) !== 64) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token no válido']);
    exit;
}

$pdo = getConnection();

// Buscar usuario con ese token pendiente de verificación
$stmt = $pdo->prepare("
    SELECT id, nombre, email
    FROM usuarios
    WHERE token_verificacion = :token
      AND email_verificado = 0
    LIMIT 1
");
$stmt->execute([':token' => $token]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'El enlace no es válido o ya fue utilizado.'
    ]);
    exit;
}

// Activar la cuenta y limpiar el token (un solo uso)
$update = $pdo->prepare("
    UPDATE usuarios
    SET email_verificado   = 1,
        token_verificacion = NULL
    WHERE id = :id
");
$update->execute([':id' => $usuario['id']]);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => '¡Cuenta verificada correctamente! Ya puedes iniciar sesión.',
    'nombre'  => $usuario['nombre'],
]);
