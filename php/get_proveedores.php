<?php
/**
 * Devuelve proveedores únicos derivados de INSUMOS, SERVICIOS y HERRAMIENTAS.
 * No existe tabla PROVEEDORES en el esquema actual; los nombres se almacenan
 * como VARCHAR en cada tabla.
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
    'SELECT p.nombre,
            COALESCE(ins.total,  0) AS total_insumos,
            COALESCE(srv.total,  0) AS total_servicios,
            COALESCE(herr.total, 0) AS total_herramientas
       FROM (
           SELECT DISTINCT proveedor AS nombre FROM INSUMOS
           UNION
           SELECT DISTINCT proveedor FROM SERVICIOS
           UNION
           SELECT DISTINCT proveedor FROM HERRAMIENTAS
       ) p
       LEFT JOIN (SELECT proveedor, COUNT(*) AS total FROM INSUMOS      GROUP BY proveedor) ins  ON ins.proveedor  = p.nombre
       LEFT JOIN (SELECT proveedor, COUNT(*) AS total FROM SERVICIOS    GROUP BY proveedor) srv  ON srv.proveedor  = p.nombre
       LEFT JOIN (SELECT proveedor, COUNT(*) AS total FROM HERRAMIENTAS GROUP BY proveedor) herr ON herr.proveedor = p.nombre
      ORDER BY p.nombre'
)->fetchAll();

$proveedores = [];
foreach ($rows as $i => $r) {
    $proveedores[] = [
        'id'                => $i + 1,
        'nombre'            => $r['nombre'],
        'telefono'          => '',
        'email'             => '',
        'totalInsumos'      => (int)$r['total_insumos'],
        'totalServicios'    => (int)$r['total_servicios'],
        'totalHerramientas' => (int)$r['total_herramientas'],
    ];
}

echo json_encode($proveedores);
