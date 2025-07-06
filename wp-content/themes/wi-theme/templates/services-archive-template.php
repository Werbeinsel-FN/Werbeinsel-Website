<?php
/* Template Name: Services */

get_header(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/services.css?v=<?php echo filemtime(get_template_directory() . '/css/services.css'); ?>">
<div class="services-main-wrapper">
    <div class="services-inner-wrapper">
        <div class="services-title-wrapper">
            <h1 class="services-title">Unsere Leistungen:</h1>		
        </div>

        <?php
        $service_categories = array(
            array(
                'slug' => 'webdesign',
                'title' => 'Webdesign',
            ),
            array(
                'slug' => 'seo',
                'title' => 'SEO',
            ),
            array(
                'slug' => 'online-marketing',
                'title' => 'Online-Marketing',
            ),
            array(
                'slug' => 'beratung',
                'title' => 'Beratung',
            ),
        );

        foreach ($service_categories as $cat) :

            $query = new WP_Query(array(
                'post_type' => 'services',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'service_category',
                        'field'    => 'slug',
                        'terms'    => $cat['slug'],
                    ),
                ),
            ));

            if ($query->have_posts()) : ?>

                <div class="service-category-section">
                    <h2 class="service-category-title"><?php echo esc_html($cat['title']); ?></h2>

                    <div class="services-grid">
                        <?php while ($query->have_posts()) : $query->the_post(); ?>
                            <div class="service-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="service-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('full'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="service-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </div>

                                <div class="service-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            <?php endif;
            wp_reset_postdata();

        endforeach; ?>

    </div>
</div>

<?php get_footer(); ?>
