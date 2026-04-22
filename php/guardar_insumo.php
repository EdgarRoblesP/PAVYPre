<?php
/**
 * Guarda (INSERT o UPDATE) un registro en pv_insumos o pv_servicios.
 * POST: id (vacío = nuevo), nombre, tipo, proveedor, costo_unitario
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link            = Conectarse();
$id              = trim($_POST['id']              ?? '');
$nombre          = trim($_POST['nombre']          ?? '');
$tipo            = trim($_POST['tipo']            ?? 'Insumo');
$proveedorNombre = trim($_POST['proveedor']       ?? '');
$costo           = (float)($_POST['costo_unitario'] ?? 0);

if (!$nombre || !$proveedorNombre) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y proveedor son obligatorios.']);
    exit;
}

if (!in_array($tipo, ['Insumo', 'Servicio'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'El tipo debe ser Insumo o Servicio.']);
    exit;
}

if ($costo < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'El costo unitario no puede ser negativo.']);
    exit;
}

// Obtener o crear el proveedor en pv_proveedores
$stmtProv = mysqli_prepare($link, 'SELECT id FROM pv_proveedores WHERE nombre = ? LIMIT 1');
mysqli_stmt_bind_param($stmtProv, 's', $proveedorNombre);
mysqli_stmt_execute($stmtProv);
$provRow = stmt_row($stmtProv);

if ($provRow) {
    $proveedorId = (int)$provRow['id'];
} else {
    $stmtIns = mysqli_prepare($link, 'INSERT INTO pv_proveedores (nombre) VALUES (?)');
    mysqli_stmt_bind_param($stmtIns, 's', $proveedorNombre);
    mysqli_stmt_execute($stmtIns);
    $proveedorId = (int) mysqli_insert_id($link);
}

if ($tipo === 'Servicio') {
    if ($id) {
        $stmt = mysqli_prepare($link, 'UPDATE pv_servicios SET tipo_traslado = ?, costo_kilometro = ?, proveedor_id = ? WHERE id_servicio = ?');
        mysqli_stmt_bind_param($stmt, 'sdis', $nombre, $costo, $proveedorId, $id);
        mysqli_stmt_execute($stmt);
    } else {
        $nuevoId = generarId($link, 'pv_servicios', 'id_servicio', 'SRV');
        $stmt    = mysqli_prepare($link, 'INSERT INTO pv_servicios (id_servicio, costo_kilometro, proveedor_id, tipo_traslado) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sdis', $nuevoId, $costo, $proveedorId, $nombre);
        mysqli_stmt_execute($stmt);
    }
} else {
    if ($id) {
        $stmt = mysqli_prepare($link, 'UPDATE pv_insumos SET tipo_material = ?, costo_unitario = ?, proveedor_id = ? WHERE id_insumo = ?');
        mysqli_stmt_bind_param($stmt, 'sdis', $nombre, $costo, $proveedorId, $id);
        mysqli_stmt_execute($stmt);
    } else {
        $nuevoId = generarId($link, 'pv_insumos', 'id_insumo', 'INS');
        $stmt    = mysqli_prepare($link, 'INSERT INTO pv_insumos (id_insumo, costo_unitario, proveedor_id, tipo_material) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sdis', $nuevoId, $costo, $proveedorId, $nombre);
        mysqli_stmt_execute($stmt);
    }
}

echo json_encode(['success' => true]);
