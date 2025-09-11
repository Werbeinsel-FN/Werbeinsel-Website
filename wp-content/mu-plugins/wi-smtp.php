<?php
/**
 * Plugin Name: WI SMTP (PHPMailer)
 * Description: Forsira WordPress da šalje mail preko SMTP-a (SES/IONOS/Strato/Gmail...) korišćenjem konstanti ili env varijabli.
 * Version: 1.0.0
 */
// Učitaj SMTP konstante ako fajl postoji
if (file_exists(__DIR__ . '/../smtp-config.php')) {
  require_once __DIR__ . '/../smtp-config.php';
}
if (!defined('ABSPATH')) exit;

add_action('phpmailer_init', function($phpmailer) {
  // Prioritet: ENV > wp-config konstante. Ako ništa nije podešeno, ne diramo default (mail()).
  $host = getenv('SMTP_HOST') ?: (defined('SMTP_HOST') ? SMTP_HOST : '');
  if (!$host) return;

  $phpmailer->isSMTP();
  $phpmailer->Host       = $host;
  $phpmailer->SMTPAuth   = true;
  $phpmailer->Port       = (int)(getenv('SMTP_PORT') ?: (defined('SMTP_PORT') ? SMTP_PORT : 587));
  $phpmailer->SMTPSecure = getenv('SMTP_SECURE') ?: (defined('SMTP_SECURE') ? SMTP_SECURE : 'tls');
  $phpmailer->Username   = getenv('SMTP_USER') ?: (defined('SMTP_USER') ? SMTP_USER : '');
  $phpmailer->Password   = getenv('SMTP_PASS') ?: (defined('SMTP_PASS') ? SMTP_PASS : '');

  $from      = getenv('SMTP_FROM') ?: (defined('SMTP_FROM') ? SMTP_FROM : 'no-reply@werbeinsel.de');
  $from_name = getenv('SMTP_FROM_NAME') ?: (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : get_bloginfo('name'));

  // setFrom treći parametar false => ne prepisuje ručno dodate headere (npr. Reply-To).
  $phpmailer->setFrom($from, $from_name, false);
  // Envelope sender za DMARC/bounce:
  $phpmailer->Sender = $from;
});

// Log ako nešto pukne
add_action('wp_mail_failed', function($err) {
  error_log('WP_MAIL_FAILED: ' . print_r($err->get_error_messages(), true));
  error_log('WP_MAIL_FAILED DATA: ' . print_r($err, true));
});
