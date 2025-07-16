<?php
/* Template Name: Services */

get_header();
?>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/services.css?v=<?php echo filemtime(get_template_directory() . '/css/services.css'); ?>">

<div class="services-main-wrapper">
    <div class="services-inner-wrapper">

        <?php
        // Get all service categories ordered by custom term meta 'term_order'
        $service_categories = get_terms(array(
            'taxonomy' => 'service_category',
            'hide_empty' => true,
        ));

        usort($service_categories, function($a, $b) {
            $order_a = intval(get_term_meta($a->term_id, 'term_order', true));
            $order_b = intval(get_term_meta($b->term_id, 'term_order', true));
            return $order_a <=> $order_b;
        });

        $section_index = 0;

        foreach ($service_categories as $cat) :

            // Query all services for the current category
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

                // Alternate section style for dark/light background
                $section_class = ($section_index % 2 === 1) ? 'service-category-section is-dark' : 'service-category-section';

                $post_count = $query->post_count;

                // Custom titles or fallback to category name
                $section_titles = ['Auf die Straße', 'Lass kleben', 'Design & Design'];
                $section_title = $section_titles[$section_index] ?? esc_html($cat->name);

                // Load all posts into an array for grouping into rows
                $posts = [];
                while ($query->have_posts()) {
                    $query->the_post();
                    $posts[] = get_post();
                }
                wp_reset_postdata();

                // Group posts into rows:
                // - If exactly 3 posts, show all 3 in one row (3 columns)
                // - Otherwise, group by 2 posts per row
                // - For odd counts > 3, last row has 3 posts
                $rows = [];
                if ($post_count === 3) {
                    $rows[] = $posts; // one row with 3 posts
                } else {
                    $i = 0;
                    while ($i < $post_count) {
                        $remaining = $post_count - $i;
                        if ($remaining == 3 && $post_count % 2 !== 0 && $post_count > 3) {
                            // Last row with 3 posts if odd and more than 3 posts
                            $rows[] = array_slice($posts, $i, 3);
                            $i += 3;
                        } else {
                            // Rows with 2 posts
                            $rows[] = array_slice($posts, $i, 2);
                            $i += 2;
                        }
                    }
                }
                ?>

                <div class="<?php echo $section_class; ?>">
                    <h1><?php echo $section_title; ?></h1>

                    <div class="services-grid-desktop">
    <?php foreach ($rows as $row_posts): ?>
        <?php
        $class = (count($row_posts) === 3) ? 'services-grid three-columns-row' : 'services-grid two-columns-row';
        ?>
        <div class="<?php echo $class; ?>">
            <?php foreach ($row_posts as $post): setup_postdata($post); ?>
                <div class="service-item">
                    <div class="service-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail(); ?>
                            <div class="service-overlay">
                                <div class="service-overlay-content">
                                    <div class="service-title"><?php the_title(); ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="services-grid-mobile">
    <div class="services-carousel">
        <?php foreach ($posts as $post): setup_postdata($post); ?>
            <div class="service-item">
                <div class="service-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail(); ?>
                        <div class="service-overlay">
                            <div class="service-overlay-content">
                                <div class="service-title"><?php the_title(); ?></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>
</div>
                </div>

        <?php
                $section_index++;
            endif;
        endforeach;
        ?>

    </div>
</div>

<?php get_footer(); ?>
