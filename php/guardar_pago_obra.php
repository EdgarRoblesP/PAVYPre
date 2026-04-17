<?php
/**
 * Registra un pago en una obra (INSERT en PV_COBROS).
 * POST: obra_id, fecha_pago, tipo_pago, monto
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link      = Conectarse();
$obraId    = trim($_POST['obra_id']   ?? '');
$fechaPago = $_POST['fecha_pago']     ?? null;
$tipoPago  = trim($_POST['tipo_pago'] ?? 'Transferencia');
$monto     = (float)($_POST['monto']  ?? 0);

if (!$obraId || !$fechaPago || $monto <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Obra, fecha y monto válido son obligatorios.']);
    exit;
}

// Obtener id_cliente de la obra
$stmtDis = mysqli_prepare($link, 'SELECT id_cliente FROM PV_DISPOSICIONES WHERE id_obra = ? LIMIT 1');
mysqli_bind_param($stmtDis, 's', $obraId);
mysqli_stmt_execute($stmtDis);
$dis = stmt_row($stmtDis);

if (!$dis) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró la disposición de la obra.']);
    exit;
}

$stmt = mysqli_prepare($link, 'INSERT INTO PV_COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra) VALUES (?, ?, ?, ?, ?)');
mysqli_bind_param($stmt, 'sdsss', $fechaPago, $monto, $tipoPago, $dis['id_cliente'], $obraId);
mysqli_stmt_execute($stmt);

echo json_encode(['success' => true]);
