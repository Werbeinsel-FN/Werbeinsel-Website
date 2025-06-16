<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <?php wp_head(); ?>
</head>
<body style="background-color: <?php echo esc_attr(get_option('wi_theme_background_color', '#ffffff')); ?>;">
    <header>
        <div class="logo-wrapper">
            <?php
            $light_logo = get_option('wi_theme_logo_light');
            ?>
            <?php if ($light_logo): ?>
				<a href="<?php echo esc_url(home_url('/')); ?>">
                	<img src="<?php echo esc_url($light_logo); ?>" alt="Light Logo" class="logo-light">
				</a>
            <?php endif; ?>
        </div>
        <?php get_template_part('mainmenu'); ?>
    </header>
</body>
</html>