-- ================================================================
-- cobros_actualizados.sql
-- Poblar / reemplazar COBROS con montos congruentes al costo_final
-- real de cada obra (gasto_empleados + gasto_insumos +
-- gasto_servicios + gasto_herramientas).
--
-- REQUISITO: Ejecutar DESPUÉS de que los campos gasto_* en OBRAS
--            estén actualizados con recalcularGastosObra().
--
-- Distribución de pagos por obra:
--   2 pagos → anticipo 55 % | finiquito 45 %
--   3 pagos → anticipo 40 % | avance 35 % | finiquito 25 %
--   4 pagos → anticipo 35 % | 2.º 25 % | 3.º 25 % | finiquito 15 %
--
-- Abreviatura usada: cf = gasto_empleados+gasto_insumos
--                         +gasto_servicios+gasto_herramientas
-- ================================================================

-- Limpiar cobros existentes para las 16 obras
DELETE FROM COBROS
 WHERE id_obra IN (
    'OBA001','OBA002','OBA003','OBA004','OBA005','OBA006','OBA007','OBA008',
    'OBA009','OBA010','OBA011','OBA012','OBA013','OBA014','OBA015','OBA016'
 );

-- ── OBA001 · CTE003 · 4 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Transferencia', 'CTE003', 'OBA001' FROM OBRAS WHERE id_obra = 'OBA001'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.30) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Efectivo',      'CTE003', 'OBA001' FROM OBRAS WHERE id_obra = 'OBA001'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.65) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Cheque',        'CTE003', 'OBA001' FROM OBRAS WHERE id_obra = 'OBA001'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 3 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.15, 2),
       'Transferencia', 'CTE003', 'OBA001' FROM OBRAS WHERE id_obra = 'OBA001';

-- ── OBA002 · CTE001 · 3 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Transferencia', 'CTE001', 'OBA002' FROM OBRAS WHERE id_obra = 'OBA002'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.45) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Cheque',        'CTE001', 'OBA002' FROM OBRAS WHERE id_obra = 'OBA002'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE001', 'OBA002' FROM OBRAS WHERE id_obra = 'OBA002';

-- ── OBA003 · CTE001 · 4 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Transferencia', 'CTE001', 'OBA003' FROM OBRAS WHERE id_obra = 'OBA003'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.30) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Efectivo',      'CTE001', 'OBA003' FROM OBRAS WHERE id_obra = 'OBA003'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.65) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Efectivo',      'CTE001', 'OBA003' FROM OBRAS WHERE id_obra = 'OBA003'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 3 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.15, 2),
       'Transferencia', 'CTE001', 'OBA003' FROM OBRAS WHERE id_obra = 'OBA003';

-- ── OBA004 · CTE002 · 4 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Cheque',        'CTE002', 'OBA004' FROM OBRAS WHERE id_obra = 'OBA004'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.30) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.30, 2),
       'Transferencia', 'CTE002', 'OBA004' FROM OBRAS WHERE id_obra = 'OBA004'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.65) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.20, 2),
       'Efectivo',      'CTE002', 'OBA004' FROM OBRAS WHERE id_obra = 'OBA004'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 3 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.15, 2),
       'Transferencia', 'CTE002', 'OBA004' FROM OBRAS WHERE id_obra = 'OBA004';

-- ── OBA005 · CTE004 · 3 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Transferencia', 'CTE004', 'OBA005' FROM OBRAS WHERE id_obra = 'OBA005'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.45) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Efectivo',      'CTE004', 'OBA005' FROM OBRAS WHERE id_obra = 'OBA005'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE004', 'OBA005' FROM OBRAS WHERE id_obra = 'OBA005';

-- ── OBA006 · CTE002 · 4 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 6 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Transferencia', 'CTE002', 'OBA006' FROM OBRAS WHERE id_obra = 'OBA006'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.28) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Cheque',        'CTE002', 'OBA006' FROM OBRAS WHERE id_obra = 'OBA006'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.62) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Efectivo',      'CTE002', 'OBA006' FROM OBRAS WHERE id_obra = 'OBA006'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 4 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.15, 2),
       'Transferencia', 'CTE002', 'OBA006' FROM OBRAS WHERE id_obra = 'OBA006';

-- ── OBA007 · CTE001 · 2 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.55, 2),
       'Cheque',        'CTE001', 'OBA007' FROM OBRAS WHERE id_obra = 'OBA007'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 1 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.45, 2),
       'Transferencia', 'CTE001', 'OBA007' FROM OBRAS WHERE id_obra = 'OBA007';

-- ── OBA008 · CTE005 · 3 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Transferencia', 'CTE005', 'OBA008' FROM OBRAS WHERE id_obra = 'OBA008'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.45) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Efectivo',      'CTE005', 'OBA008' FROM OBRAS WHERE id_obra = 'OBA008'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE005', 'OBA008' FROM OBRAS WHERE id_obra = 'OBA008';

-- ── OBA009 · CTE005 · 3 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.45, 2),
       'Transferencia', 'CTE005', 'OBA009' FROM OBRAS WHERE id_obra = 'OBA009'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.50) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.30, 2),
       'Cheque',        'CTE005', 'OBA009' FROM OBRAS WHERE id_obra = 'OBA009'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE005', 'OBA009' FROM OBRAS WHERE id_obra = 'OBA009';

-- ── OBA010 · CTE001 · 2 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.60, 2),
       'Transferencia', 'CTE001', 'OBA010' FROM OBRAS WHERE id_obra = 'OBA010'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 1 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Efectivo',      'CTE001', 'OBA010' FROM OBRAS WHERE id_obra = 'OBA010';

-- ── OBA011 · CTE003 · 4 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Cheque',        'CTE003', 'OBA011' FROM OBRAS WHERE id_obra = 'OBA011'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.30) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE003', 'OBA011' FROM OBRAS WHERE id_obra = 'OBA011'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.65) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Efectivo',      'CTE003', 'OBA011' FROM OBRAS WHERE id_obra = 'OBA011'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 3 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.15, 2),
       'Transferencia', 'CTE003', 'OBA011' FROM OBRAS WHERE id_obra = 'OBA011';

-- ── OBA012 · CTE002 · 3 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Transferencia', 'CTE002', 'OBA012' FROM OBRAS WHERE id_obra = 'OBA012'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.45) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Efectivo',      'CTE002', 'OBA012' FROM OBRAS WHERE id_obra = 'OBA012'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE002', 'OBA012' FROM OBRAS WHERE id_obra = 'OBA012';

-- ── OBA013 · CTE002 · 2 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.55, 2),
       'Transferencia', 'CTE002', 'OBA013' FROM OBRAS WHERE id_obra = 'OBA013'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 1 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.45, 2),
       'Cheque',        'CTE002', 'OBA013' FROM OBRAS WHERE id_obra = 'OBA013';

-- ── OBA014 · CTE004 · 3 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Transferencia', 'CTE004', 'OBA014' FROM OBRAS WHERE id_obra = 'OBA014'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.45) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Efectivo',      'CTE004', 'OBA014' FROM OBRAS WHERE id_obra = 'OBA014'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE004', 'OBA014' FROM OBRAS WHERE id_obra = 'OBA014';

-- ── OBA015 · CTE004 · 4 pagos ──────────────────────────────────
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Cheque',        'CTE004', 'OBA015' FROM OBRAS WHERE id_obra = 'OBA015'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.30) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE004', 'OBA015' FROM OBRAS WHERE id_obra = 'OBA015'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(fecha_fin,fecha_inicio)*0.65) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Efectivo',      'CTE004', 'OBA015' FROM OBRAS WHERE id_obra = 'OBA015'
UNION ALL
SELECT DATE_ADD(fecha_fin,   INTERVAL 3 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.15, 2),
       'Transferencia', 'CTE004', 'OBA015' FROM OBRAS WHERE id_obra = 'OBA015';

-- ── OBA016 · CTE001 · 3 pagos (obra activa — usa COALESCE para fecha_fin) ──
INSERT INTO COBROS (fecha_pago, monto, tipo_pago, id_cliente, id_obra)
SELECT DATE_ADD(fecha_inicio, INTERVAL 5 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.40, 2),
       'Transferencia', 'CTE001', 'OBA016' FROM OBRAS WHERE id_obra = 'OBA016'
UNION ALL
SELECT DATE_ADD(fecha_inicio, INTERVAL FLOOR(DATEDIFF(COALESCE(fecha_fin,CURDATE()),fecha_inicio)*0.45) DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.35, 2),
       'Efectivo',      'CTE001', 'OBA016' FROM OBRAS WHERE id_obra = 'OBA016'
UNION ALL
SELECT DATE_ADD(COALESCE(fecha_fin, CURDATE()), INTERVAL 2 DAY),
       ROUND((gasto_empleados+gasto_insumos+gasto_servicios+gasto_herramientas)*0.25, 2),
       'Transferencia', 'CTE001', 'OBA016' FROM OBRAS WHERE id_obra = 'OBA016';

-- ================================================================
-- FIN cobros_actualizados.sql
-- ================================================================
