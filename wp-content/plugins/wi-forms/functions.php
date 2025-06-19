<?php
/*
Plugin Name: WI Forms
Description: Formular-Plugin for the Werbeinsel Agency
Version: 1.0
Author: Samuel Mettenmeyer
*/

defined('ABSPATH') or die('Kein direkter Zugriff erlaubt.');

// register form shortcode
function wi_render_contact_form() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/form-template.php';
    return ob_get_clean();
}
add_shortcode('wi_contact_form', 'wi_render_contact_form');

// register JS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'wi-forms-js',
        plugin_dir_url(__FILE__) . 'js/forms.js',
        [],
        '1.0',
        true
    );

    wp_localize_script('wi-forms-js', 'WIForms', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('wi_forms_nonce')
    ]);
});


function wi_handle_form() {
    check_ajax_referer('wi_forms_nonce', 'nonce');

    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        wp_send_json(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
    }

    // Beispiel: Nachricht speichern oder per Mail versenden
    // mail(...); oder wp_mail(...);

    wp_send_json(['success' => true, 'message' => 'Vielen Dank für Ihre Nachricht!']);
}
add_action('wp_ajax_wi_handle_form', 'wi_handle_form');
add_action('wp_ajax_nopriv_wi_handle_form', 'wi_handle_form');