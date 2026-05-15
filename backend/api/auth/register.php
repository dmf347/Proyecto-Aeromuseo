<?php
/**
 * POST /api/auth/register.php
 *
 * Body JSON:
 *   { "nombre": "...", "email": "...", "password": "..." }
 *
 * Los nuevos usuarios se crean con rol 'visitante' y email_verificado = 0.
 * Se envía un email con un enlace de verificación antes de poder iniciar sesión.
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mailer.php';

use PHPMailer\PHPMailer\Exception;

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

// Generar token de verificación seguro (64 chars hex)
$token = bin2hex(random_bytes(32));

// Hash de la contraseña con bcrypt
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Insertar usuario (email_verificado = 0 por defecto)
try {
    $insert = $pdo->prepare("
        INSERT INTO usuarios (nombre, email, password, rol, email_verificado, token_verificacion)
        VALUES (:nombre, :email, :password, 'visitante', 0, :token)
    ");
    $insert->execute([
        ':nombre'   => $nombre,
        ':email'    => $email,
        ':password' => $hash,
        ':token'    => $token,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos al registrar: ' . $e->getMessage()]);
    exit;
}

// Construir enlace de verificación
$enlaceVerificacion = APP_URL . '/verificar-email?token=' . $token;

// Enviar email de verificación
try {
    $mail = crearMailer();
    $mail->addAddress($email, $nombre);
    $mail->isHTML(true);
    $mail->Subject = '✈️ Verifica tu cuenta en Aeromuseo Málaga';
    $mail->Body    = '
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#0f172a;font-family:Arial,sans-serif;">
          <div style="max-width:560px;margin:40px auto;background:#1e293b;border-radius:16px;overflow:hidden;border:1px solid #334155;">
            <div style="background:linear-gradient(135deg,#1d4ed8,#0e7490);padding:32px;text-align:center;">
              <h1 style="color:#fff;margin:0;font-size:24px;">✈️ Aeromuseo Málaga</h1>
            </div>
            <div style="padding:40px 32px;">
              <h2 style="color:#f1f5f9;margin-top:0;">¡Hola, ' . htmlspecialchars($nombre) . '!</h2>
              <p style="color:#94a3b8;line-height:1.6;">
                Gracias por registrarte en el Aeromuseo de Málaga. Para activar tu cuenta y poder iniciar sesión,
                haz clic en el botón de abajo.
              </p>
              <div style="text-align:center;margin:32px 0;">
                <a href="' . $enlaceVerificacion . '"
                   style="background:linear-gradient(135deg,#1d4ed8,#0e7490);color:#fff;padding:14px 32px;
                          border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px;display:inline-block;">
                  Verificar mi cuenta
                </a>
              </div>
              <p style="color:#64748b;font-size:13px;text-align:center;">
                Si no creaste esta cuenta, puedes ignorar este mensaje.<br>
                El enlace es de un solo uso.
              </p>
            </div>
            <div style="background:#0f172a;padding:16px;text-align:center;">
              <p style="color:#475569;font-size:12px;margin:0;">
                © ' . date('Y') . ' Aeromuseo Málaga · Este es un correo automático, no respondas a él.
              </p>
            </div>
          </div>
        </body>
        </html>
    ';
    $mail->AltBody = "Hola $nombre,\n\nVerifica tu cuenta en Aeromuseo Málaga haciendo clic en este enlace:\n$enlaceVerificacion\n\nSi no creaste esta cuenta, ignora este mensaje.";
    $mail->send();
} catch (Exception $e) {
    // Si el email falla, eliminamos al usuario para que pueda volver a intentarlo
    $pdo->prepare("DELETE FROM usuarios WHERE email = :email")->execute([':email' => $email]);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo enviar el email de verificación. Inténtalo de nuevo.']);
    exit;
}

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Cuenta creada correctamente. Revisa tu bandeja de entrada para verificar el email.',
    'emailEnviado' => true,
]);
