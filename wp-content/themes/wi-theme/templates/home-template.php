<?php
/**
 * Template Name: Home
 * Description: Template for the page "Home"
 */

get_header(); ?>

<!-- <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/home.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<main id="home-main" class="home-main">
    <div class="home-main-container">
        <!-- <section class="home-news-container">
            <?php
            // Query for posts
            $blog_posts = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 6, // Amount of posts shown
                'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
            ));

            if ($blog_posts->have_posts()) :
                while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="post-header">
                            <div class="post-header-top-wrapper">
                                <div class="post-thumbnail-wrapper">								
                                    <?php the_post_thumbnail('large', ['class' => 'post-thumbnail', 'alt' => get_the_title()]); ?> 
                                </div>
                            </div>
                        </header>
                        <div class="post-content">
                            <div class="post-category-wrapper">
                                <?php the_category(', '); ?>
                            </div>
                            <div class="post-title-wrapper">
                                <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>							
                            </div>
                            <div class="post-date-wrapper">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>
                        </div>
                    </article>
                <?php endwhile;

                // Pagination
                the_posts_pagination(array(
                    'prev_text' => __('Vorherige Seite', 'wi-theme'),
                    'next_text' => __('Nächste Seite', 'wi-theme'),
                ));
            else :
                echo '<p>' . __('Keine Beiträge gefunden.', 'wi-theme') . '</p>';
            endif;

            // Reset Post Data
            wp_reset_postdata();
            ?>
        </section> -->

        <section class="home-services-container">
            <!-- <header>
                <h1>Unsere Leistungen:</h1>
            </header> -->
            <div class="home-services-content-container">
                <div class="services-grid-desktop">
                    <?php                     
                        $service_categories = get_terms([
                            'taxonomy' => 'service_category',
                            'hide_empty' => true,
                        ]);

                        usort($service_categories, function($a, $b) {
                            return intval(get_term_meta($a->term_id, 'term_order', true)) <=> intval(get_term_meta($b->term_id, 'term_order', true));
                        });

                        $section_titles = ['Auf die Straße', 'Lass kleben', 'Design & Design'];
                        $index = 0;

                        foreach ($service_categories as $cat) :
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

                            $services = $query->posts;
                            $rows = wi_theme_group_services_into_rows($services);
                            $section_class = ($index % 2) ? 'service-category-section is-dark' : 'service-category-section';
                            $section_title = $section_titles[$index] ?? esc_html($cat->name);
                            ?>

                            <section class="<?= $section_class; ?>" aria-labelledby="section-title-<?= $index; ?>">
                                <header>
                                    <h1 id="section-title-<?= $index; ?>"><?= $section_title; ?></h1>
                                </header>

                                <?php if (!empty($rows)) : ?>
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
                                <?php else : ?>
                                    <p><?= __('Keine Services gefunden.', 'wi-theme'); ?></p>
                                <?php endif; ?>

                            </section>

                            <?php
                            wp_reset_postdata();
                            $index++;
                        endforeach; 
                    ?>
                </div>                  
            </div>
        </section>        

        <?php wi_theme_render_references(null); ?>

    </div>
</main>

<?php get_footer();