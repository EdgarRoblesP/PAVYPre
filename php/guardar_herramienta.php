<?php
/**
 * Guarda (INSERT o UPDATE) un registro en HERRAMIENTAS.
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
require_once __DIR__ . '/db_admin.php';

header('Content-Type: application/json');

try {
    $id        = trim($_POST['id']           ?? '');
    $nombre    = trim($_POST['nombre']       ?? '');
    $proveedor = trim($_POST['proveedor']    ?? '');
    $renta     = (float)($_POST['renta_semanal'] ?? 0);

    if (!$nombre || !$proveedor) {
        http_response_code(400);
        echo json_encode(['error' => 'Nombre y proveedor son obligatorios.']);
        exit;
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
            $stmt = $pdo->prepare(
                'UPDATE HERRAMIENTAS SET nombre = ?, proveedor = ?, renta_semanal = ?, imagen = ? WHERE id_herramienta = ?'
            );
            $stmt->execute([$nombre, $proveedor, $renta, $imagenPath, $id]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE HERRAMIENTAS SET nombre = ?, proveedor = ?, renta_semanal = ? WHERE id_herramienta = ?'
            );
            $stmt->execute([$nombre, $proveedor, $renta, $id]);
        }
    } else {
        $nuevoId = strtoupper(substr(uniqid(), -6));
        $stmt    = $pdo->prepare(
            'INSERT INTO HERRAMIENTAS (id_herramienta, nombre, proveedor, renta_semanal, imagen) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nuevoId, $nombre, $proveedor, $renta, $imagenPath]);
        $id = $nuevoId;
    }

    echo json_encode(['success' => true, 'id' => $id, 'imagen' => $imagenPath]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}
