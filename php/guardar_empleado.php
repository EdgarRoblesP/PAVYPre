<?php
/**
 * Guarda (INSERT o UPDATE) un registro en pv_empleados.
 * POST: id (vacío = nuevo), nombre, puesto, salario, telefono, email, direccion
 */
ob_start();
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link = Conectarse();
if (!$link) {
    echo json_encode(['error' => 'No se pudo conectar a la base de datos.']);
    exit;
}
$id           = trim($_POST['id']            ?? '');
$nombre       = trim($_POST['nombre']        ?? '');
$puesto       = trim($_POST['puesto']        ?? '');
$salario      = (float)($_POST['salario']    ?? 0);
$telefono     = trim($_POST['telefono']      ?? '');
$email        = trim($_POST['email']         ?? '');
$direccion    = trim($_POST['direccion']     ?? '');
$contrasena   = trim($_POST['contrasena']    ?? '');
$idSupervisor = trim($_POST['id_supervisor'] ?? '') ?: null;

if (!$nombre || !$puesto) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y puesto son obligatorios.']);
    exit;
}

if ($salario < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'El salario no puede ser negativo.']);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'El formato del correo electrónico no es válido.']);
    exit;
}

if ($id) {
    $stmt = mysqli_prepare($link,
        'UPDATE pv_empleados SET nombre = ?, puesto = ?, salario = ?, telefono = ?, email = ?, direccion = ?, id_supervisor = ? WHERE id_empleado = ?'
    );
    if (!$stmt) { echo json_encode(['error' => 'prepare UPDATE: ' . mysqli_error($link)]); exit; }
    mysqli_stmt_bind_param($stmt, 'ssdsssss', $nombre, $puesto, $salario, $telefono, $email, $direccion, $idSupervisor, $id);
    if (!mysqli_stmt_execute($stmt)) { echo json_encode(['error' => 'execute UPDATE: ' . mysqli_stmt_error($stmt)]); exit; }
} else {
    if (strlen($contrasena) < 8) {
        echo json_encode(['error' => 'La contraseña es obligatoria (mínimo 8 caracteres).']);
        exit;
    }
    $nuevoId = generarId($link, 'pv_empleados', 'id_empleado', 'EMP');
    $hash    = password_hash($contrasena, PASSWORD_ARGON2ID);
    $stmt    = mysqli_prepare($link,
        'INSERT INTO pv_empleados (id_empleado, nombre, puesto, telefono, direccion, email, salario, contrasena, id_supervisor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    if (!$stmt) { echo json_encode(['error' => 'prepare INSERT: ' . mysqli_error($link)]); exit; }
    mysqli_stmt_bind_param($stmt, 'ssssssdss', $nuevoId, $nombre, $puesto, $telefono, $direccion, $email, $salario, $hash, $idSupervisor);
    if (!mysqli_stmt_execute($stmt)) { echo json_encode(['error' => 'execute INSERT: ' . mysqli_stmt_error($stmt)]); exit; }
}

echo json_encode(['success' => true]);
