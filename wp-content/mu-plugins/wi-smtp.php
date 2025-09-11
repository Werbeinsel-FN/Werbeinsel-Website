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
// == Diagnostic REST route (doesn't depend on plugin activation) ==
add_action('rest_api_init', function () {
  register_rest_route('wi/v1', '/mailtest', array(
    'methods'  => 'POST',
    'callback' => function (WP_REST_Request $req) {
      $data  = $req->get_json_params();
      $token = isset($data['token']) ? sanitize_text_field($data['token']) : '';
      if (!defined('SMTP_TEST_TOKEN') || $token !== SMTP_TEST_TOKEN) {
        return new WP_Error('forbidden', 'Bad token.', array('status' => 403));
      }
      $to = isset($data['to']) && is_email($data['to']) ? $data['to']
           : (defined('SMTP_FROM') ? SMTP_FROM : get_option('admin_email'));
      $ok = wp_mail($to, 'WI SMTP test', 'Test message from MU-plugin mailtest');
      if ($ok) return new WP_REST_Response(array('ok' => true), 200);

      $err = isset($GLOBALS['WI_LAST_MAIL_ERROR']) && is_wp_error($GLOBALS['WI_LAST_MAIL_ERROR'])
        ? $GLOBALS['WI_LAST_MAIL_ERROR']->get_error_messages()
        : array('unknown error');
      return new WP_REST_Response(array('ok' => false, 'error' => $err), 500);
    },
    'permission_callback' => '__return_true',
  ));
});