<?php
/**
 * Devuelve INSUMOS y SERVICIOS como un único catálogo.
 * Campos devueltos compatibles con el array `insumos` del portal Admin:
 *   id, nombre, tipo, proveedor, costoUnitario
 * Uso exclusivo del portal Admin.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_admin.php';
header('Content-Type: application/json');

$rows = $pdo->query(
    'SELECT id_insumo  AS id,
            tipo_material  AS nombre,
            "Insumo"       AS tipo,
            proveedor,
            costo_unitario AS costoUnitario
       FROM INSUMOS
     UNION ALL
     SELECT id_servicio,
            tipo_traslado,
            "Servicio",
            proveedor,
            costo_kilometro
       FROM SERVICIOS
     ORDER BY tipo, nombre'
)->fetchAll();

$insumos = array_map(function ($r) {
    return [
        'id'           => $r['id'],
        'nombre'       => $r['nombre'],
        'tipo'         => $r['tipo'],
        'proveedor'    => $r['proveedor'],
        'costoUnitario'=> (float)$r['costoUnitario'],
    ];
}, $rows);

echo json_encode($insumos);
