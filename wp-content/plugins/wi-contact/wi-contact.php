<?php
/**
 * Plugin Name: WI Contact Endpoint
 * Description: Prima JSON iz Next.js forme i šalje email na hallo@werbeinsel.de
 * Version: 1.1.0
 */
// TEMP debug: uhvati poslednju mail grešku
$GLOBALS['WI_LAST_MAIL_ERROR'] = null;
add_action('wp_mail_failed', function($err){
  $GLOBALS['WI_LAST_MAIL_ERROR'] = $err;
});
if (!defined('ABSPATH')) exit;

// === Test endpoint za slanje maila i čitanje greške (privremeno) ===
add_action('rest_api_init', function () {
  register_rest_route('wi/v1', '/mailtest', array(
    'methods'  => 'POST',
    'callback' => function (WP_REST_Request $req) {
      $data  = $req->get_json_params();
      $token = isset($data['token']) ? sanitize_text_field($data['token']) : '';
      if (!defined('SMTP_TEST_TOKEN') || $token !== SMTP_TEST_TOKEN) {
        return new WP_Error('forbidden', 'Bad token.', array('status' => 403));
      }

      $to = isset($data['to']) && is_email($data['to']) ? $data['to'] : (defined('SMTP_FROM') ? SMTP_FROM : get_option('admin_email'));
      $ok = wp_mail($to, 'WI SMTP test', 'Test message from /wi/v1/mailtest');

      if ($ok) {
        return new WP_REST_Response(array('ok' => true), 200);
      }
      $err = isset($GLOBALS['WI_LAST_MAIL_ERROR']) && is_wp_error($GLOBALS['WI_LAST_MAIL_ERROR'])
        ? $GLOBALS['WI_LAST_MAIL_ERROR']->get_error_messages()
        : array('unknown error');
      return new WP_REST_Response(array('ok' => false, 'error' => $err), 500);
    },
    'permission_callback' => '__return_true',
  ));
});
// (Opcionalno) CORS ako šalješ sa drugog origin-a (ako je forma na drugom domenu)
add_action('rest_api_init', function () {
  remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
  add_filter('rest_pre_serve_request', function ($value) {
    $origin = defined('WI_ALLOWED_ORIGIN') ? WI_ALLOWED_ORIGIN : '*';
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    return $value;
  });
}, 15);

// REST ruta
add_action('rest_api_init', function () {
  register_rest_route('wi/v1', '/contact', array(
    'methods'  => 'POST',
    'callback' => 'wi_handle_contact',
    'permission_callback' => '__return_true',
  ));
});

function wi_handle_contact(WP_REST_Request $request) {
  $data = $request->get_json_params();

  $name     = isset($data['name'])     ? sanitize_text_field($data['name']) : '';
  $email    = isset($data['email'])    ? sanitize_email($data['email']) : '';
  $company  = isset($data['company'])  ? sanitize_text_field($data['company']) : '';
  $phone    = isset($data['phone'])    ? sanitize_text_field($data['phone']) : '';
  $budget   = isset($data['budget'])   ? sanitize_text_field($data['budget']) : '';
  $timeline = isset($data['timeline']) ? sanitize_text_field($data['timeline']) : '';
  $medium   = isset($data['medium'])   ? sanitize_text_field($data['medium']) : '';
  $message  = isset($data['message'])  ? wp_kses_post($data['message']) : '';

  if (empty($name) || empty($email) || !is_email($email)) {
    return new WP_Error('bad_request', 'Name und gültige E-Mail sind erforderlich.', array('status' => 400));
  }

  $to = apply_filters('wi_contact_to', 'hallo@werbeinsel.de');
  $subject = 'Neue Anfrage über Kontaktformular';

  $body  = '<h2>Neue Kontaktanfrage</h2>';
  $body .= '<p><strong>Name:</strong> ' . esc_html($name) . '</p>';
  $body .= '<p><strong>E-Mail:</strong> ' . esc_html($email) . '</p>';
  if ($company)  $body .= '<p><strong>Unternehmen:</strong> ' . esc_html($company) . '</p>';
  if ($phone)    $body .= '<p><strong>Telefon:</strong> ' . esc_html($phone) . '</p>';
  if ($medium)   $body .= '<p><strong>Services:</strong> ' . esc_html($medium) . '</p>';
  if ($budget)   $body .= '<p><strong>Budget:</strong> ' . esc_html($budget) . '</p>';
  if ($timeline) $body .= '<p><strong>Zeitrahmen:</strong> ' . esc_html($timeline) . '</p>';
  if ($message)  $body .= '<p><strong>Nachricht:</strong><br>' . nl2br(wp_kses_post($message)) . '</p>';

  // From setuje MU-plugin; ovde samo Content-Type + Reply-To:
  $headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'Reply-To: ' . $name . ' <' . $email . '>',
  );

  $sent = wp_mail($to, $subject, $body, $headers);
  if (!$sent) {
  $err = isset($GLOBALS['WI_LAST_MAIL_ERROR']) && is_wp_error($GLOBALS['WI_LAST_MAIL_ERROR'])
    ? $GLOBALS['WI_LAST_MAIL_ERROR']->get_error_messages()
    : array('unknown error');
  return new WP_REST_Response(array('ok' => false, 'error' => $err), 500);
}
}
