<?php
/**
 * Devuelve todas las OBRAS con sus sub-arrays anidados:
 *   empleados, recursos (insumos + servicios), pagos.
 * Uso exclusivo del portal Admin (usuario DB: admin).
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_admin.php';
header('Content-Type: application/json');

// ── 1. Lista base de obras ───────────────────────────────────
$obras = $pdo->query(
    'SELECT id_obra, ubicacion, fecha_inicio, fecha_fin,
            presupuesto_inicial, utilidad_neta,
            gasto_empleados, gasto_insumos, gasto_servicios, gasto_herramientas
       FROM OBRAS
      ORDER BY fecha_inicio DESC'
)->fetchAll();

// ── 2. Enriquecer cada obra con sus sub-tablas ───────────────
$stmtEmpl = $pdo->prepare(
    'SELECT e.nombre      AS nombreEmpleado,
            e.puesto,
            te.fecha_adicion  AS fechaInicio,
            te.fecha_termino  AS fechaTermino,
            ROUND(
                e.salario * (DATEDIFF(COALESCE(te.fecha_termino, o.fecha_fin, NOW()), te.fecha_adicion) / 7),
                2
            ) AS costoTotal
       FROM TRABAJOS_EMPLEADOS te
       JOIN EMPLEADOS e ON te.id_empleado = e.id_empleado
       JOIN OBRAS      o ON te.id_obra    = o.id_obra
      WHERE te.id_obra = ?
      ORDER BY te.fecha_adicion'
);

$stmtHerram = $pdo->prepare(
    'SELECT uh.id_herramienta                                       AS catalogId,
            h.nombre,
            h.proveedor,
            h.renta_semanal,
            uh.fecha_adicion                                        AS fechaInicio,
            uh.fecha_termino                                        AS fechaTermino,
            uh.cantidad,
            GREATEST(DATEDIFF(COALESCE(uh.fecha_termino, o.fecha_fin, NOW()), uh.fecha_adicion), 0)
                                                                    AS dias,
            ROUND(
                h.renta_semanal
                * GREATEST(DATEDIFF(COALESCE(uh.fecha_termino, o.fecha_fin, NOW()), uh.fecha_adicion) / 7, 0)
                * uh.cantidad,
                2
            )                                                       AS subtotal
       FROM USOS_HERRAMIENTAS uh
       JOIN HERRAMIENTAS h ON uh.id_herramienta = h.id_herramienta
       JOIN OBRAS         o ON uh.id_obra        = o.id_obra
      WHERE uh.id_obra = ?
      ORDER BY h.nombre'
);

$stmtInsumos = $pdo->prepare(
    'SELECT i.id_insumo                                          AS catalogId,
            i.tipo_material                                      AS nombre,
            i.proveedor,
            i.costo_unitario,
            SUM(ei.cantidad)                                     AS cantidadTotal,
            ROUND(i.costo_unitario * SUM(ei.cantidad), 2)        AS subtotal
       FROM EMPLEOS_INSUMOS ei
       JOIN INSUMOS i ON ei.id_insumo = i.id_insumo
      WHERE ei.id_obra = ?
      GROUP BY i.id_insumo, i.tipo_material, i.proveedor, i.costo_unitario
      ORDER BY i.tipo_material'
);

$stmtServicios = $pdo->prepare(
    'SELECT s.id_servicio                                        AS catalogId,
            s.tipo_traslado                                      AS nombre,
            s.proveedor,
            s.costo_kilometro,
            ROUND(SUM(rs.kilometraje), 2)                        AS kilometrajeTotal,
            ROUND(s.costo_kilometro * SUM(rs.kilometraje), 2)   AS subtotal
       FROM REQUERIMIENTOS_SERVICIOS rs
       JOIN SERVICIOS s ON rs.id_servicio = s.id_servicio
      WHERE rs.id_obra = ?
      GROUP BY s.id_servicio, s.tipo_traslado, s.proveedor, s.costo_kilometro
      ORDER BY s.tipo_traslado'
);

$stmtPagos = $pdo->prepare(
    'SELECT fecha_pago  AS fechaPago,
            monto,
            tipo_pago   AS tipoPago
       FROM COBROS
      WHERE id_obra = ?
      ORDER BY fecha_pago'
);

$resultado = [];
foreach ($obras as $o) {
    $id = $o['id_obra'];

    // Empleados
    $stmtEmpl->execute([$id]);
    $empleados = array_map(function ($e) {
        return [
            'nombreEmpleado' => $e['nombreEmpleado'],
            'puesto'         => $e['puesto'],
            'fechaInicio'    => $e['fechaInicio'] ? substr($e['fechaInicio'], 0, 10) : '',
            'fechaTermino'   => $e['fechaTermino'] ? substr($e['fechaTermino'], 0, 10) : '',
            'costoTotal'     => (float)$e['costoTotal'],
        ];
    }, $stmtEmpl->fetchAll());

    // Recursos: herramientas
    $stmtHerram->execute([$id]);
    $herramientasObra = array_map(function ($h) {
        return [
            'catalogId'       => $h['catalogId'],
            'nombre'          => $h['nombre'],
            'proveedor'       => $h['proveedor'],
            'rentaSemanal'    => (float)$h['renta_semanal'],
            'fechaInicio'     => $h['fechaInicio']  ? substr($h['fechaInicio'],  0, 10) : '',
            'fechaTermino'    => $h['fechaTermino'] ? substr($h['fechaTermino'], 0, 10) : '',
            'dias'            => (int)$h['dias'],
            'cantidadUnidades'=> (int)$h['cantidad'],
            'subtotal'        => (float)$h['subtotal'],
        ];
    }, $stmtHerram->fetchAll());

    // Recursos: insumos
    $stmtInsumos->execute([$id]);
    $insumos = array_map(function ($r) {
        return [
            'catalogId'    => $r['catalogId'],
            'nombre'       => $r['nombre'],
            'proveedor'    => $r['proveedor'],
            'costoUnitario'=> (float)$r['costo_unitario'],
            'cantidadTotal'=> (float)$r['cantidadTotal'],
            'subtotal'     => (float)$r['subtotal'],
        ];
    }, $stmtInsumos->fetchAll());

    // Recursos: servicios
    $stmtServicios->execute([$id]);
    $servicios = array_map(function ($r) {
        return [
            'catalogId'      => $r['catalogId'],
            'nombre'         => $r['nombre'],
            'proveedor'      => $r['proveedor'],
            'costoKm'        => (float)$r['costo_kilometro'],
            'kilometrajeTotal'=> (float)$r['kilometrajeTotal'],
            'subtotal'       => (float)$r['subtotal'],
        ];
    }, $stmtServicios->fetchAll());

    // Pagos
    $stmtPagos->execute([$id]);
    $pagos = array_map(function ($p) {
        return [
            'fechaPago' => substr($p['fechaPago'], 0, 10),
            'monto'     => (float)$p['monto'],
            'tipoPago'  => $p['tipoPago'],
        ];
    }, $stmtPagos->fetchAll());

    // Costo final = suma dinámica de los 4 gastos almacenados en OBRAS
    $costoFinal = (float)$o['gasto_empleados']
                + (float)$o['gasto_insumos']
                + (float)$o['gasto_servicios']
                + (float)$o['gasto_herramientas'];

    $resultado[] = [
        'id'                => $id,
        'nombre'            => $o['ubicacion'],
        'fechaInicio'       => $o['fecha_inicio'] ? substr($o['fecha_inicio'], 0, 10) : '',
        'fechaTermino'      => $o['fecha_fin']    ? substr($o['fecha_fin'],    0, 10) : '',
        'presupuestoInicial'=> (float)$o['presupuesto_inicial'],
        'costoFinal'        => $costoFinal,
        'empleados'         => $empleados,
        'herramientas'      => $herramientasObra,
        'insumos'           => $insumos,
        'servicios'         => $servicios,
        'pagos'             => $pagos,
    ];
}

echo json_encode($resultado);
