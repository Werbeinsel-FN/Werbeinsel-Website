<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const scrollBtn = document.querySelector('.scroll-top-btn');
    if (scrollBtn) {
      scrollBtn.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }
  });
</script>

<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-kreative-social">
      <a 
        class="footer-hashtag" 
        href="https://www.instagram.com/explore/tags/werbeinsel/" 
        target="_blank" 
        rel="noopener noreferrer"
      >
        #werbeinsel
      </a>
      <div class="social-icons">
        <a href="https://www.tiktok.com" target="_blank" aria-label="TikTok">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="https://www.instagram.com/werbeinsel" target="_blank" aria-label="Instagram">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="https://www.facebook.com/" target="_blank" aria-label="Facebook">
          <i class="fab fa-facebook"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="footer-menu">
    <div class="footer-menu-wrapper">
      <?php
        wp_nav_menu(array(
          'theme_location' => 'footermenu',
          'container' => false, 
          'menu_class' => 'footer-links',
          'fallback_cb' => false
        ));
      ?>
    </div>
  </div>

  <!--
  <div class="footer-copyright">
    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
  </div>
  -->

  <?php wp_footer(); ?>
</footer>
