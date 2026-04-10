<?php
/**
 * Conexión a la base de datos para el portal Admin.
 * Usuario: admin@localhost — todos los privilegios.
 */
$host     = 'localhost';
$dbname   = 'pavypre';
$username = 'admin';
$password = 'X7$Yp#3njq^!nq!HyB!b5W';
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
