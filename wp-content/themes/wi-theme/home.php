<?php
/**
 * Template Name: Blog
 * Description: Template for normal post display
 */

get_header(); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<main id="news-main" class="news-main">
    <div class="news-main-container">
		<div class="news-main-title-container">
			<h1>
				Latest News
			</h1>
		</div>
        <?php
        // Query für die Beiträge
        $blog_posts = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 10, // amount of posts
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
							<div class="post-share-icon-wrapper">
								<a href="#" class="share-button" title="Beitrag teilen">
									<i class="fas fa-share-alt"></i>
								</a>								
							</div>
						</div>
                    </header>
                    <div class="post-content">
						<div class="post-title-wrapper">
                        	<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>							
						</div>
						<div class="post-content-excerpt-wrapper">
							 <?php the_excerpt(); ?>							
						</div>
						<div class="post-read-more-wrapper">
							<div class="post-read-more-button-wrapper">
								<a href="<?php the_permalink(); ?>">
									<div class="post-read-more-icon-wrapper">
										<i class="fas fa-chevron-right"></i>
									</div>
									<div class="post-read-more-label-wrapper">
										Read more
									</div>
								</a>							
							</div>
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
    </div>
</main>

<?php get_footer();