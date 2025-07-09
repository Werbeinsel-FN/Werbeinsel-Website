<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-kreative-social">
      <p class="footer-hashtag">#kreativemeute</p>
      <div class="social-icons">
        <a href="https://www.tiktok.com" target="_blank" aria-label="TikTok">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="https://www.linkedin.com" target="_blank" aria-label="LinkedIn">
          <i class="fab fa-linkedin-in"></i>
        </a>
        <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
          <i class="fab fa-instagram"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="footer-menu">
    <?php
    wp_nav_menu(array(
      'theme_location' => 'footermenu',
  'container' => 'div',
  'container_class' => 'footer-menu-wrapper',
  'menu_class' => 'footer-links'
    ));
    ?>
    <a href="#" class="scroll-top-btn">GANZ NACH OBEN ☝️</a>
  </div>

  <div class="footer-copyright">
    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
  </div>

  <?php wp_footer(); ?>
</footer>
