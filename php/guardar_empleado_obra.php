<?php
/**
 * Registra un empleado en una obra (INSERT en pv_trabajos_empleados).
 * POST: obra_id, id_empleado, fecha_inicio, fecha_termino
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

$link        = Conectarse();
$obraId      = trim($_POST['obra_id']     ?? '');
$idEmpleado  = trim($_POST['id_empleado'] ?? '');
$fechaInicio = $_POST['fecha_inicio']     ?? null;
$fechaTerm   = $_POST['fecha_termino']    ?? null;

if (!$obraId || !$idEmpleado) {
    http_response_code(400);
    echo json_encode(['error' => 'La obra y el empleado son obligatorios.']);
    exit;
}

if (!$fechaInicio) {
    $fechaInicio = date('Y-m-d');
}

// ── 1. Verificar que el empleado exista ──────────────────────
$stmtEmpl = mysqli_prepare($link, 'SELECT id_empleado FROM pv_empleados WHERE id_empleado = ?');
mysqli_stmt_bind_param($stmtEmpl, 's', $idEmpleado);
mysqli_stmt_execute($stmtEmpl);
if (!stmt_row($stmtEmpl)) {
    http_response_code(404);
    echo json_encode(['error' => 'El empleado seleccionado no existe en la base de datos.']);
    exit;
}

// ── 2. Verificar conflicto de fechas ─────────────────────────
$nuevoFin = $fechaTerm ?: '9999-12-31';

$stmtConflicto = mysqli_prepare($link,
    'SELECT te.id_obra, o.ubicacion
       FROM pv_trabajos_empleados te
       JOIN pv_obras o ON te.id_obra = o.id_obra
      WHERE te.id_empleado = ?
        AND te.id_obra     != ?
        AND te.fecha_adicion <= ?
        AND (te.fecha_termino IS NULL OR te.fecha_termino >= ?)
      LIMIT 1'
);
mysqli_stmt_bind_param($stmtConflicto, 'ssss', $idEmpleado, $obraId, $nuevoFin, $fechaInicio);
mysqli_stmt_execute($stmtConflicto);
$conflicto = stmt_row($stmtConflicto);

if ($conflicto) {
    http_response_code(409);
    echo json_encode([
        'error' => 'El empleado ya está asignado a la obra "' . $conflicto['ubicacion'] . '" en ese período.',
    ]);
    exit;
}

// ── 3. Obtener id_cliente de la disposición de la obra ───────
$stmtDis = mysqli_prepare($link, 'SELECT id_cliente FROM pv_disposiciones WHERE id_obra = ? LIMIT 1');
mysqli_stmt_bind_param($stmtDis, 's', $obraId);
mysqli_stmt_execute($stmtDis);
$dis = stmt_row($stmtDis);

if (!$dis) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró la disposición de la obra.']);
    exit;
}

// ── 4. Insertar en pv_trabajos_empleados ────────────────────────
$fechaTermVal = $fechaTerm ?: null;
$stmtIns = mysqli_prepare($link,
    'INSERT IGNORE INTO pv_trabajos_empleados
        (id_empleado, id_cliente, id_obra, fecha_adicion, fecha_termino)
     VALUES (?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmtIns, 'sssss', $idEmpleado, $dis['id_cliente'], $obraId, $fechaInicio, $fechaTermVal);
mysqli_stmt_execute($stmtIns);

recalcularGastosObra($link, $obraId);

echo json_encode(['success' => true]);
