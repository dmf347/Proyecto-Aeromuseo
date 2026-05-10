<?php
/**
 * POST /api/auth/login.php
 *
 * Body JSON:
 *   { "email": "...", "password": "..." }
 *
 * Respuestas:
 *   200 { success: true, user: { id, nombre, email, rol } }
 *   401 { success: false, message: "Credenciales incorrectas" }
 *   400 { success: false, message: "..." }
 *   500 { success: false, message: "..." }
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Leer body JSON
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email y contraseña son obligatorios']);
    exit;
}

$email    = trim($data['email']);
$password = $data['password'];

// Validaciones básicas
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Formato de email no válido']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

// Buscar usuario en la BD
$pdo = getConnection();

$stmt = $pdo->prepare("
    SELECT id, nombre, email, password, rol, activo
    FROM usuarios
    WHERE email = :email
    LIMIT 1
");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch();

// Usuario no encontrado
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'El email no está registrado']);
    exit;
}

// Cuenta desactivada
if (!$usuario['activo']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Tu cuenta ha sido desactivada. Contacta con el administrador.']);
    exit;
}

// Verificar contraseña (bcrypt)
if (!password_verify($password, $usuario['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
    exit;
}

// ¡Login correcto! Devolver datos del usuario (sin la contraseña)
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Inicio de sesión exitoso',
    'user'    => [
        'id'     => (int) $usuario['id'],
        'nombre' => $usuario['nombre'],
        'email'  => $usuario['email'],
        'rol'    => $usuario['rol'],          // 'admin' | 'visitante'
        'isAdmin' => $usuario['rol'] === 'admin',
    ]
]);
