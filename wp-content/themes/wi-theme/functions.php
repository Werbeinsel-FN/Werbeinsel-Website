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

    wp_enqueue_style(
        'wi-header-style', 
        get_template_directory_uri() . '/css/header.css', 
        array(), 
        filemtime(get_template_directory() . '/css/header.css')
    );    

    wp_enqueue_style(
        'wi-footer-style', 
        get_template_directory_uri() . '/css/footer.css', 
        array(), 
        filemtime(get_template_directory() . '/css/footer.css')
    );

    wp_enqueue_style(
        'wi-mainmenu-style',
        get_template_directory_uri() . '/css/mainmenu.css',
        array(),
        filemtime(get_template_directory() . '/css/mainmenu.css')
    );

    wp_enqueue_style(
        'wi-home-style',
        get_template_directory_uri() . '/css/home.css',
        array(),
        filemtime(get_template_directory() . '/css/home.css')
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

///////////////////////////////////////////////////////////////////////
//	Register JS
///////////////////////////////////////////////////////////////////////

function wi_theme_enqueue_js() {
    wp_enqueue_script(
        'wi-home-js',
        get_template_directory_uri() . '/js/home.js',
        array(),
        '1.0',
        true
    );
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
//	Register Fonts
///////////////////////////////////////////////////////////////////////

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

///////////////////////////////////////////////////////////////////////
//	Register Shortcodes
///////////////////////////////////////////////////////////////////////

// [primary_menu]
function wi_theme_register_primary_menu_shortcode() {
    ob_start();
	 wp_nav_menu(array(
		'menu' => 'Hauptmenü', // Menü-Name, nicht Theme-Position
		'container' => false,
		'menu_class' => 'mod-menu'
	));
    return ob_get_clean();
}
add_shortcode('primary_menu', 'wi_theme_register_primary_menu_shortcode');

///////////////////////////////////////////////////////////////////////
//	Register Appearance > Widgets areas
///////////////////////////////////////////////////////////////////////

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
//	Register CPTs
///////////////////////////////////////////////////////////////////////

// Referenzen
function wi_theme_register_references_post_type() {
    $labels = array(
        'name' => __('Referenzen'),
        'singular_name' => __('Referenz'),
        // … weitere Labels
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-networking',
        'rewrite' => array('slug' => 'referenzen'),
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest' => true,
    );
    register_post_type('references', $args);
}
add_action('init', 'wi_theme_register_references_post_type');

function wi_theme_render_references($post) {

    // Get total references amount
    $total_query = new WP_Query([
        'post_type' => 'references',
        'posts_per_page' => -1,
        'fields' => 'ids' // only IDs for better performance
    ]);
    $total_posts = count($total_query->posts);

    if ($total_posts === 0) {
        echo '<p>' . __('Keine Referenzen gefunden.', 'wi-theme') . '</p>';
        return;
    }

    $posts_per_slider = ceil($total_posts / 3); // example: 20/3 = 7

    echo '<div class="module-box  home-customer-logos ">';
    echo '<div class="content">';
    echo '<div class="partner-content">';
    echo '<div class="content-list-wrapper partner-list-wrapper">';

    for ($i = 0; $i < 3; $i++) {
        $query = new WP_Query([
            'post_type' => 'references',
            'posts_per_page' => $posts_per_slider,
            'offset' => $i * $posts_per_slider,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        if ($query->have_posts()) {
            echo '<div class="content-list partner-list carousel" data-carousel-id="' . ($i + 1) . '">';
            while ($query->have_posts()) {
                $query->the_post(); ?>
                <div class="carousel-cell">
                    <figure class="customer-logo">
                        <?php
                        // Zwetschke demo logos (remove later)
                        if (get_the_ID() == 7) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/74/3789b22de4a589df/zwetschke_kunde_waschwelt.png" alt="Waschwelt">';
                        } 
                        else if (get_the_ID() == 8) {
                            echo '<img src="https://www.zwetschke.de/images/customerlogos/ab-in-den-urlaub/ab_in_den_urlaub_schwarz_neu.webp">';
                        }
                        else if (get_the_ID() == 9) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/51/cac6b30b1c3ad457/zwetschke_kunde_radio_fantasy.png">';
                        }   
                        else if (get_the_ID() == 10) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/93/6780d75f7d1ca13a/zwetschke_kunde_graefliche_kliniken.png">';
                        } 
                        else if (get_the_ID() == 11) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/45/021f68cf42bb6e9c/zwetschke_kunde_kesselhaus.png">';
                        }     
                        else if (get_the_ID() == 12) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/64/7563eeed48d61d11/zwetschke_kunde_thomsit.png">';
                        } 
                        else if (get_the_ID() == 13) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/100/55cf79380fce8ba6/zwetschke_kunde_blickfang.png">';
                        } 
                        else if (get_the_ID() == 14) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/77/f049d2082fe328da/zwetschke_kunde_roma.png">';
                        } 
                        else if (get_the_ID() == 15) {
                            echo '<img src="https://www.zwetschke.de/images/kundenlogos/xentral/xentral-e-mail-signatur-300px-x.webp">';
                        } 
                        else if (get_the_ID() == 16) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/89/bc6256eefe3487b1/zwetschke_kunde_uli_und_du.png">';
                        }
                        else if (get_the_ID() == 17) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/87/8a9265668733aaae/zwetschke_kunde_wald_und_schrat.png">';
                        }   
                        else if (get_the_ID() == 18) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/97/a52683d1467c47f4/zwetschke_kunde_easybill.png">';
                        }
                        else if (get_the_ID() == 19) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/95/5979479bdc2df066/zwetschke_kunde_friedel.png">';
                        }  
                        else if (get_the_ID() == 20) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/98/9d28a7596020ab73/zwetschke_kunde_der_kuechenprofi.png">';
                        }  
                        else if (get_the_ID() == 21) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/47/c056208614ef5dce/zwetschke_kunde_landeswelle.png">';
                        }  
                        else if (get_the_ID() == 22) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/96/a20a6c9819d532ce/zwetschke_kunde_energie_specht.png">';
                        }  
                        else if (get_the_ID() == 23) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/76/78cbb120243f2256/zwetschke_kunde_safeboxx.png">';
                        }  
                        else if (get_the_ID() == 24) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/80/aaf33602df553859/zwetschke_kunde_mamia.png">';
                        }  
                        else if (get_the_ID() == 25) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/102/d316df6c497c35ff/zwetschke_kunde_beko.png">';
                        }  
                        else if (get_the_ID() == 26) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/104/38907221aa7f2137/zwetschke_kunde_auto_reichhardt.png">';
                        }  
                        else if (get_the_ID() == 27) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }    
                        else if (get_the_ID() == 28) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }   
                        else if (get_the_ID() == 29) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }   
                        else if (get_the_ID() == 30) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }   
                        else if (get_the_ID() == 31) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }   
                        else if (get_the_ID() == 32) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }   
                        else if (get_the_ID() == 33) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }   
                        else if (get_the_ID() == 34) {
                            echo '<img src="https://www.zwetschke.de/content/cache/kundenlogo/60/490631f491e99577/zwetschke_kunde_schneider.png">';
                        }                         
                        else {
                            the_post_thumbnail('full');
                        }
                        ?>
                    </figure>
                </div>
                <?php
            }
            echo '</div>';
        }

        wp_reset_postdata();
    } 
    
    echo '</div></div></div></div>';
}
// add_action('init', 'wi_theme_render_references');

///////////////////////////////////////////////////////////////////////
//	Theme Options
///////////////////////////////////////////////////////////////////////

function wi_theme_add_admin_menu() {
    add_menu_page(
        __('Theme Options', 'wi-theme'), // site name
        __('Theme Options', 'wi-theme'), // menu name
        'manage_options',                // permissions
        'wi-theme-options',              // slug
        'wi_theme_options_page',         // callback-function
        '',                              // Icon (empty = default)
        61                               // Position in menu
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

//Allow (.js, ).svg & .ico upload to Mediathek
function wi_theme_allowed_upload_types($mimes) {
    //$mimes['js'] = 'application/javascript';
    $mimes['svg'] = 'image/svg+xml';
 	$mimes['ico'] = 'image/vnd.microsoft.icon';		
    return $mimes;
}
add_filter('upload_mimes', 'wi_theme_allowed_upload_types');

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

///////////////////////////////////////////////////////////////////////
//	Register Flickity Slider
///////////////////////////////////////////////////////////////////////

function wi_theme_enqueue_flickity_assets() {
    // Flickity CSS
    wp_enqueue_style('flickity-css', get_template_directory_uri() . '/css/flickity.min.css', array(), '2.3.0');

    // Flickity JS
    wp_enqueue_script('flickity-js', get_template_directory_uri() . '/js/flickity.pkgd.min.js', array('jquery'), '2.3.0', true);
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_flickity_assets');
