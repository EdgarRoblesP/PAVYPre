<?php
/**
 * Devuelve todos los CLIENTES (sin contraseña).
 * Uso exclusivo del portal Admin.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_admin.php';
header('Content-Type: application/json');

$rows = $pdo->query(
    'SELECT id_cliente AS id, nombre, telefono, email
       FROM CLIENTES
      ORDER BY nombre'
)->fetchAll();

echo json_encode($rows);
