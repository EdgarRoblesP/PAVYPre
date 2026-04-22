<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$out = [
    'session_role' => $_SESSION['user_role'] ?? '(no definido)',
    'session_id'   => session_id(),
    'db_connected' => false,
    'tablas'       => [],
    'php_version'  => PHP_VERSION,
];

$link = Conectarse();
if (!$link) {
    $out['db_error'] = mysqli_connect_error();
    echo json_encode($out);
    exit;
}

$out['db_connected'] = true;

$r = mysqli_query($link, "SHOW TABLES");
$todas = [];
while ($fila = mysqli_fetch_row($r)) {
    $t = $fila[0];
    $c = mysqli_query($link, "SELECT COUNT(*) FROM `$t`");
    $todas[$t] = $c ? (int) mysqli_fetch_row($c)[0] : 'ERROR';
}
$out['tablas'] = $todas;

// Probar las consultas preparadas de get_obras_admin.php
$pruebas = [];
$tablasPrueba = [
    'pv_obras'                    => 'SELECT id_obra FROM pv_obras LIMIT 1',
    'pv_trabajos_empleados'       => 'SELECT id_obra FROM pv_trabajos_empleados LIMIT 1',
    'pv_usos_herramientas'        => 'SELECT id_obra FROM pv_usos_herramientas LIMIT 1',
    'pv_empleos_insumos'          => 'SELECT id_obra FROM pv_empleos_insumos LIMIT 1',
    'pv_requerimientos_servicios' => 'SELECT id_obra FROM pv_requerimientos_servicios LIMIT 1',
    'pv_cobros'                   => 'SELECT id_obra FROM pv_cobros LIMIT 1',
    'pv_servicios'                => 'SELECT id_servicio FROM pv_servicios LIMIT 1',
    'pv_disposiciones'            => 'SELECT id_obra FROM pv_disposiciones LIMIT 1',
];
foreach ($tablasPrueba as $nombre => $sql) {
    $s = mysqli_prepare($link, $sql);
    $pruebas[$nombre] = $s ? 'OK' : 'FALLA: ' . mysqli_error($link);
    if ($s) mysqli_stmt_close($s);
}
$out['prepare_tests'] = $pruebas;

echo json_encode($out, JSON_PRETTY_PRINT);
