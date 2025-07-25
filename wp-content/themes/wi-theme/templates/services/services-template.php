<?php
/**
 * Template Name: Services
 * Template Post Type: page
 */

get_header(); ?>

<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">

<main class="wi-services-main" role="main">
    <div class="inner-wrapper">
        <?php
        $service_categories = get_terms([
            'taxonomy' => 'service_category',
            'hide_empty' => true,
        ]);

        usort($service_categories, function($a, $b) {
            return intval(get_term_meta($a->term_id, 'term_order', true)) <=> intval(get_term_meta($b->term_id, 'term_order', true));
        });

        $section_titles = ['Auf die Straße', 'Lass kleben', 'Design & Design'];

        foreach ($service_categories as $index => $cat) :

            $query = new WP_Query([
                'post_type' => 'services',
                'posts_per_page' => -1,
                'tax_query' => [[
                    'taxonomy' => 'service_category',
                    'field'    => 'slug',
                    'terms'    => $cat->slug,
                ]],
                'orderby' => 'menu_order',
                'order'   => 'ASC',
            ]);

            if (!$query->have_posts()) continue;

            $services = $query->posts;
            $rows = wi_theme_group_services_into_rows($services);
            $section_class = ($index % 2) ? 'service-category-section is-dark' : 'service-category-section';
            $section_title = $section_titles[$index] ?? esc_html($cat->name);
            ?>

            <section class="<?= $section_class; ?>" aria-labelledby="section-title-<?= $index; ?>">
                <header>
                    <h1 id="section-title-<?= $index; ?>"><?= $section_title; ?></h1>
                </header>

                <div class="services-grid-desktop">
                    <?php foreach ($rows as $row): ?>
                        <div class="services-grid-row <?= count($row) === 3 ? 'three-columns-row' : (count($row) === 1 ? 'one-column-row' : 'two-columns-row'); ?>">
                            <?php foreach ($row as $service): ?>
                                <?php
                                    get_template_part(
                                        'templates/services/template-parts/service-item', 
                                        null, 
                                        ['service' => $service]
                                    );
                                ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="services-grid-mobile">
                    <div class="services-carousel">
                        <?php foreach ($services as $service): ?>
                                <?php 
                                    get_template_part(
                                        'templates/services/template-parts/service-item', 
                                        null, 
                                        ['service' => $service]
                                    ); 
                                ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        <?php 
            endforeach; 
            wp_reset_postdata(); 
        ?>
    </div>
</main>

<?php get_footer(); ?>