<?php
/**
 * Guarda (INSERT o UPDATE) un registro en pv_empleados.
 * POST: id (vacío = nuevo), nombre, puesto, salario, telefono, email, direccion
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link         = Conectarse();
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

if ($id) {
    $stmt = mysqli_prepare($link,
        'UPDATE pv_empleados SET nombre = ?, puesto = ?, salario = ?, telefono = ?, email = ?, direccion = ?, id_supervisor = ? WHERE id_empleado = ?'
    );
    mysqli_stmt_bind_param($stmt, 'ssdsssss', $nombre, $puesto, $salario, $telefono, $email, $direccion, $idSupervisor, $id);
    mysqli_stmt_execute($stmt);
} else {
    if (strlen($contrasena) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña es obligatoria (mínimo 8 caracteres).']);
        exit;
    }
    $t1 = 'pv_empleados'; $t2 = 'id_empleado'; $t3 = 'EMP';
    $stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
    mysqli_stmt_bind_param($stmtSp, 'sss', $t1, $t2, $t3);
    mysqli_stmt_execute($stmtSp);
    mysqli_stmt_close($stmtSp);
    $res     = mysqli_query($link, 'SELECT @nuevo_id');
    $nuevoId = mysqli_fetch_row($res)[0];
    $hash    = password_hash($contrasena, PASSWORD_ARGON2ID);
    $stmt    = mysqli_prepare($link,
        'INSERT INTO pv_empleados (id_empleado, nombre, puesto, telefono, direccion, email, salario, contrasena, id_supervisor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'ssssssdss', $nuevoId, $nombre, $puesto, $telefono, $direccion, $email, $salario, $hash, $idSupervisor);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['success' => true]);
