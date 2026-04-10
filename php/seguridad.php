<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== 'SI') {
    session_unset();
    session_destroy();
    header("Location: ../Login.html?error=auth_required");
    exit();
}
?>
