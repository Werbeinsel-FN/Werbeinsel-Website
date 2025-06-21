<?php
/*
Plugin Name: WI Forms
Description: Formular-Plugin for the Werbeinsel Agency
Version: 1.0
Author: Samuel Mettenmeyer
*/

defined('ABSPATH') or die('Kein direkter Zugriff erlaubt.');

///////////////////////////////////////////////////////////////////////
//	Register CPT
///////////////////////////////////////////////////////////////////////

function wi_forms_register_form_post_type() {
    $labels = array(
        'name'               => 'Formulare',
        'singular_name'      => 'Formular',
        'menu_name'          => 'Formulare',
        'name_admin_bar'     => 'Formulare',
        'add_new'            => 'Neues Formular',
        'add_new_item'       => 'Neues Formular hinzufügen',
        'edit_item'          => 'Formular bearbeiten',
        'new_item'           => 'Neues Formular',
        'view_item'          => 'Formular ansehen',
        'all_items'          => 'Alle Formulare',
        'search_items'       => 'Formulare durchsuchen',
        'not_found'          => 'Kein Formular gefunden',
        'not_found_in_trash' => 'Keine Formulare im Papierkorb',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'forms'),
        'supports'           => array('title', 'editor'),
        'menu_icon'          => 'dashicons-feedback',
        'show_in_rest'       => false,
    );

    register_post_type('wi_form', $args);
}
add_action('init', 'wi_forms_register_form_post_type');

///////////////////////////////////////////////////////////////////////
//	Adjust labels
///////////////////////////////////////////////////////////////////////

function wi_forms_updated_messages($messages) {
    global $post, $post_ID;

    $messages['wi_form'] = array(
        0  => '', // Nicht verwendet
        1  => 'Das Formular wurde aktualisiert. <a href="' . esc_url(get_permalink($post_ID)) . '">Formular ansehen</a>',
        2  => 'Benutzerdefiniertes Feld aktualisiert.',
        3  => 'Benutzerdefiniertes Feld gelöscht.',
        4  => 'Das Formular wurde aktualisiert.',
        5  => isset($_GET['revision']) ? 'Formular auf Revision vom ' . wp_post_revision_title((int)$_GET['revision'], false) . ' zurückgesetzt.' : false,
        6  => 'Das Formular wurde veröffentlicht. <a href="' . esc_url(get_permalink($post_ID)) . '">Formular ansehen</a>',
        7  => 'Das Formular wurde gespeichert.',
        8  => 'Das Formular wurde übermittelt. <a href="' . esc_url(get_permalink($post_ID)) . '">Formular ansehen</a>',
        9  => 'Formular geplant für: <strong>' . date_i18n('d.m.Y @ H:i', strtotime($post->post_date)) . '</strong>.',
        10 => 'Formular-Entwurf aktualisiert.',
    );

    return $messages;
}
add_filter('post_updated_messages', 'wi_forms_updated_messages');

function wi_forms_bulk_updated_messages($bulk_messages, $bulk_counts) {
    $bulk_messages['wi_form'] = array(
        'updated'   => _n('%s Formular wurde aktualisiert.', '%s Formulare wurden aktualisiert.', $bulk_counts['updated']),
        'locked'    => _n('%s Formular ist gesperrt und konnte nicht aktualisiert werden.', '%s Formulare sind gesperrt und konnten nicht aktualisiert werden.', $bulk_counts['locked']),
        'deleted'   => _n('%s Formular wurde gelöscht.', '%s Formulare wurden gelöscht.', $bulk_counts['deleted']),
        'trashed'   => _n('%s Formular wurde in den Papierkorb verschoben.', '%s Formulare wurden in den Papierkorb verschoben.', $bulk_counts['trashed']),
        'untrashed' => _n('%s Formular wurde wiederhergestellt.', '%s Formulare wurden wiederhergestellt.', $bulk_counts['untrashed']),
    );

    return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'wi_forms_bulk_updated_messages', 10, 2);

///////////////////////////////////////////////////////////////////////
//	Adjust editor
///////////////////////////////////////////////////////////////////////

function wi_disable_visual_editor_tab_for_forms() {
    global $post_type;
    if ($post_type === 'wi_form') {
        ?>
        <script>
            jQuery(document).ready(function($) {
                $('#postdivrich .wp-editor-tabs').find('.wp-switch-editor.switch-tmce').remove();
            });
        </script>
        <?php
    }
}
add_action('admin_footer-post.php', 'wi_disable_visual_editor_tab_for_forms');
add_action('admin_footer-post-new.php', 'wi_disable_visual_editor_tab_for_forms');

///////////////////////////////////////////////////////////////////////
//	Register CSS
///////////////////////////////////////////////////////////////////////

function wi_forms_enqueue_styles() {
    wp_enqueue_style(
        'wi-forms-style',
        plugin_dir_url(__FILE__) . 'css/form.css',
        array(),
        '1.0'
    );  

}
add_action('wp_enqueue_scripts', 'wi_forms_enqueue_styles');

///////////////////////////////////////////////////////////////////////
//	Register JS
///////////////////////////////////////////////////////////////////////

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

function wi_ajax_get_form() {
    $id = absint($_POST['id'] ?? 0);

    if (!$id) {
        wp_send_json_error('Keine ID übergeben');
    }

    $post = get_post($id);

    if (!$post || $post->post_type !== 'wi_form') {
        wp_send_json_error('Formular nicht gefunden');
    }
    
    wp_send_json_success([
        'content' => apply_filters('the_content', $post->post_content)
    ]);
}
add_action('wp_ajax_wi_get_form', 'wi_ajax_get_form');
add_action('wp_ajax_nopriv_wi_get_form', 'tsg_ajax_wi_form');

///////////////////////////////////////////////////////////////////////
//	Register Shortcodes
///////////////////////////////////////////////////////////////////////

function wi_forms_render_form($atts) {
    $atts = shortcode_atts([
        'id' => 0
    ], $atts);

    $form_post = get_post($atts['id']);

    // Only CPT form posts
    if (!$form_post || $form_post->post_type !== 'wi_form') {
        return '<p><strong>Formular nicht gefunden.</strong></p>';
    }

    // render content – optional: allow `do_shortcode`
    return do_shortcode($form_post->post_content);
}
add_shortcode('wi_form', 'wi_forms_render_form');

///////////////////////////////////////////////////////////////////////
//	Handler
///////////////////////////////////////////////////////////////////////

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