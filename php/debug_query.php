<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/db.php';

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    echo "Sin sesion admin\n";
    exit;
}

$link = Conectarse();
echo "Conectado: " . ($link ? "SI" : "NO") . "\n";

// 1. Count
$r = mysqli_query($link, 'SELECT COUNT(*) FROM pv_obras');
echo "Count obras: " . (int) mysqli_fetch_row($r)[0] . "\n";

// 2. Preparar query principal
$stmt = mysqli_prepare($link,
    "SELECT id_obra, ubicacion, fecha_inicio, fecha_fin,
            presupuesto_inicial, utilidad_neta,
            gasto_empleados, gasto_insumos, gasto_servicios, gasto_herramientas
       FROM pv_obras ORDER BY fecha_inicio DESC LIMIT 5 OFFSET 0"
);
echo "Prepare obras: " . ($stmt ? "OK" : "FALLA: " . mysqli_error($link)) . "\n";

if (!$stmt) exit;

mysqli_stmt_execute($stmt);
echo "Execute obras: OK\n";

$filas = stmt_rows($stmt);
echo "Filas obras: " . count($filas) . "\n";
if (count($filas) > 0) {
    echo "Primera obra id: " . $filas[0]['id_obra'] . "\n";
}

// 3. Probar sub-query empleados
$loopId = '';
$stmtE = mysqli_prepare($link,
    'SELECT e.nombre AS nombreEmpleado FROM pv_trabajos_empleados te
     JOIN pv_empleados e ON te.id_empleado = e.id_empleado
     WHERE te.id_obra = ? LIMIT 1'
);
echo "Prepare empleados sub: " . ($stmtE ? "OK" : "FALLA: " . mysqli_error($link)) . "\n";

if ($stmtE && count($filas) > 0) {
    mysqli_stmt_bind_param($stmtE, 's', $loopId);
    $loopId = $filas[0]['id_obra'];
    mysqli_stmt_execute($stmtE);
    $emp = stmt_rows($stmtE);
    echo "Empleados en primera obra: " . count($emp) . "\n";
}

echo "FIN OK\n";
