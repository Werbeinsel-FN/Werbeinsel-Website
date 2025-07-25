<?php
/**
 * Template für die Taxonomie "service_category"
 * Pfad: templates/services/service-category-template.php
 */

get_header(); ?>

<main class="container">
    <h1><?php single_term_title(); ?></h1>
    <p><?php echo term_description(); ?></p>

    <div class="service-list">
        <?php if (have_posts()) : ?>
            <ul class="service-archive-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <li class="service-item">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="service-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>
                            <h2 class="service-title"><?php the_title(); ?></h2>
                        </a>
                        <div class="service-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>

            <?php the_posts_pagination(); ?>

        <?php else : ?>
            <p>Keine Leistungen in dieser Kategorie gefunden.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
