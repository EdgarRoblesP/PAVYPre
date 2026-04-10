<?php
/**
 * Devuelve los datos de la sesión activa.
 * Usado por los portales para mostrar el nombre y email del usuario en el header/perfil.
 */
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sin sesión activa.']);
    exit;
}

$role = $_SESSION['user_role'];
$id   = $_SESSION['user_id']   ?? null;
$name = $_SESSION['user_name'] ?? '';
$email = '';

if ($role === 'colaborador' || $role === 'cliente') {
    require_once __DIR__ . '/db_admin.php';
    if ($role === 'colaborador') {
        $s = $pdo->prepare('SELECT email FROM EMPLEADOS WHERE id_empleado = ? LIMIT 1');
    } else {
        $s = $pdo->prepare('SELECT email FROM CLIENTES WHERE id_cliente = ? LIMIT 1');
    }
    $s->execute([$id]);
    $row   = $s->fetch();
    $email = $row ? ($row['email'] ?? '') : '';
}

echo json_encode([
    'role'  => $role,
    'id'    => $id,
    'name'  => $name,
    'email' => $email,
]);
