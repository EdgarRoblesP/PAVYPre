<?php
/**
 * Devuelve el catálogo de pv_herramientas como JSON.
 * GET: q (opcional) — filtra por nombre o proveedor.
 *
 * Respuesta: array de objetos con:
 *   id_herramienta, nombre, proveedor_id, proveedor, renta_semanal, imagen
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link = Conectarse();
$q    = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = mysqli_prepare($link,
        'SELECT h.id_herramienta, h.nombre, h.proveedor_id, p.nombre AS proveedor,
                h.renta_semanal, h.imagen
           FROM pv_herramientas h
           JOIN pv_proveedores p ON h.proveedor_id = p.id
          WHERE h.nombre LIKE ? OR p.nombre LIKE ?
          ORDER BY h.nombre ASC'
    );
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $herramientas = stmt_rows($stmt);
} else {
    $result = mysqli_query($link,
        'SELECT h.id_herramienta, h.nombre, h.proveedor_id, p.nombre AS proveedor,
                h.renta_semanal, h.imagen
           FROM pv_herramientas h
           JOIN pv_proveedores p ON h.proveedor_id = p.id
          ORDER BY h.nombre ASC'
    );
    $herramientas = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

foreach ($herramientas as &$h) {
    $h['renta_semanal'] = (float)$h['renta_semanal'];
}
unset($h);

echo json_encode($herramientas);
