<?php
/**
 * Cambia la contraseña del usuario autenticado.
 * POST: contrasena_actual, nueva_contrasena, confirmar_contrasena
 * Roles válidos: admin, colaborador, cliente
 */
session_start();
header('Content-Type: application/json');

$role = $_SESSION['user_role'] ?? '';
$id   = $_SESSION['user_id']   ?? '';

if (!$role || !$id) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db.php';

$actual    = $_POST['contrasena_actual']    ?? '';
$nueva     = $_POST['nueva_contrasena']     ?? '';
$confirmar = $_POST['confirmar_contrasena'] ?? '';

if (!$actual || !$nueva || !$confirmar) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son obligatorios.']);
    exit;
}

if (strlen($nueva) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'La nueva contraseña debe tener al menos 8 caracteres.']);
    exit;
}

if ($nueva !== $confirmar) {
    http_response_code(400);
    echo json_encode(['error' => 'La nueva contraseña y la confirmación no coinciden.']);
    exit;
}

$link = Conectarse();

// ── Verificar contraseña actual ───────────────────────────────
if ($role === 'admin' || $role === 'colaborador') {
    $stmt = mysqli_prepare($link, 'SELECT contrasena FROM pv_empleados WHERE id_empleado = ? LIMIT 1');
} else {
    $stmt = mysqli_prepare($link, 'SELECT contrasena FROM pv_clientes WHERE id_cliente = ? LIMIT 1');
}
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$row = stmt_row($stmt);

if (!$row || !password_verify($actual, $row['contrasena'])) {
    http_response_code(401);
    echo json_encode(['error' => 'La contraseña actual es incorrecta.']);
    exit;
}

// ── Actualizar con Argon2ID ───────────────────────────────────
$hash = password_hash($nueva, PASSWORD_ARGON2ID);

if ($role === 'admin' || $role === 'colaborador') {
    $upd = mysqli_prepare($link, 'UPDATE pv_empleados SET contrasena = ? WHERE id_empleado = ?');
} else {
    $upd = mysqli_prepare($link, 'UPDATE pv_clientes  SET contrasena = ? WHERE id_cliente  = ?');
}
mysqli_stmt_bind_param($upd, 'ss', $hash, $id);
mysqli_stmt_execute($upd);

echo json_encode(['success' => true]);
