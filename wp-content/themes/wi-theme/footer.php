<?php
/**
 * Footer – 4 kolone kao u novom dizajnu (Brand, Navigation, Rechtliches, Social)
 */
if (!function_exists('wi_nav_url')) {
  require_once get_template_directory() . '/inc/wi-nav-links.php';
}
// U futeru se prikazuje isključivo logo iz Theme Options → Dark Logo (ni Light kao rezerva)
$footer_logo = get_option('wi_theme_logo_dark', '');
?>
<footer class="wi-footer site-footer">
  <div class="wi-footer__container">
    <div class="wi-footer__grid">
      <!-- Block 1 - Brand -->
      <div class="wi-footer__brand">
        <?php if ($footer_logo) : ?>
          <img src="<?php echo esc_url($footer_logo); ?>" alt="<?php bloginfo('name'); ?>" class="wi-footer__logo">
        <?php else : ?>
          <span class="wi-footer__logo-text"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
        <p class="wi-footer__tagline">Ihre Agentur für klassische Werbung<br>und moderne Sichtbarkeit.</p>
      </div>

      <!-- Block 2 - Navigation -->
      <div class="wi-footer__col">
        <h4 class="wi-footer__heading">Navigation</h4>
        <nav class="wi-footer__nav" aria-label="<?php esc_attr_e('Footer', 'wi-theme'); ?>">
          <a href="<?php echo esc_url(wi_nav_url('services')); ?>">Services</a>
          <a href="<?php echo esc_url(wi_nav_url('plakatierung')); ?>">Plakatierung</a>
          <a href="<?php echo esc_url(wi_nav_url('folierung')); ?>">Folierung</a>
          <a href="<?php echo esc_url(wi_nav_url('digitale-werbemittel')); ?>">Digitale Werbemittel</a>
          <a href="<?php echo esc_url(wi_nav_url('drucksachen')); ?>">Drucksachen</a>
          <a href="<?php echo esc_url(wi_nav_url('arbeiten')); ?>">Arbeiten</a>
          <a href="<?php echo esc_url(wi_nav_url('prozess')); ?>">Prozess</a>
          <a href="<?php echo esc_url(wi_nav_url('kontakt')); ?>">Kontakt</a>
        </nav>
      </div>

      <!-- Block 3 - Rechtliches -->
      <div class="wi-footer__col">
        <h4 class="wi-footer__heading">Rechtliches</h4>
        <nav class="wi-footer__nav">
          <a href="<?php echo esc_url(wi_nav_url('impressum')); ?>">Impressum</a>
          <a href="<?php echo esc_url(wi_nav_url('datenschutz')); ?>">Datenschutz</a>
          <a href="<?php echo esc_url(wi_nav_url('agb')); ?>">AGB</a>
        </nav>
      </div>

      <!-- Block 4 - Social Media -->
      <div class="wi-footer__col wi-footer__social-col">
        <h4 class="wi-footer__heading">Social Media</h4>
        <div class="wi-footer__social">
          <a href="#" class="wi-footer__social-btn" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
          </a>
          <a href="#" class="wi-footer__social-btn" aria-label="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
          <a href="#" class="wi-footer__social-btn" aria-label="TikTok">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
          </a>
        </div>
      </div>
    </div>

    <div class="wi-footer__bottom">
      <p class="wi-footer__copy">© <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Alle Rechte vorbehalten.</p>
    </div>
  </div>

  <?php
  $wi_info = get_option('wi_contact_info', array());
  $wi_wa_url = !empty($wi_info['whatsapp_url']) ? esc_url_raw($wi_info['whatsapp_url']) : '';
  if ($wi_wa_url) :
  ?>
  <a href="<?php echo esc_attr($wi_wa_url); ?>" target="_blank" rel="noopener noreferrer" class="wi-floating-wa" aria-label="WhatsApp">
    <span class="wi-floating-wa__icon-wrap">
      <svg class="wi-floating-wa__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
        <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
      </svg>
      <span class="wi-floating-wa__pulse" aria-hidden="true"></span>
    </span>
    <span class="wi-floating-wa__text">WhatsApp uns!</span>
  </a>
  <?php endif; ?>

  <?php wp_footer(); ?>
</footer>
