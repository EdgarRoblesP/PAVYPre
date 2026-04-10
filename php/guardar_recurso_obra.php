<?php
/**
 * Registra un recurso (Insumo, Servicio o Herramienta) en una obra.
 * Después de la inserción recalcula las columnas gasto_* en OBRAS.
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

require_once __DIR__ . '/db_admin.php';
require_once __DIR__ . '/recalcular_gastos_obra.php';

header('Content-Type: application/json');

$obraId  = trim($_POST['obra_id']      ?? '');
$tipo    = trim($_POST['tipo_recurso'] ?? 'Insumo');
$nombre  = trim($_POST['nombre_recurso'] ?? '');

if (!$obraId || !$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'Obra y nombre del recurso son obligatorios.']);
    exit;
}

// Obtener id_cliente de la obra
$stmtDis = $pdo->prepare('SELECT id_cliente FROM DISPOSICIONES WHERE id_obra = ? LIMIT 1');
$stmtDis->execute([$obraId]);
$dis = $stmtDis->fetch();

if (!$dis) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró la disposición de la obra.']);
    exit;
}

if ($tipo === 'Herramienta') {
    $cantidad     = max(1, (int)($_POST['cantidad_total'] ?? 1));
    $fechaInicio  = $_POST['fecha_inicio']  ?: date('Y-m-d');
    $fechaTermino = $_POST['fecha_termino'] ?: null;

    $stmtH = $pdo->prepare('SELECT id_herramienta FROM HERRAMIENTAS WHERE nombre = ? LIMIT 1');
    $stmtH->execute([$nombre]);
    $herr = $stmtH->fetch();
    if (!$herr) {
        http_response_code(404);
        echo json_encode(['error' => 'Herramienta no encontrada en el catálogo.']);
        exit;
    }
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO USOS_HERRAMIENTAS
             (id_herramienta, id_cliente, id_obra, fecha_adicion, fecha_termino, cantidad)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$herr['id_herramienta'], $dis['id_cliente'], $obraId,
                    $fechaInicio, $fechaTermino, $cantidad]);

} elseif ($tipo === 'Servicio') {
    $kilometraje = (float)($_POST['kilometraje'] ?? 0);

    $stmtSrv = $pdo->prepare('SELECT id_servicio FROM SERVICIOS WHERE tipo_traslado = ? LIMIT 1');
    $stmtSrv->execute([$nombre]);
    $srv = $stmtSrv->fetch();
    if (!$srv) {
        http_response_code(404);
        echo json_encode(['error' => 'Servicio no encontrado en el catálogo.']);
        exit;
    }
    $stmt = $pdo->prepare(
        'INSERT INTO REQUERIMIENTOS_SERVICIOS
             (id_servicio, id_cliente, id_obra, kilometraje)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$srv['id_servicio'], $dis['id_cliente'], $obraId, $kilometraje]);

} else {
    // Insumo
    $cantidad = (int)($_POST['cantidad_total'] ?? 0);

    $stmtIns = $pdo->prepare('SELECT id_insumo FROM INSUMOS WHERE tipo_material = ? LIMIT 1');
    $stmtIns->execute([$nombre]);
    $ins = $stmtIns->fetch();
    if (!$ins) {
        http_response_code(404);
        echo json_encode(['error' => 'Insumo no encontrado en el catálogo.']);
        exit;
    }
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO EMPLEOS_INSUMOS
             (id_insumo, id_cliente, id_obra, cantidad)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$ins['id_insumo'], $dis['id_cliente'], $obraId, $cantidad]);
}

recalcularGastosObra($pdo, $obraId);
echo json_encode(['success' => true]);
