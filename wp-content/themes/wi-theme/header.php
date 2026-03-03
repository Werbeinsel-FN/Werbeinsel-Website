<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body>
<header class="wi-header">
  <div class="wi-header__inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="wi-header__logo" aria-label="<?php bloginfo('name'); ?>">
      <?php
      $logo = '';
      if (has_custom_logo()) {
        $logo = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full');
      }
      if (!$logo) $logo = get_option('wi_theme_logo_light') ?: get_option('wi_theme_logo_dark');
      if ($logo) : ?>
        <img src="<?php echo esc_url($logo); ?>" alt="<?php bloginfo('name'); ?>" class="wi-header__logo-img">
      <?php else : ?>
        <span class="wi-header__logo-text"><?php bloginfo('name'); ?></span>
      <?php endif; ?>
    </a>
  </div>
  <button id="toggle-main-menu" type="button" class="wi-header__menu-btn" aria-label="Menü öffnen" aria-expanded="false" aria-controls="menu-overlay">
    <span class="wi-header__menu-icon" aria-hidden="true">
      <span></span><span></span><span></span>
    </span>
  </button>
</header>
<?php get_template_part('mainmenu'); ?>
