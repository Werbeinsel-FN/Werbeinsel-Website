<?php
/* Template Name: Services */

get_header();
?>
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">

<main class="services-main-wrapper wi-services-main" role="main">
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

        <section class="<?php echo $section_class; ?>" aria-labelledby="section-title-<?php echo $section_index; ?>">
            <!-- Section title -->
            <header>
                <h1 id="section-title-<?php echo $section_index; ?>"><?php echo $section_title; ?></h1>
            </header>

            <!-- Desktop layout -->
            <div class="services-grid-desktop">
                <?php foreach ($rows as $row_posts): ?>
                    <?php
                    $class = (count($row_posts) === 3) ? 'services-grid three-columns-row' : 'services-grid two-columns-row';
                    ?>
                    <div class="<?php echo $class; ?>">
                        <?php foreach ($row_posts as $post): setup_postdata($post); ?>
                            <article class="service-item" itemscope itemtype="https://schema.org/Service">
                                <div class="service-thumbnail">
                                    <a href="<?php the_permalink(); ?>" itemprop="url">
                                        <?php the_post_thumbnail('full', ['itemprop' => 'image']); ?>
                                        <div class="service-overlay">
                                            <div class="service-overlay-content">
                                                <div class="service-subtitle">
                                                    <h3 itemprop="description">Lorem Ipsum</h3>
                                                </div>
                                                <div class="service-title">
                                                    <h2 itemprop="name"><?php the_title(); ?></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Mobile layout -->
            <div class="services-grid-mobile">
                <div class="services-carousel">
                    <?php foreach ($posts as $post): setup_postdata($post); ?>
                        <article class="service-item" itemscope itemtype="https://schema.org/Service">
                            <div class="service-thumbnail">
                                <a href="<?php the_permalink(); ?>" itemprop="url">
                                    <?php the_post_thumbnail('full', ['itemprop' => 'image']); ?>
                                    <div class="service-overlay">
                                        <div class="service-overlay-content">
                                            <div class="service-title">
                                                <h2 itemprop="name"><?php the_title(); ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>

        <?php
                    $section_index++;
                endif;
            endforeach;
        ?>
    </div>
</main>

<?php get_footer(); ?>
