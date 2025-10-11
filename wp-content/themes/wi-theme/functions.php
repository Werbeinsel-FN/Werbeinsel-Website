<?php

function wi_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array(
        'primary' => __('Hauptmenü', 'wi-theme'),
        'footer'        => __('Footer Menu', 'wi-theme'),          // za Impressum/AGBs/...
        'footer_social' => __('Footer Social Menu', 'wi-theme'), 
    ));
}
add_action('after_setup_theme', 'wi_theme_setup');

///////////////////////////////////////////////////////////////////////
//	Register CSS
///////////////////////////////////////////////////////////////////////

function wi_theme_enqueue_styles() {


    // register main stylesheet
    wp_enqueue_style(
        'wi-style', 
        get_stylesheet_uri()
    );

    // register header stylesheet
    wp_enqueue_style(
        'wi-header-style', 
        get_template_directory_uri() . '/css/header.css', 
        array(), 
        filemtime(get_template_directory() . '/css/header.css')
    );    

    // register footer stylesheet
    wp_enqueue_style(
        'wi-footer-style', 
        get_template_directory_uri() . '/css/footer.css', 
        array(), 
        filemtime(get_template_directory() . '/css/footer.css')
    );

    // register mainmenu stylesheet
        wp_enqueue_style(
        'wi-mainmenu-style',
        get_template_directory_uri() . '/css/mainmenu.css',
        array(),
        filemtime(get_template_directory() . '/css/mainmenu.css')
    );
    wp_enqueue_style(
    'wi-contact-style',
    get_template_directory_uri() . '/css/contact.css',
    array('wi-style'), // zavisi od glavnog stila
    filemtime(get_template_directory() . '/css/contact.css')
);
  wp_enqueue_script(
        'wi-contact-js',
        get_template_directory_uri() . '/js/contact.js',
        array('jquery'),
        filemtime(get_template_directory() . '/js/contact.js'),
        true
    );
    // load foundation icons
    wp_enqueue_style(
        'foundation-icons',
        'https://cdn.jsdelivr.net/npm/foundation-icons/foundation-icons.css',
        array(), // no dependencies
        null     // no fixed version no
    );    
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_styles');

function wi_theme_widgets_init() {
	// register sidebar widget area
    register_sidebar(array(
        'name' => 'Sidebar',
        'id' => 'sidebar-1',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
	// register footer widget area
    register_sidebar(array(
        'name' => 'Footer',
        'id' => 'footer-widget',
        'before_widget' => '<div class="footer-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'wi_theme_widgets_init');

///////////////////////////////////////////////////////////////////////
//	Register JS
///////////////////////////////////////////////////////////////////////

function wi_theme_enqueue_js() {
    if (is_page('kontakt-new')) {
        wp_enqueue_script(
            'wi-theme-js',
            get_template_directory_uri() . '/js/contact.js',
            array(),
            '1.0',
            true
        );
    }
    if (is_page('datenschutz')) {
        wp_enqueue_script(
            'wi-theme-js',
            get_template_directory_uri() . '/js/datenschutz.js',
            array(),
            '1.0',
            true
        );
    }
    wp_enqueue_script(
        'holi-theme-admin-js', 
        get_template_directory_uri() . '/js/mainmenu.js', 
        array('jquery'), 
        null, 
        true
    );
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_js');
add_action('wp_enqueue_scripts', function () {
    // CSS (već imaš)
    wp_enqueue_style('wi-style', get_stylesheet_uri());

    // JS – obavezno učitaj jQuery pa naš fajl
    wp_enqueue_script(
        'wi-mainmenu',
        get_template_directory_uri() . '/js/mainmenu.js',
        array('jquery'),
        filemtime(get_template_directory() . '/js/mainmenu.js'),
        true
    );
});
///////////////////////////////////////////////////////////////////////
//	Theme Options
///////////////////////////////////////////////////////////////////////

function wi_theme_add_admin_menu() {
    add_menu_page(
        __('Theme Options', 'wi-theme'), // Seitenname
        __('Theme Options', 'wi-theme'), // Menüname
        'manage_options',                 // Berechtigung
        'wi-theme-options',             // Slug
        'wi_theme_options_page',        // Callback-Funktion
        '',                               // Icon (leer = Standard)
        61                                // Position im Menü
    );
}
add_action('admin_menu', 'wi_theme_add_admin_menu');

function wi_theme_options_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Theme Options', 'wi-theme'); ?></h1>    
       
        <?php
        // if there is a saved msg it is shown here
        settings_errors();
        ?>
       
        <form method="post" action="options.php">
            <?php
            // load settings and show fields
            settings_fields('wi_theme_options_group');
            do_settings_sections('wi-theme-options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function wi_theme_options_page_save_feedback() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        add_settings_error(
            'wi_theme_options_group', // id of settings group
            'wi_theme_success', // id of msg
            __('Änderungen gespeichert!', 'wi-theme'), // the msg
            'updated' // success msg style
        );
    }
}
add_action('admin_notices', 'wi_theme_options_page_save_feedback');

function wi_theme_settings_init() {
    // register options
    register_setting('wi_theme_options_group', 'wi_theme_logo_light');
    register_setting('wi_theme_options_group', 'wi_theme_logo_dark');
    register_setting('wi_theme_options_group', 'wi_theme_background_color');
    register_setting('wi_theme_options_group', 'wi_theme_main_font_color');
	register_setting('wi_theme_options_group', 'wi_theme_menu_font');

    add_settings_section(
        'wi_theme_settings_section',
        __('Allgemeine Einstellungen', 'wi-theme'),
        'wi_theme_settings_section_callback',
        'wi-theme-options'
    );

    add_settings_field(
        'wi_theme_logo_light',
        __('Light Logo:', 'wi-theme'),
        'wi_theme_logo_light_render',
        'wi-theme-options',
        'wi_theme_settings_section'
    );

    add_settings_field(
        'wi_theme_logo_dark',
        __('Dark Logo:', 'wi-theme'),
        'wi_theme_logo_dark_render',
        'wi-theme-options',
        'wi_theme_settings_section'
    );

    add_settings_field(
        'wi_theme_background_color',
        __('Hintergrundfarbe:', 'wi-theme'),
        'wi_theme_background_color_render', // Callback for color picker
        'wi-theme-options',
        'wi_theme_settings_section'
    );
    
    add_settings_field(
        'wi_theme_main_font_color',
        __('Haupt-Schriftfarbe:', 'wi-theme'),
        'wi_theme_main_font_color_render',
        'wi-theme-options',
        'wi_theme_settings_section'
    );
	
	add_settings_field(
        'wi_theme_menu_font',
        __('Menü-Schriftart:', 'wi-theme'),
        'wi_theme_menu_font_render',
        'wi-theme-options',
        'wi_theme_settings_section'
    );
}
add_action('admin_init', 'wi_theme_settings_init');

function wi_theme_settings_section_callback() {
    echo __('Hier kann man allgemeine Einstellungen für das Theme anpassen.', 'wi-theme');
}

function wi_theme_option_example_render() {
    $value = get_option('wi_theme_option_example', '');
    ?>
    <input type="text" name="wi_theme_option_example" value="<?php echo esc_attr($value); ?>" placeholder="z. B. Deine Website-Farbe">
    <?php
}

function wi_theme_logo_light_render() {
    $logo_url = get_option('wi_theme_logo_light', '');
    ?>
    <div id="wi_theme_logo_light_preview" style="margin-bottom: 10px;">
        <?php if ($logo_url): ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php _e('Light-Logo Vorschau', 'wi-theme'); ?>" style="max-width: 200px;">
        <?php else: ?>
            <p><?php _e('Kein Light-Logo ausgewählt.', 'wi-theme'); ?></p>
        <?php endif; ?>
    </div>
    <button type="button" class="button" id="wi_theme_logo_light_button"><?php _e('Light-Logo auswählen', 'wi-theme'); ?></button>
    <input type="hidden" name="wi_theme_logo_light" id="wi_theme_logo_light" value="<?php echo esc_attr($logo_url); ?>" />
    <?php
}

function wi_theme_logo_dark_render() {
    $logo_url = get_option('wi_theme_logo_dark', '');
    ?>
    <div id="wi_theme_logo_dark_preview" style="margin-bottom: 10px;">
        <?php if ($logo_url): ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php _e('Dark-Logo Vorschau', 'wi-theme'); ?>" style="max-width: 200px;">
        <?php else: ?>
            <p><?php _e('Kein Dark-Logo ausgewählt.', 'wi-theme'); ?></p>
        <?php endif; ?>
    </div>
    <button type="button" class="button" id="wi_theme_logo_dark_button"><?php _e('Dark-Logo auswählen', 'wi-theme'); ?></button>
    <input type="hidden" name="wi_theme_logo_dark" id="wi_theme_logo_dark" value="<?php echo esc_attr($logo_url); ?>" />
    <?php
}

function wi_theme_background_color_render() {
    $background_color = get_option('wi_theme_background_color', '#ffffff'); // default: white
    ?>
    <input type="text" name="wi_theme_background_color" id="wi_theme_background_color" value="<?php echo esc_attr($background_color); ?>" />
    <div id="color-picker"></div>
    <script>
        (function($) {
            $(document).ready(function() {
                $('#wi_theme_background_color').wpColorPicker();
            });
        })(jQuery);
    </script>
    <?php
}

function wi_theme_main_font_color_render() {
    $font_color = get_option('wi_theme_main_font_color', '#333333'); // set default color
    ?>
    <input type="text" name="wi_theme_main_font_color" value="<?php echo esc_attr($font_color); ?>" class="my-color-field" data-default-color="#333333" />
    <?php
}

function wi_theme_menu_font_render() {
    $selected_font = get_option('wi_theme_menu_font', 'Arial'); // default: Arial
    $fonts = array('Arial', 'Verdana', 'Times New Roman', 'Georgia', 'Courier New', 'Roboto', 'Open Sans'); // list of availiable font families
    ?>
    <select name="wi_theme_menu_font">
        <?php foreach ($fonts as $font): ?>
            <option value="<?php echo esc_attr($font); ?>" <?php selected($selected_font, $font); ?>>
                <?php echo esc_html($font); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function wi_theme_add_inline_styles() {
    $menu_font = get_option('wi_theme_menu_font', 'Arial');
    $main_font = get_option('wi_theme_main_font', 'Arial');
	$heading_font = get_option('wi_theme_heading_font', 'Arial');
	$text_font_size = get_option('wi_theme_text_font_size', '25');
 
    echo "<style>
        :root {
            --menu-font: '{$menu_font}';
            --main-font: '{$main_font}';
			--heading-font: '{$heading_font}';
            --e-global-typography-text-font-family: '{$main_font}'; /* Elementor Schriftart setzen */
			--e-global-typography-heading-font-family: '{$heading_font}';
			--text-font-size: {$text_font_size}px;
			--color-main-text: #fff;
        }
                              
		body {
            font-size: var(--text-font-size);
        }
    </style>";
}
add_action('wp_head', 'wi_theme_add_inline_styles');

/* chosen color to font color */
function wi_theme_custom_css() {
    $main_font_color = esc_attr(get_option('wi_theme_main_font_color', '#333333'));
    ?>
    <style>
        body {
            color: <?php echo $main_font_color; ?>;
        }
        a {
            color: <?php echo $main_font_color; ?>;
        }
        a:hover {
            color: <?php echo adjust_color_brightness($main_font_color, -30); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'wi_theme_custom_css');

function wi_theme_enqueue_google_fonts() {
    $menu_font = get_option('wi_theme_menu_font', 'Arial');
    $main_font = get_option('wi_theme_main_font', 'Arial');
    $heading_font = get_option('wi_theme_heading_font', 'Arial');
    
    if ($heading_font && $heading_font !== 'Arial') {
        wp_enqueue_style('wi-theme-heading-font', 'https://fonts.googleapis.com/css2?family=' . urlencode($heading_font) . ':wght@400;700&display=swap', false);
    }
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_google_fonts');

function wi_theme_enqueue_roboto_font() {
    wp_enqueue_style(
        'roboto-font',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_roboto_font');

/**
 * helper-function for color brightness adjustment
 *
 * @param string $hex hex color (#RRGGBB).
 * @param int $steps step amount (-255 bis 255).
 * @return string adjusted color (#RRGGBB).
 */
function adjust_color_brightness($hex, $steps) {
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));

    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}

function wi_theme_admin_scripts($hook) {
    // Hook name for the theme options page
    if ($hook !== 'toplevel_page_wi-theme-options') {
        return;
    }

    wp_enqueue_media(); // load media library
    wp_enqueue_script('wi-theme-admin-js', get_template_directory_uri() . '/js/admin.js', array('jquery'), null, true);
    wp_enqueue_style('wp-color-picker'); // color picker
    wp_enqueue_script('wp-color-picker'); // color picker script
}
add_action('admin_enqueue_scripts', 'wi_theme_admin_scripts');

///////////////////////////////////////////////////////////////////////
//	Register Leaflet (free contact map)
///////////////////////////////////////////////////////////////////////

// function bsg_enqueue_leaflet_assets() {
//     if (is_page_template('location-search.php')) {
//         wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
//         wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);
//     }
// }
// add_action('wp_enqueue_scripts', 'bsg_enqueue_leaflet_assets');
// === HEADER VIDEO META BOX (samo za Template: Home) ===


function wi_home_header_video_cb($post) {
    // Prikaži samo ako je izabran Home template
    $tpl = get_page_template_slug($post->ID);
    if ($tpl !== 'home.php' && $tpl !== 'template-home.php' && $tpl !== 'page-home.php') {
        echo '<p style="color:#666;">'.esc_html__('Ovaj metabox je vidljiv samo na Home template-u.', 'wi').'</p>';
        return;
    }

    wp_nonce_field('wi_save_home_header_video', 'wi_home_header_video_nonce');

    $video_id = get_post_meta($post->ID, '_wi_header_video_id', true);
    $video_url = $video_id ? wp_get_attachment_url($video_id) : '';

    ?>
    <div>
        <p>
            <input type="hidden" id="wi_header_video_id" name="wi_header_video_id" value="<?php echo esc_attr($video_id); ?>">
            <input type="text" id="wi_header_video_url" class="widefat" placeholder="<?php esc_attr_e('URL do MP4 fajla', 'wi'); ?>" value="<?php echo esc_attr($video_url); ?>" readonly>
        </p>
        <p>
            <button type="button" class="button" id="wi_header_video_select"><?php esc_html_e('Izaberi MP4 iz medije', 'wi'); ?></button>
            <button type="button" class="button" id="wi_header_video_clear" style="margin-left:6px;"><?php esc_html_e('Ukloni', 'wi'); ?></button>
        </p>
        <p style="color:#666;margin-top:8px;">
            <?php esc_html_e('Ako je popunjen video, koristi se on. Ako nije, koristi se Istaknuta slika (Featured image).', 'wi'); ?>
        </p>
    </div>
    <script>
    (function($){
        $(function(){
            var frame;
            $('#wi_header_video_select').on('click', function(e){
                e.preventDefault();
                if (frame) { frame.open(); return; }
                frame = wp.media({
                    title: 'Izaberi MP4',
                    button: { text: 'Koristi ovaj video' },
                    library: { type: 'video' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    // prihvatamo samo mp4 radi kompatibilnosti
                    if (attachment && attachment.url && /\.mp4($|\?)/i.test(attachment.url)) {
                        $('#wi_header_video_id').val(attachment.id);
                        $('#wi_header_video_url').val(attachment.url);
                    } else {
                        alert('Molim izaberite MP4 fajl.');
                    }
                });
                frame.open();
            });

            $('#wi_header_video_clear').on('click', function(){
                $('#wi_header_video_id').val('');
                $('#wi_header_video_url').val('');
            });
        });
    })(jQuery);
    </script>
    <?php
}

add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['wi_home_header_video_nonce']) || !wp_verify_nonce($_POST['wi_home_header_video_nonce'], 'wi_save_home_header_video')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $video_id = isset($_POST['wi_header_video_id']) ? intval($_POST['wi_header_video_id']) : 0;
    if ($video_id) {
        update_post_meta($post_id, '_wi_header_video_id', $video_id);
    } else {
        delete_post_meta($post_id, '_wi_header_video_id');
    }
});

// Uveri se da je thumbnail podržan (za sliku)
add_action('after_setup_theme', function(){
    add_theme_support('post-thumbnails');
});
// === HOME: "Grüß Gott!" sekcija (naslov + tekst) ===


add_action('save_post_page', function($post_id){
    if (!isset($_POST['wi_home_intro_nonce']) || !wp_verify_nonce($_POST['wi_home_intro_nonce'], 'wi_home_intro_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    $title = isset($_POST['wi_intro_title']) ? wp_kses_post($_POST['wi_intro_title']) : '';
    $text  = isset($_POST['wi_intro_text'])  ? wp_kses_post($_POST['wi_intro_text'])  : '';

    if ($title !== '') update_post_meta($post_id, '_wi_intro_title', $title); else delete_post_meta($post_id, '_wi_intro_title');
    if ($text  !== '') update_post_meta($post_id, '_wi_intro_text',  $text ); else delete_post_meta($post_id, '_wi_intro_text');
});

/* === SERVICES sekcija (pre OUR CLIENTS) =============================== */


function wi_home_services_cb($post){
    $tpl = get_page_template_slug($post->ID);
    if (strpos((string)$tpl, 'home') === false) {
        echo '<p style="color:#666;">'.esc_html__('Ovaj metabox je vidljiv samo na Home template-u.', 'wi').'</p>';
        return;
    }

    wp_nonce_field('wi_home_services_save', 'wi_home_services_nonce');

    $section_title = get_post_meta($post->ID, '_wi_services_title', true);

    // 3 slot-a (možeš povećati broj po želji)
    $items = [];
    for ($i=1; $i<=3; $i++){
        $items[$i] = [
            'img_id' => get_post_meta($post->ID, "_wi_services_{$i}_img_id", true),
            'title'  => get_post_meta($post->ID, "_wi_services_{$i}_title",  true),
        ];
    }

    ?>
    <style>
      .wi-field { margin: 10px 0 16px; }
      .wi-row { border:1px solid #ddd; padding:12px; border-radius:8px; margin:12px 0; background:#fafafa; }
      .wi-thumb { width: 120px; height: 80px; object-fit: cover; border-radius:6px; display:block; background:#eee; }
      .wi-flex { display:flex; gap:12px; align-items:flex-start; }
      .wi-actions{ display:flex; gap:8px; margin-top:6px; }
      .wi-small{ color:#666; font-size:12px; }
      .wi-input{ width:100%; }
    </style>

    <div class="wi-field">
        <label><strong><?php esc_html_e('Naslov sekcije', 'wi'); ?></strong></label>
        <input type="text" class="widefat" name="wi_services_title" value="<?php echo esc_attr($section_title ?: 'Services'); ?>">
        <p class="wi-small"><?php esc_html_e('Veliki naslov iznad kartica (npr. "Services").', 'wi'); ?></p>
    </div>

    <?php for ($i=1; $i<=3; $i++):
        $img_id = intval($items[$i]['img_id']);
        $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'large') : '';
        $title   = $items[$i]['title'];
    ?>
      <div class="wi-row">
        <h4 style="margin:0 0 8px;">Kartica <?php echo $i; ?></h4>
        <div class="wi-flex">
          <div>
            <img id="wi_services_<?php echo $i; ?>_preview" class="wi-thumb" src="<?php echo esc_url($img_url ?: ''); ?>" alt="">
            <div class="wi-actions">
              <input type="hidden" id="wi_services_<?php echo $i; ?>_img_id" name="wi_services_<?php echo $i; ?>_img_id" value="<?php echo esc_attr($img_id); ?>">
              <button type="button" class="button wi-pick" data-slot="<?php echo $i; ?>"><?php esc_html_e('Izaberi sliku', 'wi'); ?></button>
              <button type="button" class="button wi-clear" data-slot="<?php echo $i; ?>"><?php esc_html_e('Ukloni', 'wi'); ?></button>
            </div>
          </div>
          <div class="wi-input">
            <label><strong><?php esc_html_e('Naslov kartice', 'wi'); ?></strong></label>
            <input type="text" class="widefat" name="wi_services_<?php echo $i; ?>_title" value="<?php echo esc_attr($title ?: 'Service'); ?>">
            <p class="wi-small"><?php esc_html_e('Kratak naslov koji se prikazuje preko slike (npr. "Vehicle Wrapping").', 'wi'); ?></p>
          </div>
        </div>
      </div>
    <?php endfor; ?>

    <script>
    (function($){
      $(function(){
        var frame;
        $('.wi-pick').on('click', function(e){
          e.preventDefault();
          var slot = $(this).data('slot');

          if (frame) frame.close();
          frame = wp.media({ title: 'Izaberi sliku', button:{ text: 'Koristi sliku' }, library:{ type:'image' }, multiple:false });
          frame.on('select', function(){
            var att = frame.state().get('selection').first().toJSON();
            $('#wi_services_'+slot+'_img_id').val(att.id);
            $('#wi_services_'+slot+'_preview').attr('src', att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url);
          });
          frame.open();
        });

        $('.wi-clear').on('click', function(){
          var slot = $(this).data('slot');
          $('#wi_services_'+slot+'_img_id').val('');
          $('#wi_services_'+slot+'_preview').attr('src','');
        });
      });
    })(jQuery);
    </script>
    <?php
}

add_action('save_post_page', function($post_id){
    if (!isset($_POST['wi_home_services_nonce']) || !wp_verify_nonce($_POST['wi_home_services_nonce'], 'wi_home_services_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    update_post_meta($post_id, '_wi_services_title', sanitize_text_field($_POST['wi_services_title'] ?? ''));

    for ($i=1; $i<=3; $i++){
        $img_id = isset($_POST["wi_services_{$i}_img_id"]) ? intval($_POST["wi_services_{$i}_img_id"]) : 0;
        $title  = isset($_POST["wi_services_{$i}_title"])   ? wp_kses_post($_POST["wi_services_{$i}_title"])   : '';
        if ($img_id) update_post_meta($post_id, "_wi_services_{$i}_img_id", $img_id); else delete_post_meta($post_id, "_wi_services_{$i}_img_id");
        if ($title !== '') update_post_meta($post_id, "_wi_services_{$i}_title", $title); else delete_post_meta($post_id, "_wi_services_{$i}_title");
    }
});

/* Obavezno: učitaj WP media skriptu na admin-u da bi radilo biranje slike */
add_action('admin_enqueue_scripts', function($hook){
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
    }
});
// Helper: da li je stranica na Home template-u?
function wi_is_home_template($post_id){
    $tpl = (string) get_page_template_slug($post_id);
    if (!$tpl) return false;
    return in_array($tpl, ['home.php','template-home.php','page-home.php'], true)
        || strpos($tpl, 'home') !== false;
}

// Registruj HOME metaboxove samo na Home template-u
function wi_register_home_metaboxes($post){
    if (!$post instanceof WP_Post) return;
    if (!wi_is_home_template($post->ID)) return;

    // Header video (MP4)
    add_meta_box(
        'wi_home_header_video',
        __('Header video (MP4)', 'wi'),
        'wi_home_header_video_cb',
        'page',
        'side',
        'default'
    );

    // Intro (ispod hero)
    add_meta_box(
        'wi_home_intro_box',
        __('Intro sekcija (ispod hero)', 'wi'),
        'wi_home_intro_box_cb',
        'page',
        'normal',
        'high'
    );

    // Services (pre OUR CLIENTS)
    add_meta_box(
        'wi_home_services',
        __('Services sekcija (pre OUR CLIENTS)', 'wi'),
        'wi_home_services_cb',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_page', 'wi_register_home_metaboxes');

/** ---------------------------
 *  CTA (pre footera) – metabox
 *  Key: _wi_cta_title (dozvoljen <br>)
 * --------------------------- */
function wi_is_cta_allowed($post_id){
    // 1) Ako je ovo postavljena "Front page" (Settings → Reading), prikaži CTA
    $front_id = (int) get_option('page_on_front');
    if ($front_id && $front_id === (int) $post_id) {
        return true;
    }

    // 2) Ako template slug odgovara nekom od "home" fajlova, prikaži CTA
    $tpl = (string) get_page_template_slug($post_id); // može biti '' (prazno) za Default Template
    $allowed_exact = [
        'home.php',
        'template-home.php',
        'page-home.php',
        'home-template.php',
        'front-page.php',
    ];

    if (in_array($tpl, $allowed_exact, true)) {
        return true;
    }

    // 3) Labava provera: ako sadrži "home" u nazivu fajla
    if ($tpl !== '' && strpos($tpl, 'home') !== false) {
        return true;
    }

    // Inače: nema CTA
    return false;
}
function wi_register_cta_metabox($post){
    if (!$post instanceof WP_Post) return;
    if (!wi_is_cta_allowed($post->ID)) return;

    add_meta_box(
        'wi_cta_box',
        __('CTA sekcija (pre footera)', 'wi'),
        'wi_render_cta_box',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes_page', 'wi_register_cta_metabox');

function wi_render_cta_box($post) {
    // (uklonili smo uslov koji je davao poruku)
    wp_nonce_field('wi_save_cta_box', 'wi_cta_nonce');

    $val = get_post_meta($post->ID, '_wi_cta_title', true);
    ?>
    <p><label for="wi_cta_title"><strong><?php _e('Naslov (dozvoljen <br>)', 'wi'); ?></strong></label></p>
    <textarea id="wi_cta_title" name="wi_cta_title" rows="3" style="width:100%;max-width:800px;"><?php
        echo esc_textarea($val);
    ?></textarea>
    <p style="opacity:.75;margin-top:.25rem">
        <?php _e('Možeš koristiti <br> za novi red.', 'wi'); ?>
    </p>
    <?php
}


add_action('save_post', function ($post_id) {
    if (!isset($_POST['wi_cta_nonce']) || !wp_verify_nonce($_POST['wi_cta_nonce'], 'wi_save_cta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $raw = isset($_POST['wi_cta_title']) ? $_POST['wi_cta_title'] : '';
    // dozvoli samo <br> u naslovu
    $allowed = array('br' => array());
    $clean = wp_kses($raw, $allowed);
    update_post_meta($post_id, '_wi_cta_title', $clean);
});
function wi_home_intro_box_cb($post){
    wp_nonce_field('wi_home_intro_save','wi_home_intro_nonce');
    $title = get_post_meta($post->ID, '_wi_intro_title', true);
    $text  = get_post_meta($post->ID, '_wi_intro_text', true);
    ?>
    <p><label for="wi_intro_title"><strong><?php esc_html_e('Naslov', 'wi'); ?></strong></label></p>
    <input id="wi_intro_title" name="wi_intro_title" type="text" class="widefat" value="<?php echo esc_attr($title); ?>" placeholder="Grüß Gott!">
    <p style="margin-top:12px;"><strong><?php esc_html_e('Tekst', 'wi'); ?></strong></p>
    <?php
    wp_editor($text,'wi_intro_text',[
        'textarea_name'=>'wi_intro_text',
        'media_buttons'=>false,
        'textarea_rows'=>6,
        'teeny'=>true,
        'quicktags'=>false,
    ]);
}
/* =========================
 *  IMPRESSUM (metabox na Edit Page)
 * ========================= */

// 2.1 Specifikacija sekcija + podrazumevane vrednosti (kao u pluginu)
function wi_impressum_sections_spec() {
    return [
        'tmg'     => 'ANGABEN GEMÄSS § 5 TMG',
        'kontakt' => 'KONTAKT',
        'ustid'   => 'UMSATZSTEUER-ID',
    ];
}
function wi_impressum_defaults() {
    return [
        'tmg' => [
            'title' => 'ANGABEN GEMÄSS § 5 TMG',
            'paras' => ['WERBEINSEL','Flughafen 76/3','88046 Friedrichshafen','Deutschland'],
        ],
        'kontakt' => [
            'title' => 'KONTAKT',
            'paras' => ['Telefon: +49 7541 700 57 44','E-Mail: hallo@werbeinsel.de'],
        ],
        'ustid' => [
            'title' => 'UMSATZSTEUER-ID',
            'paras' => ['Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:','DE322482204'],
        ],
    ];
}

// 2.2 Da li je ova stranica Impressum template?
function wi_is_impressum_template($post_id){
    $tpl = (string) get_page_template_slug($post_id); // npr. "impressum.php"
    if ($tpl && ( $tpl === 'impressum.php' || strpos($tpl, 'impressum') !== false )) {
        return true;
    }
    // fallback: ako je slug stranice "impressum"
    $p = get_post($post_id);
    if ($p && $p->post_name === 'impressum') {
        return true;
    }
    return false;
}

// 2.3 Registruj metabox SAMO na Impressum stranici
add_action('add_meta_boxes_page', function($post){
    if (!$post instanceof WP_Post) return;
    if (!wi_is_impressum_template($post->ID)) return;

    add_meta_box(
        'wi_impressum_box',
        __('Impressum – sekcije', 'wi'),
        'wi_impressum_metabox_render',
        'page',
        'normal',
        'high'
    );
});

// 2.4 Render polja (repeater kao u pluginu)
function wi_impressum_metabox_render($post){
    wp_nonce_field('wi_impressum_save','wi_impressum_nonce');

    $defs = wi_impressum_defaults();
    $spec = wi_impressum_sections_spec();
    $data = get_post_meta($post->ID, '_wi_impressum_data', true);
    if (!is_array($data)) { $data = []; }
    // merge defaults
    $data = wp_parse_args($data, $defs);
    ?>
    <style>
      .wi-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin:16px 0}
      .wi-grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
      .wi-para{display:flex;gap:8px;align-items:center;margin:8px 0}
      .wi-para input{flex:1}
      .wi-ghost{opacity:.55}
    </style>
    <?php foreach ($spec as $key => $label):
        $title = $data[$key]['title'] ?? $defs[$key]['title'];
        $paras = $data[$key]['paras'] ?? $defs[$key]['paras'];
        $name_title = "_wi_impressum_data[$key][title]";
        $name_paras = "_wi_impressum_data[$key][paras]";
    ?>
      <div class="wi-card">
        <h2 style="margin:0 0 10px;"><?php echo esc_html($label); ?></h2>

        <div class="wi-grid">
          <div>
            <label><strong>H3 naslov</strong></label>
            <input type="text" class="regular-text" name="<?php echo esc_attr($name_title); ?>" value="<?php echo esc_attr($title); ?>">
          </div>
        </div>

        <div class="wi-paras" data-name="<?php echo esc_attr($name_paras); ?>">
          <!-- proto red (skriven) -->
          <div class="wi-para wi-proto wi-ghost" style="display:none">
            <input type="text" value="" placeholder="Tekst paragrafa">
            <button class="button button-secondary wi-del" type="button">Ukloni</button>
          </div>

          <?php if (is_array($paras)): foreach ($paras as $p): ?>
            <div class="wi-para">
              <input type="text" name="<?php echo esc_attr($name_paras); ?>[]" value="<?php echo esc_attr($p); ?>" placeholder="Tekst paragrafa">
              <button class="button button-secondary wi-del" type="button">Ukloni</button>
            </div>
          <?php endforeach; endif; ?>
        </div>

        <p><button type="button" class="button button-primary wi-add">+ Dodaj paragraf</button></p>
      </div>
    <?php endforeach; ?>

    <script>
    (function($){
      $(function(){
        $('.wi-add').on('click', function(e){
          e.preventDefault();
          const card = $(this).closest('.wi-card');
          const list = card.find('.wi-paras');
          const proto= list.find('.wi-proto').first().clone();
          proto.removeClass('wi-proto wi-ghost').show();
          // dodaj name na input
          const base = list.data('name');
          proto.find('input').attr('name', base+'[]').val('');
          list.append(proto);
        });
        $(document).on('click', '.wi-del', function(e){
          e.preventDefault();
          const row = $(this).closest('.wi-para');
          const list= row.parent();
          if(list.find('.wi-para').length>1){ row.remove(); } else { row.find('input').val(''); }
        });
      });
    })(jQuery);
    </script>
    <?php
}

// 2.5 Snimi podatke
add_action('save_post_page', function($post_id){
    if (!isset($_POST['wi_impressum_nonce']) || !wp_verify_nonce($_POST['wi_impressum_nonce'], 'wi_impressum_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    // snimamo samo ako je zaista Impressum stranica
    if (!wi_is_impressum_template($post_id)) return;

    $defs = wi_impressum_defaults();
    $spec = wi_impressum_sections_spec();

    $in = isset($_POST['_wi_impressum_data']) && is_array($_POST['_wi_impressum_data']) ? $_POST['_wi_impressum_data'] : [];
    $out = [];

    foreach ($spec as $key => $label) {
        $title = isset($in[$key]['title']) ? sanitize_text_field(wp_unslash($in[$key]['title'])) : '';
        $paras = isset($in[$key]['paras']) && is_array($in[$key]['paras']) ? $in[$key]['paras'] : [];

        $clean = [];
        foreach ($paras as $p) {
            $p = trim(wp_unslash($p));
            if ($p !== '') $clean[] = sanitize_text_field($p);
        }
        if (empty($clean)) $clean = $defs[$key]['paras'];

        $out[$key] = [
            'title' => ($title !== '' ? $title : $defs[$key]['title']),
            'paras' => $clean,
        ];
    }
    update_post_meta($post_id, '_wi_impressum_data', $out);
});

// 2.6 Template helper: dohvati uvek kompletne podatke za dati page ID
function wi_get_impressum_data($post_id){
    $defs = wi_impressum_defaults();
    $meta = get_post_meta($post_id, '_wi_impressum_data', true);
    if (!is_array($meta)) $meta = [];
    return wp_parse_args($meta, $defs);
}

// 2.7 Jednokratna migracija iz starog plugina (ako postoji opcija)
add_action('admin_init', function(){
    $opt = get_option('wi_impressum_options', null);
    if (!is_array($opt)) return;

    // pokušaj naći stranicu Impressum po slugu ili po template-u
    $page_id = 0;
    $by_slug = get_page_by_path('impressum', OBJECT, 'page');
    if ($by_slug) $page_id = (int) $by_slug->ID;

    if (!$page_id) {
        // fallback: nađi prvu stranicu sa template-om koji sadrži 'impressum'
        $q = new WP_Query([
            'post_type' => 'page',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => '_wp_page_template',
                    'value' => 'impressum',
                    'compare' => 'LIKE',
                ]
            ]
        ]);
        if ($q->have_posts()) { $page_id = (int) $q->posts[0]->ID; }
        wp_reset_postdata();
    }

    if ($page_id && !get_post_meta($page_id, '_wi_impressum_data', true)) {
        update_post_meta($page_id, '_wi_impressum_data', wp_parse_args($opt, wi_impressum_defaults()));
        // (opciono) možeš obrisati opciju posle migracije:
        // delete_option('wi_impressum_options');
    }
});
/* =========================
 *  DATENSCHUTZ (metabox na Edit Page)
 * ========================= */

// Sekcije + default vrednosti
function wi_datenschutz_defaults() {
    return [
        'verantwortlicher' => [
            'title' => 'VERANTWORTLICHER',
            'paras' => ['AGENCY GmbH','Musterstraße 123','12345 Berlin','Deutschland','E-Mail: datenschutz@agency.com'],
        ],
        'erhebung' => [
            'title' => 'ERHEBUNG UND VERARBEITUNG PERSONENBEZOGENER DATEN',
            'text'  => 'Wir erheben und verarbeiten personenbezogene Daten nur, soweit dies zur Erfüllung unserer vertraglichen Pflichten oder zur Wahrung berechtigter Interessen erforderlich ist.',
        ],
        'rechte' => [
            'title' => 'IHRE RECHTE',
            'text'  => 'Sie haben das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Widerspruch und Datenübertragbarkeit.',
        ],
        'cookie' => [
            'title' => 'COOKIE-EINSTELLUNGEN',
            'text'  => 'Verwalten Sie Ihre Cookie-Präferenzen und Datenschutzeinstellungen.',
            // dugme ostaje hard-code u templatu
        ],
    ];
}

// Da li je Datenschutz template (ili slug)
function wi_is_datenschutz_template($post_id){
    $tpl = (string) get_page_template_slug($post_id);
    if ($tpl && ( $tpl === 'datenschutz.php' || strpos($tpl, 'datenschutz') !== false )) return true;
    $p = get_post($post_id);
    return ($p && $p->post_name === 'datenschutz');
}

// Registracija metaboxa samo na Datenschutz stranici
add_action('add_meta_boxes_page', function($post){
    if (!$post instanceof WP_Post) return;
    if (!wi_is_datenschutz_template($post->ID)) return;

    add_meta_box(
        'wi_datenschutz_box',
        __('Datenschutz – sadržaj', 'wi'),
        'wi_datenschutz_metabox_render',
        'page',
        'normal',
        'high'
    );
});

// Render metaboxa
function wi_datenschutz_metabox_render($post){
    wp_nonce_field('wi_datenschutz_save','wi_datenschutz_nonce');

    $defs = wi_datenschutz_defaults();
    $data = get_post_meta($post->ID, '_wi_datenschutz_data', true);
    if (!is_array($data)) $data = [];
    $data = wp_parse_args($data, $defs);

    $v = $data; // alias
    ?>
    <style>
      .wi-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin:16px 0}
      .wi-para{display:flex;gap:8px;align-items:center;margin:8px 0}
      .wi-para input{flex:1}
      .wi-ghost{opacity:.55}
      .wi-full{width:100%}
    </style>

    <!-- VERANTWORTLICHER (title + repeater lines) -->
    <div class="wi-card">
      <h2>VERANTWORTLICHER</h2>
      <p><label><strong>H3 naslov</strong></label>
      <input type="text" class="regular-text wi-full" name="_wi_datenschutz_data[verantwortlicher][title]" value="<?php echo esc_attr($v['verantwortlicher']['title']); ?>"></p>

      <div class="wi-paras" data-name="_wi_datenschutz_data[verantwortlicher][paras]">
        <div class="wi-para wi-proto wi-ghost" style="display:none">
          <input type="text" value="" placeholder="Red (paragraf)">
          <button class="button button-secondary wi-del" type="button">Ukloni</button>
        </div>
        <?php foreach ($v['verantwortlicher']['paras'] as $p): ?>
          <div class="wi-para">
            <input type="text" name="_wi_datenschutz_data[verantwortlicher][paras][]" value="<?php echo esc_attr($p); ?>">
            <button class="button button-secondary wi-del" type="button">Ukloni</button>
          </div>
        <?php endforeach; ?>
      </div>
      <p><button type="button" class="button button-primary wi-add">+ Dodaj red</button></p>
    </div>

    <!-- ERHEBUNG (title + textarea) -->
    <div class="wi-card">
      <h2>ERHEBUNG UND VERARBEITUNG …</h2>
      <p><label><strong>H3 naslov</strong></label>
      <input type="text" class="regular-text wi-full" name="_wi_datenschutz_data[erhebung][title]" value="<?php echo esc_attr($v['erhebung']['title']); ?>"></p>
      <p><label><strong>Tekst</strong></label>
      <textarea class="wi-full" rows="5" name="_wi_datenschutz_data[erhebung][text]"><?php echo esc_textarea($v['erhebung']['text']); ?></textarea></p>
    </div>

    <!-- RECHTE (title + textarea) -->
    <div class="wi-card">
      <h2>IHRE RECHTE</h2>
      <p><label><strong>H3 naslov</strong></label>
      <input type="text" class="regular-text wi-full" name="_wi_datenschutz_data[rechte][title]" value="<?php echo esc_attr($v['rechte']['title']); ?>"></p>
      <p><label><strong>Tekst</strong></label>
      <textarea class="wi-full" rows="5" name="_wi_datenschutz_data[rechte][text]"><?php echo esc_textarea($v['rechte']['text']); ?></textarea></p>
    </div>

    <!-- COOKIE BOX (title + text) – dugme ostaje hard-code -->
    <div class="wi-card">
      <h2>COOKIE-EINSTELLUNGEN</h2>
      <p><label><strong>Naslov</strong></label>
      <input type="text" class="regular-text wi-full" name="_wi_datenschutz_data[cookie][title]" value="<?php echo esc_attr($v['cookie']['title']); ?>"></p>
      <p><label><strong>Tekst</strong></label>
      <textarea class="wi-full" rows="4" name="_wi_datenschutz_data[cookie][text]"><?php echo esc_textarea($v['cookie']['text']); ?></textarea></p>
      <p style="opacity:.7">Napomena: tekst na dugmetu se ne menja ovde.</p>
    </div>

    <script>
    (function($){
      $(function(){
        $('.wi-add').on('click', function(e){
          e.preventDefault();
          const card = $(this).closest('.wi-card');
          const list = card.find('.wi-paras');
          const proto= list.find('.wi-proto').first().clone();
          proto.removeClass('wi-proto wi-ghost').show();
          const base = list.data('name');
          proto.find('input').attr('name', base+'[]').val('');
          list.append(proto);
        });
        $(document).on('click', '.wi-del', function(e){
          e.preventDefault();
          const row = $(this).closest('.wi-para');
          const list= row.parent();
          if(list.find('.wi-para').length>1){ row.remove(); } else { row.find('input').val(''); }
        });
      });
    })(jQuery);
    </script>
    <?php
}

// Snimanje
add_action('save_post_page', function($post_id){
    if (!isset($_POST['wi_datenschutz_nonce']) || !wp_verify_nonce($_POST['wi_datenschutz_nonce'], 'wi_datenschutz_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;
    if (!wi_is_datenschutz_template($post_id)) return;

    $defs = wi_datenschutz_defaults();
    $in   = isset($_POST['_wi_datenschutz_data']) && is_array($_POST['_wi_datenschutz_data']) ? $_POST['_wi_datenschutz_data'] : [];
    $out  = [];

    // Verantwortlicher
    $vt = isset($in['verantwortlicher']['title']) ? sanitize_text_field(wp_unslash($in['verantwortlicher']['title'])) : '';
    $vp = isset($in['verantwortlicher']['paras']) ? (array)$in['verantwortlicher']['paras'] : [];
    $vp_clean = [];
    foreach ($vp as $p){ $p = trim(wp_unslash($p)); if ($p!=='') $vp_clean[] = sanitize_text_field($p); }
    if (empty($vp_clean)) $vp_clean = $defs['verantwortlicher']['paras'];
    $out['verantwortlicher'] = [
        'title' => ($vt !== '' ? $vt : $defs['verantwortlicher']['title']),
        'paras' => $vp_clean,
    ];

    // Erhebung
    $et = isset($in['erhebung']['title']) ? sanitize_text_field(wp_unslash($in['erhebung']['title'])) : '';
    $ex = isset($in['erhebung']['text'])  ? wp_kses_post(wp_unslash($in['erhebung']['text'])) : '';
    $out['erhebung'] = [
        'title' => ($et !== '' ? $et : $defs['erhebung']['title']),
        'text'  => ($ex !== '' ? $ex : $defs['erhebung']['text']),
    ];

    // Rechte
    $rt = isset($in['rechte']['title']) ? sanitize_text_field(wp_unslash($in['rechte']['title'])) : '';
    $rx = isset($in['rechte']['text'])  ? wp_kses_post(wp_unslash($in['rechte']['text'])) : '';
    $out['rechte'] = [
        'title' => ($rt !== '' ? $rt : $defs['rechte']['title']),
        'text'  => ($rx !== '' ? $rx : $defs['rechte']['text']),
    ];

    // Cookie
    $ct = isset($in['cookie']['title']) ? sanitize_text_field(wp_unslash($in['cookie']['title'])) : '';
    $cx = isset($in['cookie']['text'])  ? wp_kses_post(wp_unslash($in['cookie']['text'])) : '';
    $out['cookie'] = [
        'title' => ($ct !== '' ? $ct : $defs['cookie']['title']),
        'text'  => ($cx !== '' ? $cx : $defs['cookie']['text']),
    ];

    update_post_meta($post_id, '_wi_datenschutz_data', $out);
});

// Helper za template
function wi_get_datenschutz_data($post_id){
    $defs = wi_datenschutz_defaults();
    $meta = get_post_meta($post_id, '_wi_datenschutz_data', true);
    if (!is_array($meta)) $meta = [];
    return wp_parse_args($meta, $defs);
}
