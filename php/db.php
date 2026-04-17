<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_USER', 'proydweb_bd2026');
define('DB_PASS', 'DWeb_p2@26');
define('DB_NAME', 'proydweb_p2026');

function Conectarse() {
    // Intentamos obtener las variables de Railway, si no existen usamos los valores de tu TablePlus/Local
    $host = getenv('MYSQLHOST')     ?: 'nozomi.proxy.rlwy.net'; 
    $user = getenv('MYSQLUSER')     ?: 'root'; 
    $pass = getenv('MYSQLPASSWORD') ?: 'SsQyBAOSQZaLSsChHbgdNTIelHbVYDlz'; // Pon la que copiaste de Railway
    $db   = getenv('MYSQLDATABASE') ?: 'railway'; 
    $port = (int)getenv('MYSQLPORT') ?: 3306; // El puerto que te de Railway (ej. 39281)

    // Conexión usando las variables
    $link = @mysqli_connect($host, $user, $pass, $db, $port);
    
    return $link ? $link : false;
}

/**
 * Devuelve todas las filas de un stmt ya ejecutado como array asociativo.
 * Alternativa a mysqli_stmt_get_result() que no requiere mysqlnd.
 */
function stmt_rows(mysqli_stmt $stmt) {
    $meta = mysqli_stmt_result_metadata($stmt);
    if (!$meta) return array();

    $fields = array();
    $row    = array();
    while ($f = mysqli_fetch_field($meta)) {
        $fields[]      = $f->name;
        $row[$f->name] = null;
    }
    mysqli_free_result($meta);

    // Construir $args con referencias directas — array_merge las destruye
    $args = array($stmt);
    foreach ($fields as $name) {
        $args[] = &$row[$name];
    }
    call_user_func_array('mysqli_stmt_bind_result', $args);

    $result = array();
    while (mysqli_stmt_fetch($stmt)) {
        $copy = array();
        foreach ($fields as $name) {
            $copy[$name] = $row[$name];
        }
        $result[] = $copy;
    }
    mysqli_stmt_free_result($stmt);
    return $result;
}

/**
 * Devuelve la primera fila de un stmt ya ejecutado, o null si no hay filas.
 */
function stmt_row(mysqli_stmt $stmt) {
    $rows = stmt_rows($stmt);
    return isset($rows[0]) ? $rows[0] : null;
}

/**
 * Devuelve el valor de la primera columna de la primera fila.
 */
function stmt_value(mysqli_stmt $stmt) {
    $row = stmt_row($stmt);
    if (!$row) return null;
    $vals = array_values($row);
    return $vals[0];
}
?>
