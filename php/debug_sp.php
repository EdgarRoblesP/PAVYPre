<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$link = Conectarse();
$t1 = 'pv_empleados'; $t2 = 'id_empleado'; $t3 = 'TST';
$stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
mysqli_stmt_bind_param($stmtSp, 'sss', $t1, $t2, $t3);

try {
    $exec = mysqli_stmt_execute($stmtSp);
    if (!$exec) {
        echo json_encode(['execute' => 'FALLO', 'error' => mysqli_stmt_error($stmtSp)]);
    } else {
        mysqli_stmt_close($stmtSp);
        while (mysqli_more_results($link)) { mysqli_next_result($link); }
        $r       = mysqli_query($link, 'SELECT @nuevo_id AS id');
        $nuevoId = $r ? (mysqli_fetch_assoc($r)['id'] ?? 'NULL') : mysqli_error($link);
        echo json_encode(['execute' => 'OK', 'nuevo_id' => $nuevoId]);
    }
} catch (Exception $e) {
    echo json_encode(['exception' => $e->getMessage()]);
}
