<?php
/**
 * Solicita un enlace de recuperación de contraseña.
 * POST: email
 */
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$email = trim(strtolower($_POST['email'] ?? ''));

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Correo electrónico inválido.']);
    exit;
}

$respuestaGenerica = json_encode(['success' => true]);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail_config.php';

require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$link = Conectarse();

// ── Crear tabla pv_reset_tokens si no existe ─────────────────────────────────
mysqli_query($link,
    'CREATE TABLE IF NOT EXISTS pv_reset_tokens (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        email       VARCHAR(320) NOT NULL,
        token       CHAR(64)     NOT NULL,
        expires_at  DATETIME     NOT NULL,
        used        TINYINT(1)   NOT NULL DEFAULT 0,
        created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_token (token),
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

// ── Buscar usuario: pv_empleados o pv_clientes ────────────────────────────────
$tabla    = null;
$userName = '';

$s = mysqli_prepare($link, 'SELECT nombre FROM pv_empleados WHERE email = ? LIMIT 1');
mysqli_stmt_bind_param($s, 's', $email);
mysqli_stmt_execute($s);
$row = stmt_row($s);
if ($row) {
    $tabla    = 'pv_empleados';
    $userName = $row['nombre'];
}

if (!$tabla) {
    $s = mysqli_prepare($link, 'SELECT nombre FROM pv_clientes WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($s, 's', $email);
    mysqli_stmt_execute($s);
    $row = stmt_row($s);
    if ($row) {
        $tabla    = 'pv_clientes';
        $userName = $row['nombre'];
    }
}

if (!$tabla) {
    echo $respuestaGenerica;
    exit;
}

// ── Eliminar tokens previos no usados del mismo email ───────────────────────
$del = mysqli_prepare($link, 'DELETE FROM pv_reset_tokens WHERE email = ?');
mysqli_stmt_bind_param($del, 's', $email);
mysqli_stmt_execute($del);

// ── Generar token seguro ─────────────────────────────────────────────────────
$token     = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', time() + 1800);

$ins = mysqli_prepare($link, 'INSERT INTO pv_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($ins, 'sss', $email, $token, $expiresAt);
mysqli_stmt_execute($ins);

// ── Construir enlace de recuperación ────────────────────────────────────────
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseDir   = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$enlace    = $protocolo . '://' . $host . $baseDir . '/RecuperarContrasena.html?token=' . $token;

// ── Enviar correo con PHPMailer ──────────────────────────────────────────────
try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress($email, $userName);

    $mail->isHTML(true);
    $mail->Subject = '=?UTF-8?B?' . base64_encode('Recuperación de contraseña — PAVYPRE') . '?=';
    $mail->Body    = '
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:0;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:32px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0"
             style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);">
        <tr>
          <td style="background:#FFC107;padding:28px 40px;">
            <h1 style="margin:0;font-size:26px;color:#000;font-weight:bold;">PAVYPRE</h1>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 40px;color:#222;">
            <p style="font-size:18px;margin-top:0;">Hola, <strong>' . htmlspecialchars($userName, ENT_QUOTES) . '</strong></p>
            <p style="font-size:15px;line-height:1.6;">
              Recibimos una solicitud para restablecer la contraseña asociada a este correo.
              Haz clic en el botón de abajo para crear una nueva contraseña.
            </p>
            <p style="font-size:13px;color:#666;">El enlace es válido por <strong>30 minutos</strong> y solo puede usarse una vez.</p>
            <table cellpadding="0" cellspacing="0" style="margin:32px 0;">
              <tr>
                <td style="background:#FFC107;border-radius:30px;">
                  <a href="' . $enlace . '"
                     style="display:inline-block;padding:14px 36px;font-size:16px;font-weight:bold;color:#000;text-decoration:none;">
                    Restablecer contraseña
                  </a>
                </td>
              </tr>
            </table>
            <p style="font-size:13px;color:#666;">
              Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
              <a href="' . $enlace . '" style="color:#B8860B;word-break:break-all;">' . $enlace . '</a>
            </p>
            <hr style="border:none;border-top:1px solid #eee;margin:28px 0;">
            <p style="font-size:13px;color:#999;margin:0;">
              Si no solicitaste este cambio, puedes ignorar este mensaje. Tu contraseña no será modificada.
            </p>
          </td>
        </tr>
        <tr>
          <td style="background:#111;padding:18px 40px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#888;">© PAVYPRE — Todos los derechos reservados</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';

    $mail->AltBody = "Hola {$userName},\r\n\r\n"
                   . "Recibimos una solicitud para restablecer tu contraseña.\r\n\r\n"
                   . "Enlace de recuperación (válido 30 min):\r\n{$enlace}\r\n\r\n"
                   . "Si no solicitaste este cambio, ignora este mensaje.\r\n\r\n"
                   . "— Equipo PAVYPRE";

    $mail->send();

} catch (Exception $e) {
    error_log('[PAVYPRE] Error al enviar correo de recuperación a ' . $email . ': ' . $e->getMessage());
}

echo $respuestaGenerica;
