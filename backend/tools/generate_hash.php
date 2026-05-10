<?php
/**
 * Ejecuta este script UNA SOLA VEZ para generar el hash bcrypt correcto
 * de las contraseñas de los usuarios de prueba.
 *
 * Uso: php generate_hash.php
 * Luego copia el hash generado al INSERT del schema.sql
 */

$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Contraseña: $password\n";
echo "Hash bcrypt: $hash\n\n";
echo "Copia este hash en el schema.sql reemplazando el campo 'password'\n";
