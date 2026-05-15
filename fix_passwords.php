<?php
require_once __DIR__ . '/backend/config/database.php';

try {
    $pdo = getConnection();
    
    $password_clara = 'password123';
    $hash_correcto = password_hash($password_clara, PASSWORD_BCRYPT, ['cost' => 12]);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET password = :hash WHERE email IN ('admin@aeromuseo.es', 'visitante@aeromuseo.es')");
    $stmt->execute([':hash' => $hash_correcto]);
    
    echo "Contraseñas actualizadas correctamente a 'password123'. Hash usado: " . $hash_correcto . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
