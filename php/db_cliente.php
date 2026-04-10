<?php
/**
 * Conexión a la base de datos para el portal Cliente.
 * Usuario: cliente@localhost — solo lectura.
 */
$host     = 'localhost';
$dbname   = 'pavypre';
$username = 'cliente';
$password = 'Y%LVkp286s%THCKEyceR4B';
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
