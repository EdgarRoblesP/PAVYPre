<?php
/**
 * Cambia la contraseña del usuario autenticado.
 * POST: contrasena_actual, nueva_contrasena, confirmar_contrasena
 * Roles válidos: colaborador, cliente
 */
session_start();
header('Content-Type: application/json');

$role = $_SESSION['user_role'] ?? '';
$id   = $_SESSION['user_id']   ?? '';

if (!$role || !$id || $role === 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_admin.php';

$actual    = $_POST['contrasena_actual']    ?? '';
$nueva     = $_POST['nueva_contrasena']     ?? '';
$confirmar = $_POST['confirmar_contrasena'] ?? '';

// ── Validaciones ──────────────────────────────────────────────
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

// ── Verificar contraseña actual ───────────────────────────────
if ($role === 'colaborador') {
    $stmt = $pdo->prepare('SELECT contrasena FROM EMPLEADOS WHERE id_empleado = ? LIMIT 1');
} else {
    $stmt = $pdo->prepare('SELECT contrasena FROM CLIENTES WHERE id_cliente = ? LIMIT 1');
}
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row || !password_verify($actual, $row['contrasena'])) {
    http_response_code(401);
    echo json_encode(['error' => 'La contraseña actual es incorrecta.']);
    exit;
}

// ── Actualizar con Argon2ID ───────────────────────────────────
$hash = password_hash($nueva, PASSWORD_ARGON2ID);

if ($role === 'colaborador') {
    $upd = $pdo->prepare('UPDATE EMPLEADOS SET contrasena = ? WHERE id_empleado = ?');
} else {
    $upd = $pdo->prepare('UPDATE CLIENTES  SET contrasena = ? WHERE id_cliente  = ?');
}
$upd->execute([$hash, $id]);

echo json_encode(['success' => true]);
