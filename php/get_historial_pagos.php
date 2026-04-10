<?php
/**
 * Devuelve todos los cobros del cliente autenticado.
 * Filtrado por id_cliente de sesión; la clave foránea de COBROS
 * garantiza que cada cobro pertenece a una obra del cliente.
 * Usa la conexión de solo lectura (usuario: cliente).
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'cliente') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_cliente.php';
header('Content-Type: application/json');

$idCliente = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT id_obra,
            fecha_pago,
            monto,
            tipo_pago AS tipoPago
       FROM COBROS
      WHERE id_cliente = ?
      ORDER BY id_obra, fecha_pago ASC'
);
$stmt->execute([$idCliente]);
$rows = $stmt->fetchAll();

$pagos = array_map(function ($r) {
    return [
        'id_obra'  => $r['id_obra'],
        'fecha'    => substr($r['fecha_pago'], 0, 10),
        'monto'    => (float)$r['monto'],
        'tipoPago' => $r['tipoPago'],
    ];
}, $rows);

echo json_encode($pagos);
