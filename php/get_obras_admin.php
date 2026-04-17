<?php
/**
 * Devuelve pv_obras paginadas con sus sub-arrays anidados.
 *
 * Parámetros GET:
 *   modo=catalogo  → devuelve solo [{id, nombre}] de todas las obras (para selects)
 *   page=N         → página a mostrar (default 1)
 *   buscar=texto   → filtro parcial sobre ubicacion (default '')
 *
 * Respuesta normal: { total, page, pages, per_page, obras: [...] }
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link = Conectarse();

// ── Modo catálogo: id + nombre de todas las obras ────────────────────────────
if (($_GET['modo'] ?? '') === 'catalogo') {
    $result = mysqli_query($link, 'SELECT id_obra AS id, ubicacion AS nombre FROM pv_obras ORDER BY ubicacion');
    echo json_encode(array_map(function ($r) {
        return ['id' => $r['id'], 'nombre' => $r['nombre']];
    }, mysqli_fetch_all($result, MYSQLI_ASSOC)));
    exit;
}

// ── Modo reporte: id + nombre + presupuesto + costoFinal ─────────────────────
if (($_GET['modo'] ?? '') === 'reporte') {
    $result = mysqli_query($link,
        'SELECT id_obra, ubicacion, presupuesto_inicial,
                gasto_empleados + gasto_insumos + gasto_servicios + gasto_herramientas AS costo_final
           FROM pv_obras
          ORDER BY costo_final DESC'
    );
    echo json_encode(array_map(function ($r) {
        return [
            'id'                 => $r['id_obra'],
            'nombre'             => $r['ubicacion'],
            'presupuestoInicial' => (float)$r['presupuesto_inicial'],
            'costoFinal'         => (float)$r['costo_final'],
        ];
    }, mysqli_fetch_all($result, MYSQLI_ASSOC)));
    exit;
}

// ── Parámetros de paginación ─────────────────────────────────────────────────
$per_page = 5;
$page     = max(1, (int)($_GET['page']   ?? 1));
$buscar   = trim($_GET['buscar'] ?? '');
$offset   = ($page - 1) * $per_page;

// ── Contar total de obras ────────────────────────────────────────────────────
if ($buscar !== '') {
    $like      = '%' . $buscar . '%';
    $stmtCount = mysqli_prepare($link, 'SELECT COUNT(*) FROM pv_obras WHERE ubicacion LIKE ?');
    mysqli_stmt_bind_param($stmtCount, 's', $like);
    mysqli_stmt_execute($stmtCount);
    $total = (int) stmt_value($stmtCount);
} else {
    $total = (int) mysqli_fetch_row(mysqli_query($link, 'SELECT COUNT(*) FROM pv_obras'))[0];
}

$pages  = max(1, (int)ceil($total / $per_page));
$page   = min($page, $pages);
$offset = ($page - 1) * $per_page;

// ── Lista base de obras con LIMIT / OFFSET ───────────────────────────────────
if ($buscar !== '') {
    $like      = '%' . $buscar . '%';
    $stmtObras = mysqli_prepare($link,
        "SELECT id_obra, ubicacion, fecha_inicio, fecha_fin,
                presupuesto_inicial, utilidad_neta,
                gasto_empleados, gasto_insumos, gasto_servicios, gasto_herramientas
           FROM pv_obras
          WHERE ubicacion LIKE ?
          ORDER BY fecha_inicio DESC
          LIMIT $per_page OFFSET $offset"
    );
    mysqli_stmt_bind_param($stmtObras, 's', $like);
} else {
    $stmtObras = mysqli_prepare($link,
        "SELECT id_obra, ubicacion, fecha_inicio, fecha_fin,
                presupuesto_inicial, utilidad_neta,
                gasto_empleados, gasto_insumos, gasto_servicios, gasto_herramientas
           FROM pv_obras
          ORDER BY fecha_inicio DESC
          LIMIT $per_page OFFSET $offset"
    );
}
mysqli_stmt_execute($stmtObras);
$obras = stmt_rows($stmtObras);

// ── Preparar consultas de sub-tablas (bind_param usa referencia) ─────────────
$loopId = '';

$stmtEmpl = mysqli_prepare($link,
    'SELECT e.nombre      AS nombreEmpleado,
            e.puesto,
            te.fecha_adicion  AS fechaInicio,
            te.fecha_termino  AS fechaTermino,
            ROUND(
                e.salario * (DATEDIFF(COALESCE(te.fecha_termino, o.fecha_fin, NOW()), te.fecha_adicion) / 7),
                2
            ) AS costoTotal
       FROM pv_trabajos_empleados te
       JOIN pv_empleados e ON te.id_empleado = e.id_empleado
       JOIN pv_obras      o ON te.id_obra    = o.id_obra
      WHERE te.id_obra = ?
      ORDER BY te.fecha_adicion'
);
mysqli_stmt_bind_param($stmtEmpl, 's', $loopId);

$stmtHerram = mysqli_prepare($link,
    'SELECT uh.id_herramienta                                       AS catalogId,
            h.nombre,
            p.nombre                                               AS proveedor,
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
       FROM pv_usos_herramientas uh
       JOIN pv_herramientas h  ON uh.id_herramienta = h.id_herramienta
       JOIN pv_proveedores  p  ON h.proveedor_id    = p.id
       JOIN pv_obras         o ON uh.id_obra        = o.id_obra
      WHERE uh.id_obra = ?
      ORDER BY h.nombre'
);
mysqli_stmt_bind_param($stmtHerram, 's', $loopId);

$stmtInsumos = mysqli_prepare($link,
    'SELECT i.id_insumo                                          AS catalogId,
            i.tipo_material                                      AS nombre,
            p.nombre                                             AS proveedor,
            i.costo_unitario,
            SUM(ei.cantidad)                                     AS cantidadTotal,
            ROUND(i.costo_unitario * SUM(ei.cantidad), 2)        AS subtotal
       FROM pv_empleos_insumos ei
       JOIN pv_insumos     i ON ei.id_insumo   = i.id_insumo
       JOIN pv_proveedores p ON i.proveedor_id = p.id
      WHERE ei.id_obra = ?
      GROUP BY i.id_insumo, i.tipo_material, p.nombre, i.costo_unitario
      ORDER BY i.tipo_material'
);
mysqli_stmt_bind_param($stmtInsumos, 's', $loopId);

$stmtServicios = mysqli_prepare($link,
    'SELECT s.id_servicio                                        AS catalogId,
            s.tipo_traslado                                      AS nombre,
            p.nombre                                             AS proveedor,
            s.costo_kilometro,
            ROUND(SUM(rs.kilometraje), 2)                        AS kilometrajeTotal,
            ROUND(s.costo_kilometro * SUM(rs.kilometraje), 2)   AS subtotal
       FROM pv_requerimientos_servicios rs
       JOIN pv_servicios    s ON rs.id_servicio  = s.id_servicio
       JOIN pv_proveedores  p ON s.proveedor_id  = p.id
      WHERE rs.id_obra = ?
      GROUP BY s.id_servicio, s.tipo_traslado, p.nombre, s.costo_kilometro
      ORDER BY s.tipo_traslado'
);
mysqli_stmt_bind_param($stmtServicios, 's', $loopId);

$stmtPagos = mysqli_prepare($link,
    'SELECT fecha_pago  AS fechaPago,
            monto,
            tipo_pago   AS tipoPago
       FROM pv_cobros
      WHERE id_obra = ?
      ORDER BY fecha_pago'
);
mysqli_stmt_bind_param($stmtPagos, 's', $loopId);

// ── Enriquecer cada obra con sus sub-tablas ──────────────────────────────────
$resultado = [];
foreach ($obras as $o) {
    $loopId = $o['id_obra'];

    mysqli_stmt_execute($stmtEmpl);
    $empleados = array_map(function ($e) {
        return [
            'nombreEmpleado' => $e['nombreEmpleado'],
            'puesto'         => $e['puesto'],
            'fechaInicio'    => $e['fechaInicio']  ? substr($e['fechaInicio'],  0, 10) : '',
            'fechaTermino'   => $e['fechaTermino'] ? substr($e['fechaTermino'], 0, 10) : '',
            'costoTotal'     => (float)$e['costoTotal'],
        ];
    }, stmt_rows($stmtEmpl));

    mysqli_stmt_execute($stmtHerram);
    $herramientasObra = array_map(function ($h) {
        return [
            'catalogId'        => $h['catalogId'],
            'nombre'           => $h['nombre'],
            'proveedor'        => $h['proveedor'],
            'rentaSemanal'     => (float)$h['renta_semanal'],
            'fechaInicio'      => $h['fechaInicio']  ? substr($h['fechaInicio'],  0, 10) : '',
            'fechaTermino'     => $h['fechaTermino'] ? substr($h['fechaTermino'], 0, 10) : '',
            'dias'             => (int)$h['dias'],
            'cantidadUnidades' => (int)$h['cantidad'],
            'subtotal'         => (float)$h['subtotal'],
        ];
    }, stmt_rows($stmtHerram));

    mysqli_stmt_execute($stmtInsumos);
    $insumos = array_map(function ($r) {
        return [
            'catalogId'     => $r['catalogId'],
            'nombre'        => $r['nombre'],
            'proveedor'     => $r['proveedor'],
            'costoUnitario' => (float)$r['costo_unitario'],
            'cantidadTotal' => (float)$r['cantidadTotal'],
            'subtotal'      => (float)$r['subtotal'],
        ];
    }, stmt_rows($stmtInsumos));

    mysqli_stmt_execute($stmtServicios);
    $servicios = array_map(function ($r) {
        return [
            'catalogId'        => $r['catalogId'],
            'nombre'           => $r['nombre'],
            'proveedor'        => $r['proveedor'],
            'costoKm'          => (float)$r['costo_kilometro'],
            'kilometrajeTotal' => (float)$r['kilometrajeTotal'],
            'subtotal'         => (float)$r['subtotal'],
        ];
    }, stmt_rows($stmtServicios));

    mysqli_stmt_execute($stmtPagos);
    $pagos = array_map(function ($p) {
        return [
            'fechaPago' => substr($p['fechaPago'], 0, 10),
            'monto'     => (float)$p['monto'],
            'tipoPago'  => $p['tipoPago'],
        ];
    }, stmt_rows($stmtPagos));

    $costoFinal = (float)$o['gasto_empleados']
                + (float)$o['gasto_insumos']
                + (float)$o['gasto_servicios']
                + (float)$o['gasto_herramientas'];

    $resultado[] = [
        'id'                 => $loopId,
        'nombre'             => $o['ubicacion'],
        'fechaInicio'        => $o['fecha_inicio'] ? substr($o['fecha_inicio'], 0, 10) : '',
        'fechaTermino'       => $o['fecha_fin']    ? substr($o['fecha_fin'],    0, 10) : '',
        'presupuestoInicial' => (float)$o['presupuesto_inicial'],
        'costoFinal'         => $costoFinal,
        'empleados'          => $empleados,
        'herramientas'       => $herramientasObra,
        'insumos'            => $insumos,
        'servicios'          => $servicios,
        'pagos'              => $pagos,
    ];
}

echo json_encode([
    'total'    => $total,
    'page'     => $page,
    'pages'    => $pages,
    'per_page' => $per_page,
    'obras'    => $resultado,
]);
