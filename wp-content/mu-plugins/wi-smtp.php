<?php
/**
 * Plugin Name: WI SMTP (PHPMailer)
 * Description: Forsira SMTP i loguje detalje kad slanje padne (za dijagnostiku).
 * Version: 1.1.0
 */
if (!defined('ABSPATH')) exit;

// Učitaj SMTP konstante ako fajl postoji (drži lozinku van wp-config.php)
if (file_exists(__DIR__ . '/../smtp-config.php')) {
  require_once __DIR__ . '/../smtp-config.php';
}

// Global za hvatanje zadnje wp_mail greške
$GLOBALS['WI_LAST_MAIL_ERROR'] = null;

add_action('phpmailer_init', function($phpmailer) {
  // Ako nema hosta, ne diramo ništa (koristi se podrazumevani mail())
  $host = getenv('SMTP_HOST') ?: (defined('SMTP_HOST') ? SMTP_HOST : '');
  if (!$host) return;

  $phpmailer->isSMTP();
  $phpmailer->Host       = $host;
  $phpmailer->SMTPAuth   = true;
  $phpmailer->Port       = (int)(getenv('SMTP_PORT') ?: (defined('SMTP_PORT') ? SMTP_PORT : 587));
  $phpmailer->SMTPSecure = getenv('SMTP_SECURE') ?: (defined('SMTP_SECURE') ? SMTP_SECURE : 'tls');
  $phpmailer->Username   = getenv('SMTP_USER') ?: (defined('SMTP_USER') ? SMTP_USER : '');
  $phpmailer->Password   = getenv('SMTP_PASS') ?: (defined('SMTP_PASS') ? SMTP_PASS : '');

  // Preporuke za kompatibilnost (IONOS/ostali)
  $phpmailer->SMTPAutoTLS = true; // STARTTLS ako je moguće
  $phpmailer->Timeout     = 15;   // sekundi

  // From i envelope (DMARC)
  $from      = getenv('SMTP_FROM') ?: (defined('SMTP_FROM') ? SMTP_FROM : '');
  $from_name = getenv('SMTP_FROM_NAME') ?: (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : get_bloginfo('name'));
  if ($from) {
    $phpmailer->setFrom($from, $from_name, false);
    $phpmailer->Sender = $from;
  }

  // Detaljan PHPMailer debug -> u error_log (privremeno dok ne rešimo)
  $phpmailer->SMTPDebug = 2;
  $phpmailer->Debugoutput = function($str, $level) {
    error_log("SMTP[$level]: $str");
  };
});

// Loguj i sačuvaj zadnju grešku da je možemo vratiti kroz REST
add_action('wp_mail_failed', function($wp_error) {
  $GLOBALS['WI_LAST_MAIL_ERROR'] = $wp_error;
  error_log('WP_MAIL_FAILED: ' . print_r($wp_error->get_error_messages(), true));
  error_log('WP_MAIL_FAILED DATA: ' . print_r($wp_error, true));
});
