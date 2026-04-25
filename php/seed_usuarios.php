<?php
/**
 * seed_usuarios.php — Siembra inicial de empleados y clientes.
 *
 * INSTRUCCIONES:
 *   1. Subir al servidor junto con db.php.
 *   2. Acceder una sola vez desde el navegador o CLI.
 *   3. Eliminar (o mover fuera del webroot) tras confirmar la inserción.
 *
 * Lógica: comprueba si cada ID ya existe antes de insertar (idempotente).
 * Contraseña plana '12345678' → hash Argon2id via password_hash().
 */

require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

$link = Conectarse();
if (!$link) {
    die('<p style="color:red">Error: no se pudo conectar a la base de datos.</p>');
}

// ════════════════════════════════════════════════════════
//  HELPER: imprime tabla HTML de un resultado mysqli
// ════════════════════════════════════════════════════════
function printTable(mysqli_result $res): void {
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    if (!$rows) {
        echo '<p><em>Sin registros.</em></p>';
        return;
    }
    echo '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:13px">';
    echo '<tr style="background:#222;color:#FFC107">';
    foreach (array_keys($rows[0]) as $col) {
        echo "<th>$col</th>";
    }
    echo '</tr>';
    foreach ($rows as $i => $row) {
        $bg = $i % 2 === 0 ? '#f9f9f9' : '#fff';
        echo "<tr style=\"background:$bg\">";
        foreach ($row as $val) {
            echo '<td>' . htmlspecialchars((string)($val ?? 'NULL')) . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}

// ════════════════════════════════════════════════════════
//  SELECT — estado actual de la BD
// ════════════════════════════════════════════════════════
echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
      <title>Seed Usuarios</title></head><body style="font-family:sans-serif;padding:24px">';

echo '<h1 style="color:#111">Seed: pv_empleados / pv_clientes</h1>';
echo '<hr>';

echo '<h2>📋 IDs existentes — pv_empleados</h2>';
$resEmp = mysqli_query($link,
    'SELECT id_empleado, nombre, puesto, email FROM pv_empleados ORDER BY id_empleado'
);
printTable($resEmp);

echo '<h2>📋 IDs existentes — pv_clientes</h2>';
$resCli = mysqli_query($link,
    'SELECT id_cliente, nombre, email FROM pv_clientes ORDER BY id_cliente'
);
printTable($resCli);

echo '<hr><h2>⚙️ Insertando registros…</h2>';

// ════════════════════════════════════════════════════════
//  Contraseña única para todos los registros de siembra
// ════════════════════════════════════════════════════════
$passwordPlano = '12345678';
$hash = password_hash($passwordPlano, PASSWORD_ARGON2ID);

// ════════════════════════════════════════════════════════
//  HELPER: inserta un empleado si el ID no existe
// ════════════════════════════════════════════════════════
function insertarEmpleado(
    mysqli $link,
    string $id, string $nombre, string $puesto,
    string $telefono, string $direccion, string $email,
    float  $salario, ?string $supervisor, string $hash
): void {
    // Verificar existencia
    $chk = mysqli_prepare($link,
        'SELECT COUNT(*) FROM pv_empleados WHERE id_empleado = ?'
    );
    mysqli_stmt_bind_param($chk, 's', $id);
    mysqli_stmt_execute($chk);
    if ((int) stmt_value($chk) > 0) {
        echo "<p>⏭️  <strong>$id</strong> ($nombre) — ya existe, omitido.</p>";
        return;
    }

    $stmt = mysqli_prepare($link,
        'INSERT INTO pv_empleados
            (id_empleado, nombre, puesto, telefono, direccion, email, salario, id_supervisor, contrasena)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param(
        $stmt, 'ssssssdss',
        $id, $nombre, $puesto, $telefono, $direccion, $email, $salario, $supervisor, $hash
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<p>✅ <strong>$id</strong> ($nombre) — insertado.</p>";
    } else {
        $err = mysqli_stmt_error($stmt);
        echo "<p style='color:red'>❌ <strong>$id</strong> ($nombre) — ERROR: $err</p>";
    }
}

// ════════════════════════════════════════════════════════
//  HELPER: inserta un cliente si el ID no existe
// ════════════════════════════════════════════════════════
function insertarCliente(
    mysqli $link,
    string $id, string $nombre, string $telefono,
    string $direccion, string $email, string $hash
): void {
    $chk = mysqli_prepare($link,
        'SELECT COUNT(*) FROM pv_clientes WHERE id_cliente = ?'
    );
    mysqli_stmt_bind_param($chk, 's', $id);
    mysqli_stmt_execute($chk);
    if ((int) stmt_value($chk) > 0) {
        echo "<p>⏭️  <strong>$id</strong> ($nombre) — ya existe, omitido.</p>";
        return;
    }

    $stmt = mysqli_prepare($link,
        'INSERT INTO pv_clientes
            (id_cliente, nombre, telefono, direccion, email, contrasena)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param(
        $stmt, 'ssssss',
        $id, $nombre, $telefono, $direccion, $email, $hash
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<p>✅ <strong>$id</strong> ($nombre) — insertado.</p>";
    } else {
        $err = mysqli_stmt_error($stmt);
        echo "<p style='color:red'>❌ <strong>$id</strong> ($nombre) — ERROR: $err</p>";
    }
}

// ════════════════════════════════════════════════════════
//  EMPLEADOS
//  Nota: EPO044 tenía typo 'Aministrador' → corregido a 'Administrador'
// ════════════════════════════════════════════════════════
echo '<h3>— Administradores —</h3>';

insertarEmpleado($link, 'EPO030', 'Lorna Rosas',      'Administrador', '2218756820', 'Sur 21, Puebla',                   'lornaveronica.rosas@upaep.mx',       22000.92, null, $hash);
insertarEmpleado($link, 'EPO032', 'Clarissa Pitol',   'Administrador', '2721889486', 'Avenida 2, Orizaba',               'clarissa.pitol@upaep.edu.mx',         10300.92, null, $hash);
insertarEmpleado($link, 'EPO044', 'Manuel Castillo',  'Administrador', '2213476789', 'Calle Lázaro Cárdenas, Puebla',    'manuel.castillo@upaep.edu.mx',         3045.60, null, $hash);
insertarEmpleado($link, 'EPO048', 'Gerardo Alvarado', 'Administrador', '2223412789', 'Calle 24 de Agosto, Puebla',       'gerardo.alvarado01@upaep.edu.mx',      7250.00, null, $hash);

echo '<h3>— Colaboradores —</h3>';

insertarEmpleado($link, 'EPO033', 'Jesus Pineda',      'Obrero',          '2223456789', 'Calle 5 de Febrero, Puebla',            'jesuseduardo.pineda@upaep.edu.mx',   1850.50, null, $hash);
insertarEmpleado($link, 'EPO034', 'Leonel Bautista',   'Obrero',          '2226256789', 'Calle Independencia, Puebla',           'leonelbaruch.bautista@upaep.edu.mx', 1920.75, null, $hash);
insertarEmpleado($link, 'EPO035', 'Jose Reyes',        'Obrero',          '2223454319', 'Calle Reforma, Puebla',                 'josepablo.reyes@upaep.mx',           2100.00, null, $hash);
insertarEmpleado($link, 'EPO036', 'Francisco Gil',     'Obrero',          '2213456789', 'Calle Juárez, Puebla',                  'franciscojavier.gil@upaep.mx',       2250.25, null, $hash);
insertarEmpleado($link, 'EPO037', 'Leonardo Cruz',     'Obrero',          '2223412349', 'Calle 16 de Septiembre, Puebla',        'leonardo.cruz02@upaep.mx',           2380.00, null, $hash);
insertarEmpleado($link, 'EPO038', 'Erick Hernandez',   'Sensorista',      '22231892',   'Calle 2 de Abril, Puebla',              'erick.hernandez01@upaep.mx',         3450.30, null, $hash);
insertarEmpleado($link, 'EPO039', 'Ernesto Montaño',   'Obrero',          '2223456732', 'Calle 20 de Noviembre, Puebla',         'ernesto.montano@upaep.mx',           2560.80, null, $hash);
insertarEmpleado($link, 'EPO040', 'Emiliano Castaños', 'Obrero',          '2228906789', 'Calle Benito Juárez, Puebla',           'emilianoandre.castanos@upaep.mx',    2675.00, null, $hash);
insertarEmpleado($link, 'EPO042', 'Fernando Leon',     'Obrero',          '2212351239', 'Calle Ignacio Zaragoza, Puebla',        'fernando.leon@upaep.mx',             2910.20, null, $hash);
insertarEmpleado($link, 'EPO043', 'Naomi Paul',        'Ingeniero Civil', '2223478909', 'Calle Manuel Ávila Camacho, Puebla',    'naomiastrid.paul@upaep.mx',          6850.00, null, $hash);
insertarEmpleado($link, 'EPO045', 'Andrea Martinez',   'Obrero',          '2228766789', 'Calle Francisco I. Madero, Puebla',     'andrea.martinez@upaep.mx',           3180.90, null, $hash);
insertarEmpleado($link, 'EPO046', 'Alexander Alvarez', 'Obrero',          '2210456789', 'Calle Venustiano Carranza, Puebla',     'alexanderivan.alvarez@upaep.edu.mx', 3325.15, null, $hash);
insertarEmpleado($link, 'EPO047', 'Paola Castillo',    'Obrero',          '2223009789', 'Calle Emiliano Zapata, Puebla',         'paola.castillo01@upaep.edu.mx',      3470.40, null, $hash);
insertarEmpleado($link, 'EPO049', 'Jose Cerezo',       'Obrero',          '2223009789', 'Calle 8 de Diciembre, Puebla',          'josedamian.cerezo@upaep.edu.mx',     3615.70, null, $hash);
insertarEmpleado($link, 'EPO050', 'Alberto Diaz',      'Obrero',          '2223654389', 'Calle 21 de Marzo, Puebla',             'alberto.diaz@upaep.edu.mx',          3760.85, null, $hash);

// ════════════════════════════════════════════════════════
//  CLIENTES
// ════════════════════════════════════════════════════════
echo '<h3>— Clientes —</h3>';

insertarCliente($link, 'CTE006', 'David Marroquin',    '2221123489', 'Calle 1 de Mayo, Puebla',       'davidabraham.marroquin@upaep.edu.mx',     $hash);
insertarCliente($link, 'CTE007', 'Alejandro Lita',     '2123467789', 'Calle 10 de Mayo, Puebla',      'alejandro.lita@upaep.edu.mx',             $hash);
insertarCliente($link, 'CTE008', 'Emilio Peralta',     '2223456780', 'Calle 12 de Octubre, Puebla',   'emiliotomas.peralta@upaep.edu.mx',        $hash);
insertarCliente($link, 'CTE009', 'Tomás Muñoz',        '2213498789', 'Calle 18 de Julio, Puebla',     'tomas.munoz@upaep.edu.mx',                $hash);
insertarCliente($link, 'CTE010', 'Angel Cerino',       '2220456789', 'Calle 25 de Diciembre, Puebla', 'angelgabriel.cerino@upaep.edu.mx',        $hash);
insertarCliente($link, 'CTE011', 'Javier Santiago',    '2223456709', 'Calle 6 de Enero, Puebla',      'javieralejandro.santiago@upaep.edu.mx',   $hash);
insertarCliente($link, 'CTE012', 'Diego Jalife',       '2223410969', 'Calle 19 de Abril, Puebla',     'diego.jalife@upaep.edu.mx',               $hash);
insertarCliente($link, 'CTE013', 'Alexander Alcazar',  '2218925649', 'Calle 3 de Junio, Puebla',      'alexanderyamil.alcazar@upaep.edu.mx',     $hash);
insertarCliente($link, 'CTE014', 'Jorge Piedras',      '2217893679', 'Calle 7 de Agosto, Puebla',     'jorgebryan.piedras@upaep.edu.mx',         $hash);
insertarCliente($link, 'CTE015', 'Johnny Arellano',    '2213420389', 'Calle 11 de Noviembre, Puebla', 'johnnydionicio.arellano@upaep.edu.mx',    $hash);

// ════════════════════════════════════════════════════════
//  SELECT final — estado después de la inserción
// ════════════════════════════════════════════════════════
echo '<hr><h2>✅ Estado final — pv_empleados</h2>';
$resEmp2 = mysqli_query($link,
    'SELECT id_empleado, nombre, puesto, email FROM pv_empleados ORDER BY id_empleado'
);
printTable($resEmp2);

echo '<h2>✅ Estado final — pv_clientes</h2>';
$resCli2 = mysqli_query($link,
    'SELECT id_cliente, nombre, email FROM pv_clientes ORDER BY id_cliente'
);
printTable($resCli2);

echo '<hr><p style="color:gray;font-size:12px">
      ⚠️ Elimina este archivo del servidor una vez confirmada la inserción.</p>';
echo '</body></html>';
