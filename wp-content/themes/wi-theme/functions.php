<?php

function wi_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 48,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    register_nav_menus(array(
        'primary' => __('Hauptmenü', 'wi-theme'),
        'footer'        => __('Footer Menu', 'wi-theme'),          // za Impressum/AGBs/...
        'footer_social' => __('Footer Social Menu', 'wi-theme'), 
    ));
}
add_action('after_setup_theme', 'wi_theme_setup');

add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ($relation_type !== 'preconnect') {
        return $urls;
    }
    $urls[] = 'https://fonts.googleapis.com';
    $urls[] = array(
        'href'        => 'https://fonts.gstatic.com',
        'crossorigin' => 'anonymous',
    );
    return $urls;
}, 10, 2);

/**
 * Manje HTTP zahteva i „šuma” u HTML-u (emoji CDN, nepotrebni head linkovi).
 */
function wi_theme_performance_head_cleanup() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('template_redirect', 'rest_output_link_header', 11);
}
add_action('init', 'wi_theme_performance_head_cleanup');

/**
 * Sitemap u robots.txt (radi samo ako u root-u nema statičkog robots.txt).
 */
function wi_theme_robots_txt_sitemap($output, $public) {
    if ((string) $public === '0') {
        return $output;
    }
    $line = 'Sitemap: ' . home_url('/wp-sitemap.xml');
    if (strpos($output, 'Sitemap:') === false) {
        $output .= "\n" . $line . "\n";
    }
    return $output;
}
add_filter('robots_txt', 'wi_theme_robots_txt_sitemap', 10, 2);

/** Jedan zahtev za glavne fontove (bez duplog učitavanja u CSS/header). */
function wi_theme_enqueue_primary_fonts() {
    wp_enqueue_style(
        'wi-theme-fonts',
        'https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&family=Poppins:wght@400;500;700;800&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_primary_fonts', 2);

function wi_theme_needs_contact_assets() {
    return is_page_template('templates/contact-template.php') || is_page('kontakt-new');
}

/**
 * Osnovni SEO bez plugina: meta description, Open Graph, Twitter card.
 * (Ako koristiš Yoast/Rank Math, često već dodaju ove tagove — oni imaju prioritet u sadržaju stranice.)
 */
function wi_theme_seo_meta_tags() {
    if (is_admin()) {
        return;
    }
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        return;
    }
    $raw = '';
    if (is_singular()) {
        global $post;
        if ($post instanceof WP_Post) {
            $raw = (string) get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
            if ($raw === '' && has_excerpt($post)) {
                $raw = get_the_excerpt($post);
            }
            if ($raw === '' && get_post_meta($post->ID, '_rank_math_description', true)) {
                $raw = (string) get_post_meta($post->ID, '_rank_math_description', true);
            }
        }
    }
    if ($raw === '') {
        $raw = get_bloginfo('description', 'display');
    }
    $desc = wp_strip_all_tags((string) $raw);
    $desc = preg_replace('/\s+/u', ' ', $desc);
    $desc = trim($desc);
    if ($desc !== '') {
        if (function_exists('mb_strlen') && mb_strlen($desc) > 160) {
            $desc = mb_substr($desc, 0, 157) . '…';
        } elseif (strlen($desc) > 160) {
            $desc = substr($desc, 0, 157) . '…';
        }
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }

    $canonical = '';
    if (is_singular()) {
        $canonical = get_permalink();
    } elseif (is_front_page()) {
        $canonical = home_url('/');
    }
    if (!$canonical) {
        return;
    }

    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";

    $title = wp_get_document_title();
    $og_type = is_front_page() ? 'website' : 'article';

    echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc !== '') {
        echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    }
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name', 'display')) . '">' . "\n";

    $img = '';
    if (is_singular() && has_post_thumbnail()) {
        $img = get_the_post_thumbnail_url(null, 'large');
    } elseif (get_theme_mod('custom_logo')) {
        $img = wp_get_attachment_image_url((int) get_theme_mod('custom_logo'), 'full');
    }
    if ($img) {
        echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="' . esc_attr($img ? 'summary_large_image' : 'summary') . '">' . "\n";
}
add_action('wp_head', 'wi_theme_seo_meta_tags', 4);

/**
 * Strukturirani podaci: WebSite + Organization (bez dodatnog plugina).
 */
function wi_theme_json_ld_graph() {
    if (is_admin()) {
        return;
    }
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        return;
    }
    $url  = home_url('/');
    $name = get_bloginfo('name', 'display');
    $logo = '';
    if (get_theme_mod('custom_logo')) {
        $logo = wp_get_attachment_image_url((int) get_theme_mod('custom_logo'), 'full');
    }
    if (!$logo) {
        $logo = (string) (get_option('wi_theme_logo_dark') ?: get_option('wi_theme_logo_light'));
    }
    $org = array(
        '@type' => 'Organization',
        '@id'   => $url . '#organization',
        'name'  => $name,
        'url'   => $url,
    );
    if ($logo !== '') {
        $org['logo'] = array(
            '@type' => 'ImageObject',
            'url'   => $logo,
        );
    }
    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array(
            array(
                '@type'     => 'WebSite',
                '@id'       => $url . '#website',
                'url'       => $url,
                'name'      => $name,
                'publisher' => array('@id' => $url . '#organization'),
                'inLanguage' => 'de',
            ),
            $org,
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}
add_action('wp_head', 'wi_theme_json_ld_graph', 5);

/** Erstellt die 6 Portfolio-Detail-Seiten (Unsere Arbeiten), falls sie noch nicht existieren. */
function wi_create_portfolio_detail_pages() {
  $pages = [
    'fahrzeugbeschriftung' => 'Fahrzeugbeschriftung',
    'schaufenster-werbung' => 'Schaufenster-Werbung',
    'grossflachenplakat' => 'Großflächenplakat',
    'leuchtreklame' => 'Leuchtreklame',
    'city-light-poster' => 'City-Light-Poster',
    'fassaden-beschriftung' => 'Fassaden-Beschriftung',
  ];
  $template = 'templates/portfolio-detail-template.php';
  foreach ($pages as $slug => $title) {
    if (get_page_by_path($slug)) {
      continue;
    }
    $page_id = wp_insert_post([
      'post_title'   => $title,
      'post_name'    => $slug,
      'post_status'  => 'publish',
      'post_type'    => 'page',
      'post_author'  => 1,
    ]);
    if ($page_id && !is_wp_error($page_id)) {
      update_post_meta($page_id, '_wp_page_template', $template);
    }
  }
}
add_action('after_switch_theme', 'wi_create_portfolio_detail_pages');
add_action('init', function () {
  if (get_option('wi_portfolio_detail_pages_version') === '1') {
    return;
  }
  wi_create_portfolio_detail_pages();
  update_option('wi_portfolio_detail_pages_version', '1');
}, 20);

/** Jobs-Seite mit Template anlegen (slug „jobs“), falls noch nicht vorhanden. */
function wi_create_jobs_page() {
  if (get_page_by_path('jobs')) {
    return;
  }
  $page_id = wp_insert_post([
    'post_title'   => 'Jobs',
    'post_name'    => 'jobs',
    'post_status'  => 'publish',
    'post_type'    => 'page',
    'post_author'  => 1,
  ]);
  if ($page_id && !is_wp_error($page_id)) {
    update_post_meta($page_id, '_wp_page_template', 'templates/jobs-template.php');
  }
}
add_action('after_switch_theme', 'wi_create_jobs_page');
add_action('init', function () {
  if (get_option('wi_jobs_page_version') === '1') {
    return;
  }
  wi_create_jobs_page();
  update_option('wi_jobs_page_version', '1');
}, 21);

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
        'wi-floating-whatsapp-style',
        get_template_directory_uri() . '/css/floating-whatsapp.css',
        array(),
        filemtime(get_template_directory() . '/css/floating-whatsapp.css')
    );
    if (wi_theme_needs_contact_assets()) {
        wp_enqueue_style(
            'wi-contact-style',
            get_template_directory_uri() . '/css/contact.css',
            array('wi-style'),
            filemtime(get_template_directory() . '/css/contact.css')
        );
        wp_enqueue_script(
            'wi-contact-js',
            get_template_directory_uri() . '/js/contact.js',
            array(),
            filemtime(get_template_directory() . '/js/contact.js'),
            true
        );
    }
    if (is_page() && get_page_template_slug() === 'templates/portfolio-detail-template.php') {
        wp_enqueue_style(
            'wi-portfolio-detail-style',
            get_template_directory_uri() . '/css/portfolio-detail.css',
            array('wi-style', 'wi-floating-whatsapp-style'),
            filemtime(get_template_directory() . '/css/portfolio-detail.css')
        );
    }
    if (is_page() && get_page_template_slug() === 'templates/impressum-template.php') {
        wp_enqueue_style(
            'wi-impressum-style',
            get_template_directory_uri() . '/css/impressum.css',
            array('wi-style'),
            filemtime(get_template_directory() . '/css/impressum.css')
        );
    }
    if (is_page() && get_page_template_slug() === 'templates/jobs-template.php') {
        wp_enqueue_style(
            'wi-jobs-style',
            get_template_directory_uri() . '/css/jobs.css',
            array('wi-style'),
            filemtime(get_template_directory() . '/css/jobs.css')
        );
        $jobs_script_deps = array();
        $rec_site_jobs    = get_option('wi_contact_recaptcha_site', '');
        if ($rec_site_jobs !== '') {
            wp_register_script(
                'wi-recaptcha-v3',
                'https://www.google.com/recaptcha/api.js?render=' . rawurlencode($rec_site_jobs),
                array(),
                null,
                true
            );
            $jobs_script_deps[] = 'wi-recaptcha-v3';
        }
        wp_enqueue_script(
            'wi-jobs-form',
            get_template_directory_uri() . '/js/jobs-form.js',
            $jobs_script_deps,
            filemtime(get_template_directory() . '/js/jobs-form.js'),
            true
        );
    }
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
    wp_enqueue_script(
        'wi-mainmenu',
        get_template_directory_uri() . '/js/mainmenu.js',
        array('jquery'),
        filemtime(get_template_directory() . '/js/mainmenu.js'),
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

// === HERO SECTION (inc/hero-metabox.php) ===
require_once get_template_directory() . '/inc/hero-metabox.php';
// === HOME SECTIONS (About, Services, Portfolio, Clients, Testimonials, CTA) ===
require_once get_template_directory() . '/inc/home-sections-metabox.php';
require_once get_template_directory() . '/inc/jobs-metabox.php';
// === NAV LINKS HELPER ===
require_once get_template_directory() . '/inc/wi-nav-links.php';
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
        <input type="text" class="widefat" name="wi_services_title" value="<?php echo esc_attr($section_title ?: 'Was wir machen'); ?>">
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
        'wi_home_hero_section',
        __('Hero sekcija (vrhu stranice)', 'wi'),
        'wi_home_hero_section_cb',
        'page',
        'normal',
        'high'
    );

    // About (dizajn iz sajt)
    add_meta_box('wi_home_about', __('About Sekcija', 'wi'), 'wi_home_about_cb', 'page', 'normal', 'high');
    // Services (4 kartice)
    add_meta_box('wi_home_services_design', __('Services Sekcija (4 Karten)', 'wi'), 'wi_home_services_design_cb', 'page', 'normal', 'high');
    // Portfolio (6 projekata)
    add_meta_box('wi_home_portfolio', __('Portfolio Sekcija', 'wi'), 'wi_home_portfolio_cb', 'page', 'normal', 'high');
    // Clients (marquee)
    add_meta_box('wi_home_clients', __('Clients Sekcija', 'wi'), 'wi_home_clients_cb', 'page', 'normal', 'high');
    // Testimonials (slider)
    add_meta_box('wi_home_testimonials', __('Testimonials Sekcija', 'wi'), 'wi_home_testimonials_cb', 'page', 'normal', 'high');
    // CTA (finalni poziv)
    add_meta_box('wi_home_cta_design', __('CTA Sekcija (vor Footer)', 'wi'), 'wi_home_cta_design_cb', 'page', 'normal', 'high');
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
    if (wi_is_home_template($post->ID)) return; // Home hat eigenes CTA-Metabox

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
// --- Impressum template check & metabox (dizajn: sajt/ImpressumPage.tsx) ---
function wi_is_impressum_template($post_id) {
    $tpl = (string) get_page_template_slug($post_id);
    return $tpl === 'templates/impressum-template.php' || strpos($tpl, 'impressum') !== false;
}

function wi_impressum_default_data() {
    return [
        'ddg' => [
            'title' => "Angaben gemäß § 5 DDG\n(Digitale-Dienste-Gesetz)",
            'paras' => ['Werbeinsel', 'Inhaber: Kristian Cajic', 'Musterstraße 12', '88045 Friedrichshafen', 'Deutschland'],
        ],
        'kontakt' => [
            'title' => 'Kontakt',
            'paras' => ['Telefon: +49 (0) XXX XXXXXXX', 'E-Mail: info@werbeinsel.de'],
        ],
        'ustid' => [
            'title' => 'Umsatzsteuer-ID',
            'text'  => "Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:\nDE123456789 (bitte eintragen oder entfernen, falls nicht vorhanden)",
        ],
        'verantwortlich' => [
            'title' => "Verantwortlich für den Inhalt\nnach § 18 Abs. 2 MStV",
            'paras' => ['Kristian Cajic', 'Musterstraße 12', '88045 Friedrichshafen'],
        ],
        'disclaimer_title' => "Haftungsausschluss\n(Disclaimer)",
        'haftung_inhalte' => [
            'title' => 'Haftung für Inhalte',
            'text'  => "Als Diensteanbieter sind wir gemäß § 7 Abs. 1 DDG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 DDG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.\n\nVerpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.",
        ],
        'haftung_links' => [
            'title' => 'Haftung für Links',
            'text'  => "Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.\n\nDie verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar.\n\nBei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.",
        ],
        'urheberrecht' => [
            'title' => 'Urheberrecht',
            'text'  => "Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechts bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.\n\nDownloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet.\n\nSollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.",
        ],
    ];
}

add_action('add_meta_boxes_page', function ($post) {
    if (!$post instanceof WP_Post || !wi_is_impressum_template($post->ID)) return;
    add_meta_box(
        'wi_impressum_data',
        __('Impressum – Inhalt (alle Texte bearbeiten)', 'wi'),
        'wi_impressum_metabox_cb',
        'page',
        'normal',
        'high'
    );
});

function wi_impressum_metabox_cb($post) {
    wp_nonce_field('wi_save_impressum_data', 'wi_impressum_nonce');
    $data = get_post_meta($post->ID, '_wi_impressum_data', true);
    if (!is_array($data)) $data = [];
    $defaults = wi_impressum_default_data();
    $data = wp_parse_args($data, $defaults);
    foreach (array_keys($defaults) as $k) {
        if (is_array($defaults[$k]) && isset($data[$k]) && is_array($data[$k])) {
            $data[$k] = wp_parse_args($data[$k], $defaults[$k]);
        }
    }

    $paras_to_text = function ($paras) { return is_array($paras) ? implode("\n", $paras) : (string) $paras; };
    $text_to_paras = function ($text) { return array_filter(array_map('trim', explode("\n", (string) $text))); };
    ?>
    <p><strong><?php esc_html_e('Angaben § 5 DDG', 'wi'); ?></strong></p>
    <p><label>Überschrift</label><br><input type="text" name="wi_impressum_ddg_title" value="<?php echo esc_attr($data['ddg']['title']); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Zeilen (eine pro Zeile)</label><br><textarea name="wi_impressum_ddg_paras" rows="6" style="width:100%;max-width:600px;"><?php echo esc_textarea($paras_to_text($data['ddg']['paras'])); ?></textarea></p>

    <p><strong><?php esc_html_e('Kontakt', 'wi'); ?></strong></p>
    <p><label>Überschrift</label><br><input type="text" name="wi_impressum_kontakt_title" value="<?php echo esc_attr($data['kontakt']['title']); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Zeilen (eine pro Zeile)</label><br><textarea name="wi_impressum_kontakt_paras" rows="4" style="width:100%;max-width:600px;"><?php echo esc_textarea($paras_to_text($data['kontakt']['paras'])); ?></textarea></p>

    <p><strong><?php esc_html_e('Umsatzsteuer-ID', 'wi'); ?></strong></p>
    <p><label>Überschrift</label><br><input type="text" name="wi_impressum_ustid_title" value="<?php echo esc_attr($data['ustid']['title']); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Text</label><br><textarea name="wi_impressum_ustid_text" rows="4" style="width:100%;max-width:600px;"><?php echo esc_textarea(isset($data['ustid']['text']) ? $data['ustid']['text'] : ''); ?></textarea></p>

    <p><strong><?php esc_html_e('Verantwortlich § 18 MStV', 'wi'); ?></strong></p>
    <p><label>Überschrift</label><br><input type="text" name="wi_impressum_verantwortlich_title" value="<?php echo esc_attr($data['verantwortlich']['title']); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Zeilen (eine pro Zeile)</label><br><textarea name="wi_impressum_verantwortlich_paras" rows="4" style="width:100%;max-width:600px;"><?php echo esc_textarea($paras_to_text($data['verantwortlich']['paras'])); ?></textarea></p>

    <p><strong><?php esc_html_e('Haftungsausschluss', 'wi'); ?></strong></p>
    <p><label>Überschrift Disclaimer</label><br><input type="text" name="wi_impressum_disclaimer_title" value="<?php echo esc_attr($data['disclaimer_title']); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Haftung für Inhalte – Überschrift</label><br><input type="text" name="wi_impressum_hinhalte_title" value="<?php echo esc_attr($data['haftung_inhalte']['title']); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Haftung für Inhalte – Text (Absätze durch Leerzeile)</label><br><textarea name="wi_impressum_hinhalte_text" rows="8" style="width:100%;max-width:600px;"><?php echo esc_textarea(isset($data['haftung_inhalte']['text']) ? $data['haftung_inhalte']['text'] : ''); ?></textarea></p>
    <p><label>Haftung für Links – Überschrift</label><br><input type="text" name="wi_impressum_hlinks_title" value="<?php echo esc_attr($data['haftung_links']['title'] ?? ''); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Haftung für Links – Text</label><br><textarea name="wi_impressum_hlinks_text" rows="8" style="width:100%;max-width:600px;"><?php echo esc_textarea(isset($data['haftung_links']['text']) ? $data['haftung_links']['text'] : ''); ?></textarea></p>
    <p><label>Urheberrecht – Überschrift</label><br><input type="text" name="wi_impressum_urheber_title" value="<?php echo esc_attr($data['urheberrecht']['title'] ?? ''); ?>" style="width:100%;max-width:600px;"></p>
    <p><label>Urheberrecht – Text</label><br><textarea name="wi_impressum_urheber_text" rows="8" style="width:100%;max-width:600px;"><?php echo esc_textarea(isset($data['urheberrecht']['text']) ? $data['urheberrecht']['text'] : ''); ?></textarea></p>
    <?php
}

add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['wi_impressum_nonce']) || !wp_verify_nonce($_POST['wi_impressum_nonce'], 'wi_save_impressum_data')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;
    if (!wi_is_impressum_template($post_id)) return;

    $text_to_paras = function ($text) { return array_filter(array_map('trim', explode("\n", (string) $text))); };
    $data = [
        'ddg' => [
            'title' => isset($_POST['wi_impressum_ddg_title']) ? sanitize_text_field($_POST['wi_impressum_ddg_title']) : '',
            'paras' => $text_to_paras(isset($_POST['wi_impressum_ddg_paras']) ? $_POST['wi_impressum_ddg_paras'] : ''),
        ],
        'kontakt' => [
            'title' => isset($_POST['wi_impressum_kontakt_title']) ? sanitize_text_field($_POST['wi_impressum_kontakt_title']) : '',
            'paras' => $text_to_paras(isset($_POST['wi_impressum_kontakt_paras']) ? $_POST['wi_impressum_kontakt_paras'] : ''),
        ],
        'ustid' => [
            'title' => isset($_POST['wi_impressum_ustid_title']) ? sanitize_text_field($_POST['wi_impressum_ustid_title']) : '',
            'text'  => isset($_POST['wi_impressum_ustid_text']) ? wp_kses_post($_POST['wi_impressum_ustid_text']) : '',
        ],
        'verantwortlich' => [
            'title' => isset($_POST['wi_impressum_verantwortlich_title']) ? sanitize_text_field($_POST['wi_impressum_verantwortlich_title']) : '',
            'paras' => $text_to_paras(isset($_POST['wi_impressum_verantwortlich_paras']) ? $_POST['wi_impressum_verantwortlich_paras'] : ''),
        ],
        'disclaimer_title' => isset($_POST['wi_impressum_disclaimer_title']) ? sanitize_text_field($_POST['wi_impressum_disclaimer_title']) : '',
        'haftung_inhalte' => [
            'title' => isset($_POST['wi_impressum_hinhalte_title']) ? sanitize_text_field($_POST['wi_impressum_hinhalte_title']) : '',
            'text'  => isset($_POST['wi_impressum_hinhalte_text']) ? wp_kses_post($_POST['wi_impressum_hinhalte_text']) : '',
        ],
        'haftung_links' => [
            'title' => isset($_POST['wi_impressum_hlinks_title']) ? sanitize_text_field($_POST['wi_impressum_hlinks_title']) : '',
            'text'  => isset($_POST['wi_impressum_hlinks_text']) ? wp_kses_post($_POST['wi_impressum_hlinks_text']) : '',
        ],
        'urheberrecht' => [
            'title' => isset($_POST['wi_impressum_urheber_title']) ? sanitize_text_field($_POST['wi_impressum_urheber_title']) : '',
            'text'  => isset($_POST['wi_impressum_urheber_text']) ? wp_kses_post($_POST['wi_impressum_urheber_text']) : '',
        ],
    ];
    update_post_meta($post_id, '_wi_impressum_data', $data);
}, 20);

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


/* jQuery UI sortable (admin) */
add_action('admin_enqueue_scripts', function($hook){
  if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
  wp_enqueue_script('jquery-ui-sortable');
});
