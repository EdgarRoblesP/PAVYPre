<?php
/**
 * Diagnóstico del flujo de login.
 * Accede con GET: php/test_login.php
 */
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);

require_once __DIR__ . '/db.php';
$link = Conectarse();

if (!$link) {
    echo "FALLO: No se pudo conectar a la BD\n";
    exit;
}
echo "Conexión: OK\n\n";

// Verificar tablas
foreach (['pv_empleados', 'pv_clientes'] as $tabla) {
    $r = mysqli_query($link, "SHOW TABLES LIKE '$tabla'");
    $existe = $r && mysqli_num_rows($r) > 0;
    echo "Tabla $tabla: " . ($existe ? "EXISTE" : "NO EXISTE") . "\n";
}

echo "\n";

// Probar prepare en pv_empleados
$sql = 'SELECT id_empleado, nombre, puesto, contrasena FROM pv_empleados WHERE email = ? LIMIT 1';
$stmt = mysqli_prepare($link, $sql);
if ($stmt === false) {
    echo "mysqli_prepare pv_empleados: FALLO — " . mysqli_error($link) . "\n";
} else {
    echo "mysqli_prepare pv_empleados: OK\n";
    $email = 'admin@pavypre.com';
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $row = stmt_row($stmt);
    echo "Fila para admin@pavypre.com: " . ($row ? json_encode($row, JSON_UNESCAPED_UNICODE) : "ninguna") . "\n";
}

echo "\n";

// Probar prepare en pv_clientes
$sql2 = 'SELECT id_cliente, nombre, contrasena FROM pv_clientes WHERE email = ? LIMIT 1';
$stmt2 = mysqli_prepare($link, $sql2);
if ($stmt2 === false) {
    echo "mysqli_prepare pv_clientes: FALLO — " . mysqli_error($link) . "\n";
} else {
    echo "mysqli_prepare pv_clientes: OK\n";
}
