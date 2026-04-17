<?php
/**
 * Guarda (INSERT o UPDATE) un registro en PV_CLIENTES.
 * POST: id (vacío = nuevo), nombre, telefono, email
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link       = Conectarse();
$id         = trim($_POST['id']         ?? '');
$nombre     = trim($_POST['nombre']     ?? '');
$telefono   = trim($_POST['telefono']   ?? '');
$email      = trim($_POST['email']      ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

if (!$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre del cliente es obligatorio.']);
    exit;
}

if ($id) {
    $stmt = mysqli_prepare($link, 'UPDATE PV_CLIENTES SET nombre = ?, telefono = ?, email = ? WHERE id_cliente = ?');
    mysqli_bind_param($stmt, 'ssss', $nombre, $telefono, $email, $id);
    mysqli_stmt_execute($stmt);
} else {
    if (strlen($contrasena) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña es obligatoria (mínimo 8 caracteres).']);
        exit;
    }
    $t1 = 'PV_CLIENTES'; $t2 = 'id_cliente'; $t3 = 'CLI';
    $stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
    mysqli_bind_param($stmtSp, 'sss', $t1, $t2, $t3);
    mysqli_stmt_execute($stmtSp);
    mysqli_stmt_close($stmtSp);
    $res     = mysqli_query($link, 'SELECT @nuevo_id');
    $nuevoId = mysqli_fetch_row($res)[0];
    $hash    = password_hash($contrasena, PASSWORD_ARGON2ID);
    $dir     = '';
    $stmt    = mysqli_prepare($link, 'INSERT INTO PV_CLIENTES (id_cliente, nombre, telefono, direccion, email, contrasena) VALUES (?, ?, ?, ?, ?, ?)');
    mysqli_bind_param($stmt, 'ssssss', $nuevoId, $nombre, $telefono, $dir, $email, $hash);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['success' => true]);
