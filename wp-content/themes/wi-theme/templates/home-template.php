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
        
        <!-- <div class="content-list kundenlogo-list carousel">
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/83/77f2c94828514284/zwetschke_kunde_l_und_p.png" />             
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/84/3714a587692de166/zwetschke_kunde_kuku.png" />      
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/45/021f68cf42bb6e9c/zwetschke_kunde_kesselhaus.png" />      
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/51/cac6b30b1c3ad457/zwetschke_kunde_radio_fantasy.png" />      
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/47/c056208614ef5dce/zwetschke_kunde_landeswelle.png" />      
            </figure>
            </div>                    
        </div>                

        <div class="content-list kundenlogo-list carousel">
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/96/a20a6c9819d532ce/zwetschke_kunde_energie_specht.png" />             
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/97/a52683d1467c47f4/zwetschke_kunde_easybill.png" />      
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/89/bc6256eefe3487b1/zwetschke_kunde_uli_und_du.png" />      
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/content/cache/kundenlogo/92/722da6bb4caa6557/zwetschke_kunde_hbw.png" />      
            </figure>
            </div>
            <div class="carousel-cell">
            <figure class="customer-logo">
                <img src="https://www.zwetschke.de/images/kundenlogos/xentral/xentral-e-mail-signatur-300px-x.webp" />      
            </figure>
            </div>                    
        </div>   -->

        <?php wi_theme_render_references(null); ?>

    </div>
</main>

<?php get_footer();