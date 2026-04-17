<?php
/**
 * Restablece la contraseña usando un token de recuperación válido.
 * POST: token, nueva_contrasena, confirmar_contrasena
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$token     = trim($_POST['token']           ?? '');
$nueva     = $_POST['nueva_contrasena']     ?? '';
$confirmar = $_POST['confirmar_contrasena'] ?? '';

if (!$token || strlen($token) !== 64 || !ctype_xdigit($token)) {
    http_response_code(400);
    echo json_encode(['error' => 'Token inválido.']);
    exit;
}

if (!$nueva || !$confirmar) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son obligatorios.']);
    exit;
}

if (strlen($nueva) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres.']);
    exit;
}

if ($nueva !== $confirmar) {
    http_response_code(400);
    echo json_encode(['error' => 'Las contraseñas no coinciden.']);
    exit;
}

require_once __DIR__ . '/db.php';
$link = Conectarse();

// ── Verificar token ──────────────────────────────────────────────────────────
$stmt = mysqli_prepare($link,
    'SELECT id, email, expires_at, used FROM PV_RESET_TOKENS WHERE token = ? LIMIT 1'
);
mysqli_bind_param($stmt, 's', $token);
mysqli_stmt_execute($stmt);
$row = stmt_row($stmt);

if (!$row) {
    http_response_code(400);
    echo json_encode(['error' => 'El enlace no es válido. Solicita uno nuevo.']);
    exit;
}

if ($row['used']) {
    http_response_code(400);
    echo json_encode(['error' => 'Este enlace ya fue utilizado. Solicita uno nuevo.']);
    exit;
}

if (strtotime($row['expires_at']) < time()) {
    http_response_code(400);
    echo json_encode(['error' => 'El enlace ha expirado. Solicita uno nuevo.']);
    exit;
}

$email = $row['email'];
$hash  = password_hash($nueva, PASSWORD_ARGON2ID);

// ── Actualizar contraseña en PV_EMPLEADOS o PV_CLIENTES ─────────────────────
$actualizado = false;

$upd = mysqli_prepare($link, 'UPDATE PV_EMPLEADOS SET contrasena = ? WHERE email = ?');
mysqli_bind_param($upd, 'ss', $hash, $email);
mysqli_stmt_execute($upd);
if (mysqli_stmt_affected_rows($upd) > 0) {
    $actualizado = true;
}

if (!$actualizado) {
    $upd = mysqli_prepare($link, 'UPDATE PV_CLIENTES SET contrasena = ? WHERE email = ?');
    mysqli_bind_param($upd, 'ss', $hash, $email);
    mysqli_stmt_execute($upd);
    if (mysqli_stmt_affected_rows($upd) > 0) {
        $actualizado = true;
    }
}

if (!$actualizado) {
    http_response_code(400);
    echo json_encode(['error' => 'No se encontró el usuario asociado a este enlace.']);
    exit;
}

// ── Marcar token como usado ──────────────────────────────────────────────────
$tokenId = $row['id'];
$upd     = mysqli_prepare($link, 'UPDATE PV_RESET_TOKENS SET used = 1 WHERE id = ?');
mysqli_bind_param($upd, 'i', $tokenId);
mysqli_stmt_execute($upd);

echo json_encode(['success' => true]);
