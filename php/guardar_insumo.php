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

// Obtener o crear el proveedor en pv_proveedores
$stmtProv = mysqli_prepare($link, 'SELECT id FROM pv_proveedores WHERE nombre = ? LIMIT 1');
mysqli_bind_param($stmtProv, 's', $proveedorNombre);
mysqli_stmt_execute($stmtProv);
$provRow = stmt_row($stmtProv);

if ($provRow) {
    $proveedorId = (int)$provRow['id'];
} else {
    $stmtIns = mysqli_prepare($link, 'INSERT INTO pv_proveedores (nombre) VALUES (?)');
    mysqli_bind_param($stmtIns, 's', $proveedorNombre);
    mysqli_stmt_execute($stmtIns);
    $proveedorId = (int) mysqli_insert_id($link);
}

if ($tipo === 'Servicio') {
    if ($id) {
        $stmt = mysqli_prepare($link, 'UPDATE pv_servicios SET tipo_traslado = ?, costo_kilometro = ?, proveedor_id = ? WHERE id_servicio = ?');
        mysqli_bind_param($stmt, 'sdis', $nombre, $costo, $proveedorId, $id);
        mysqli_stmt_execute($stmt);
    } else {
        $t1 = 'pv_servicios'; $t2 = 'id_servicio'; $t3 = 'SRV';
        $stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
        mysqli_bind_param($stmtSp, 'sss', $t1, $t2, $t3);
        mysqli_stmt_execute($stmtSp);
        mysqli_stmt_close($stmtSp);
        $res     = mysqli_query($link, 'SELECT @nuevo_id');
        $nuevoId = mysqli_fetch_row($res)[0];
        $stmt    = mysqli_prepare($link, 'INSERT INTO pv_servicios (id_servicio, costo_kilometro, proveedor_id, tipo_traslado) VALUES (?, ?, ?, ?)');
        mysqli_bind_param($stmt, 'sdis', $nuevoId, $costo, $proveedorId, $nombre);
        mysqli_stmt_execute($stmt);
    }
} else {
    if ($id) {
        $stmt = mysqli_prepare($link, 'UPDATE pv_insumos SET tipo_material = ?, costo_unitario = ?, proveedor_id = ? WHERE id_insumo = ?');
        mysqli_bind_param($stmt, 'sdis', $nombre, $costo, $proveedorId, $id);
        mysqli_stmt_execute($stmt);
    } else {
        $t1 = 'pv_insumos'; $t2 = 'id_insumo'; $t3 = 'INS';
        $stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
        mysqli_bind_param($stmtSp, 'sss', $t1, $t2, $t3);
        mysqli_stmt_execute($stmtSp);
        mysqli_stmt_close($stmtSp);
        $res     = mysqli_query($link, 'SELECT @nuevo_id');
        $nuevoId = mysqli_fetch_row($res)[0];
        $stmt    = mysqli_prepare($link, 'INSERT INTO pv_insumos (id_insumo, costo_unitario, proveedor_id, tipo_material) VALUES (?, ?, ?, ?)');
        mysqli_bind_param($stmt, 'sdis', $nuevoId, $costo, $proveedorId, $nombre);
        mysqli_stmt_execute($stmt);
    }
}

echo json_encode(['success' => true]);
