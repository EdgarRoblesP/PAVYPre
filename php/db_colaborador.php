<?php
/**
 * Conexión a la base de datos para el portal Colaborador.
 * Usuario: colaborador@localhost — solo lectura.
 */
$host     = 'localhost';
$dbname   = 'pavypre';
$username = 'colaborador';
$password = 'iC2J&8zQ6sJd%tmAN2&6Kp';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}
