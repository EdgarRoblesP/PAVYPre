<?php
/**
 * Utilidad: recalcula y actualiza las 4 columnas de gasto en PV_OBRAS.
 * Reglas de negocio aplicadas:
 *   - Para obras con fecha_fin, se usa esa fecha como tope (no NOW()).
 *   - Herramientas: renta_semanal × (días / 7) × cantidad.
 */

function recalcularGastosObra(mysqli $link, string $obraId): void
{
    // ── Gasto empleados: salario_semanal × semanas ────────────
    $stmt = mysqli_prepare($link,
        'SELECT COALESCE(SUM(
             ROUND(
                 e.salario
                 * (DATEDIFF(COALESCE(te.fecha_termino, o.fecha_fin, NOW()), te.fecha_adicion) / 7),
                 2
             )
         ), 0)
           FROM PV_TRABAJOS_EMPLEADOS te
           JOIN PV_EMPLEADOS e ON te.id_empleado  = e.id_empleado
           JOIN PV_OBRAS      o ON te.id_obra      = o.id_obra
          WHERE te.id_obra = ?'
    );
    mysqli_bind_param($stmt, 's', $obraId);
    mysqli_stmt_execute($stmt);
    $gastoEmpleados = (float) stmt_value($stmt);

    // ── Gasto insumos: costo_unitario × cantidad ──────────────
    $stmt = mysqli_prepare($link,
        'SELECT COALESCE(SUM(i.costo_unitario * ei.cantidad), 0)
           FROM PV_EMPLEOS_INSUMOS ei
           JOIN PV_INSUMOS i ON ei.id_insumo = i.id_insumo
          WHERE ei.id_obra = ?'
    );
    mysqli_bind_param($stmt, 's', $obraId);
    mysqli_stmt_execute($stmt);
    $gastoInsumos = (float) stmt_value($stmt);

    // ── Gasto servicios: costo_km × kilometraje ───────────────
    $stmt = mysqli_prepare($link,
        'SELECT COALESCE(SUM(s.costo_kilometro * rs.kilometraje), 0)
           FROM PV_REQUERIMIENTOS_SERVICIOS rs
           JOIN PV_SERVICIOS s ON rs.id_servicio = s.id_servicio
          WHERE rs.id_obra = ?'
    );
    mysqli_bind_param($stmt, 's', $obraId);
    mysqli_stmt_execute($stmt);
    $gastoServicios = (float) stmt_value($stmt);

    // ── Gasto herramientas: renta_semanal × semanas × cantidad ─
    $stmt = mysqli_prepare($link,
        'SELECT COALESCE(SUM(
             h.renta_semanal
             * GREATEST(DATEDIFF(COALESCE(uh.fecha_termino, o.fecha_fin, NOW()), uh.fecha_adicion) / 7, 0)
             * uh.cantidad
         ), 0)
           FROM PV_USOS_HERRAMIENTAS uh
           JOIN PV_HERRAMIENTAS h ON uh.id_herramienta = h.id_herramienta
           JOIN PV_OBRAS         o ON uh.id_obra        = o.id_obra
          WHERE uh.id_obra = ?'
    );
    mysqli_bind_param($stmt, 's', $obraId);
    mysqli_stmt_execute($stmt);
    $gastoHerramientas = (float) stmt_value($stmt);

    // ── Actualizar las 4 columnas en PV_OBRAS ────────────────────
    $upd = mysqli_prepare($link,
        'UPDATE PV_OBRAS
            SET gasto_empleados    = ?,
                gasto_insumos      = ?,
                gasto_servicios    = ?,
                gasto_herramientas = ?
          WHERE id_obra = ?'
    );
    mysqli_bind_param($upd, 'dddds', $gastoEmpleados, $gastoInsumos, $gastoServicios, $gastoHerramientas, $obraId);
    mysqli_stmt_execute($upd);
}
