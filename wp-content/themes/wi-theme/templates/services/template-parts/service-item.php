<?php
    if (!isset($args['service']) || ! $args['service'] instanceof WP_Post) {
        return;
    }

    $service = $args['service'];

    $GLOBALS['post'] = $service; // <-- Manuell setzen!
    setup_postdata($service);
?>

<article class="service-item" itemscope itemtype="https://schema.org/Service">
    <a href="<?= get_permalink($service); ?>" itemprop="url">
        <?= get_the_post_thumbnail($service, 'full', ['itemprop' => 'image']); ?>
        <div class="service-overlay">
            <div class="service-subtitle">
                <?php
                $terms = get_the_terms($service->ID, 'service_category');
                if (!empty($terms) && !is_wp_error($terms)) :
                    echo '<h3 itemprop="description">' . esc_html($terms[0]->name) . '</h3>';
                endif;
                ?>
            </div>
            <div class="service-title">
                <h2 itemprop="name"><?= get_the_title($service); ?></h2>
            </div>
        </div>
    </a>
</article>