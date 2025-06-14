<?php
/**
 * Template Name: Home
 * Description: Template for the page "Home"
 */

get_header(); ?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/home.css?v=<?php echo filemtime(get_template_directory() . '/css/home.css'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<main id="home-main" class="home-main">
    <div class="home-main-container">
        <section class="home-news-container">
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
        </section>



    </div>
</main>

<?php get_footer();