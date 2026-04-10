<?php
/**
 * Registra un empleado en una obra (INSERT en TRABAJOS_EMPLEADOS).
 * POST: obra_id, id_empleado, fecha_inicio, fecha_termino
 *
 * Validaciones:
 *  1. El empleado debe existir en EMPLEADOS.
 *  2. No debe tener otra asignación activa en las mismas fechas.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$obraId      = trim($_POST['obra_id']     ?? '');
$idEmpleado  = trim($_POST['id_empleado'] ?? '');
$fechaInicio = $_POST['fecha_inicio']     ?? null;
$fechaTerm   = $_POST['fecha_termino']    ?? null;

// ── Validación básica ────────────────────────────────────────
if (!$obraId || !$idEmpleado) {
    http_response_code(400);
    echo json_encode(['error' => 'La obra y el empleado son obligatorios.']);
    exit;
}

if (!$fechaInicio) {
    $fechaInicio = date('Y-m-d');
}

// ── 1. Verificar que el empleado exista ──────────────────────
$stmtEmpl = $pdo->prepare('SELECT id_empleado FROM EMPLEADOS WHERE id_empleado = ?');
$stmtEmpl->execute([$idEmpleado]);
if (!$stmtEmpl->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'El empleado seleccionado no existe en la base de datos.']);
    exit;
}

// ── 2. Verificar conflicto de fechas ─────────────────────────
// Hay conflicto si el empleado tiene otra asignación cuyo rango
// se solapa con el nuevo: existente.inicio <= nuevo.fin  Y  existente.fin >= nuevo.inicio
$nuevoFin = $fechaTerm ?: '9999-12-31';

$stmtConflicto = $pdo->prepare(
    'SELECT te.id_obra, o.ubicacion
       FROM TRABAJOS_EMPLEADOS te
       JOIN OBRAS o ON te.id_obra = o.id_obra
      WHERE te.id_empleado = ?
        AND te.id_obra     != ?
        AND te.fecha_adicion <= ?
        AND (te.fecha_termino IS NULL OR te.fecha_termino >= ?)
      LIMIT 1'
);
$stmtConflicto->execute([$idEmpleado, $obraId, $nuevoFin, $fechaInicio]);
$conflicto = $stmtConflicto->fetch();

if ($conflicto) {
    http_response_code(409);
    echo json_encode([
        'error' => 'El empleado ya está asignado a la obra "' . $conflicto['ubicacion'] . '" en ese período.',
    ]);
    exit;
}

// ── 3. Obtener id_cliente de la disposición de la obra ───────
$stmtDis = $pdo->prepare('SELECT id_cliente FROM DISPOSICIONES WHERE id_obra = ? LIMIT 1');
$stmtDis->execute([$obraId]);
$dis = $stmtDis->fetch();

if (!$dis) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró la disposición de la obra.']);
    exit;
}

// ── 4. Insertar en TRABAJOS_EMPLEADOS ────────────────────────
$stmtIns = $pdo->prepare(
    'INSERT IGNORE INTO TRABAJOS_EMPLEADOS
        (id_empleado, id_cliente, id_obra, fecha_adicion, fecha_termino)
     VALUES (?, ?, ?, ?, ?)'
);
$stmtIns->execute([
    $idEmpleado,
    $dis['id_cliente'],
    $obraId,
    $fechaInicio,
    $fechaTerm ?: null,
]);

echo json_encode(['success' => true]);
