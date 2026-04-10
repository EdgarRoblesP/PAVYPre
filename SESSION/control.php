<?php
// Autenticación contra la base de datos pavypre
session_start();

$user = trim($_POST['usuario']    ?? '');
$pass = trim($_POST['contrasena'] ?? '');

if (!$user || !$pass) {
    header("Location: index.php?errorusuario=1");
    exit;
}

// ── 1. Admin (credencial fija) ───────────────────────────────
if ($user === 'admin@pavypre.com' && $pass === '123456') {
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'admin';
    $_SESSION['user_name']     = 'Administrador';
    header("Location: aplicacion.php");
    exit;
}

// ── 2. Conexión a la BD pavypre ──────────────────────────────
require_once __DIR__ . '/../php/db_admin.php'; // $pdo disponible

// ── 3. Colaborador (tabla EMPLEADOS) ────────────────────────
$stmt = $pdo->prepare('SELECT id_empleado, nombre FROM EMPLEADOS WHERE email = ? AND contrasena = ? LIMIT 1');
$stmt->execute([$user, $pass]);
$row = $stmt->fetch();
if ($row) {
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'colaborador';
    $_SESSION['user_id']       = $row['id_empleado'];
    $_SESSION['user_name']     = $row['nombre'];
    header("Location: aplicacion.php");
    exit;
}

// ── 4. Cliente (tabla CLIENTES) ──────────────────────────────
$stmt = $pdo->prepare('SELECT id_cliente, nombre FROM CLIENTES WHERE email = ? AND contrasena = ? LIMIT 1');
$stmt->execute([$user, $pass]);
$row = $stmt->fetch();
if ($row) {
    $_SESSION['autentificado'] = 'SI';
    $_SESSION['user_role']     = 'cliente';
    $_SESSION['user_id']       = $row['id_cliente'];
    $_SESSION['user_name']     = $row['nombre'];
    header("Location: aplicacion.php");
    exit;
}

// ── 5. Credenciales inválidas ────────────────────────────────
header("Location: index.php?errorusuario=1");
exit;
?>