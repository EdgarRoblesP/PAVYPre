<?php
/**
 * Verifica si un token de recuperación es válido.
 * GET: token
 */
header('Content-Type: application/json');

$token = trim($_GET['token'] ?? '');

if (!$token || strlen($token) !== 64 || !ctype_xdigit($token)) {
    echo json_encode(['valido' => false, 'error' => 'Token inválido.']);
    exit;
}

require_once __DIR__ . '/db.php';
$link = Conectarse();

mysqli_query($link,
    'CREATE TABLE IF NOT EXISTS PV_RESET_TOKENS (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        email       VARCHAR(320) NOT NULL,
        token       CHAR(64)     NOT NULL,
        expires_at  DATETIME     NOT NULL,
        used        TINYINT(1)   NOT NULL DEFAULT 0,
        created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_token (token),
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

$stmt = mysqli_prepare($link,
    'SELECT id, expires_at, used FROM PV_RESET_TOKENS WHERE token = ? LIMIT 1'
);
mysqli_bind_param($stmt, 's', $token);
mysqli_stmt_execute($stmt);
$row = stmt_row($stmt);

if (!$row) {
    echo json_encode(['valido' => false, 'error' => 'El enlace no es válido o ya fue utilizado.']);
    exit;
}

if ($row['used']) {
    echo json_encode(['valido' => false, 'error' => 'Este enlace ya fue utilizado. Solicita uno nuevo si es necesario.']);
    exit;
}

if (strtotime($row['expires_at']) < time()) {
    echo json_encode(['valido' => false, 'error' => 'El enlace ha expirado. Solicita uno nuevo.']);
    exit;
}

echo json_encode(['valido' => true]);
