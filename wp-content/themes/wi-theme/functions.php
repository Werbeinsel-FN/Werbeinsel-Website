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
// Pomoćnik: napravi Gutenberg blok markup za jednu sekciju (h3 + više p)
function wi_build_impressum_section_block($title, $paras) {
    $html  = '<!-- wp:group {"className":"text-center impressum-section"} --><div class="wp-block-group text-center impressum-section">';
    $html .= '<!-- wp:heading {"level":3,"className":"text-3xl poppins-bold text-black mb-6"} -->';
    $html .= '<h3 class="text-3xl poppins-bold text-black mb-6">' . esc_html($title) . '</h3>';
    $html .= '<!-- /wp:heading -->';
    $html .= '<!-- wp:group {"className":"poppins text-lg text-black space-y-3"} --><div class="wp-block-group poppins text-lg text-black space-y-3">';
    foreach ($paras as $idx => $p) {
        $p = trim($p);
        if ($p === '') continue;
        $strong = $idx === 0 ? '<strong>' . esc_html($p) . '</strong>' : esc_html($p);
        $html .= '<!-- wp:paragraph --><p>' . $strong . '</p><!-- /wp:paragraph -->';
    }
    $html .= '</div><!-- /wp:group -->';
    $html .= '</div><!-- /wp:group -->';
    return $html;
}

// Jednokratna migracija metabox -> post_content (samo za stranu koja koristi Impressum template)
add_action('admin_init', function () {
    if (!is_admin()) return;

    // Nadji stranicu sa šablonom "impressum.php" ili slug-om "impressum"
    $page = get_page_by_path('impressum', OBJECT, 'page');
    if (!$page) {
        $q = new WP_Query([
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'meta_query'     => [[
                'key'     => '_wp_page_template',
                'value'   => 'impressum',
                'compare' => 'LIKE',
            ]],
            'fields'         => 'all',
        ]);
        if ($q->have_posts()) $page = $q->posts[0];
        wp_reset_postdata();
    }
    if (!$page) return;

    // Ako već ima sadržaj u editoru, ne radi ništa (pretpostavljamo da je migrirano).
    if (!empty($page->post_content)) return;

    // Učitaj metabox podatke ili defaulte
    $defs = [
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
    $meta = get_post_meta($page->ID, '_wi_impressum_data', true);
    if (!is_array($meta)) $meta = [];
    $data = wp_parse_args($meta, $defs);

    // Sastavi blok sadržaj
    $content  = '';
    $content .= wi_build_impressum_section_block($data['tmg']['title'],     (array)$data['tmg']['paras']);
    $content .= wi_build_impressum_section_block($data['kontakt']['title'], (array)$data['kontakt']['paras']);
    $content .= wi_build_impressum_section_block($data['ustid']['title'],   (array)$data['ustid']['paras']);

    // Upis u post_content
    wp_update_post([
        'ID'           => $page->ID,
        'post_content' => $content,
    ]);
});
/* ===== Datenschutz: migracija metabox -> post_content (jednokratno) ===== */

// Helperi za generisanje blok markupa
function wi_ds_block_section_list($title, $lines) {
    $html  = '<!-- wp:group {"className":"datenschutz-section text-center"} -->';
    $html .= '<div class="wp-block-group datenschutz-section text-center">';
    $html .= '<!-- wp:heading {"level":3,"className":"section-title"} -->';
    $html .= '<h3 class="section-title">'.esc_html($title).'</h3>';
    $html .= '<!-- /wp:heading -->';
    $html .= '<!-- wp:group {"className":"section-content"} --><div class="wp-block-group section-content">';
    foreach ((array)$lines as $p) {
        $p = trim($p);
        if ($p==='') continue;
        $html .= '<!-- wp:paragraph --><p>'.esc_html($p).'</p><!-- /wp:paragraph -->';
    }
    $html .= '</div><!-- /wp:group -->';
    $html .= '</div><!-- /wp:group -->';
    return $html;
}

function wi_ds_block_section_text($title, $text) {
    $html  = '<!-- wp:group {"className":"datenschutz-section"} -->';
    $html .= '<div class="wp-block-group datenschutz-section">';
    $html .= '<!-- wp:heading {"level":3,"className":"section-title"} -->';
    $html .= '<h3 class="section-title">'.esc_html($title).'</h3>';
    $html .= '<!-- /wp:heading -->';
    $html .= '<!-- wp:paragraph {"className":"section-text"} -->';
    $html .= '<p class="section-text">'.wp_kses_post($text).'</p>';
    $html .= '<!-- /wp:paragraph -->';
    $html .= '</div><!-- /wp:group -->';
    return $html;
}

function wi_ds_block_cookie_box($title, $text) {
    $html  = '<!-- wp:group {"className":"cookie-box"} -->';
    $html .= '<div class="wp-block-group cookie-box">';
    $html .= '<!-- wp:heading {"level":4,"className":"cookie-title"} -->';
    $html .= '<h4 class="cookie-title">'.esc_html($title).'</h4>';
    $html .= '<!-- /wp:heading -->';
    $html .= '<!-- wp:paragraph {"className":"cookie-text"} -->';
    $html .= '<p class="cookie-text">'.wp_kses_post($text).'</p>';
    $html .= '<!-- /wp:paragraph -->';
    // Dugme ostaje fiksno po zahtevu
    $html .= '<!-- wp:html --><button class="cookie-button">EINSTELLUNGEN BEARBEITEN</button><!-- /wp:html -->';
    $html .= '</div><!-- /wp:group -->';
    return $html;
}

// Vrati podrazumevane vrednosti (isti kao kod tebe)
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
        ],
    ];
}

// Prepoznaj Datenschutz stranicu (template ili slug)
function wi_is_datenschutz_template($post_id){
    $tpl = (string) get_page_template_slug($post_id);
    if ($tpl && ( $tpl === 'datenschutz.php' || strpos($tpl, 'datenschutz') !== false )) return true;
    $p = get_post($post_id);
    return ($p && $p->post_name === 'datenschutz');
}

// Jednokratna migracija u editor
add_action('admin_init', function () {
    if (!is_admin()) return;

    // Nađi stranicu: prvo slug, pa po template-u
    $page = get_page_by_path('datenschutz', OBJECT, 'page');
    if (!$page) {
        $q = new WP_Query([
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'meta_query'     => [[
                'key'     => '_wp_page_template',
                'value'   => 'datenschutz',
                'compare' => 'LIKE',
            ]],
        ]);
        if ($q->have_posts()) $page = $q->posts[0];
        wp_reset_postdata();
    }
    if (!$page) return;

    // Ako već postoji sadržaj u editoru, ne diramo (pretpostavka: već migrirano)
    if (!empty($page->post_content)) return;

    // Učitaj metabox podatke ili defaulte
    $defs = wi_datenschutz_defaults();
    $meta = get_post_meta($page->ID, '_wi_datenschutz_data', true);
    if (!is_array($meta)) $meta = [];
    $data = wp_parse_args($meta, $defs);

    // Sastavi Gutenberg sadržaj
    $content  = '';
    $content .= wi_ds_block_section_list($data['verantwortlicher']['title'], (array)$data['verantwortlicher']['paras']);
    $content .= wi_ds_block_section_text($data['erhebung']['title'], $data['erhebung']['text']);
    $content .= wi_ds_block_section_text($data['rechte']['title'],   $data['rechte']['text']);
    $content .= wi_ds_block_cookie_box($data['cookie']['title'],     $data['cookie']['text']);

    // Upis u post_content
    wp_update_post([
        'ID'           => $page->ID,
        'post_content' => $content,
    ]);
});
/* ===== AGBs: migracija u Gutenberg blokove (jednokratno) ===== */

// Default sadržaj (možeš prilagoditi)
function wi_agbs_defaults() {
  return [
    [
      'title' => '§ 1 GELTUNGSBEREICH',
      'text'  => 'Diese Allgemeinen Geschäftsbedingungen gelten für alle Verträge zwischen der AGENCY GmbH und ihren Kunden. Abweichende Bedingungen des Kunden werden nur dann Vertragsbestandteil, wenn wir diesen ausdrücklich schriftlich zustimmen.'
    ],
    [
      'title' => '§ 2 VERTRAGSSCHLUSS',
      'text'  => 'Unsere Angebote sind freibleibend und unverbindlich. Der Vertrag kommt durch unsere schriftliche Auftragsbestätigung oder durch Beginn der Ausführung zustande.'
    ],
    [
      'title' => '§ 3 PREISE UND ZAHLUNGSBEDINGUNGEN',
      'text'  => 'Alle Preise verstehen sich netto zuzüglich der gesetzlichen Umsatzsteuer. Rechnungen sind innerhalb von 14 Tagen nach Rechnungsdatum zur Zahlung fällig.'
    ],
  ];
}

// Helper: jedna sekcija = H3 + p (sa tvojim klasama)
function wi_agbs_section_block($title, $text) {
  $html  = '<!-- wp:group --><div class="wp-block-group">';
  $html .= '<!-- wp:heading {"level":3,"className":"text-2xl unbounded-bold text-black mb-4"} -->';
  $html .= '<h3 class="text-2xl unbounded-bold text-black mb-4">'.esc_html($title).'</h3>';
  $html .= '<!-- /wp:heading -->';
  $html .= '<!-- wp:paragraph {"className":"poppins text-black leading-relaxed"} -->';
  $html .= '<p class="poppins text-black leading-relaxed">'.wp_kses_post($text).'</p>';
  $html .= '<!-- /wp:paragraph -->';
  $html .= '</div><!-- /wp:group -->';
  return $html;
}

// Prepoznaj AGBs stranicu
function wi_is_agbs_template($post_id){
  $tpl = (string) get_page_template_slug($post_id);
  if ($tpl && ( $tpl === 'agbs.php' || strpos($tpl, 'agbs') !== false )) return true;
  $p = get_post($post_id);
  return ($p && in_array($p->post_name, ['agbs','agb','agb-s','a-g-b','bedingungen'], true));
}

// Migracija (radi samo ako je editor prazan)
add_action('admin_init', function () {
  if (!is_admin()) return;

  // Prvo probaj slug
  $page = get_page_by_path('agbs', OBJECT, 'page');
  if (!$page) $page = get_page_by_path('agb', OBJECT, 'page');

  // Ako nije nađeno po slugu, probaj po template-u
  if (!$page) {
    $q = new WP_Query([
      'post_type'      => 'page',
      'posts_per_page' => 1,
      'meta_query'     => [[
        'key'     => '_wp_page_template',
        'value'   => 'agbs',
        'compare' => 'LIKE',
      ]],
    ]);
    if ($q->have_posts()) $page = $q->posts[0];
    wp_reset_postdata();
  }
  if (!$page) return;

  if (!empty($page->post_content)) return; // već ima sadržaj, ne diramo

  $defs = wi_agbs_defaults();
  $content = '';
  foreach ($defs as $sec) {
    $content .= wi_agbs_section_block($sec['title'], $sec['text']);
  }

  wp_update_post([
    'ID'           => $page->ID,
    'post_content' => $content,
  ]);
});

/* ===== Plakatwerbung admin UI (bez ACF) ===== */

function wi_is_plakatwerbung_page($post_id){
  if (!$post_id) return false;
  $tpl  = (string) get_page_template_slug($post_id);
  $slug = (string) get_post_field('post_name', $post_id);
  $is_service_detail = $tpl && (strpos($tpl, 'service-detail') !== false);
  return $is_service_detail && $slug === 'plakatwerbung';
}

function wi_plakat_defaults(){
  return [
    'hero_ids'    => [], // attachment IDs
    'intro_title' => "PLAKAT\nWERBUNG",
    'intro_text'  => 'Plakatwerbung ist eine der effektivsten Formen der Außenwerbung...',
    'steps_title' => "ÜBERLEGE\nNOCH",
    'region_title'=> 'REGION',
    'region_paras'=> [
      'Wir plakatieren in der gesamten Region Friedrichshafen und Umgebung...',
      'Mit über 50 Premium-Standorten erreichen Sie täglich tausende von potenziellen Kunden...',
      'Mit über 50 Premium-Standorten erreichen Sie täglich tausende von potenziellen Kunden...'
    ],
    'region_id'   => 0, // attachment ID
    'cta_title'   => "BEREIT FÜR\nMAXIMUM IMPACT?",
    'cta_btn_text'=> 'JA',
    'faq'         => [
      ['q'=>'Wie lange im Voraus...','a'=>'Idealerweise 2–4&nbsp;Wochen...'],
      ['q'=>'Welche Plakatgrößen...','a'=>'DIN A1, DIN A0...'],
      ['q'=>'Erstellen Sie auch das Design...','a'=>'Ja, unser Grafikteam...'],
      ['q'=>'Wie wählen Sie die Standorte...','a'=>'Nach Zielgruppe, Frequenz...'],
      ['q'=>'Was passiert bei schlechtem Wetter...','a'=>'Regelmäßige Kontrollen...'],
    ],
    'quotes'      => [
      ['text'=>'„Exzellente Standortwahl ...“','org'=>'Bodensee Events AG','person'=>'Sandra Müller'],
      ['text'=>'„Sichtbar bessere Reichweite ...“','org'=>'City Kultur GmbH','person'=>'Lukas Hartmann'],
      ['text'=>'„Schnelle Umsetzung ...“','org'=>'Seepark Center','person'=>'Mira Hoffmann'],
    ],
  ];
}

function wi_get_plakat_meta($post_id){
  $d = wi_plakat_defaults();
  $m = get_post_meta($post_id, '_wi_plakat', true);
  if (!is_array($m)) $m = [];
  $m = wp_parse_args($m, $d);
  // osiguraj tipove
  $m['hero_ids']  = array_values(array_filter(array_map('intval', (array)$m['hero_ids'])));
  $m['region_id'] = (int) $m['region_id'];
  $m['faq']       = array_map(fn($r)=>['q'=> (string)($r['q']??''), 'a'=> (string)($r['a']??'')], (array)$m['faq']);
  $m['quotes']    = array_map(fn($r)=>['text'=> (string)($r['text']??''), 'org'=> (string)($r['org']??''), 'person'=> (string)($r['person']??'')], (array)$m['quotes']);
  return $m;
}

add_action('add_meta_boxes_page', function($post){
  if (!$post instanceof WP_Post) return;
  if (!wi_is_plakatwerbung_page($post->ID)) return;

  add_meta_box('wi_plakat_box', 'Plakatwerbung – slike i tekst', 'wi_plakat_metabox', 'page', 'normal', 'high');
});

function wi_plakat_metabox($post){
  wp_nonce_field('wi_plakat_save','wi_plakat_nonce');
  $m = wi_get_plakat_meta($post->ID);

  // Media za hero
  $hero_thumbs = array_map(function($id){
    $src = wp_get_attachment_image_url($id, 'medium');
    return $src ? '<li data-id="'.$id.'"><img src="'.esc_url($src).'"><button type="button" class="button-link delete">&times;</button></li>' : '';
  }, $m['hero_ids']);
  $hero_thumbs = implode('', array_filter($hero_thumbs));

  // Region img
  $region_src = $m['region_id'] ? wp_get_attachment_image_url($m['region_id'], 'large') : '';

  ?>
  <style>
    .wi-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin:16px 0}
    .wi-row{display:grid;gap:12px}
    #wi-hero-list{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0;padding:0}
    #wi-hero-list li{list-style:none;position:relative}
    #wi-hero-list img{display:block;width:120px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd}
    #wi-hero-list .delete{position:absolute;right:2px;top:2px;background:#000;color:#fff;border-radius:50%;line-height:1;width:20px;height:20px;text-align:center}
    .wi-rep table{width:100%;border-collapse:collapse}
    .wi-rep th,.wi-rep td{border-bottom:1px solid #eee;padding:6px}
    .wi-rep input,.wi-rep textarea{width:100%}
    .wi-note{opacity:.7;font-size:12px}
  </style>

  <div class="wi-card">
    <h2>Hero galerija (više slika)</h2>
    <input type="hidden" id="wi-hero-ids" name="_wi_plakat[hero_ids]" value="<?php echo esc_attr(implode(',', $m['hero_ids'])); ?>">
    <ul id="wi-hero-list"><?php echo $hero_thumbs ?: '<li class="wi-note">Nema slika još.</li>'; ?></ul>
    <p>
      <button type="button" class="button" id="wi-hero-add">Dodaj/izmeni slike</button>
      <span class="wi-note">Prevuci za promenу redosleda.</span>
    </p>
  </div>

  <div class="wi-card">
    <h2>Intro</h2>
    <div class="wi-row">
      <label>Naslov (multi-line, koristi Enter)</label>
      <input type="text" class="regular-text" name="_wi_plakat[intro_title]" value="<?php echo esc_attr($m['intro_title']); ?>">
      <label>Tekst</label>
      <textarea name="_wi_plakat[intro_text]" rows="3"><?php echo esc_textarea($m['intro_text']); ?></textarea>
    </div>
  </div>

  <div class="wi-card">
    <h2>Steps naslov</h2>
    <input type="text" class="regular-text" name="_wi_plakat[steps_title]" value="<?php echo esc_attr($m['steps_title']); ?>">
  </div>

  <div class="wi-card">
    <h2>Region</h2>
    <div class="wi-row">
      <label>Naslov</label>
      <input type="text" name="_wi_plakat[region_title]" value="<?php echo esc_attr($m['region_title']); ?>">
      <label>Paragrafi (jedan po liniji)</label>
      <textarea name="_wi_plakat[region_paras]" rows="4"><?php echo esc_textarea(implode("\n",$m['region_paras'])); ?></textarea>

      <label>Slika</label>
      <input type="hidden" id="wi-region-id" name="_wi_plakat[region_id]" value="<?php echo (int)$m['region_id']; ?>">
      <div id="wi-region-preview"><?php echo $region_src?'<img src="'.esc_url($region_src).'" style="max-width:240px;border:1px solid #ddd;border-radius:6px">':'<em class="wi-note">Nema slike</em>'; ?></div>
      <p><button type="button" class="button" id="wi-region-pick">Odaberi sliku</button>
         <button type="button" class="button button-link-delete" id="wi-region-clear">Ukloni</button></p>
    </div>
  </div>

  <div class="wi-card wi-rep">
    <h2>FAQ</h2>
    <table id="wi-faq"><thead><tr><th>Pitanje</th><th>Odgovor (HTML dozvoljen)</th><th></th></tr></thead><tbody>
      <?php foreach ($m['faq'] as $row): ?>
      <tr>
        <td><input type="text" name="_wi_plakat[faq_q][]" value="<?php echo esc_attr($row['q']); ?>"></td>
        <td><textarea name="_wi_plakat[faq_a][]" rows="2"><?php echo esc_textarea($row['a']); ?></textarea></td>
        <td><button type="button" class="button wi-del">&times;</button></td>
      </tr>
      <?php endforeach; ?>
    </tbody></table>
    <p><button type="button" class="button" id="wi-faq-add">+ Dodaj</button></p>
  </div>

  <div class="wi-card wi-rep">
    <h2>Citati</h2>
    <table id="wi-quotes"><thead><tr><th>Tekst</th><th>Organizacija</th><th>Osoba</th><th></th></tr></thead><tbody>
      <?php foreach ($m['quotes'] as $q): ?>
      <tr>
        <td><textarea name="_wi_plakat[q_text][]" rows="2"><?php echo esc_textarea($q['text']); ?></textarea></td>
        <td><input type="text" name="_wi_plakat[q_org][]" value="<?php echo esc_attr($q['org']); ?>"></td>
        <td><input type="text" name="_wi_plakat[q_person][]" value="<?php echo esc_attr($q['person']); ?>"></td>
        <td><button type="button" class="button wi-del">&times;</button></td>
      </tr>
      <?php endforeach; ?>
    </tbody></table>
    <p><button type="button" class="button" id="wi-quote-add">+ Dodaj</button></p>
  </div>

  <div class="wi-card">
    <h2>CTA</h2>
    <label>Naslov</label>
    <input type="text" name="_wi_plakat[cta_title]" value="<?php echo esc_attr($m['cta_title']); ?>">
    <label>Dugme – tekst</label>
    <input type="text" name="_wi_plakat[cta_btn_text]" value="<?php echo esc_attr($m['cta_btn_text']); ?>">
    <p class="wi-note">Link ostaje /kontakt/ (kao i do sada).</p>
  </div>

  <script>
  (function($){
    // Media
    $(function(){
      // gallery
      let frame;
      $('#wi-hero-add').on('click', function(e){
        e.preventDefault();
        if (!frame) {
          frame = wp.media({ title: 'Odaberi slike (više)', multiple: true, library:{type:'image'} });
        frame.on('select', function(){
  const selection = frame.state().get('selection');
  const ids = [];
  const list = $('#wi-hero-list').empty();

  selection.each(function(attachment){
    const att = attachment.toJSON();
    ids.push(att.id);

    // fallback ako nema medium
    let thumb = att.url;
    if (att.sizes) {
      if (att.sizes.medium) {
        thumb = att.sizes.medium.url;
      } else if (att.sizes.thumbnail) {
        thumb = att.sizes.thumbnail.url;
      }
    }

    list.append(
      '<li data-id="'+att.id+'">' +
        '<img src="'+thumb+'">' +
        '<button type="button" class="button-link delete">&times;</button>' +
      '</li>'
    );
  });

  $('#wi-hero-ids').val(ids.join(','));
});
frame.on('open', function(){
  const selection = frame.state().get('selection');
  const ids = ($('#wi-hero-ids').val() || '').split(',').map(id => parseInt(id,10)).filter(Boolean);

  ids.forEach(function(id){
    const attachment = wp.media.attachment(id);
    attachment.fetch();
    selection.add(attachment ? [attachment] : []);
  });
});

        }
        frame.open();
      });
      $('#wi-hero-list').on('click','.delete',function(){
        $(this).closest('li').remove();
        const ids = $('#wi-hero-list li').map(function(){return $(this).data('id');}).get();
        $('#wi-hero-ids').val(ids.join(','));
      }).sortable({
        update:function(){
          const ids = $('#wi-hero-list li').map(function(){return $(this).data('id');}).get();
          $('#wi-hero-ids').val(ids.join(','));
        }
      });

      // region image
      let rframe;
      $('#wi-region-pick').on('click', function(e){
        e.preventDefault();
        if (!rframe) {
          rframe = wp.media({ title:'Odaberi sliku', multiple:false, library:{type:'image'} });
          rframe.on('select', function(){
            const att = rframe.state().get('selection').first().toJSON();
            $('#wi-region-id').val(att.id);
            $('#wi-region-preview').html('<img src="'+(att.sizes.large?att.sizes.large.url:att.url)+'" style="max-width:240px;border:1px solid #ddd;border-radius:6px">');
          });
        }
        rframe.open();
      });
      $('#wi-region-clear').on('click', function(e){
        e.preventDefault(); $('#wi-region-id').val('0'); $('#wi-region-preview').html('<em class="wi-note">Nema slike</em>');
      });

      // repeaters
      $('#wi-faq-add').on('click', function(){
        $('#wi-faq tbody').append('<tr><td><input type="text" name="_wi_plakat[faq_q][]" value=""></td><td><textarea name="_wi_plakat[faq_a][]" rows="2"></textarea></td><td><button type="button" class="button wi-del">&times;</button></td></tr>');
      });
      $('#wi-quotes').on('click','.wi-del', function(){ $(this).closest('tr').remove(); });
      $('#wi-faq').on('click','.wi-del', function(){ $(this).closest('tr').remove(); });
      $('#wi-quote-add').on('click', function(){
        $('#wi-quotes tbody').append('<tr><td><textarea name="_wi_plakat[q_text][]" rows="2"></textarea></td><td><input type="text" name="_wi_plakat[q_org][]" value=""></td><td><input type="text" name="_wi_plakat[q_person][]" value=""></td><td><button type="button" class="button wi-del">&times;</button></td></tr>');
      });
    });
  })(jQuery);
  </script>
  <?php
}

add_action('admin_enqueue_scripts', function($hook){
  if (in_array($hook, ['post-new.php','post.php'], true)) {
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');
  }
});

add_action('save_post_page', function($post_id){
  if (!isset($_POST['wi_plakat_nonce']) || !wp_verify_nonce($_POST['wi_plakat_nonce'], 'wi_plakat_save')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_page', $post_id)) return;
  if (!wi_is_plakatwerbung_page($post_id)) return;

  $defs = wi_plakat_defaults(); $in = $_POST['_wi_plakat'] ?? []; $out = [];

  // hero ids
  $ids = array_filter(array_map('intval', explode(',', (string)($in['hero_ids'] ?? ''))));
  $out['hero_ids'] = !empty($ids) ? $ids : $defs['hero_ids'];

  // intro/steps
  $out['intro_title'] = trim((string)($in['intro_title'] ?? '')) ?: $defs['intro_title'];
  $out['intro_text']  = trim((string)($in['intro_text']  ?? '')) ?: $defs['intro_text'];
  $out['steps_title'] = trim((string)($in['steps_title'] ?? '')) ?: $defs['steps_title'];

  // region
  $out['region_title'] = trim((string)($in['region_title'] ?? '')) ?: $defs['region_title'];
  $paras = preg_split('/\r\n|\r|\n/', (string)($in['region_paras'] ?? ''));
  $paras = array_values(array_filter(array_map('trim', (array)$paras)));
  $out['region_paras'] = !empty($paras) ? array_map('sanitize_text_field', $paras) : $defs['region_paras'];
  $out['region_id'] = max(0, (int)($in['region_id'] ?? 0));

  // CTA
  $out['cta_title']    = trim((string)($in['cta_title'] ?? '')) ?: $defs['cta_title'];
  $out['cta_btn_text'] = trim((string)($in['cta_btn_text'] ?? '')) ?: $defs['cta_btn_text'];

  // FAQ
  $qs = $in['faq_q'] ?? []; $as = $in['faq_a'] ?? []; $faq=[];
  foreach ((array)$qs as $k=>$q) {
    $q = trim((string)$q); $a = trim((string)($as[$k] ?? ''));
    if ($q==='') continue; $faq[] = ['q'=>sanitize_text_field($q),'a'=>wp_kses_post($a)];
  }
  $out['faq'] = !empty($faq)?$faq:$defs['faq'];

  // Quotes
  $t = $in['q_text'] ?? []; $o = $in['q_org'] ?? []; $p = $in['q_person'] ?? []; $qq=[];
  foreach ((array)$t as $k=>$txt) {
    $txt = trim((string)$txt); if ($txt==='') continue;
    $qq[] = ['text'=>sanitize_text_field($txt),'org'=>sanitize_text_field($o[$k] ?? ''),'person'=>sanitize_text_field($p[$k] ?? '')];
  }
  $out['quotes'] = !empty($qq)?$qq:$defs['quotes'];

  update_post_meta($post_id, '_wi_plakat', $out);
});
