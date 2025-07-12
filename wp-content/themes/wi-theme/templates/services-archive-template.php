<?php
/* Template Name: Services */

get_header(); ?>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/services.css?v=<?php echo filemtime(get_template_directory() . '/css/services.css'); ?>">

<div class="services-main-wrapper">
    <div class="services-inner-wrapper">
        <!-- <div class="services-title-wrapper">
            <h1 class="services-title">Unsere Leistungen:</h1>		
        </div> -->

        <?php
        // load categories, but only those which have min 1 service assigned (?) 
        $service_categories = get_terms(array(
            'taxonomy' => 'service_category',
            'hide_empty' => true,
            // 'orderby'    => 'term_order',
            // 'order'      => 'ASC',
        ));

        usort($service_categories, function($a, $b) {
            $order_a = intval(get_term_meta($a->term_id, 'term_order', true));
            $order_b = intval(get_term_meta($b->term_id, 'term_order', true));
            return $order_a <=> $order_b;
        } );

        $section_index = 0;

        foreach ($service_categories as $cat) :

            $query = new WP_Query(array(
                'post_type' => 'services',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'service_category',
                        'field'    => 'slug',
                        'terms'    => $cat->slug,
                    ),
                ),
                'orderby' => 'menu_order',
                'order'   => 'ASC',
            ));

            if ($query->have_posts()) :

                $section_class = ($section_index % 2 === 1) ? 'service-category-section is-dark' : 'service-category-section';
                ?>

                <div class="<?php echo $section_class; ?>">
                    <?php if ( $cat->term_id === 4 ) : ?>
                        <h1>Auf die Straße</h1>
                    <?php elseif ( $cat->term_id === 5 ) : ?>
                        <h1>Lass kleben</h1>     
                    <?php elseif ( $cat->term_id === 6 ) : ?>
                        <h1>Design & Design</h1>                                           
                    <?php /*else : ?>

                        <h2 class="service-category-title"><?php echo esc_html($cat->name); ?></h2>

                    <?php*/ endif; ?>

                    <?php if ( $cat->term_id === 4 ) : ?>
                        <div class="services-grid category-4">
                            <div class="category-4-top">
                                <?php 
                                $i = 0;
                                while ($query->have_posts()) : $query->the_post(); 
                                $i++;
                                if ($i <= 2) : ?>
                                    <div class="service-item service-item-<?php echo $i; ?>">
                                        <div class="service-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail(); ?>
                                                <div class="service-overlay">
                                                    <div class="service-overlay-content">
                                                        <div class="service-excerpt"><?php echo wp_trim_words(get_the_content(), 10, '...'); ?></div>
                                                        <div class="service-title"><?php the_title(); ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endwhile; ?>
                            </div>

                            <div class="category-4-bottom">
                                <?php 
                                // Query zurücksetzen, um wieder bei Post 3 anzufangen:
                                $query->rewind_posts();
                                $i = 0;

                                while ($query->have_posts()) : $query->the_post(); 
                                $i++;
                                if ($i > 2) : ?>
                                    <div class="service-item service-item-<?php echo $i; ?>">
                                        <div class="service-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail(); ?>
                                                <div class="service-overlay">
                                                    <div class="service-overlay-content">
                                                        <div class="service-excerpt"><?php echo wp_trim_words(get_the_content(), 10, '...'); ?></div>
                                                        <div class="service-title"><?php the_title(); ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php else: ?>


                        <div class="services-grid category-<?php echo $cat->term_id; ?>">
                            <?php 
                            $i = 0;
                            while ($query->have_posts()) : $query->the_post(); 
                            $i++;
                            ?>
                                <div class="service-item service-item-<?php echo $i; ?>">
                                    <div class="service-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail(); ?>
                                            <div class="service-overlay">
                                                <div class="service-overlay-content">
                                                    <!-- <div class="service-excerpt"><?php echo wp_trim_words(get_the_content(), 10, '...'); ?></div> -->
                                                    <div class="service-title"><?php the_title(); ?></div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                    <?php endif; ?>

                </div>

            <?php
                wp_reset_postdata();
                $section_index++;
            endif;

        endforeach; ?>

    </div>
</div>

<?php get_footer(); ?>
