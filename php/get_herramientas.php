<?php
/**
 * Devuelve el catálogo de HERRAMIENTAS como JSON.
 * GET: q (opcional) — filtra por nombre o proveedor.
 *
 * Respuesta: array de objetos con:
 *   id_herramienta, nombre, proveedor, renta_semanal, imagen
 */
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare(
        'SELECT id_herramienta, nombre, proveedor, renta_semanal, imagen
           FROM HERRAMIENTAS
          WHERE nombre LIKE ? OR proveedor LIKE ?
          ORDER BY nombre ASC'
    );
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query(
        'SELECT id_herramienta, nombre, proveedor, renta_semanal, imagen
           FROM HERRAMIENTAS
          ORDER BY nombre ASC'
    );
}

$herramientas = $stmt->fetchAll();

// Normalizar tipos para el front-end
foreach ($herramientas as &$h) {
    $h['renta_semanal'] = (float)$h['renta_semanal'];
    // imagen puede ser NULL; dejarlo como null para que JS use el placeholder
}
unset($h);

echo json_encode($herramientas);
