<?php
/**
 * Autenticación contra la base de datos.
 * - Admin:       credencial fija (no existe tabla de admins en el esquema).
 * - Colaborador: tabla EMPLEADOS  (email + contrasena).
 * - Cliente:     tabla CLIENTES   (email + contrasena).
 */
session_start();

// Credencial del administrador del sistema
define('ADMIN_EMAIL',    'admin@pavypre.com');
define('ADMIN_PASSWORD', '123456');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Login.html');
    exit;
}

$email       = trim($_POST['email']    ?? '');
$formPassword = trim($_POST['password'] ?? '');

if (!$email || !$formPassword) {
    header('Location: ../Login.html?error=auth');
    exit;
}

// ── 1. Admin ────────────────────────────────────────────────
if ($email === ADMIN_EMAIL && $formPassword === ADMIN_PASSWORD) {
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'admin';
    $_SESSION['user_id']       = 'admin';
    $_SESSION['user_name']     = 'Administrador';
    header('Location: ../Admin.html');
    exit;
}

// ── 2. Conexión a la BD ──────────────────────────────────────
// IMPORTANTE: require_once declara $password con la clave de BD;
// por eso se usa $formPassword para la contraseña del formulario.
require_once __DIR__ . '/db_admin.php';   // $pdo disponible

// ── 3. Colaborador (EMPLEADOS) ───────────────────────────────
$stmt = $pdo->prepare('SELECT id_empleado, nombre, contrasena FROM EMPLEADOS WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$row = $stmt->fetch();
if ($row && password_verify($formPassword, $row['contrasena'])) {
    session_regenerate_id(true);
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'colaborador';
    $_SESSION['user_id']       = $row['id_empleado'];
    $_SESSION['user_name']     = $row['nombre'];
    header('Location: ../Colaborador.html');
    exit;
}

// ── 4. Cliente (CLIENTES) ────────────────────────────────────
$stmt = $pdo->prepare('SELECT id_cliente, nombre, contrasena FROM CLIENTES WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$row = $stmt->fetch();
if ($row && password_verify($formPassword, $row['contrasena'])) {
    session_regenerate_id(true);
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'cliente';
    $_SESSION['user_id']       = $row['id_cliente'];
    $_SESSION['user_name']     = $row['nombre'];
    header('Location: ../Cliente.html');
    exit;
}

// ── 5. Credenciales inválidas ────────────────────────────────
header('Location: ../Login.html?error=auth');
exit;
