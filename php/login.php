<?php
/**
 * Autenticación contra la base de datos.
 * - Admin:       tabla pv_empleados con puesto = 'Administrador'.
 * - Colaborador: tabla pv_empleados (cualquier otro puesto).
 * - Cliente:     tabla pv_clientes.
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Login.html');
    exit;
}

$email        = trim($_POST['email']    ?? '');
$formPassword = trim($_POST['password'] ?? '');

if (!$email || !$formPassword) {
    header('Location: ../Login.html?error=auth');
    exit;
}

require_once __DIR__ . '/db.php';
$link = Conectarse();

// ── 1. Empleados (Admin y Colaborador — pv_empleados) ───────────
$stmt = mysqli_prepare($link, 'SELECT id_empleado, nombre, puesto, contrasena FROM pv_empleados WHERE email = ? LIMIT 1');
mysqli_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$row = stmt_row($stmt);

if ($row && password_verify($formPassword, $row['contrasena'])) {
    session_regenerate_id(true);
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_id']       = $row['id_empleado'];
    $_SESSION['user_name']     = $row['nombre'];

    if ($row['puesto'] === 'Administrador') {
        $_SESSION['user_role'] = 'admin';
        header('Location: ../Admin.html');
    } else {
        $_SESSION['user_role'] = 'colaborador';
        header('Location: ../Colaborador.html');
    }
    exit;
}

// ── 2. Cliente (pv_clientes) ────────────────────────────────────
$stmt = mysqli_prepare($link, 'SELECT id_cliente, nombre, contrasena FROM pv_clientes WHERE email = ? LIMIT 1');
mysqli_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$row = stmt_row($stmt);

if ($row && password_verify($formPassword, $row['contrasena'])) {
    session_regenerate_id(true);
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'cliente';
    $_SESSION['user_id']       = $row['id_cliente'];
    $_SESSION['user_name']     = $row['nombre'];
    header('Location: ../Cliente.html');
    exit;
}

// ── 3. Credenciales inválidas ────────────────────────────────
header('Location: ../Login.html?error=auth');
exit;
