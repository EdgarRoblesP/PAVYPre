<?php
/**
 * Guarda (INSERT o UPDATE) un proveedor en pv_proveedores.
 * POST: id (vacío = nuevo), nombre, telefono, email, direccion
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

$link      = Conectarse();
$id        = (int)($_POST['id']        ?? 0);
$nombre    = trim($_POST['nombre']    ?? '');
$telefono  = trim($_POST['telefono']  ?? '') ?: null;
$email     = trim($_POST['email']     ?? '') ?: null;
$direccion = trim($_POST['direccion'] ?? '') ?: null;

if (!$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre del proveedor es obligatorio.']);
    exit;
}

if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'El formato del correo electrónico no es válido.']);
    exit;
}

if ($id) {
    $stmt = mysqli_prepare($link,
        'UPDATE pv_proveedores SET nombre = ?, telefono = ?, email = ?, direccion = ? WHERE id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $nombre, $telefono, $email, $direccion, $id);
    mysqli_stmt_execute($stmt);
} else {
    $stmt = mysqli_prepare($link,
        'INSERT INTO pv_proveedores (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'ssss', $nombre, $telefono, $email, $direccion);
    mysqli_stmt_execute($stmt);
    $id = (int) mysqli_insert_id($link);
}

echo json_encode(['success' => true, 'id' => $id]);
