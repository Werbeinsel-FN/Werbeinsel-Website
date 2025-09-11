<?php
/**
 * Auto-aktivacija WP Mail SMTP (jednokratno).
 */
if (!defined('ABSPATH')) exit;

add_action('plugins_loaded', function () {
  include_once ABSPATH . 'wp-admin/includes/plugin.php';

  $plugin_slug = 'wp-mail-smtp/wp_mail_smtp.php';

  // Ako nije aktivan, aktiviraj i odmah se samoubij (obriši sebe posle).
  if (!is_plugin_active($plugin_slug)) {
    activate_plugin($plugin_slug, '', false, false);
  }
});
