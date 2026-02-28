<?php
/**
 * Footer – 4 kolone kao u novom dizajnu (Brand, Navigation, Rechtliches, Social)
 */
if (!function_exists('wi_nav_url')) {
  require_once get_template_directory() . '/inc/wi-nav-links.php';
}
$footer_logo = get_option('wi_theme_logo_dark') ?: get_option('wi_theme_logo_light');
?>
<script>
(function() {
  let initialized = false;
  function initScrollToTop() {
    if (initialized) return;
    const rocketBtn = document.querySelector(".wi-footer__rocket");
    if (!rocketBtn) return;
    rocketBtn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      const first = document.querySelector('#home-hero') || document.querySelector('header') || document.body;
      if (first && first.scrollIntoView) {
        first.scrollIntoView({ behavior: "smooth", block: "start" });
      } else {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
    });
    initialized = true;
  }
  if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initScrollToTop);
  } else {
    initScrollToTop();
  }
})();
</script>
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
        <a href="#" class="wi-footer__rocket" aria-label="Nach oben">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="wi-footer__rocket-icon"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>
        </a>
      </div>
    </div>

    <div class="wi-footer__bottom">
      <p class="wi-footer__copy">© <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Alle Rechte vorbehalten.</p>
    </div>
  </div>
  <?php wp_footer(); ?>
</footer>
