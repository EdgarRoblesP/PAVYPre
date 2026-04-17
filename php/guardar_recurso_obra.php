<?php
/**
 * Registra un recurso (Insumo, Servicio o Herramienta) en una obra.
 *
 * POST común:     obra_id, tipo_recurso (Insumo|Servicio|Herramienta), nombre_recurso
 * POST Insumo:    cantidad_total
 * POST Servicio:  kilometraje
 * POST Herram.:   cantidad_total, fecha_inicio (opt.), fecha_termino (opt.)
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/recalcular_gastos_obra.php';
header('Content-Type: application/json');

$link   = Conectarse();
$obraId = trim($_POST['obra_id']        ?? '');
$tipo   = trim($_POST['tipo_recurso']   ?? 'Insumo');
$nombre = trim($_POST['nombre_recurso'] ?? '');

if (!$obraId || !$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'Obra y nombre del recurso son obligatorios.']);
    exit;
}

// Obtener id_cliente de la obra
$stmtDis = mysqli_prepare($link, 'SELECT id_cliente FROM pv_disposiciones WHERE id_obra = ? LIMIT 1');
mysqli_stmt_bind_param($stmtDis, 's', $obraId);
mysqli_stmt_execute($stmtDis);
$dis = stmt_row($stmtDis);

if (!$dis) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró la disposición de la obra.']);
    exit;
}

if ($tipo === 'Herramienta') {
    $cantidad     = max(1, (int)($_POST['cantidad_total'] ?? 1));
    $fechaInicio  = $_POST['fecha_inicio']  ?: date('Y-m-d');
    $fechaTermino = $_POST['fecha_termino'] ?: null;

    $stmtH = mysqli_prepare($link, 'SELECT id_herramienta FROM pv_herramientas WHERE nombre = ? LIMIT 1');
    mysqli_stmt_bind_param($stmtH, 's', $nombre);
    mysqli_stmt_execute($stmtH);
    $herr = stmt_row($stmtH);
    if (!$herr) {
        http_response_code(404);
        echo json_encode(['error' => 'Herramienta no encontrada en el catálogo.']);
        exit;
    }
    $stmt = mysqli_prepare($link,
        'INSERT IGNORE INTO pv_usos_herramientas
             (id_herramienta, id_cliente, id_obra, fecha_adicion, fecha_termino, cantidad)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'sssssi', $herr['id_herramienta'], $dis['id_cliente'], $obraId,
                                       $fechaInicio, $fechaTermino, $cantidad);
    mysqli_stmt_execute($stmt);

} elseif ($tipo === 'Servicio') {
    $kilometraje = (float)($_POST['kilometraje'] ?? 0);

    $stmtSrv = mysqli_prepare($link, 'SELECT id_servicio FROM pv_servicios WHERE tipo_traslado = ? LIMIT 1');
    mysqli_stmt_bind_param($stmtSrv, 's', $nombre);
    mysqli_stmt_execute($stmtSrv);
    $srv = stmt_row($stmtSrv);
    if (!$srv) {
        http_response_code(404);
        echo json_encode(['error' => 'Servicio no encontrado en el catálogo.']);
        exit;
    }
    $stmt = mysqli_prepare($link,
        'INSERT INTO pv_requerimientos_servicios
             (id_servicio, id_cliente, id_obra, kilometraje)
         VALUES (?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'sssd', $srv['id_servicio'], $dis['id_cliente'], $obraId, $kilometraje);
    mysqli_stmt_execute($stmt);

} else {
    // Insumo
    $cantidad = (int)($_POST['cantidad_total'] ?? 0);

    $stmtIns = mysqli_prepare($link, 'SELECT id_insumo FROM pv_insumos WHERE tipo_material = ? LIMIT 1');
    mysqli_stmt_bind_param($stmtIns, 's', $nombre);
    mysqli_stmt_execute($stmtIns);
    $ins = stmt_row($stmtIns);
    if (!$ins) {
        http_response_code(404);
        echo json_encode(['error' => 'Insumo no encontrado en el catálogo.']);
        exit;
    }
    $stmt = mysqli_prepare($link,
        'INSERT IGNORE INTO pv_empleos_insumos
             (id_insumo, id_cliente, id_obra, cantidad)
         VALUES (?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'sssi', $ins['id_insumo'], $dis['id_cliente'], $obraId, $cantidad);
    mysqli_stmt_execute($stmt);
}

recalcularGastosObra($link, $obraId);
echo json_encode(['success' => true]);
