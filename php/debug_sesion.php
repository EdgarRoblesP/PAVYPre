<?php
session_start();
header('Content-Type: application/json');
echo json_encode([
    'session'      => $_SESSION,
    'user_role'    => $_SESSION['user_role'] ?? '(no definido)',
    'es_admin'     => (($_SESSION['user_role'] ?? '') === 'admin'),
    'session_id'   => session_id(),
]);
