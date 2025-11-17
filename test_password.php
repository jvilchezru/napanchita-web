<?php

/**
 * Script temporal para verificar/generar hash de password
 */

// Password que queremos usar
$password = 'password123';

// Generar nuevo hash
$nuevo_hash = password_hash($password, PASSWORD_BCRYPT);

echo "==========================================\n";
echo "TEST DE CONTRASEÑAS\n";
echo "==========================================\n\n";

// Hash actual en la BD (el que está dando problemas)
$hash_bd = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "1. Hash actual en BD:\n";
echo $hash_bd . "\n\n";

echo "2. ¿Verifica con 'password123'?\n";
if (password_verify($password, $hash_bd)) {
    echo "✓ SÍ - El hash es correcto\n\n";
} else {
    echo "✗ NO - El hash NO corresponde a 'password123'\n\n";
}

echo "3. Nuevo hash generado para 'password123':\n";
echo $nuevo_hash . "\n\n";

echo "4. Verificar nuevo hash:\n";
if (password_verify($password, $nuevo_hash)) {
    echo "✓ SÍ - El nuevo hash funciona correctamente\n\n";
} else {
    echo "✗ NO - Error al generar hash\n\n";
}

echo "==========================================\n";
echo "USAR ESTE HASH EN LA BASE DE DATOS:\n";
echo "==========================================\n";
echo $nuevo_hash . "\n\n";

echo "SQL PARA ACTUALIZAR:\n";
echo "UPDATE usuarios SET password = '{$nuevo_hash}' WHERE email = 'admin@napanchita.com';\n";
echo "UPDATE usuarios SET password = '{$nuevo_hash}' WHERE email = 'mesero@napanchita.com';\n";
echo "UPDATE usuarios SET password = '{$nuevo_hash}' WHERE email = 'repartidor@napanchita.com';\n";
