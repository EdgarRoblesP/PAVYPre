<?php
// Sin session_start, sin includes — solo probar si db.php se puede parsear
ob_start();
$ok = @include_once __DIR__ . '/db.php';
$output = ob_get_clean();

if ($ok === false) {
    echo "FALLO: db.php no pudo incluirse<br>";
} else {
    echo "db.php incluido OK<br>";
}

if ($output !== '') {
    echo "Output de db.php: <pre>" . htmlspecialchars($output) . "</pre>";
}

echo "Conectarse existe: " . (function_exists('Conectarse') ? 'SI' : 'NO') . "<br>";
echo "stmt_rows existe: "  . (function_exists('stmt_rows')  ? 'SI' : 'NO') . "<br>";
echo "stmt_row existe: "   . (function_exists('stmt_row')   ? 'SI' : 'NO') . "<br>";
echo "stmt_value existe: " . (function_exists('stmt_value') ? 'SI' : 'NO') . "<br>";
