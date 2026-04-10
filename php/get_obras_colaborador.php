<?php
/**
 * Devuelve las obras en que participó el colaborador autenticado.
 * Usa la conexión de solo lectura (usuario: colaborador).
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'colaborador') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_colaborador.php';
header('Content-Type: application/json');

$idEmpleado = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT o.id_obra,
            o.ubicacion,
            te.fecha_adicion  AS fechaInicio,
            te.fecha_termino  AS fechaTermino,
            e.salario         AS salarioSemanal,
            e.puesto
       FROM TRABAJOS_EMPLEADOS te
       JOIN OBRAS     o ON te.id_obra     = o.id_obra
       JOIN EMPLEADOS e ON te.id_empleado = e.id_empleado
      WHERE te.id_empleado = ?
      ORDER BY te.fecha_adicion DESC'
);
$stmt->execute([$idEmpleado]);
$rows = $stmt->fetchAll();

$hoy = date('Y-m-d');

$obras = array_map(function ($r) use ($hoy) {
    $fin = $r['fechaTermino'] ? substr($r['fechaTermino'], 0, 10) : $hoy;
    return [
        'id'             => $r['id_obra'],
        'nombreObra'     => $r['ubicacion'],
        'fechaInicio'    => $r['fechaInicio'] ? substr($r['fechaInicio'], 0, 10) : '',
        'fechaTermino'   => $fin,
        'salarioSemanal' => (float)$r['salarioSemanal'],
        'puesto'         => $r['puesto'],
    ];
}, $rows);

echo json_encode($obras);
