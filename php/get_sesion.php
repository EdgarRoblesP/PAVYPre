<?php
/**
 * Devuelve los datos de la sesión activa.
 */
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sin sesión activa.']);
    exit;
}

$role  = $_SESSION['user_role'];
$id    = $_SESSION['user_id']   ?? null;
$name  = $_SESSION['user_name'] ?? '';
$email = '';

require_once __DIR__ . '/db.php';
$link = Conectarse();

if ($role === 'admin' || $role === 'colaborador') {
    $s = mysqli_prepare($link, 'SELECT email FROM pv_empleados WHERE id_empleado = ? LIMIT 1');
    mysqli_stmt_bind_param($s, 's', $id);
    mysqli_stmt_execute($s);
    $row   = stmt_row($s);
    $email = $row ? ($row['email'] ?? '') : '';
} elseif ($role === 'cliente') {
    $s = mysqli_prepare($link, 'SELECT email FROM pv_clientes WHERE id_cliente = ? LIMIT 1');
    mysqli_stmt_bind_param($s, 's', $id);
    mysqli_stmt_execute($s);
    $row   = stmt_row($s);
    $email = $row ? ($row['email'] ?? '') : '';
}

echo json_encode([
    'role'  => $role,
    'id'    => $id,
    'name'  => $name,
    'email' => $email,
]);
