<?php
/**
 * Devuelve todos los pv_clientes (sin contraseña).
 * Uso exclusivo del portal Admin.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link   = Conectarse();
$result = mysqli_query($link,
    'SELECT id_cliente AS id, nombre, telefono, email
       FROM pv_clientes
      ORDER BY nombre'
);

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
