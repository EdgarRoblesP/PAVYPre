<?php
/**
 * SCRIPT DE MIGRACIÓN (ejecutar UNA sola vez).
 * Convierte todas las contraseñas en texto plano de EMPLEADOS y CLIENTES
 * a hashes Argon2ID compatibles con password_verify().
 *
 * Cómo ejecutar:
 *   php php/migrar_contrasenas.php
 *   — o bien — acceder desde el navegador mientras el servidor esté activo.
 *
 * ELIMINAR este archivo después de ejecutarlo.
 */

// Solo accesible desde CLI o localhost
if (PHP_SAPI !== 'cli') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($ip, ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        exit('Acceso denegado. Ejecuta este script desde CLI o localhost.');
    }
}

require_once __DIR__ . '/db_admin.php';

$tablas = [
    ['tabla' => 'EMPLEADOS', 'pk' => 'id_empleado'],
    ['tabla' => 'CLIENTES',  'pk' => 'id_cliente'],
];

$totalActualizados = 0;
$totalOmitidos     = 0;

foreach ($tablas as ['tabla' => $tabla, 'pk' => $pk]) {
    $rows = $pdo->query("SELECT {$pk}, contrasena FROM {$tabla}")->fetchAll();

    foreach ($rows as $row) {
        $pwd = $row['contrasena'];

        // Si ya es un hash Argon2 o bcrypt, omitir
        if (str_starts_with($pwd, '$argon2') || str_starts_with($pwd, '$2y$')) {
            $totalOmitidos++;
            continue;
        }

        $hash = password_hash($pwd, PASSWORD_ARGON2ID);
        $upd  = $pdo->prepare("UPDATE {$tabla} SET contrasena = ? WHERE {$pk} = ?");
        $upd->execute([$hash, $row[$pk]]);
        $totalActualizados++;

        echo "[{$tabla}] {$row[$pk]} → migrado\n";
    }
}

echo "\nMigración completada: {$totalActualizados} actualizados, {$totalOmitidos} ya estaban hasheados.\n";
echo "RECUERDA: elimina este archivo (php/migrar_contrasenas.php) una vez ejecutado.\n";
