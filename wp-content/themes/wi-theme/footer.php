<footer>
    <div class="footer-widgets">
        <?php if (is_active_sidebar('footer-widget')) : ?>
            <?php dynamic_sidebar('footer-widget'); ?>
        <?php else : ?>
            <p><?php _e('Füge Widgets hinzu unter Design > Widgets.', 'wi-theme'); ?></p>
        <?php endif; ?>
    </div>
    <p class="footer-copyright" style="border-top:0.05rem solid white;padding-top:1rem;">
        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
    </p>
    <?php wp_footer(); ?>
</footer>