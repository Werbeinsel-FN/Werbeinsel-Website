<?php
    if (!isset($args['service']) || ! $args['service'] instanceof WP_Post) {
        return;
    }

    $service = $args['service'];

    $GLOBALS['post'] = $service; // <-- Manuell setzen!
    setup_postdata($service);
?>

<article class="service-item" itemscope itemtype="https://schema.org/Service">
    <?= get_the_post_thumbnail($service, 'full', ['itemprop' => 'image']); ?>
        <div class="service-overlay">
            <div class="service-subtitle">
                <?php
                $terms = get_the_terms($service->ID, 'service_category');
                if (!empty($terms) && !is_wp_error($terms)) :
                    $term = $terms[0];
                    $term_link = get_term_link($term);
                    if (!is_wp_error($term_link)) :
                ?>
                        <h3 itemprop="description">
                            <a href="<?= esc_url($term_link); ?>" itemprop="category"><?= esc_html($term->name); ?></a>
                        </h3>
                <?php
                    endif;
                endif;
                ?>
            </div>
            <div class="service-title">
                <h2 itemprop="name">
                    <a href="<?= get_permalink($service); ?>" itemprop="url">
                        <?= get_the_title($service); ?>
                    </a>
                </h2>
            </div>
        </div>
</article>