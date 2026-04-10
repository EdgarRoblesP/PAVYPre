<?php
/**
 * Devuelve todos los EMPLEADOS con la última obra asignada.
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
    'SELECT e.id_empleado AS id,
            e.nombre,
            e.puesto,
            e.telefono,
            e.email,
            e.direccion,
            e.salario,
            (SELECT te.id_obra
               FROM TRABAJOS_EMPLEADOS te
              WHERE te.id_empleado = e.id_empleado
              ORDER BY te.fecha_adicion DESC
              LIMIT 1) AS obraAsignadaId
       FROM EMPLEADOS e
      ORDER BY e.nombre'
)->fetchAll();

$empleados = array_map(function ($e) {
    return [
        'id'            => $e['id'],
        'nombre'        => $e['nombre'],
        'puesto'        => $e['puesto'],
        'telefono'      => $e['telefono'],
        'email'         => $e['email'],
        'direccion'     => $e['direccion'],
        'salario'       => (float)$e['salario'],
        'obraAsignadaId'=> $e['obraAsignadaId'],
    ];
}, $rows);

echo json_encode($empleados);
