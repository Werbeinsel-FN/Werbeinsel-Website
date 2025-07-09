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
        'rewrite'            => array('slug' => 'form'),
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
//	Shortcodes
///////////////////////////////////////////////////////////////////////

// [wi_form id="123"]
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

// Helper function for attribute processment
function wi_forms_parse_shortcode_atts($atts, $defaults = []) {
    return shortcode_atts($defaults, $atts);
}

// [text] & [text*]
function wi_forms_register_text_shortcode($atts, $content = null, $tag = '') {
    $required = ($tag === 'text*') ? 'required' : '';
    $atts = shortcode_atts([
        'name' => '',
        'id' => '',
        'autocomplete' => '',
        'placeholder' => '',
    ], $atts);

    $name = $atts['name'] ?: 'text';
    $id = $atts['id'] ?: $name;

    $classes = ['form-control'];
    if ($required) $classes[] = 'required';

    return sprintf(
        '<input type="text" inputmode="text" name="wi_form[%s]" class="%s" id="wi_form_%s" autocomplete="%s" value="" placeholder="%s"%s>',
        esc_attr($name),
        esc_attr(implode(' ', $classes)),
        esc_attr($name),
        esc_attr($atts['autocomplete']),
        esc_attr($content ?: $atts['placeholder']),
        $required ? ' required' : ''
    );
}
add_shortcode('text', 'wi_forms_register_text_shortcode');
add_shortcode('text*', 'wi_forms_register_text_shortcode');

// [email*]
function wi_forms_register_email_shortcode($atts, $content = null, $tag = '') {
    $required = ($tag === 'email*') ? 'required' : '';
    $atts = shortcode_atts([
        'name' => '',
        'id' => '',
        'autocomplete' => '',
        'placeholder' => '',
    ], $atts);

    // If there is no name, take 'email' as fallback
    $name = $atts['name'] ?: 'email';
    $id = $atts['id'] ?: 'email-input';

    // build classes
    $classes = ['form-control', 'validate-email'];
    if ($required) $classes[] = 'required';

    return sprintf(
        '<input type="email" inputmode="email" name="wi_form[%s]" class="%s" id="wi_form_%s" value="" placeholder="%s" %s>',
        esc_attr($name),
        esc_attr(implode(' ', $classes)),
        esc_attr($name),
        esc_attr($content ?: $atts['placeholder']),
        $required ? 'required' : ''
    );
}
add_shortcode('email', 'wi_forms_register_email_shortcode');
add_shortcode('email*', 'wi_forms_register_email_shortcode');

// [tel]
function wi_forms_register_tel_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'name' => '',
        'id' => '',
        'autocomplete' => '',
        'placeholder' => '',
    ], $atts);

    $name = $atts['name'] ?: 'tel';
    $id = $atts['id'] ?: $name;

    return sprintf(
        '<input type="tel" inputmode="tel" name="wi_form[%s]" class="form-control" id="wi_form_%s" autocomplete="%s" value="" placeholder="%s">',
        esc_attr($name),
        esc_attr($name),
        esc_attr($atts['autocomplete']),
        esc_attr($content ?: $atts['placeholder'])
    );
}
add_shortcode('tel', 'wi_forms_register_tel_shortcode');

// [textarea*]
function wi_forms_register_textarea_shortcode($atts, $content = null, $tag = '') {
    $required = ($tag === 'textarea*') ? 'required' : '';
    $atts = shortcode_atts([
        'name' => '',
        'id' => '',
        'placeholder' => '',
    ], $atts);

    $name = $atts['name'] ?: 'textarea';
    $id = $atts['id'] ?: $name;

    $classes = ['form-control'];
    if ($required) $classes[] = 'required';

    return sprintf(
        '<textarea name="wi_form[%s]" class="%s" id="wi_form_%s" placeholder="%s"%s></textarea>',
        esc_attr($name),
        esc_attr(implode(' ', $classes)),
        esc_attr($name),
        esc_attr($content ?: $atts['placeholder']),
        $required ? ' required' : ''
    );
}
add_shortcode('textarea', 'wi_forms_register_textarea_shortcode');
add_shortcode('textarea*', 'wi_forms_register_textarea_shortcode');

// [checkbox]
function wi_forms_register_checkbox_shortcode($atts, $content = null) {
    // name-Attribut holen und entfernen
    $name = isset($atts['name']) ? $atts['name'] : (isset($atts[0]) ? $atts[0] : 'checkbox');
    unset($atts['name']);

    // alle anderen Attributwerte (also "1", "2", "3"...) als Optionen behandeln
    $options = array_values(array_filter($atts, 'is_string'));

    $output = '<div class="wi-checkbox-group">';
    foreach ($options as $option) {
        $value = esc_attr($option);
        $id = 'wi_form_' . sanitize_title($name . '_' . $value);
        $output .= sprintf(
            '<label for="%s"><input type="checkbox" name="wi_form[%s][]" id="%s" value="%s" class="form-control-checkbox"> %s</label><br>',
            $id,
            esc_attr($name),
            esc_attr($id),
            $value,
            esc_html($option)
        );
    }
    $output .= '</div>';
    return $output;
}
add_shortcode('checkbox', 'wi_forms_register_checkbox_shortcode');

// [submit]
function wi_forms_register_submit_shortcode($atts = [], $content = null) {
    // read default class & label
    $atts = shortcode_atts([
        'class' => ''
    ], $atts);

    $class = esc_attr($atts['class']);
    $label = esc_html($content ?: 'Absenden');

    return sprintf('<button type="submit" class="%s">%s</button>', $class, $label);
}
add_shortcode('submit', 'wi_forms_register_submit_shortcode');


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