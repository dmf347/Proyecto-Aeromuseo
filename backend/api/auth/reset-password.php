<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['token'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token y nueva contraseña son obligatorios']);
    exit;
}

$token = $data['token'];
$password = $data['password'];

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE token_recuperacion = :token AND expiracion_token_recuperacion > NOW() LIMIT 1");
$stmt->execute([':token' => $token]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El enlace es inválido o ha expirado. Por favor, solicita uno nuevo.']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Actualizar contraseña y limpiar el token
$update = $pdo->prepare("UPDATE usuarios SET password = :password, token_recuperacion = NULL, expiracion_token_recuperacion = NULL WHERE id = :id");
$update->execute([
    ':password' => $hash,
    ':id' => $usuario['id']
]);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión con tu nueva contraseña.'
]);
