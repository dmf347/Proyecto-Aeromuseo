<?php
/**
 * POST /api/auth/register.php
 *
 * Body JSON:
 *   { "nombre": "...", "email": "...", "password": "..." }
 *
 * Los nuevos usuarios siempre se crean con rol 'visitante'.
 * Solo un admin puede cambiar el rol desde la BD directamente.
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['nombre'], $data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'nombre, email y password son obligatorios']);
    exit;
}

$nombre   = trim($data['nombre']);
$email    = trim($data['email']);
$password = $data['password'];

// Validaciones
if (strlen($nombre) < 2) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El nombre debe tener al menos 2 caracteres']);
    exit;
}

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

$pdo = getConnection();

// Comprobar si el email ya existe
$check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
$check->execute([':email' => $email]);

if ($check->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Este email ya está registrado']);
    exit;
}

// Hash de la contraseña con bcrypt
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Insertar usuario
$insert = $pdo->prepare("
    INSERT INTO usuarios (nombre, email, password, rol)
    VALUES (:nombre, :email, :password, 'visitante')
");
$insert->execute([
    ':nombre'   => $nombre,
    ':email'    => $email,
    ':password' => $hash,
]);

$newId = (int) $pdo->lastInsertId();

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Cuenta creada correctamente',
    'user'    => [
        'id'      => $newId,
        'nombre'  => $nombre,
        'email'   => $email,
        'rol'     => 'visitante',
        'isAdmin' => false,
    ]
]);
