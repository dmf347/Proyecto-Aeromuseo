<?php
require_once __DIR__ . '/backend/config/database.php';
try {
    $pdo = getConnection();
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN token_recuperacion VARCHAR(64) NULL AFTER token_verificacion");
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN expiracion_token_recuperacion DATETIME NULL AFTER token_recuperacion");
    echo "Columnas agregadas con exito!\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Las columnas ya existen.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
