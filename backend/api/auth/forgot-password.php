<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El correo electrónico es obligatorio']);
    exit;
}

$email = trim($data['email']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Formato de email no válido']);
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch();

// Por seguridad, no revelamos si el email existe o no, siempre devolvemos éxito.
// Pero solo enviamos el correo si existe.
if ($usuario) {
    $token = bin2hex(random_bytes(32));
    // El token expirará en 1 hora
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $update = $pdo->prepare("UPDATE usuarios SET token_recuperacion = :token, expiracion_token_recuperacion = :expiracion WHERE id = :id");
    $update->execute([
        ':token' => $token,
        ':expiracion' => $expiracion,
        ':id' => $usuario['id']
    ]);

    $enlaceRecuperacion = APP_URL . '/reset-password?token=' . $token;

    try {
        $mail = crearMailer();
        $mail->addAddress($email, $usuario['nombre']);
        $mail->Subject = 'Recuperacion de Contrasena - Aeromuseo';
        
        $mensajeHTML = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
            <h2 style='color: #0d47a1; text-align: center;'>Recuperación de Contraseña</h2>
            <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
            <p>Hemos recibido una solicitud para restablecer tu contraseña en la plataforma del Aeromuseo.</p>
            <p>Haz clic en el siguiente botón para crear una nueva contraseña. Este enlace caducará en 1 hora.</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$enlaceRecuperacion}' style='background-color: #0d47a1; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;'>Restablecer Contraseña</a>
            </div>
            <p>Si no has solicitado este cambio, puedes ignorar este correo sin problema.</p>
            <hr style='border: none; border-top: 1px solid #eee; margin-top: 30px;' />
            <p style='font-size: 12px; color: #888; text-align: center;'>Aeromuseo &copy; " . date('Y') . "</p>
        </div>";

        $mail->isHTML(true);
        $mail->Body = $mensajeHTML;
        $mail->send();
    } catch (Exception $e) {
        // Ignorar error de envio para no revelar que existe o no
        error_log("Error enviando email de recuperacion: " . $e->getMessage());
    }
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Si el correo existe en nuestra base de datos, te enviaremos un enlace para restablecer tu contraseña.'
]);
