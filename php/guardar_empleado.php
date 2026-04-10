<?php
/**
 * Guarda (INSERT o UPDATE) un registro en EMPLEADOS.
 * POST: id (vacío = nuevo), nombre, puesto, salario, telefono, email, direccion
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$id         = trim($_POST['id'] ?? '');
$nombre     = trim($_POST['nombre'] ?? '');
$puesto     = trim($_POST['puesto'] ?? '');
$salario    = (float)($_POST['salario'] ?? 0);
$telefono   = trim($_POST['telefono'] ?? '');
$email      = trim($_POST['email'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

if (!$nombre || !$puesto) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y puesto son obligatorios.']);
    exit;
}

if ($id) {
    $stmt = $pdo->prepare('UPDATE EMPLEADOS SET nombre = ?, puesto = ?, salario = ?, telefono = ?, email = ?, direccion = ? WHERE id_empleado = ?');
    $stmt->execute([$nombre, $puesto, $salario, $telefono, $email, $direccion, $id]);
} else {
    if (strlen($contrasena) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña es obligatoria (mínimo 8 caracteres).']);
        exit;
    }
    $nuevoId = strtoupper(substr(uniqid(), -6));
    $hash    = password_hash($contrasena, PASSWORD_ARGON2ID);
    $stmt = $pdo->prepare('INSERT INTO EMPLEADOS (id_empleado, nombre, puesto, telefono, direccion, email, salario, contrasena) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$nuevoId, $nombre, $puesto, $telefono, $direccion, $email, $salario, $hash]);
}

echo json_encode(['success' => true]);
