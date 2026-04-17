<?php
/**
 * Guarda (INSERT o UPDATE) un registro en PV_HERRAMIENTAS.
 * POST: id (vacío = nuevo), nombre, proveedor, renta_semanal
 * FILE: imagen (opcional)
 */
session_start();
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$link            = Conectarse();
$id              = trim($_POST['id']              ?? '');
$nombre          = trim($_POST['nombre']          ?? '');
$proveedorNombre = trim($_POST['proveedor']       ?? '');
$renta           = (float)($_POST['renta_semanal'] ?? 0);

if (!$nombre || !$proveedorNombre) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y proveedor son obligatorios.']);
    exit;
}

// Obtener o crear el proveedor en PV_PROVEEDORES
$stmtProv = mysqli_prepare($link, 'SELECT id FROM PV_PROVEEDORES WHERE nombre = ? LIMIT 1');
mysqli_bind_param($stmtProv, 's', $proveedorNombre);
mysqli_stmt_execute($stmtProv);
$provRow = stmt_row($stmtProv);

if ($provRow) {
    $proveedorId = (int)$provRow['id'];
} else {
    $stmtIns = mysqli_prepare($link, 'INSERT INTO PV_PROVEEDORES (nombre) VALUES (?)');
    mysqli_bind_param($stmtIns, 's', $proveedorNombre);
    mysqli_stmt_execute($stmtIns);
    $proveedorId = (int) mysqli_insert_id($link);
}

// Manejo de imagen
$imagenPath = null;
if (!empty($_FILES['imagen']['tmp_name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'herramientas' . DIRECTORY_SEPARATOR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext      = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $filename = preg_replace('/[^a-z0-9_-]/i', '', pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME))
                . '_' . time() . '.' . $ext;
    $destino  = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
        $imagenPath = 'uploads/herramientas/' . $filename;
    } else {
        echo json_encode(['error' => 'No se pudo guardar la imagen. Verifica permisos del directorio uploads/.']);
        exit;
    }
} elseif (!empty($_FILES['imagen']['error']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'La imagen supera upload_max_filesize en php.ini.',
        UPLOAD_ERR_FORM_SIZE  => 'La imagen supera MAX_FILE_SIZE del formulario.',
        UPLOAD_ERR_PARTIAL    => 'La imagen se subió parcialmente.',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal de PHP.',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir la imagen en disco.',
    ];
    $msg = $uploadErrors[$_FILES['imagen']['error']] ?? 'Error desconocido al subir la imagen.';
    http_response_code(400);
    echo json_encode(['error' => $msg]);
    exit;
}

if ($id) {
    if ($imagenPath) {
        $stmt = mysqli_prepare($link,
            'UPDATE PV_HERRAMIENTAS SET nombre = ?, proveedor_id = ?, renta_semanal = ?, imagen = ? WHERE id_herramienta = ?'
        );
        mysqli_bind_param($stmt, 'sidss', $nombre, $proveedorId, $renta, $imagenPath, $id);
    } else {
        $stmt = mysqli_prepare($link,
            'UPDATE PV_HERRAMIENTAS SET nombre = ?, proveedor_id = ?, renta_semanal = ? WHERE id_herramienta = ?'
        );
        mysqli_bind_param($stmt, 'sids', $nombre, $proveedorId, $renta, $id);
    }
    mysqli_stmt_execute($stmt);
} else {
    $t1 = 'PV_HERRAMIENTAS'; $t2 = 'id_herramienta'; $t3 = 'HER';
    $stmtSp = mysqli_prepare($link, 'CALL sp_generar_id(?, ?, ?, @nuevo_id)');
    mysqli_bind_param($stmtSp, 'sss', $t1, $t2, $t3);
    mysqli_stmt_execute($stmtSp);
    mysqli_stmt_close($stmtSp);
    $res     = mysqli_query($link, 'SELECT @nuevo_id');
    $nuevoId = mysqli_fetch_row($res)[0];
    $stmt    = mysqli_prepare($link,
        'INSERT INTO PV_HERRAMIENTAS (id_herramienta, nombre, proveedor_id, renta_semanal, imagen) VALUES (?, ?, ?, ?, ?)'
    );
    mysqli_bind_param($stmt, 'ssids', $nuevoId, $nombre, $proveedorId, $renta, $imagenPath);
    mysqli_stmt_execute($stmt);
    $id = $nuevoId;
}

echo json_encode(['success' => true, 'id' => $id, 'imagen' => $imagenPath]);
