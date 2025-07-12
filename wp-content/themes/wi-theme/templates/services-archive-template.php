<?php
/* Template Name: Services */

get_header(); ?>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/services.css?v=<?php echo filemtime(get_template_directory() . '/css/services.css'); ?>">

<div class="services-main-wrapper">
    <div class="services-inner-wrapper">
        <div class="services-title-wrapper">
            <h1 class="services-title">Unsere Leistungen:</h1>		
        </div>

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
            ));

            if ($query->have_posts()) :

                $section_class = ($section_index % 2 === 1) ? 'service-category-section is-dark' : 'service-category-section';
                ?>

                <div class="<?php echo $section_class; ?>">
                    <?php if ( $cat->term_id === 4 ) : ?>
                        <h1>Auf die Straße</h1>
                    <?php else : ?>

                        <h2 class="service-category-title"><?php echo esc_html($cat->name); ?></h2>

                    <?php endif; ?>

                    <div class="services-grid">
                        <?php while ($query->have_posts()) : $query->the_post(); ?>
                            <div class="service-item">
                                <div class="service-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php
                                        $content = get_the_content();
                                        preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
                                        if (!empty($image['src'])) {
                                            echo '<img src="' . esc_url($image['src']) . '" alt="' . esc_attr(get_the_title()) . '">';
                                        } else {
                                            echo '<img src="https://via.placeholder.com/600x400?text=No+Image" alt="Placeholder">';
                                        }
                                        ?>
                                        <div class="service-overlay">
                                            <div class="service-overlay-content">
                                                <div class="service-excerpt"><?php echo wp_trim_words(get_the_content(), 10, '...'); ?></div>
                                                <div class="service-title"><?php the_title(); ?></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            <?php
                wp_reset_postdata();
                $section_index++;
            endif;

        endforeach; ?>

    </div>
</div>

<?php get_footer(); ?>
