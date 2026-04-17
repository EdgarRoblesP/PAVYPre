<?php
/**
 * Devuelve todos los proveedores registrados en pv_proveedores.
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
    'SELECT p.id,
            p.nombre,
            COALESCE(p.telefono,  \'\') AS telefono,
            COALESCE(p.email,     \'\') AS email,
            COALESCE(p.direccion, \'\') AS direccion,
            COALESCE(ins.total,   0)   AS total_insumos,
            COALESCE(srv.total,   0)   AS total_servicios,
            COALESCE(herr.total,  0)   AS total_herramientas
       FROM pv_proveedores p
       LEFT JOIN (SELECT proveedor_id, COUNT(*) AS total FROM pv_insumos      GROUP BY proveedor_id) ins  ON ins.proveedor_id  = p.id
       LEFT JOIN (SELECT proveedor_id, COUNT(*) AS total FROM pv_servicios    GROUP BY proveedor_id) srv  ON srv.proveedor_id  = p.id
       LEFT JOIN (SELECT proveedor_id, COUNT(*) AS total FROM pv_herramientas GROUP BY proveedor_id) herr ON herr.proveedor_id = p.id
      ORDER BY p.nombre'
);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$proveedores = [];
foreach ($rows as $r) {
    $proveedores[] = [
        'id'                => (int)$r['id'],
        'nombre'            => $r['nombre'],
        'telefono'          => $r['telefono'],
        'email'             => $r['email'],
        'direccion'         => $r['direccion'],
        'totalInsumos'      => (int)$r['total_insumos'],
        'totalServicios'    => (int)$r['total_servicios'],
        'totalHerramientas' => (int)$r['total_herramientas'],
    ];
}

echo json_encode($proveedores);
