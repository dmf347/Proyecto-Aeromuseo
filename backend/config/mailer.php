<?php
/**
 * Configuración de PHPMailer con Gmail SMTP.
 * Devuelve una instancia lista para usar.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ─── Credenciales SMTP ────────────────────────────────────────────────────────
define('MAIL_FROM',     'davidmf185@gmail.com');
define('MAIL_FROM_NAME','Aeromuseo Málaga');
define('MAIL_PASSWORD', 'bjtz ampo symm ypeb');   // Contraseña de aplicación Google

// ─── URL base del frontend (Angular en dev) ───────────────────────────────────
define('APP_URL', 'http://localhost:4200');

/**
 * Crea y configura una instancia de PHPMailer.
 */
function crearMailer(): PHPMailer {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_FROM;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->Timeout = 5; // Añadir un timeout corto para que no se quede colgado si hay problemas de red

    return $mail;
}
