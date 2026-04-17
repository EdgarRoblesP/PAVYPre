<?php
/**
 * Guarda (INSERT o UPDATE) un registro en pv_obras.
 * POST: id (vacío = nuevo), nombre, fecha_inicio, fecha_termino,
 *       presupuesto_inicial, cliente_id (solo en INSERT)
 * Al crear, también inserta el registro en pv_disposiciones.
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link          = Conectarse();
$id            = trim($_POST['id']                  ?? '');
$nombre        = trim($_POST['nombre']              ?? '');
$fecha_inicio  = $_POST['fecha_inicio']             ?? null;
$fecha_termino = $_POST['fecha_termino']            ?? null;
$presupuesto   = (float)($_POST['presupuesto_inicial'] ?? 0);
$cliente_id    = trim($_POST['cliente_id']          ?? '');

if (!$nombre) {
    http_response_code(400);
    echo json_encode(['error' => 'El nombre de la obra es obligatorio.']);
    exit;
}

if ($id) {
    // Edición: solo se actualiza el nombre
    $stmt = mysqli_prepare($link, 'UPDATE pv_obras SET ubicacion = ? WHERE id_obra = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $nombre, $id);
    mysqli_stmt_execute($stmt);
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

    $t1 = 'pv_obras'; $t2 = 'id_obra'; $t3 = 'OBR';
    $stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
    mysqli_stmt_bind_param($stmtSp, 'sss', $t1, $t2, $t3);
    mysqli_stmt_execute($stmtSp);
    mysqli_stmt_close($stmtSp);
    $res     = mysqli_query($link, 'SELECT @nuevo_id');
    $nuevoId = mysqli_fetch_row($res)[0];

    $fechaTermVal = $fecha_termino ?: null;

    mysqli_begin_transaction($link);
    $ok = true;

    $stmtObra = mysqli_prepare($link,
        'INSERT INTO pv_obras (id_obra, ubicacion, presupuesto_inicial, utilidad_neta, gasto_empleados, gasto_insumos, gasto_servicios, gasto_herramientas, fecha_inicio, fecha_fin)
         VALUES (?, ?, ?, 0, 0, 0, 0, 0, ?, ?)'
    );
    mysqli_stmt_bind_param($stmtObra, 'ssdss', $nuevoId, $nombre, $presupuesto, $fecha_inicio, $fechaTermVal);
    if (!mysqli_stmt_execute($stmtObra)) {
        $ok = false;
    }

    if ($ok) {
        $stmtDis = mysqli_prepare($link, 'INSERT INTO pv_disposiciones (id_obra, id_cliente) VALUES (?, ?)');
        mysqli_stmt_bind_param($stmtDis, 'ss', $nuevoId, $cliente_id);
        if (!mysqli_stmt_execute($stmtDis)) {
            $ok = false;
        }
    }

    if ($ok) {
        mysqli_commit($link);
    } else {
        mysqli_rollback($link);
        http_response_code(500);
        echo json_encode(['error' => 'Error al registrar la obra y su disposición.']);
        exit;
    }
}

echo json_encode(['success' => true]);
