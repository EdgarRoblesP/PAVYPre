<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_USER', 'proydweb_bd2026');
define('DB_PASS', 'DWeb_p2@26');
define('DB_NAME', 'proydweb_p2026');

// Conexión a la base de datos con Railway (descomenta si quieres usar Railway)
/*
function Conectarse() {
    $host = getenv('MYSQLHOST')     ?: 'nozomi.proxy.rlwy.net';
    $user = getenv('MYSQLUSER')     ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'SsQyBAOSQZaLSsChHbgdNTIelHbVYDlz';
    $db   = getenv('MYSQLDATABASE') ?: 'railway';
    $port = (int)getenv('MYSQLPORT') ?: 3306;

    $link = @mysqli_connect($host, $user, $pass, $db, $port);
    return $link ? $link : false;
}
*/

// Conexión a la base de datos con Proydweb
function Conectarse() {
    mysqli_report(MYSQLI_REPORT_OFF);
    $link = @mysqli_connect('localhost', DB_USER, DB_PASS, DB_NAME, 3306);
    if ($link) mysqli_set_charset($link, 'utf8mb4');
    return $link ? $link : false;
}


/**
 * Genera el siguiente ID con formato PREFIJO + número de 3 dígitos (e.g. EMP001).
 * Reemplaza la llamada a sp_generar_id sin necesitar el stored procedure.
 */
function generarId(mysqli $link, string $tabla, string $columna, string $prefijo): string {
    $inicio = strlen($prefijo) + 1;
    $sql    = "SELECT COALESCE(MAX(CAST(SUBSTRING(`$columna`, $inicio) AS UNSIGNED)), 0) + 1 FROM `$tabla`";
    $res    = mysqli_query($link, $sql);
    $sig    = $res ? (int) mysqli_fetch_row($res)[0] : 1;
    return $prefijo . str_pad($sig, 3, '0', STR_PAD_LEFT);
}

/**
 * Devuelve todas las filas de un stmt ya ejecutado como array asociativo.
 * Usa bind_result para compatibilidad sin mysqlnd.
 * El @ suprime el aviso de deprecación de result_metadata en PHP 8.4.
 */
function stmt_rows(mysqli_stmt $stmt): array {
    $meta = @mysqli_stmt_result_metadata($stmt);
    if (!$meta) return array();

    $fields = array();
    $row    = array();
    while ($f = mysqli_fetch_field($meta)) {
        $fields[]      = $f->name;
        $row[$f->name] = null;
    }
    mysqli_free_result($meta);

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
function stmt_row(mysqli_stmt $stmt): ?array {
    $rows = stmt_rows($stmt);
    return $rows[0] ?? null;
}

/**
 * Devuelve el valor de la primera columna de la primera fila.
 */
function stmt_value(mysqli_stmt $stmt): mixed {
    $row = stmt_row($stmt);
    if (!$row) return null;
    return array_values($row)[0];
}
?>
