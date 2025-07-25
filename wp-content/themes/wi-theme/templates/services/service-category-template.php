<?php
/**
 * Template for the service-categories
 * Path: templates/services/service-category-template.php
 */

get_header(); 
?>

<main class="wi-services-category-main">
    <div class="inner-wrapper">
        <section class="service-category-section">
            <header>
                <h1><?php single_term_title(); ?></h1>
                <p><?php echo term_description(); ?></p>
            </header>
            <div class="services-grid-desktop">
                <?php 
                    $term = get_queried_object();
                    $query = new WP_Query([
                        'post_type' => 'services',
                        'posts_per_page' => -1,
                        'tax_query' => [[
                            'taxonomy' => 'service_category',
                            'field'    => 'slug',
                            'terms' => $term->slug,
                        ]],
                        'orderby' => 'menu_order',
                        'order'   => 'ASC',
                    ]);

                    $services = $query->posts;
                    $rows = wi_theme_group_services_into_rows($services);                
                
                foreach ($rows as $row): ?>
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
        </section>
    </div>
</main>

<?php get_footer(); ?>
