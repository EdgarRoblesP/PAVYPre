
<?php
/**
 * Configuración del servidor SMTP para PHPMailer.
 *
 * Ejemplos comunes:
 *
 * ── Gmail ────────────────────────────────────────────────────────────────────
 *   SMTP_HOST    = 'smtp.gmail.com'
 *   SMTP_PORT    = 587
 *   SMTP_SECURE  = 'tls'
 *   SMTP_USER    = 'tucuenta@gmail.com'
 *   SMTP_PASS    = 'contraseña de aplicación' (no tu contraseña normal;
 *                  generarla en: Cuenta Google → Seguridad → Contraseñas de app)
 *
 * ── Outlook / Hotmail ────────────────────────────────────────────────────────
 *   SMTP_HOST    = 'smtp.office365.com'
 *   SMTP_PORT    = 587
 *   SMTP_SECURE  = 'tls'
 *
 * ── Zoho Mail ────────────────────────────────────────────────────────────────
 *   SMTP_HOST    = 'smtp.zoho.com'
 *   SMTP_PORT    = 587
 *   SMTP_SECURE  = 'tls'
 */

define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_SECURE',    'tls');           // 'tls' (puerto 587) o 'ssl' (puerto 465)
define('SMTP_USER',      'roblesedgar797@gmail.com');
define('SMTP_PASS',      'vlgk aetd xswa xerm');   // Contraseña de aplicación

// Remitente que verán los destinatarios
define('MAIL_FROM',      'roblesedgar797@gmail.com');
define('MAIL_FROM_NAME', 'PAVYPRE');
