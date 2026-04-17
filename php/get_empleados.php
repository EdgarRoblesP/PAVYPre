<?php
/**
 * Devuelve todos los PV_EMPLEADOS con la última obra asignada.
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
    'SELECT e.id_empleado AS id,
            e.nombre,
            e.puesto,
            e.telefono,
            e.email,
            e.direccion,
            e.salario,
            e.id_supervisor,
            s.nombre AS nombre_supervisor,
            (SELECT te.id_obra
               FROM PV_TRABAJOS_EMPLEADOS te
              WHERE te.id_empleado = e.id_empleado
              ORDER BY te.fecha_adicion DESC
              LIMIT 1) AS obraAsignadaId
       FROM PV_EMPLEADOS e
       LEFT JOIN PV_EMPLEADOS s ON e.id_supervisor = s.id_empleado
      ORDER BY e.nombre'
);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$empleados = array_map(function ($e) {
    return [
        'id'               => $e['id'],
        'nombre'           => $e['nombre'],
        'puesto'           => $e['puesto'],
        'telefono'         => $e['telefono'],
        'email'            => $e['email'],
        'direccion'        => $e['direccion'],
        'salario'          => (float)$e['salario'],
        'supervisorId'     => $e['id_supervisor'],
        'supervisorNombre' => $e['nombre_supervisor'],
        'obraAsignadaId'   => $e['obraAsignadaId'],
    ];
}, $rows);

echo json_encode($empleados);
