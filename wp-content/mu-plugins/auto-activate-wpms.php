<?php
/**
 * Aktiviert WP Mail SMTP Pro nach einem Deployment einmalig.
 *
 * Hinweis: Läuft nur im Backend und merkt sich per Option, dass die
 * Aktivierung erledigt ist – vorher wurde der Code bei jedem Request
 * ausgeführt, und zwar mit einem falschen Plugin-Pfad.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', function () {
    if (get_option('wi_wpms_autoactivated') === '1') {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin = 'wp-mail-smtp-pro/wp_mail_smtp.php';

    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin) && !is_plugin_active($plugin)) {
        $result = activate_plugin($plugin, '', false, true);
        if (is_wp_error($result)) {
            error_log('WI: WP Mail SMTP konnte nicht aktiviert werden: ' . $result->get_error_message());
            return;
        }
    }

    update_option('wi_wpms_autoactivated', '1', false);
});
