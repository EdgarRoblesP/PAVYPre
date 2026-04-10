<?php
/**
 * Guarda (INSERT o UPDATE) un registro en CLIENTES.
 * POST: id (vacío = nuevo), nombre, telefono, email
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$id         = trim($_POST['id'] ?? '');
$nombre     = trim($_POST['nombre'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$email      = trim($_POST['email'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

if (!$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre del cliente es obligatorio.']);
    exit;
}

if ($id) {
    $stmt = $pdo->prepare('UPDATE CLIENTES SET nombre = ?, telefono = ?, email = ? WHERE id_cliente = ?');
    $stmt->execute([$nombre, $telefono, $email, $id]);
} else {
    if (strlen($contrasena) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña es obligatoria (mínimo 8 caracteres).']);
        exit;
    }
    $nuevoId = strtoupper(substr(uniqid(), -6));
    $hash    = password_hash($contrasena, PASSWORD_ARGON2ID);
    $stmt = $pdo->prepare('INSERT INTO CLIENTES (id_cliente, nombre, telefono, direccion, email, contrasena) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$nuevoId, $nombre, $telefono, '', $email, $hash]);
}

echo json_encode(['success' => true]);
