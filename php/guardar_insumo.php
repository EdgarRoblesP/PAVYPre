<?php
/**
 * Guarda (INSERT o UPDATE) un registro en INSUMOS o SERVICIOS.
 * POST: id (vacío = nuevo), nombre, tipo, proveedor, costo_unitario
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$id        = trim($_POST['id']             ?? '');
$nombre    = trim($_POST['nombre']         ?? '');
$tipo      = trim($_POST['tipo']           ?? 'Insumo');
$proveedor = trim($_POST['proveedor']      ?? '');
$costo     = (float)($_POST['costo_unitario'] ?? 0);

if (!$nombre || !$proveedor) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y proveedor son obligatorios.']);
    exit;
}

if ($tipo === 'Servicio') {
    if ($id) {
        $stmt = $pdo->prepare('UPDATE SERVICIOS SET tipo_traslado = ?, costo_kilometro = ?, proveedor = ? WHERE id_servicio = ?');
        $stmt->execute([$nombre, $costo, $proveedor, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO SERVICIOS (id_servicio, costo_kilometro, proveedor, tipo_traslado) VALUES (?, ?, ?, ?)');
        $nuevoId = strtoupper(substr(uniqid(), -6));
        $stmt->execute([$nuevoId, $costo, $proveedor, $nombre]);
    }
} else {
    if ($id) {
        $stmt = $pdo->prepare('UPDATE INSUMOS SET tipo_material = ?, costo_unitario = ?, proveedor = ? WHERE id_insumo = ?');
        $stmt->execute([$nombre, $costo, $proveedor, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO INSUMOS (id_insumo, costo_unitario, proveedor, tipo_material) VALUES (?, ?, ?, ?)');
        $nuevoId = strtoupper(substr(uniqid(), -6));
        $stmt->execute([$nuevoId, $costo, $proveedor, $nombre]);
    }
}

echo json_encode(['success' => true]);
