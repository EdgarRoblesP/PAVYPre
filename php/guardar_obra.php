<?php
/**
 * Guarda (INSERT o UPDATE) un registro en OBRAS.
 * POST: id (vacío = nuevo), nombre, fecha_inicio, fecha_termino,
 *       presupuesto_inicial, cliente_id (solo en INSERT)
 * Al crear, también inserta el registro en DISPOSICIONES.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

$id                 = trim($_POST['id'] ?? '');
$nombre             = trim($_POST['nombre'] ?? '');
$fecha_inicio       = $_POST['fecha_inicio'] ?? null;
$fecha_termino      = $_POST['fecha_termino'] ?? null;
$presupuesto        = (float)($_POST['presupuesto_inicial'] ?? 0);
$cliente_id         = trim($_POST['cliente_id'] ?? '');

if (!$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre de la obra es obligatorio.']);
    exit;
}

if ($id) {
    // Edición: solo se actualiza el nombre
    $stmt = $pdo->prepare('UPDATE OBRAS SET ubicacion = ? WHERE id_obra = ?');
    $stmt->execute([$nombre, $id]);
} else {
    if (!$fecha_inicio) {
        http_response_code(400);
        echo json_encode(['error' => 'La fecha de inicio es obligatoria para nuevas obras.']);
        exit;
    }
    if (!$cliente_id) {
        http_response_code(400);
        echo json_encode(['error' => 'El cliente es obligatorio para nuevas obras.']);
        exit;
    }

    $nuevoId = strtoupper(substr(uniqid(), -6));

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO OBRAS (id_obra, ubicacion, presupuesto_inicial, utilidad_neta, gasto_empleados, gasto_insumos, gasto_servicios, gasto_herramientas, fecha_inicio, fecha_fin) VALUES (?, ?, ?, 0, 0, 0, 0, 0, ?, ?)');
        $stmt->execute([
            $nuevoId,
            $nombre,
            $presupuesto,
            $fecha_inicio,
            $fecha_termino ?: null
        ]);

        $stmtDis = $pdo->prepare('INSERT INTO DISPOSICIONES (id_obra, id_cliente) VALUES (?, ?)');
        $stmtDis->execute([$nuevoId, $cliente_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Error al registrar la obra y su disposición.']);
        exit;
    }
}

echo json_encode(['success' => true]);
