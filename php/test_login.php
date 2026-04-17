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
foreach (['PV_EMPLEADOS', 'PV_CLIENTES'] as $tabla) {
    $r = mysqli_query($link, "SHOW TABLES LIKE '$tabla'");
    $existe = $r && mysqli_num_rows($r) > 0;
    echo "Tabla $tabla: " . ($existe ? "EXISTE" : "NO EXISTE") . "\n";
}

echo "\n";

// Probar prepare en PV_EMPLEADOS
$sql = 'SELECT id_empleado, nombre, puesto, contrasena FROM PV_EMPLEADOS WHERE email = ? LIMIT 1';
$stmt = mysqli_prepare($link, $sql);
if ($stmt === false) {
    echo "mysqli_prepare PV_EMPLEADOS: FALLO — " . mysqli_error($link) . "\n";
} else {
    echo "mysqli_prepare PV_EMPLEADOS: OK\n";
    $email = 'admin@pavypre.com';
    mysqli_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $row = stmt_row($stmt);
    echo "Fila para admin@pavypre.com: " . ($row ? json_encode($row, JSON_UNESCAPED_UNICODE) : "ninguna") . "\n";
}

echo "\n";

// Probar prepare en PV_CLIENTES
$sql2 = 'SELECT id_cliente, nombre, contrasena FROM PV_CLIENTES WHERE email = ? LIMIT 1';
$stmt2 = mysqli_prepare($link, $sql2);
if ($stmt2 === false) {
    echo "mysqli_prepare PV_CLIENTES: FALLO — " . mysqli_error($link) . "\n";
} else {
    echo "mysqli_prepare PV_CLIENTES: OK\n";
}
