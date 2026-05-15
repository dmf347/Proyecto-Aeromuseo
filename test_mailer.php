<?php
require_once __DIR__ . '/backend/config/mailer.php';

echo "Testing mailer...\n";
try {
    $mail = crearMailer();
    $mail->addAddress('davidmf185@gmail.com', 'David Test');
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email.';
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->send();
    echo "Message has been sent\n";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}
