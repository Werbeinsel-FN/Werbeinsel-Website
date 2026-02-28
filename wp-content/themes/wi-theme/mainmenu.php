<?php
/**
 * Fullscreen overlay menu – otvara se hamburgerom iz headera
 */
if (!function_exists('wi_nav_url')) {
  require_once get_template_directory() . '/inc/wi-nav-links.php';
}
?>
<div id="menu-overlay" class="wi-menu-overlay" aria-hidden="true">
  <div class="wi-menu-overlay__inner">
    <button class="wi-menu-overlay__close" aria-label="Menü schließen">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
    <nav class="wi-menu-overlay__nav" role="navigation">
      <a href="<?php echo esc_url(wi_nav_url('services')); ?>" class="wi-menu-overlay__link">Services</a>
      <a href="<?php echo esc_url(wi_nav_url('plakatierung')); ?>" class="wi-menu-overlay__link">Plakatierung</a>
      <a href="<?php echo esc_url(wi_nav_url('arbeiten')); ?>" class="wi-menu-overlay__link">Arbeiten</a>
      <a href="<?php echo esc_url(wi_nav_url('prozess')); ?>" class="wi-menu-overlay__link">Prozess</a>
      <a href="<?php echo esc_url(wi_nav_url('kontakt')); ?>" class="wi-menu-overlay__link">Kontakt</a>
    </nav>
  </div>
</div>
