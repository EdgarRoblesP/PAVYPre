<?php
/**
 * Devuelve todos los cobros del cliente autenticado.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'cliente') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link      = Conectarse();
$idCliente = $_SESSION['user_id'];

$stmt = mysqli_prepare($link,
    'SELECT id_obra,
            fecha_pago,
            monto,
            tipo_pago AS tipoPago
       FROM pv_cobros
      WHERE id_cliente = ?
      ORDER BY id_obra, fecha_pago ASC'
);
mysqli_stmt_bind_param($stmt, 's', $idCliente);
mysqli_stmt_execute($stmt);
$rows = stmt_rows($stmt);

$pagos = array_map(function ($r) {
    return [
        'id_obra'  => $r['id_obra'],
        'fecha'    => substr($r['fecha_pago'], 0, 10),
        'monto'    => (float)$r['monto'],
        'tipoPago' => $r['tipoPago'],
    ];
}, $rows);

echo json_encode($pagos);
