<?php
/**
 * Devuelve las obras del cliente autenticado, con pagos anidados.
 * Usa la conexión de solo lectura (usuario: cliente).
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'cliente') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

require_once __DIR__ . '/db_cliente.php';
header('Content-Type: application/json');

$idCliente = $_SESSION['user_id'];

// ── Obras del cliente ────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT o.id_obra,
            o.ubicacion,
            o.fecha_inicio,
            o.fecha_fin,
            o.presupuesto_inicial
       FROM OBRAS o
       JOIN DISPOSICIONES d ON o.id_obra = d.id_obra
      WHERE d.id_cliente = ?
      ORDER BY o.fecha_inicio DESC'
);
$stmt->execute([$idCliente]);
$obras = $stmt->fetchAll();

// ── Serializar obras (sin pagos — los carga get_historial_pagos.php) ─────────
$resultado = array_map(function ($o) {
    return [
        'id'                 => $o['id_obra'],
        'nombre'             => $o['ubicacion'],
        'fechaInicio'        => $o['fecha_inicio'] ? substr($o['fecha_inicio'], 0, 10) : '',
        'fechaFinalizacion'  => $o['fecha_fin']    ? substr($o['fecha_fin'],    0, 10) : '',
        'presupuestoInicial' => (float)$o['presupuesto_inicial'],
    ];
}, $obras);

echo json_encode($resultado);
