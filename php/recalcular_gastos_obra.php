<?php
/**
 * Utilidad: recalcula y actualiza las 4 columnas de gasto en OBRAS.
 * Reglas de negocio aplicadas:
 *   - Para obras con fecha_fin, se usa esa fecha como tope (no NOW()).
 *   - Herramientas: renta_semanal × (días / 7) × cantidad.
 */

function recalcularGastosObra(PDO $pdo, string $obraId): void
{
    // ── Gasto empleados: salario_semanal × semanas ────────────
    // COALESCE: usa fecha_termino del registro, luego fecha_fin de la obra,
    // y solo como último recurso NOW() (obras activas sin fecha de término).
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(
             ROUND(
                 e.salario
                 * (DATEDIFF(COALESCE(te.fecha_termino, o.fecha_fin, NOW()), te.fecha_adicion) / 7),
                 2
             )
         ), 0)
           FROM TRABAJOS_EMPLEADOS te
           JOIN EMPLEADOS e ON te.id_empleado  = e.id_empleado
           JOIN OBRAS      o ON te.id_obra      = o.id_obra
          WHERE te.id_obra = ?'
    );
    $stmt->execute([$obraId]);
    $gastoEmpleados = (float) $stmt->fetchColumn();

    // ── Gasto insumos: costo_unitario × cantidad ──────────────
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(i.costo_unitario * ei.cantidad), 0)
           FROM EMPLEOS_INSUMOS ei
           JOIN INSUMOS i ON ei.id_insumo = i.id_insumo
          WHERE ei.id_obra = ?'
    );
    $stmt->execute([$obraId]);
    $gastoInsumos = (float) $stmt->fetchColumn();

    // ── Gasto servicios: costo_km × kilometraje ───────────────
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(s.costo_kilometro * rs.kilometraje), 0)
           FROM REQUERIMIENTOS_SERVICIOS rs
           JOIN SERVICIOS s ON rs.id_servicio = s.id_servicio
          WHERE rs.id_obra = ?'
    );
    $stmt->execute([$obraId]);
    $gastoServicios = (float) $stmt->fetchColumn();

    // ── Gasto herramientas: renta_semanal × semanas × cantidad ─
    $stmt = $pdo->prepare(
        'SELECT COALESCE(SUM(
             h.renta_semanal
             * GREATEST(DATEDIFF(COALESCE(uh.fecha_termino, o.fecha_fin, NOW()), uh.fecha_adicion) / 7, 0)
             * uh.cantidad
         ), 0)
           FROM USOS_HERRAMIENTAS uh
           JOIN HERRAMIENTAS h ON uh.id_herramienta = h.id_herramienta
           JOIN OBRAS         o ON uh.id_obra        = o.id_obra
          WHERE uh.id_obra = ?'
    );
    $stmt->execute([$obraId]);
    $gastoHerramientas = (float) $stmt->fetchColumn();

    // ── Actualizar las 4 columnas en OBRAS ────────────────────
    $upd = $pdo->prepare(
        'UPDATE OBRAS
            SET gasto_empleados    = ?,
                gasto_insumos      = ?,
                gasto_servicios    = ?,
                gasto_herramientas = ?
          WHERE id_obra = ?'
    );
    $upd->execute([$gastoEmpleados, $gastoInsumos, $gastoServicios, $gastoHerramientas, $obraId]);
}
