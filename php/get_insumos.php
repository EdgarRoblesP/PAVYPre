<?php
/**
 * Devuelve pv_insumos y pv_servicios como un único catálogo.
 * Uso exclusivo del portal Admin.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link   = Conectarse();
$result = mysqli_query($link,
    'SELECT i.id_insumo      AS id,
            i.tipo_material  AS nombre,
            "Insumo"         AS tipo,
            i.proveedor_id,
            p.nombre         AS proveedor,
            i.costo_unitario AS costoUnitario
       FROM pv_insumos i
       JOIN pv_proveedores p ON i.proveedor_id = p.id
     UNION ALL
     SELECT s.id_servicio,
            s.tipo_traslado,
            "Servicio",
            s.proveedor_id,
            p2.nombre,
            s.costo_kilometro
       FROM pv_servicios s
       JOIN pv_proveedores p2 ON s.proveedor_id = p2.id
     ORDER BY tipo, nombre'
);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$insumos = array_map(function ($r) {
    return [
        'id'            => $r['id'],
        'nombre'        => $r['nombre'],
        'tipo'          => $r['tipo'],
        'proveedor_id'  => (int)$r['proveedor_id'],
        'proveedor'     => $r['proveedor'],
        'costoUnitario' => (float)$r['costoUnitario'],
    ];
}, $rows);

echo json_encode($insumos);
