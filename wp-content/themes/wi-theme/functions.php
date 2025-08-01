<?php

function wi_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array(
        'primary' => __('Hauptmenü', 'wi-theme'),
        'footermenu' => __('Footer Menu', 'wi-theme'),
    ));
}
add_action('after_setup_theme', 'wi_theme_setup');

/* ========================================
    Register Templates
======================================== */

function wi_theme_register_templates() {
    // Register services template
    add_filter('theme_page_templates', function($templates) {
        $templates['templates/services/services-template.php'] = 'Services';
        return $templates;
    });

    // Load page template from subfolder
    add_filter('template_include', function($template) {
        if (is_page_template('templates/services/services-template.php')) {
            return get_theme_file_path('templates/services/services-template.php');
        }
        return $template;
    });

    // Load service template
    add_filter('single_template', function($template) {
        global $post;
        if ($post && $post->post_type === 'services') {
            $custom_template = locate_template('templates/services/service-template.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        return $template;
    });

    // Load service category template
    add_filter('taxonomy_template', function($template) {
        $taxonomy = get_query_var('taxonomy');
        if ($taxonomy === 'service_category') {
            $custom_template = locate_template('templates/services/service-category-template.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        return $template;
    });
}
add_action('init', 'wi_theme_register_templates');

/* ========================================
    Register CSS
======================================== */

function wi_theme_enqueue_styles() {
    // Global Styles (always loaded first)
    wp_enqueue_style('wi-style', get_stylesheet_uri());

    $styles_to_load = [
        'wi-css-variables'          => '/css/base/variables.css',
        'wi-typography'             => '/css/base/typography.css',
        'wi-global-style'           => '/css/base/globals.css',
        'wi-layout-content-style'   => '/css/layout/content.css',
        'wi-header-style'           => '/css/layout/header.css',
        'wi-footer-style'           => '/css/layout/footer.css',
        'wi-mainmenu-style'         => '/css/layout/mainmenu.css',
    ];

    foreach ($styles_to_load as $handle => $relative_path) {
        $full_path = get_template_directory() . $relative_path;
        if (file_exists($full_path)) {
            wp_enqueue_style(
                $handle,
                get_template_directory_uri() . $relative_path,
                [],
                filemtime($full_path)
            );
        }
    }

    // === Page Styles === //

    // Home
    if (is_page_template('templates/home-template.php')) {
        wi_enqueue_service_styles();
        wi_enqueue_page_style('wi-home-style', '/css/pages/home.css');
    }

    // Services (overview)
    if (is_page_template('templates/services/services-template.php')) {
        wi_enqueue_service_styles();
    }

    // Service Category Page
    if (is_tax('service_category')) {
        wi_enqueue_service_styles(); // same styles as overview page
        wi_enqueue_page_style(
            'wi-service-category-style', 
            '/css/pages/service-category.css', 
            [
                'wi-layout-content-style',
                'wi-grid-style',
                'wi-service-item-style',
                'wi-service-item-overlay-style',
            ]
        );
    }

    // Service Single
    if (is_singular('services')) {
        wp_enqueue_style(
            'wi-single-service-style',
            get_template_directory_uri() . '/css/pages/service.css',
            [
                'wi-layout-content-style',
            ],
            filemtime(get_template_directory() . '/css/pages/service.css')
        );
    }    

    // Contact
if (is_page_template('templates/contact-template.php')) {
    wi_enqueue_page_style('wi-contact-style', '/css/pages/contact.css');
}

    // Imprint
    if (is_page_template('templates/impressum-template.php')) {
        wi_enqueue_page_style('wi-imprint-style', '/css/pages/impressum.css');
    }

    // Privacy Declaration
    if (is_page_template('templates/datenschutz-template.php')) {
        wi_enqueue_page_style('wi-privacy-style', '/css/pages/datenschutz.css');
    }    

    // Load Foundation Icons
    wp_enqueue_style(
        'foundation-icons',
        'https://cdn.jsdelivr.net/npm/foundation-icons/foundation-icons.css',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_styles');

// Helper function to register style sheets
function wi_enqueue_page_style($handle, $relative_path, $deps = []) {
    wp_enqueue_style(
        $handle,
        get_template_directory_uri() . $relative_path,
        $deps,
        filemtime(get_template_directory() . $relative_path)
    );
}

// Grouped styles for all services
function wi_enqueue_service_styles() {
    $service_styles = [
        'wi-grid-style' => '/css/layout/grid.css',
        'wi-service-item-style' => '/css/components/service-items.css',
        'wi-service-item-overlay-style' => '/css/components/service-item-overlays.css',
    ];

    foreach ($service_styles as $handle => $path) {
        wi_enqueue_page_style($handle, $path, ['wi-layout-content-style']);
    }

    wi_enqueue_page_style('wi-services-style', '/css/pages/services.css', array_keys($service_styles));
}

/* ========================================
    Register JS
======================================== */

function wi_theme_enqueue_js() {
    wp_enqueue_script(
        'wi-home-js',
        get_template_directory_uri() . '/js/home.js',
        array(),
        '1.0',
        true
    );
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
        'wi-theme-admin-js', 
        get_template_directory_uri() . '/js/mainmenu.js', 
        array('jquery'), 
        null, 
        true
    );
}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_js');

/* ========================================
    Register Fonts
======================================== */

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

/* ========================================
    Register Shortcodes
======================================== */
// [primary_menu]
function wi_theme_register_primary_menu_shortcode() {
    ob_start();
	 wp_nav_menu(array(
		'menu' => 'Hauptmenü', // menu name, not theme position
		'container' => false,
		'menu_class' => 'mod-menu'
	));
    return ob_get_clean();
}
add_shortcode('primary_menu', 'wi_theme_register_primary_menu_shortcode');

/* ========================================
    Register Appearance > Widgets areas
======================================== */

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

/* ========================================
    Register CPTs
======================================== */

// === Referenzen ===

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

function wi_theme_render_references() {
    $rows = 3;
    $min_items = 24;

    $query = new WP_Query([
        'post_type' => 'references',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    $posts = [];
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = get_post();
        }
        wp_reset_postdata();
    }

    $total_real = count($posts);
    $total_items = max($total_real, $min_items);

    // Fill with Dummy Logo, if needed
    if ($total_real < $min_items) {
        $num_placeholders = $min_items - $total_real;
        for ($i = 0; $i < $num_placeholders; $i++) {
            $posts[] = null;
        }
    }

    // Calculate dynamic item distribution
    $base = floor($total_items / $rows);
    $extra = $total_items % $rows;
    $row_lengths = [];
    for ($i = 0; $i < $rows; $i++) {
        $row_lengths[] = $base + ($i < $extra ? 1 : 0);
    }

    // WordPress logo SVG from jsDelivr
    $default_logo = 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/wordpress.svg';

    echo '<div class="module-box home-customer-logos">';
    echo '<div class="content">';
    echo '<div class="partner-content">';
    echo '<div class="content-list-wrapper partner-list-wrapper">';

    $index = 0;
    for ($row = 0; $row < $rows; $row++) {
        echo '<div class="content-list partner-list carousel" data-carousel-id="' . ($row + 1) . '">';

        for ($i = 0; $i < $row_lengths[$row]; $i++) {

            $post = $posts[$index];

            echo '<div class="carousel-cell">';
            echo '<figure class="customer-logo">';

            if ($post && $post instanceof WP_Post) {
                $id = $post->ID;

                if (has_post_thumbnail($id)) {
                    echo get_the_post_thumbnail($id, 'full');
                } else {
                    echo '<img src="' . esc_url($default_logo) . '" alt="Default Logo">';
                }
            } else {
                echo '<img src="' . esc_url($default_logo) . '" alt="Default Logo">';
            }

            echo '</figure>';
            echo '</div>'; // .carousel-cell

            $index++;
        }

        echo '</div>'; // .carousel
    }

    echo '</div></div></div></div>';
}
// add_action('init', 'wi_theme_render_references');

// === Services ===

function wi_theme_register_services_post_type() {
    $labels = array(
        'name'                  => __('Leistungen', 'wi-theme'),
        'singular_name'         => __('Leistung', 'wi-theme'),
        'menu_name'             => __('Leistungen', 'wi-theme'),
        'name_admin_bar'        => __('Leistung', 'wi-theme'),
        'add_new'               => __('Neue Leistung hinzufügen', 'wi-theme'),
        'add_new_item'          => __('Neue Leistung hinzufügen', 'wi-theme'),
        'new_item'              => __('Neue Leistung', 'wi-theme'),
        'edit_item'             => __('Leistung bearbeiten', 'wi-theme'),
        'view_item'             => __('Leistung ansehen', 'wi-theme'),
        'all_items'             => __('Alle Leistungen', 'wi-theme'),
        'search_items'          => __('Leistungen durchsuchen', 'wi-theme'),
        'parent_item_colon'     => __('Übergeordnete Leistung:', 'wi-theme'),
        'not_found'             => __('Keine Leistungen gefunden.', 'wi-theme'),
        'not_found_in_trash'    => __('Keine Leistungen im Papierkorb.', 'wi-theme'),
        'featured_image'        => __('Leistungsbild', 'wi-theme'),
        'set_featured_image'    => __('Leistungsbild festlegen', 'wi-theme'),
        'remove_featured_image' => __('Leistungsbild entfernen', 'wi-theme'),
        'use_featured_image'    => __('Als Leistungsbild verwenden', 'wi-theme'),
        'archives'              => __('Leistungsarchiv', 'wi-theme'),
        'insert_into_item'      => __('In Leistung einfügen', 'wi-theme'),
        'uploaded_to_this_item' => __('Zu dieser Leistung hochgeladen', 'wi-theme'),
        'filter_items_list'     => __('Leistungen filtern', 'wi-theme'),
        'items_list_navigation' => __('Leistungen Navigation', 'wi-theme'),
        'items_list'            => __('Leistungen Liste', 'wi-theme'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-portfolio',
        'rewrite' => array(
            'slug' => 'services/%service_category%',
            'with_front' => false
        ),
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes'),
        'show_in_rest'       => true,
    );

    register_post_type('services', $args);

    // Reload permalinks (only once, otherwise needs to be commented)
    //flush_rewrite_rules();
}
add_action('init', 'wi_theme_register_services_post_type');

function wi_theme_services_post_type_link($post_link, $post) {
    if ($post->post_type === 'services') {
        $terms = get_the_terms($post->ID, 'service_category');
        if ($terms && !is_wp_error($terms)) {
            $term = array_shift($terms);
            return str_replace('%service_category%', $term->slug, $post_link);
        } else {
            // Falls keine Kategorie vorhanden ist
            return str_replace('%service_category%', 'kategorie', $post_link);
        }
    }
    return $post_link;
}
add_filter('post_type_link', 'wi_theme_services_post_type_link', 10, 2);

function wi_theme_register_service_categories() {
    $labels = array(
        'name'              => __('Leistungskategorien', 'wi-theme'),
        'singular_name'     => __('Leistungskategorie', 'wi-theme'),
        'search_items'      => __('Kategorien durchsuchen', 'wi-theme'),
        'all_items'         => __('Alle Kategorien', 'wi-theme'),
        'parent_item'       => __('Übergeordnete Kategorie', 'wi-theme'),
        'parent_item_colon' => __('Übergeordnete Kategorie:', 'wi-theme'),
        'edit_item'         => __('Kategorie bearbeiten', 'wi-theme'),
        'update_item'       => __('Kategorie aktualisieren', 'wi-theme'),
        'add_new_item'      => __('Neue Kategorie hinzufügen', 'wi-theme'),
        'new_item_name'     => __('Neuer Kategoriename', 'wi-theme'),
        'menu_name'         => __('Leistungskategorien', 'wi-theme'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest' => true,
        'rewrite'           => array(
            'slug' => 'services',
            'with_front' => false,
            'hierarchical' => true
        ),
    );

    register_taxonomy('service_category', array('services'), $args);
}
add_action('init', 'wi_theme_register_service_categories');

// disable fuckin' Gutenberg editor
add_filter( 'use_block_editor_for_post_type', function( $use_block_editor, $post_type ) {
    if ( 'services' === $post_type ) {
        return false; // force classic editor
    }
    return $use_block_editor;
}, 10, 2 );

// --- Services Order ---

function wi_theme_add_service_order_column($columns) {
    $columns['menu_order'] = 'Reihenfolge';
    return $columns;
}
add_filter('manage_services_posts_columns', 'wi_theme_add_service_order_column');

function wi_theme_fill_service_order_column($column, $post_id) {
    if ($column === 'menu_order') {
        echo get_post_field('menu_order', $post_id);
    }
}
add_action('manage_services_posts_custom_column', 'wi_theme_fill_service_order_column', 10, 2);

function wi_theme_make_service_order_column_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}
add_filter('manage_edit-services_sortable_columns', 'wi_theme_make_service_order_column_sortable');

function wi_theme_orderby_menu_order($query) {
    if (! is_admin() || ! $query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'menu_order') {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'asc');
    }
}
add_action('pre_get_posts', 'wi_theme_orderby_menu_order');

function wi_theme_reorder_services_columns($columns) {

    $new_columns = [
        'cb'                        => $columns['cb'],
        'title'                     => $columns['title'],
        'taxonomy-service_category' => $columns['taxonomy-service_category'] ?? 'Leistungskategorien',
        'menu_order'                => 'Reihenfolge',
        'date'                      => $columns['date'],
    ];

    return $new_columns;
}
add_filter('manage_edit-services_columns', 'wi_theme_reorder_services_columns');

// --- Services Category Order ---

function wi_theme_add_category_order_field($taxonomy) {
    ?>
    <div class="form-field">
        <label for="term_order">Reihenfolge</label>
        <input name="term_order" id="term_order" type="number" value="0" size="5">
        <p class="description">Zahl für die Sortierung (kleinste zuerst).</p>
    </div>
    <?php
}
add_action('service_category_add_form_fields', 'wi_theme_add_category_order_field');

function wi_theme_edit_category_order_field($term, $taxonomy) {
    $order = get_term_meta($term->term_id, 'term_order', true);
    ?>

    <tr class="form-field">
        <th scope="row"><label for="term_order">Reihenfolge</label></th>
        <td>
            <input name="term_order" id="term_order" type="number" value="<?php echo esc_attr($order); ?>" size="5">

            <p class="description">Zahl für die Sortierung (kleinste zuerst).</p>
        </td>
    </tr>
    <?php
}
add_action( 'service_category_edit_form_fields', 'wi_theme_edit_category_order_field', 10, 2 );

function wi_theme_save_category_order($term_id, $tt_id) {
    if (isset( $_POST['term_order'])) {
        update_term_meta($term_id, 'term_order', intval($_POST['term_order']));
    }
}
add_action('created_service_category', 'wi_theme_save_category_order', 10, 2 );
add_action('edited_service_category', 'wi_theme_save_category_order', 10, 2 );

function wi_theme_add_order_column($columns) {
    $columns['term_order'] = 'Reihenfolge';
    return $columns;
}
add_filter('manage_edit-service_category_columns', 'wi_theme_add_order_column');

function wi_theme_reorder_category_columns($columns) {

    $new_columns = [
        'cb'          => $columns['cb'],
        'name'        => $columns['name'],
        'description' => $columns['description'],
        'slug'        => $columns['slug'],
        'term_order'  => 'Reihenfolge',
        'posts'       => $columns['posts'], // „Count“ is 'posts' internally
    ];

    return $new_columns;
}
add_filter('manage_edit-service_category_columns', 'wi_theme_reorder_category_columns');

function wi_theme_show_order_column($output, $column_name, $term_id) {
    if ('term_order' === $column_name) {
        $output = intval(get_term_meta($term_id, 'term_order', true));
    }
    return $output;
}
add_filter('manage_service_category_custom_column', 'wi_theme_show_order_column', 10, 3);

function wi_theme_make_order_column_sortable($columns) {
    $columns['term_order'] = 'term_order';
    return $columns;
}
add_filter('manage_edit-service_category_sortable_columns', 'wi_theme_make_order_column_sortable');

function wi_theme_orderby_term_order($query) {
    if ( is_admin()
         && ! empty($query->query_vars['taxonomy'])
         && $query->query_vars['taxonomy'] === 'service_category'
         && ! empty($query->query_vars['orderby'])
         && $query->query_vars['orderby'] === 'term_order') {

        $query->query_vars['orderby'] = 'term_order';
    }
}
add_action('pre_get_terms', 'wi_theme_orderby_term_order');

function wi_theme_quickedit_term_order($column_name, $screen, $taxonomy) {
    if ($taxonomy !== 'service_category' || $column_name !== 'term_order') {
        return;
    }
    ?>
    <fieldset>
        <div class="inline-edit-col">
            <label>
                <span class="title">Reihenfolge</span>
                <span class="input-text-wrap">
                    <input type="number" name="term_order" class="term-order-field" value="" />
                </span>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'wi_theme_quickedit_term_order', 10, 3);

function wi_theme_quickedit_js() {
    $screen = get_current_screen();
    if ($screen->taxonomy !== 'service_category') {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($){
        $('body').on('click', '.editinline', function(){
            var tr = $(this).closest('tr');
            var termOrder = tr.find('td.column-term_order').text().trim();
            $('input[name="term_order"]', '.inline-edit-row').val(termOrder);
        });
    });
    </script>
    <?php
}
add_action('admin_footer-edit-tags.php', 'wi_theme_quickedit_js');

// function wi_theme_create_default_service_categories() {
//     if (!term_exists('Webdesign', 'service_category')) {
//         wp_insert_term('Webdesign', 'service_category');
//     }
//     if (!term_exists('SEO', 'service_category')) {
//         wp_insert_term('SEO', 'service_category');
//     }
//     if (!term_exists('Online-Marketing', 'service_category')) {
//         wp_insert_term('Online-Marketing', 'service_category');
//     }
//     if (!term_exists('Beratung', 'service_category')) {
//         wp_insert_term('Beratung', 'service_category');
//     }
// }
// add_action('after_switch_theme', 'wi_theme_create_default_service_categories');

// --- Helper Functions ---

function wi_theme_group_services_into_rows($services) {
    $service_count = count($services);
    $rows = [];

    if ($service_count === 3) {
        $rows[] = $services;
    } else {
        $i = 0;
        while ($i < $service_count) {
            $remaining = $service_count - $i;
            if ($remaining === 3 && $service_count > 3 && $service_count % 2 !== 0) {
                $rows[] = array_slice($services, $i, 3);
                $i += 3;
            } else {
                $rows[] = array_slice($services, $i, 2);
                $i += 2;
            }
        }
    }

    return $rows;
}

/* ========================================
    Theme Options
======================================== */

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

/* ========================================
    Register Leaflet (free contact map)
======================================== */

// function wi_theme_enqueue_leaflet_assets() {
//
//     wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
//     wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);
//
// }
// add_action('wp_enqueue_scripts', 'wi_theme_enqueue_leaflet_assets');

/* ========================================
    Register Flickity Slider
======================================== */

function wi_theme_enqueue_flickity_assets() {
    // Flickity CSS
    wp_enqueue_style('flickity-css', get_template_directory_uri() . '/css/flickity.min.css', array(), '2.3.0');

    // Flickity JS
    wp_enqueue_script('flickity-js', get_template_directory_uri() . '/js/flickity.pkgd.min.js', array('jquery'), '2.3.0', true);

}
add_action('wp_enqueue_scripts', 'wi_theme_enqueue_flickity_assets');


