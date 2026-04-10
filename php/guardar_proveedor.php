<?php
/**
 * Guarda (INSERT o UPDATE) un proveedor.
 * POST: id (vacío = nuevo), nombre, telefono, email
 * Nota: el esquema actual no tiene tabla PROVEEDORES independiente.
 * Este archivo queda listo para cuando se agregue dicha tabla.
 */
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$id       = trim($_POST['id'] ?? '');
$nombre   = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email    = trim($_POST['email'] ?? '');

if (!$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre del proveedor es obligatorio.']);
    exit;
}

// TODO: reemplazar con INSERT/UPDATE a tabla PROVEEDORES cuando exista en el esquema.
// Por ahora devolvemos éxito para no romper el flujo del frontend.
echo json_encode(['success' => true, 'nota' => 'Tabla PROVEEDORES pendiente de crear en el esquema.']);
