<?php
/**
 * Fullscreen overlay menu – otvara se hamburgerom iz headera
 * Layout: logo levo gore, X desno gore, linkovi centrirani, WhatsApp dole desno
 */
if (!function_exists('wi_nav_url')) {
  require_once get_template_directory() . '/inc/wi-nav-links.php';
}
$menu_logo = get_option('wi_theme_logo_light') ?: get_option('wi_theme_logo_dark');
if (!$menu_logo && has_custom_logo()) {
  $menu_logo = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full');
}
$wi_info = get_option('wi_contact_info', array());
$wi_wa_url = !empty($wi_info['whatsapp_url']) ? esc_url_raw($wi_info['whatsapp_url']) : '';
?>
<div id="menu-overlay" class="wi-menu-overlay" aria-hidden="true">
  <div class="wi-menu-overlay__top">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="wi-menu-overlay__logo" aria-label="<?php bloginfo('name'); ?>">
      <?php if ($menu_logo) : ?>
        <img src="<?php echo esc_url($menu_logo); ?>" alt="<?php bloginfo('name'); ?>" class="wi-menu-overlay__logo-img">
      <?php else : ?>
        <span class="wi-menu-overlay__logo-text"><?php bloginfo('name'); ?></span>
      <?php endif; ?>
    </a>
    <button class="wi-menu-overlay__close" aria-label="Menü schließen">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
  </div>
  <div class="wi-menu-overlay__inner">
    <nav class="wi-menu-overlay__nav" role="navigation">
      <a href="<?php echo esc_url(wi_nav_url('services')); ?>" class="wi-menu-overlay__link">Services</a>
      <a href="<?php echo esc_url(wi_nav_url('plakatierung')); ?>" class="wi-menu-overlay__link">Plakatierung</a>
      <a href="<?php echo esc_url(wi_nav_url('arbeiten')); ?>" class="wi-menu-overlay__link">Arbeiten</a>
      <a href="<?php echo esc_url(wi_nav_url('prozess')); ?>" class="wi-menu-overlay__link">Prozess</a>
      <a href="<?php echo esc_url(wi_nav_url('kontakt')); ?>" class="wi-menu-overlay__link">Kontakt</a>
    </nav>
  </div>
  <?php if ($wi_wa_url) : ?>
  <a href="<?php echo esc_attr($wi_wa_url); ?>" target="_blank" rel="noopener noreferrer" class="wi-menu-overlay__wa" aria-label="WhatsApp">
    <span class="wi-menu-overlay__wa-icon">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </span>
    <span class="wi-menu-overlay__wa-text">WhatsApp uns!</span>
  </a>
  <?php endif; ?>
</div>
