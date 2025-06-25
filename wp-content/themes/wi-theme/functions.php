<?php

function wi_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array(
        'primary' => __('Hauptmenü', 'wi-theme'),
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
